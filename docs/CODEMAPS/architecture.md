<!-- Generated: 2026-06-20 | High-level system + the cloaking flow -->

# Architecture — MySocialVSL

**Single origin: everything served by Laravel** so public VSL pages + custom domains pass through server-side cloaking BEFORE any JS loads. SPA built into `public/app/` (git-ignored, built on deploy).

```
Browser ──> Laravel Cloud (PHP + SPA shell in public/app)
              ├─ /                → custom.domain mw → page (if custom domain) | SPA home
              ├─ /p/{slug}        → cloaking → decoy (bot) | redirect (geo/direct) | SPA shell (human)
              ├─ /dashboard, ...  → SPA shell (Vue router)
              ├─ /api/*           → JSON API (Sanctum)
              └─ media (video/img)→ Cloudflare R2 (free egress, r2.dev / cdn domain)
```

## The core: public VSL page request `/p/{slug}`
The product's moat. Lives in `PublicPageController::show` + trait `Concerns/ResolvesPublicPages`.
```
resolvePublicPage(slug)        # active page + links + geoRules + user; force deeplink off if plan=free; white-label only if plan=agency
  → publicPageGuards():        # run server-side, before any JS
      bot? (Shield)            → log bot_blocked, return decoy HTML   # Instagram is NOT a bot
      geo rule match?          → log page_view, redirect away
      page_type=direct?        → render direct-redirect.blade (intent:// / extbrowser bypass)
      else                     → null
  → renderApp(slug, page)      # SPA shell; OG tags for non-protected; X-Robots noindex if bot_protection
```
Same trait feeds BOTH web (HTML) and API (`PageController::showPublic`) so cloaking can't diverge.

## Deploy
Push `main` → Laravel Cloud builds (composer + `npm --prefix frontend run build` → `public/app` + manifest, `migrate --force`, caches). See `DEPLOY.md`. Health: `GET /up`.

## Plan gating (server-side, in resolvePublicPage)
`free` = deeplink/strict off · `agency` = white-label (hides "Powered by"). Aligned with competitor GetMySocial.
