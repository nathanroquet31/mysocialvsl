<!-- Generated: 2026-06-20 | External services + libs -->

# Dependencies — MySocialVSL

## External services
```
Stripe        billing. config/services.php → stripe.{secret, price_pro, price_agency, price_extra_pages,
              price_extra_links, agency_tiers{pages,links: 50..800,0=∞ → price ids}, agency_tier_amounts}.
              Webhook: POST /api/billing/webhook (STRIPE_WEBHOOK_SECRET).
Cloudflare R2 media storage (S3 driver). config/filesystems.php disk 'r2'. UploadController switches to R2
              when R2_ACCESS_KEY_ID set, else local. Public via r2.dev / cdn domain. Free egress. No transcoding
              (ffmpeg absent on Laravel Cloud) → accept mp4/webm only.
Resend        transactional email (SMTP). services.resend / MAIL_*. Used for password reset + trial reminders.
Stevebauman/Location  IP → country/city. Used in cloaking geo-routing + NewLogin notification (best-effort, never blocks).
Sanctum       token auth (session tokens + API keys with abilities v3:read/v3:write).
Instagram/Slack  config keys present but inactive (social analytics "Soon").
```

## Backend (composer)
Laravel (PHP 8.5) · laravel/sanctum · stripe/stripe-php · stevebauman/location.

## Frontend (frontend/package.json)
vue ^3.5 · pinia · vue-router · axios · tailwindcss ^4 (@tailwindcss/vite) · vue-chartjs · globe.gl · @number-flow/vue.
Icons: Bootstrap Icons (global). Build: Vite → public/app (+ .vite/manifest.json read by resources/views/app.blade.php).

## Env (key vars, see DEPLOY.md)
APP_URL (distinguishes custom domains) · DB_* · R2_* · STRIPE_*/STRIPE_PRICE_* · MAIL_* (Resend) · FRONTEND_URL.
Do NOT set VITE_API_URL at build (axios uses relative /api).
