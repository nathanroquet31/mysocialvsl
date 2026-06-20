<!-- Generated: 2026-06-20 | Routes → controllers → services -->

# Backend — MySocialVSL (Laravel, app/)

## Web routes (routes/web.php) — SSR + cloaking
```
GET /p/{slug}   [custom.domain] → PublicPageController::show   # cloaking, see architecture.md
GET /           [custom.domain] → PublicPageController::root   # page on custom domain, else SPA home
GET /{any}      → PublicPageController::spa                     # SPA fallback (excludes api/ storage/ app/)
```

## API routes (routes/api.php) — prefix /api, auth:sanctum unless noted
```
PUBLIC:
  POST /register /login /forgot-password /reset-password   AuthController (throttled)
  POST /billing/webhook                                    StripeController::webhook (no auth)
  GET  /p/{slug}            [custom.domain]                PageController::showPublic  (JSON, same cloaking trait)
  POST /p/{slug}/event      [custom.domain, throttle]      AnalyticsController::track

AUTH (manager):
  GET  /me · POST /logout                                  AuthController
  apiResource pages                                        PageController (CRUD, quota 403 plan_limit)
  GET  /pages/{id}/analytics · /analytics/dashboard · /analytics/live   AnalyticsController
  GET  /slugs/{slug}/available                             PageController::checkSlug
  PATCH /user · /user/password · DELETE /user             UserController
  POST /upload/image · /upload/video                      UploadController (R2 if configured)
  POST /billing/checkout · /preview · /portal · /addons   StripeController
  GET/PUT /pages/{id}/geo-rules                            GeoRuleController
  GET/POST/DELETE /api-keys                                ApiKeyController (shown once at mint)
  GET/DELETE /sessions                                     SessionController
  GET  /affiliate                                          AffiliateController (read-only stub, no payout)
  *    /social-accounts                                    SocialAccountController (sync = no-op, "Soon")
  GET  /notifications · /unread-count · /plan-upgrade · POST /read-all · PATCH /{id}/read   NotificationController
  ADMIN (mw admin): /admin/metrics /users /{u}/beta-partner /{u}/grant-plan /{u}/pages    AdminController

V3 (Bearer API key, scopes v3:read/write):
  GET /v3 /v3/me /v3/links /v3/links/{id} /v3/links/{id}/analytics
```

## Key flows
- **Cloaking**: `Concerns/ResolvesPublicPages` (resolvePublicPage + publicPageGuards) — shared by web + API. See architecture.md.
- **Stripe** `StripeController`: checkout (pro/agency + tiered extra packs), in-place sub update w/ proration, portal, addons. Webhook → `handleCheckoutCompleted` / `handleSubscriptionUpdated` / `handleSubscriptionDeleted` / `handlePaymentFailed`. Each plan change → `PlanChanged` notification.
- **Trials**: card-free via `User::startTrial`. `ProcessTrials` command (scheduled daily 09:00, routes/console.php) → reminders 7d/1d + downgrade on expiry.

## Middleware (app/Http/Middleware)
`CustomDomainMiddleware` (host → page.custom_domain → inject slug) · `EnsureAdmin` (is_admin) · `SecureHeaders`.

## Console commands (app/Console/Commands)
`user:make-admin` (MakeAdmin) · `user:set-plan` (SetPlan, → PlanChanged) · `trials:process` (ProcessTrials).

## Notifications (app/Notifications, channel=database)
`Welcome` · `PlanChanged` (billing) · `PaymentFailed` · `NewLogin` (security, new-IP only) · `TrialReminder` (mail).

## Known gaps (see memory project_audit_v1)
Affiliate = tracker without payout · Social analytics = placeholder · Custom domains: server resolves but `DomainsPage.vue` saves to wrong endpoint (`/user` instead of `/pages/{id}`).
