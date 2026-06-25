<!-- Generated: 2026-06-20 | Vue 3 SPA in frontend/ -->

# Frontend — MySocialVSL (Vue 3 SPA, frontend/src)

Vue 3 + Pinia + vue-router + axios + Tailwind v4. Built to `public/app/` (served by Laravel). Light mode is the default; a light/dark toggle drives theme-aware CSS vars (see `stores/theme.js`) — mockups/devices stay dark on purpose. Icons: Bootstrap Icons (`bi bi-*`). Components use **inline `:style` objects** keyed on theme refs/CSS vars, not utility classes. Unit tests: Vitest (`npm run test`).

## Router pages (src/pages)
```
PUBLIC:  Home (landing) · Login · Register · ForgotPassword · ResetPassword · PublicPage (fan-facing VSL) · legal/{Terms,PrivacyPolicy,Cookies}
APP:     Dashboard (analytics overview) · AnalyticsDashboard (+ globe live) · PageCreate (create+edit, 3 VSL templates)
         Billing · AffiliatePage · DomainsPage · ApiAccessPage · SocialAnalyticsPage · ProfilePage · SettingsPage
         AdminPage · HelpCenterPage · CreatorGuide · SupportPage · LegacyPage
```

## Layout & key components (src/components)
```
DashboardLayout.vue   sidebar + header; hosts <NotificationBell/> + <PlanUpgradePopup/> + header-actions slot
NotificationBell.vue  bell + dropdown; polls /notifications/unread-count (60s); tabs by category, This week/Earlier
PlanUpgradePopup.vue  celebratory modal; polls /notifications/plan-upgrade (45s) + on mount; celebrated ids in localStorage
AgencyConfigurator.vue Agency tier sliders (pages/links, +$10/25)
GlobeViz.vue          live analytics globe (globe.gl)  ·  DeeplinkDemo / PhoneDemo / VSLPageMockup  landing visuals
PublicBlocks.vue      renders the public VSL page blocks  ·  VideoUpload.vue  R2 upload
NavItem · StatCard · DateRangePicker · ThemeToggle · ToggleSwitch · Tooltip · LogoMark
```

## Stores & lib (src/stores, src/lib)
```
stores/auth.js    user, token, login/logout, loads /me
stores/theme.js   dark/light tokens via CSS vars (dark-only in V1)
lib/axios.js      api instance, baseURL /api, Bearer token from localStorage
lib/legalLang.js  legal page i18n helper
```

## Conventions
- API calls via `import api from '@/lib/axios'`.
- New dashboard UI → match DashboardLayout style (inline styles + theme refs + bi icons).
- Public page rendering must stay consistent with server cloaking (see architecture.md).
