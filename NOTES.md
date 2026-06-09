# MySocialVSL — Notes techniques

## Stack

| Couche | Tech |
|--------|------|
| Frontend | Vue 3 + Vite + Tailwind v4 + AOS |
| Backend | Laravel 11 + Sanctum |
| CSS | Inter (Google Fonts) + inline styles pour les spacings critiques |
| Paiement | Stripe (à intégrer) |
| Deploy | Netlify (frontend) / Hetzner VPS (prod) |

---

## Bug critique résolu : Tailwind v4 spacing = 0

**Problème** : Dans Tailwind v4, toutes les utilities de spacing génèrent :
```css
.mb-8  { margin-bottom: calc(var(--spacing) * 8); }
.py-16 { padding: calc(var(--spacing) * 16) 0; }
```
Si `--spacing` n'est pas défini → `calc(undefined * 8)` = **0px**. Sections écrasées ou vides noirs.

**Fix appliqué** dans `style.css` :
```css
:root { --spacing: 0.25rem; }
```

**Fix définitif** : pour tous les spacings critiques (sections, grids, padding), utiliser des **styles inline** au lieu des utilities Tailwind :
```html
<!-- NE PAS faire -->
<div class="mb-8 gap-20 py-16">

<!-- FAIRE -->
<div style="margin-bottom:32px;gap:80px;padding:64px 0">
```

---

## Bug input bloqué dans PageCreate

**Problème** : `<template v-for>` dans le stepper + `v-if` sur les steps → les `<input>` étaient détruits/recréés à chaque changement d'étape, empêchant la saisie.

**Fix** : remplacer `<template v-for>` par `<div v-for>` dans le stepper, et utiliser `v-show` au lieu de `v-if` pour les steps (les inputs restent montés).

---

## Deep Linking Instagram → App native

**Problème** : Instagram ouvre les liens dans son WebView interne. OnlyFans ne fonctionne pas bien dedans — friction, cookies bloqués, paiements qui échouent.

**Solution** (`index.html` de la landing Karine) :
```javascript
(function() {
  var ua = navigator.userAgent || '';
  if (!/Instagram/.test(ua)) return;

  var url = window.location.href;
  var enc = encodeURIComponent(url);
  var isAndroid = /Android/.test(ua);

  if (isAndroid) {
    var u = new URL(url);
    var scheme = 'intent://' + u.host + u.pathname + u.search
      + '#Intent;scheme=https;S.browser_fallback_url=' + enc + ';end';
  } else {
    var scheme = 'instagram://extbrowser/?url=' + enc;
  }

  window.location.href = scheme;
  setTimeout(function() { window.location.href = url; }, 1500);
})();
```

**Résultat** : 297 clics, 20 subs, **6.73% CVR** en 24h après mise en place.

---

## Backend Laravel — Points clés

### Auth API (JSON uniquement)
Laravel redirige par défaut vers `/login` si non authentifié. À corriger dans `bootstrap/app.php` :
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->redirectGuestsTo(
        fn () => response()->json(['message' => 'Unauthenticated.'], 401)
    );
})
```

### PageController — update des links
Le `update` doit supprimer + recréer les liens :
```php
if ($request->has('links')) {
    $page->links()->delete();
    foreach ($request->links as $i => $link) {
        $page->links()->create([
            'type'  => $link['type'],
            'label' => $link['label'],
            'url'   => $link['url'],
            'order' => $i,
        ]);
    }
}
```

### Migration étendue (2026-06-03)
Nouveaux champs sur `pages` : `template`, `avatar_url`, `bg_image_url`, `verified_badge`, `show_avatar`, `online_status`, `location`, `response_time`, `countdown_end`, `promo_text`, `bot_protection`, `deep_link_enabled`, `strict_deep_link`, `link_preview_enabled`, `custom_domain`.

Nouveaux champs sur `page_links` : `icon`, `image_url`.

---

## Page Builder (PageEdit.vue) — 6 onglets

| Onglet | Contenu |
|--------|---------|
| Général | Nom, slug éditable, template |
| Profil | Avatar, bg, couleurs, bio, badge vérifié, **vidéo VSL**, age gate |
| Contenu | 8 types de liens : classic, image button, produit, creator card, grid, tips, countdown, carousel |
| Social | Quick add : OnlyFans, MYM, Instagram, TikTok, X, Telegram, YouTube, Twitch |
| Avancé | Online status, location, response time, promo text, countdown |
| Paramètres | Deep link, strict deep link, bot protection, open graph, custom domain, activer/désactiver |

Live preview téléphone + toggle desktop, mise à jour en temps réel.

---

## Page Creation (PageCreate.vue) — 6 étapes

1. **Profil** — Nom, handle, bio, type (Landing Page / Direct Link)
2. **Template** — Dark / Neon / Minimal + couleurs personnalisées
3. **Vidéo** — URL + preview iframe
4. **Liens** — Sélection plateformes par toggle
5. **Options** — Deep link, 18+, bot protection, open graph
6. **Récap** — Résumé avant création

Stepper cliquable (retour aux étapes déjà visitées). Redirige vers le builder après création.

---

## Home.vue — Hero

Hero en 2 colonnes :
- Gauche : titre, sous-titre, CTA, 3 social proof stats
- Droite : `PhoneDemo.vue` (iPhone 3D draggable) à `scale(0.65)`

```vue
<div style="transform:scale(0.65);transform-origin:center center">
  <PhoneDemo name="🌸 Karine" ... />
</div>
```

Photo : `frontend/public/karine.JPG` (copiée depuis `ofm-video-lp/`)

---

## Design — Règles appliquées

- **Couleur principale** : `#2563EB` (blue-600)
- **Fond** : `#000` (landing) / `#f8f8f8` (dashboard/builder)
- **Largeur max** : `1152px` centré avec `margin: 0 auto`
- **Padding sections** : `96px` top/bottom
- **Footer** : `flex-direction:column; align-items:center` — centré
- **Pricing card Pro** : `transform:translateY(-8px)` + halo bleu
- **Animations** : AOS `fade-up/right/left`, duration 700ms, once:true

---

## Plans Stripe (à implémenter)

| Plan | Prix | Pages | Branding |
|------|------|-------|----------|
| Free | 0€ | 1 | MySocialVSL visible |
| Pro | 19€/mois | 5 | No branding |
| Agency | 49€/mois | Illimité | White label |

---

## Fichiers clés

```
frontend/src/pages/Home.vue               — Landing page publique
frontend/src/pages/Dashboard.vue          — Dashboard après login
frontend/src/pages/PageCreate.vue         — Wizard création 6 étapes
frontend/src/pages/PageEdit.vue           — Builder 6 onglets + live preview
frontend/src/components/PhoneDemo.vue     — iPhone 3D draggable
frontend/src/style.css                    — CSS global (--spacing fix)
frontend/public/karine.JPG                — Photo profil démo
app/Http/Controllers/Api/PageController.php
app/Models/Page.php
app/Models/PageLink.php
bootstrap/app.php                         — Fix auth JSON 401
database/migrations/2026_06_03_000001_extend_pages_table.php
/Users/nathan.roquet/Onlyfans Business/ofm-video-lp/index.html  — Landing Karine (live)
```
