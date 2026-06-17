# Déploiement — Laravel Cloud + Cloudflare R2 (single-origin)

Topologie : **tout est servi par Laravel** (front Vue, API, pages publiques VSL).
Une seule origine → pas de CORS, et les pages publiques + custom domains passent
forcément par le cloaking serveur (Shield Protection™).

```
Navigateur ──> Laravel Cloud (PHP + SPA buildé dans public/app)
                 ├─ /                → SPA (home) ou page publique si custom domain
                 ├─ /p/{slug}        → cloaking serveur → decoy (bot) | SPA shell (humain)
                 ├─ /dashboard, ...  → SPA
                 ├─ /api/*           → API JSON
                 └─ vidéos/images    → Cloudflare R2 (egress gratuit)
```

## 1. Cloudflare R2 (stockage vidéos/images)

1. Cloudflare → R2 → *Create bucket* (ex : `mysocialvsl-uploads`).
2. R2 → *Manage API tokens* → créer un token **Object Read & Write** sur ce bucket.
3. Activer un domaine public sur le bucket (R2 → Settings → *Public access* /
   *Custom domain*, ex : `cdn.mysocialvsl.com`) pour servir les fichiers.
4. Renseigner dans l'env (voir §3) : `R2_ACCESS_KEY_ID`, `R2_SECRET_ACCESS_KEY`,
   `R2_BUCKET`, `R2_ENDPOINT` (`https://<ACCOUNT_ID>.r2.cloudflarestorage.com`),
   `R2_PUBLIC_URL` (`https://cdn.mysocialvsl.com`).

`UploadController` bascule automatiquement sur R2 dès que `R2_ACCESS_KEY_ID` est
défini (sinon disk local).

## 2. Build de déploiement (Laravel Cloud → Build Commands)

```bash
composer install --no-dev --optimize-autoloader
npm --prefix frontend ci
npm --prefix frontend run build       # → public/app + manifest lu par resources/views/app.blade.php
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> Le build du front sort dans `public/app/` (git-ignoré) ; le shell Blade lit
> `public/app/.vite/manifest.json` pour référencer les assets hashés.
> **Ne pas** définir `VITE_API_URL` au build → axios utilise `/api` en relatif.

Health check : `GET /up`.

## 3. Variables d'environnement (Laravel Cloud)

| Clé | Valeur |
|---|---|
| `APP_KEY` | `php artisan key:generate --show` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://mysocialvsl.com` (domaine principal — sert à distinguer les custom domains) |
| `DB_*` | fournies par la base managée Laravel Cloud |
| `SESSION_DRIVER` / `CACHE_STORE` / `QUEUE_CONNECTION` | `database` (ou `redis` si dispo) |
| `R2_*` | voir §1 |
| `STRIPE_KEY` / `STRIPE_SECRET` / `STRIPE_WEBHOOK_SECRET` | depuis le dashboard Stripe |
| `STRIPE_PRICE_*` | IDs des prix Stripe |
| `MAIL_*` | Resend (clé API dans `MAIL_PASSWORD`) |
| `FRONTEND_URL` | `https://mysocialvsl.com` |

## 4. DNS (Namecheap)

- `mysocialvsl.com` (+ `www`) → l'app Laravel Cloud (CNAME/A fourni par Laravel Cloud).
- `cdn.mysocialvsl.com` → le bucket R2 (custom domain R2).

## 5. Custom domains des clientes

Le client pointe son domaine (CNAME) vers l'app. `CustomDomainMiddleware` résout
le `Host` → la page (`pages.custom_domain`) → sert la page publique avec cloaking.
Laravel Cloud provisionne le TLS automatiquement pour les domaines ajoutés.

## 6. À tester sur un vrai téléphone après déploiement

- Ouvrir un lien `/p/{slug}` depuis l'app Instagram (Android **et** iPhone) →
  vérifier le bypass deeplink (sortie du webview in-app).
- Partager le lien et vérifier que l'aperçu (crawler Meta) tombe sur le decoy.
