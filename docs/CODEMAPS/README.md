<!-- Generated: 2026-06-20 | Maintainer: read this first each session before exploring the code -->

# CODEMAPS — MySocialVSL

Token-lean architecture maps so an AI agent (or a human) gets oriented in **one read** instead of exploring 100 files. Inspired by the ECC "codemaps" idea, generated from this repo.

| Map | What it covers |
|-----|----------------|
| [architecture.md](architecture.md) | Single-origin topology, the `/p/{slug}` cloaking flow, data flow |
| [backend.md](backend.md) | Routes → controllers → services, cloaking, Stripe, commands, notifications |
| [frontend.md](frontend.md) | Vue SPA: router pages, layout, key components, stores |
| [data.md](data.md) | Models, relationships, tables, analytics event types |
| [dependencies.md](dependencies.md) | Stripe, Cloudflare R2, Resend, Sanctum, geo, frontend libs |

**Refresh policy:** regenerate after a major feature or refactor. If a map looks stale vs the code, trust the code and flag it. Stack: Laravel (PHP 8.5) API + SSR public pages · Vue 3 SPA (`frontend/`) · single origin · SQLite (local) / managed DB (prod) · Cloudflare R2 media.
