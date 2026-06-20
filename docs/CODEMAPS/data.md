<!-- Generated: 2026-06-20 | Models, relationships, tables -->

# Data — MySocialVSL (app/Models, database/migrations)

## Models & relationships
```
User 1──* Page          User::pages()         Page::user()
User 1──* SocialAccount  User::socialAccounts()
Page 1──* PageLink       Page::links()  (orderBy order)
Page 1──* PageGeoRule    Page::geoRules() (orderBy order)
Page 1──* AnalyticsEvent Page::analytics()
User self-ref: referred_by → users.id (affiliate)
Notifications: morphMany (Laravel database channel) on User (Notifiable)
```

## Key tables / columns
```
users          plan(free|pro|agency), is_admin, is_beta_partner, stripe_customer_id, stripe_subscription_id,
               trial_ends_at, trial_reminders(json), plan_expires_at, agency_pages, agency_links (0=∞),
               extra_pages, extra_links, affiliate_code, referred_by, timezone, preferences(json), avatar_url
pages          slug, user_id, is_active, page_type(vsl|direct), direct_url, custom_domain, bot_protection,
               deep_link_enabled, strict_deep_link, link_preview_enabled, group_name, video_url,
               vsl_enabled/position, fomo_*, popup_*, cta_reveal, meta_title/description, og_image_url, utm_passthrough
page_links     page_id, type(onlyfans|mym|telegram|instagram|custom), label, url, title, subtitle,
               image_url, meta(json), order, is_visible, btn_color
page_geo_rules page_id, country_code, redirect_url, order
analytics_events page_id, type, device, country, (video position fields)
social_accounts  user_id, platform, ... (sync placeholder)
personal_access_tokens  + kind(session|api), ip_address, user_agent   (Sanctum, used for new-login detect)
notifications  uuid id, type, notifiable, data(json: category/title/description/icon/meta), read_at
```

## Analytics event types
`page_view` · `link_click` · `bot_blocked` · `video_play` · `time_on_page`

## Migrations
~24 files in database/migrations (base tables + parity/agency/trial/popup/video fields + notifications).
⚠️ Local SQLite migration table is out of sync (fails on create_page_links "already exists") — for a NEW migration use `php artisan migrate --path=<file>`. Prod DB (managed) is unaffected.
