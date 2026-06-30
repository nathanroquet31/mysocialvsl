<?php

namespace App\Console\Commands;

use App\Services\MonitoringDigest;
use App\Services\TelegramNotifier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Daily self-monitoring report, sent server-side to the founder's own Telegram.
 *
 * Runs entirely inside our own infrastructure: it probes our public prod URL
 * (health, TLS, cloaking, API) and reads our own DB for the business digest,
 * then pushes a single summary to our private Telegram chat. Secrets live only
 * in the app's environment (TELEGRAM_*), never leaving our boundary.
 *
 * Every check is wrapped so one failure never aborts the run — the Telegram
 * summary always goes out, even when prod is degraded (that's the whole point).
 */
class DailyMonitoringReport extends Command
{
    protected $signature = 'monitoring:report';

    protected $description = 'Probe prod (health/TLS/cloaking/API) + business digest, and send the summary to Telegram';

    private const UA_HUMAN = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile Safari/604.1';

    private const UA_BOT = 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)';

    public function handle(MonitoringDigest $digest): int
    {
        $base = rtrim((string) config('app.url'), '/') ?: 'https://mysocialvsl.com';
        $slug = (string) config('services.monitoring.slug', 'lillylou');

        $anomaly = false;
        $lines   = [];

        $lines[] = $this->checkHealth($base, $anomaly);
        $lines[] = $this->checkTls($base);
        $lines[] = $this->checkCloaking($base, $slug, $anomaly);
        $lines[] = $this->checkApi($base, $slug, $anomaly);
        $lines[] = $this->checkHome($base);

        $head = $anomaly ? '🚨 MySocialVSL ANOMALIE' : '✅ MySocialVSL OK';

        $message = implode("\n", [
            $head,
            ...$lines,
            '— Mes chiffres (24h) —',
            $this->businessBlock($digest),
            now()->utc()->format('H:i') . ' UTC',
        ]);

        $this->line($message);

        $this->sendTelegram($message);

        return self::SUCCESS;
    }

    /** C1 — /up should be 200; warn on slow responses. */
    private function checkHealth(string $base, bool &$anomaly): string
    {
        try {
            $start  = microtime(true);
            $status = Http::timeout(15)->withUserAgent(self::UA_HUMAN)->get("{$base}/up")->status();
            $secs   = round(microtime(true) - $start, 2);

            if ($status === 200) {
                return $secs > 2.5 ? "⚠️ Santé 200 {$secs}s (lent)" : "✅ Santé 200 {$secs}s";
            }
            $anomaly = true;

            return "🚨 Santé {$status}";
        } catch (Throwable $e) {
            $anomaly = true;

            return '🚨 Santé injoignable';
        }
    }

    /** C2 — TLS certificate expiry; warn under 14 days. */
    private function checkTls(string $base): string
    {
        try {
            $host = parse_url($base, PHP_URL_HOST) ?: 'mysocialvsl.com';
            $ctx  = stream_context_create(['ssl' => ['capture_peer_cert' => true, 'verify_peer' => false, 'verify_peer_name' => false]]);
            $sock = @stream_socket_client("ssl://{$host}:443", $errno, $errstr, 8, STREAM_CLIENT_CONNECT, $ctx);

            if (! $sock) {
                return '⚠️ TLS inconnu';
            }

            $params = stream_context_get_params($sock);
            fclose($sock);
            $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate'] ?? null);
            $expTs = $cert['validTo_time_t'] ?? null;

            if (! $expTs) {
                return '⚠️ TLS inconnu';
            }
            $days = (int) floor(($expTs - time()) / 86400);

            return $days < 14 ? "⚠️ TLS {$days}j" : "✅ TLS {$days}j";
        } catch (Throwable $e) {
            return '⚠️ TLS inconnu';
        }
    }

    /**
     * C3 — cloaking, the core value: a human UA gets the real SPA shell
     * (contains /app/assets), a Meta crawler UA gets the decoy (no /app/assets).
     */
    private function checkCloaking(string $base, string $slug, bool &$anomaly): string
    {
        try {
            $human = Http::timeout(15)->withUserAgent(self::UA_HUMAN)->get("{$base}/{$slug}")->body();
            $bot   = Http::timeout(15)->withUserAgent(self::UA_BOT)->get("{$base}/{$slug}")->body();

            $humanReal = str_contains($human, '/app/assets');
            $botReal   = str_contains($bot, '/app/assets');

            if ($humanReal && ! $botReal) {
                return '✅ Cloaking OK (vraie page + decoy)';
            }
            if (! $humanReal && $botReal) {
                $anomaly = true;

                return '🚨 Cloaking INVERSÉ';
            }

            return "⚠️ Cloaking à vérifier (slug {$slug} ?)";
        } catch (Throwable $e) {
            return '⚠️ Cloaking injoignable';
        }
    }

    /** C4 — public page API returns 200 (a 500 means the DB path is broken). */
    private function checkApi(string $base, string $slug, bool &$anomaly): string
    {
        try {
            $status = Http::timeout(15)->withUserAgent(self::UA_HUMAN)->get("{$base}/api/p/{$slug}")->status();

            if ($status === 200) {
                return '✅ API 200';
            }
            if ($status === 500) {
                $anomaly = true;

                return '🚨 API 500';
            }

            return "⚠️ API {$status}";
        } catch (Throwable $e) {
            return '⚠️ API injoignable';
        }
    }

    /** C5 — homepage serves the SPA shell. */
    private function checkHome(string $base): string
    {
        try {
            $body = Http::timeout(15)->withUserAgent(self::UA_HUMAN)->get("{$base}/")->body();

            return str_contains($body, '/app/assets') ? '✅ Home OK' : '⚠️ Home sans /app/assets';
        } catch (Throwable $e) {
            return '⚠️ Home injoignable';
        }
    }

    /** Formats the read-only business snapshot into the Telegram body. */
    private function businessBlock(MonitoringDigest $digest): string
    {
        try {
            $d  = $digest->snapshot();
            $bp = $d['by_plan'];

            return implode("\n", [
                "🆕 Inscriptions: {$d['signups_24h']}",
                "📄 Pages: {$d['pages_created_24h']}",
                "💳 Payants: {$d['paying_users']} (free {$bp['free']}/pro {$bp['pro']}/agency {$bp['agency']})",
                "⏳ Trials: {$d['active_trials']} (<48h: {$d['trials_expiring_48h']})",
                "👀 Visites: {$d['visits_24h']} · Clics: {$d['clicks_24h']}",
            ]);
        } catch (Throwable $e) {
            return 'Stats: indispo (erreur DB)';
        }
    }

    /** Push the summary to the founder's own Telegram chat (config-driven). */
    private function sendTelegram(string $message): void
    {
        if (! config('services.telegram.bot_token') || ! config('services.telegram.chat_id')) {
            $this->warn('Telegram not configured (TELEGRAM_BOT_TOKEN / TELEGRAM_CHAT_ID) — report not sent.');

            return;
        }

        app(TelegramNotifier::class)->send($message)
            ? $this->info('Telegram report sent.')
            : $this->error('Telegram send failed.');
    }
}
