<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Sends a plain-text message to the founder's own Telegram chat.
 *
 * Used for operational pings (daily monitoring report, real-time billing
 * events). Never throws: a Telegram outage must never break the request that
 * triggered the ping (e.g. a Stripe webhook). Returns whether it was sent.
 */
class TelegramNotifier
{
    public function send(string $message): bool
    {
        $token  = (string) config('services.telegram.bot_token');
        $chatId = (string) config('services.telegram.chat_id');

        if ($token === '' || $chatId === '') {
            return false;
        }

        try {
            $send = fn () => Http::timeout(10)
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text'    => $message,
                ])
                ->json('ok') === true;

            // One retry — transient Telegram hiccups are common.
            if ($send()) {
                return true;
            }
            if ($send()) {
                return true;
            }

            Log::warning('TelegramNotifier: send returned ok != true');

            return false;
        } catch (Throwable $e) {
            Log::warning('TelegramNotifier: ' . $e->getMessage());

            return false;
        }
    }
}
