<template>
  <LegalLayout :title="title" :updated="updated" :sections="sections" />
</template>

<script setup>
import LegalLayout from '@/components/LegalLayout.vue'

const title = { en: 'Privacy Policy', fr: 'Politique de confidentialité' }
const updated = '2026-06-17'

const sections = {
  en: [
    { h: '1. Who we are', p: [
      'MySocialVSL ("we", "us", the "Service") is a link-in-bio and video sales page platform operated by <strong>Obsidian Creators LLC</strong>, 30 N Gould St, Ste R, <strong>Sheridan, WY 82801, USA</strong>. For any privacy question, contact us at <a href="mailto:support@mysocialvsl.com">support@mysocialvsl.com</a>.',
      'This policy explains what personal data we collect, why, and the rights you have over it. It is written to comply with the EU/EEA General Data Protection Regulation (GDPR).',
    ]},
    { h: '2. Data we collect', list: [
      '<strong>Account data</strong> — name, email, password (hashed) you provide at sign-up.',
      '<strong>Content data</strong> — the pages, links, videos and media you upload to build your VSL pages.',
      '<strong>Usage & analytics data</strong> — clicks, page views, approximate location (country/region derived from IP), device and browser type, referrer. Used to provide your analytics dashboard.',
      '<strong>Payment data</strong> — handled by our payment processor <strong>Stripe</strong>. We never store your full card details; we keep only a customer/subscription identifier and plan status.',
      '<strong>Cookies</strong> — see our <a href="/cookies">Cookie Policy</a>.',
    ]},
    { h: '3. How we use your data', list: [
      'Provide, operate and secure the Service and your account.',
      'Generate the analytics and conversion metrics shown in your dashboard.',
      'Process subscriptions and payments via Stripe.',
      'Communicate with you about your account, security and service updates.',
      'Prevent fraud, abuse and comply with legal obligations.',
    ]},
    { h: '4. Legal bases (GDPR)', p: [
      'We rely on: <strong>contract</strong> (to provide the Service you subscribed to), <strong>legitimate interest</strong> (security, analytics, product improvement), <strong>consent</strong> (non-essential cookies), and <strong>legal obligation</strong> (accounting, fraud prevention).',
    ]},
    { h: '5. Sharing & sub-processors', p: [
      'We share data only with service providers needed to run the Service: <strong>Stripe</strong> (payments), <strong>Laravel Cloud</strong> (hosting), <strong>Cloudflare</strong> (CDN, domains, media storage). We do not sell your personal data.',
    ]},
    { h: '6. Data retention', p: [
      'We keep account and content data while your account is active. After deletion, data is removed within a reasonable period, except where we must retain it for legal or accounting reasons (e.g. invoices).',
    ]},
    { h: '7. International transfers', p: [
      'Our infrastructure is hosted in the EU (Frankfurt). Where a provider processes data outside the EEA, it is covered by appropriate safeguards (e.g. EU Standard Contractual Clauses).',
    ]},
    { h: '8. Your rights', p: [
      'You may request access, rectification, erasure, restriction, portability, and object to processing of your personal data. You can also lodge a complaint with your local data protection authority. To exercise your rights, email <a href="mailto:support@mysocialvsl.com">support@mysocialvsl.com</a>.',
    ]},
    { h: '9. Security', p: [
      'Passwords are hashed, traffic is encrypted in transit (HTTPS), and access to data is restricted. No method of transmission is 100% secure, but we take reasonable measures to protect your data.',
    ]},
    { h: '10. Age requirement', p: [
      'The Service is intended for adults only and is <strong>strictly reserved to users aged 18 or over</strong>. We do not knowingly collect data from minors.',
    ]},
    { h: '11. Changes', p: [
      'We may update this policy. Material changes will be notified through the Service or by email. Continued use after changes means you accept the updated policy.',
    ]},
    { h: '12. Contact', p: [
      'Questions about this policy: <a href="mailto:support@mysocialvsl.com">support@mysocialvsl.com</a>.',
    ]},
  ],
  fr: [
    { h: '1. Qui sommes-nous', p: [
      'MySocialVSL (« nous », le « Service ») est une plateforme de page link-in-bio et de pages de vente vidéo exploitée par <strong>Obsidian Creators LLC</strong>, 30 N Gould St, Ste R, <strong>Sheridan, WY 82801, USA</strong>. Pour toute question relative à la confidentialité, contactez-nous à <a href="mailto:support@mysocialvsl.com">support@mysocialvsl.com</a>.',
      'Cette politique explique quelles données personnelles nous collectons, pourquoi, et les droits dont vous disposez. Elle est rédigée conformément au Règlement général sur la protection des données (RGPD).',
    ]},
    { h: '2. Données collectées', list: [
      '<strong>Données de compte</strong> — nom, email, mot de passe (haché) fournis à l’inscription.',
      '<strong>Contenus</strong> — pages, liens, vidéos et médias que vous importez pour créer vos pages VSL.',
      '<strong>Données d’usage & analytics</strong> — clics, vues, localisation approximative (pays/région dérivée de l’IP), type d’appareil et de navigateur, source de trafic. Utilisées pour votre tableau de bord analytics.',
      '<strong>Données de paiement</strong> — gérées par notre prestataire <strong>Stripe</strong>. Nous ne stockons jamais vos coordonnées bancaires complètes ; uniquement un identifiant client/abonnement et le statut du plan.',
      '<strong>Cookies</strong> — voir notre <a href="/cookies">Politique cookies</a>.',
    ]},
    { h: '3. Utilisation des données', list: [
      'Fournir, exploiter et sécuriser le Service et votre compte.',
      'Générer les statistiques et métriques de conversion affichées dans votre tableau de bord.',
      'Traiter les abonnements et paiements via Stripe.',
      'Communiquer avec vous (compte, sécurité, mises à jour du service).',
      'Prévenir la fraude et les abus, et respecter nos obligations légales.',
    ]},
    { h: '4. Bases légales (RGPD)', p: [
      'Nous nous appuyons sur : l’<strong>exécution du contrat</strong> (fournir le Service souscrit), l’<strong>intérêt légitime</strong> (sécurité, analytics, amélioration produit), le <strong>consentement</strong> (cookies non essentiels), et l’<strong>obligation légale</strong> (comptabilité, lutte anti-fraude).',
    ]},
    { h: '5. Partage & sous-traitants', p: [
      'Nous partageons les données uniquement avec les prestataires nécessaires au fonctionnement du Service : <strong>Stripe</strong> (paiements), <strong>Laravel Cloud</strong> (hébergement), <strong>Cloudflare</strong> (CDN, domaines, stockage des médias). Nous ne vendons pas vos données personnelles.',
    ]},
    { h: '6. Conservation', p: [
      'Nous conservons les données de compte et de contenu tant que votre compte est actif. Après suppression, les données sont effacées dans un délai raisonnable, sauf obligation légale ou comptable de conservation (ex. factures).',
    ]},
    { h: '7. Transferts internationaux', p: [
      'Notre infrastructure est hébergée dans l’UE (Francfort). Lorsqu’un prestataire traite des données hors EEE, des garanties appropriées s’appliquent (ex. clauses contractuelles types de l’UE).',
    ]},
    { h: '8. Vos droits', p: [
      'Vous pouvez demander l’accès, la rectification, l’effacement, la limitation, la portabilité de vos données et vous opposer à leur traitement. Vous pouvez aussi introduire une réclamation auprès de votre autorité de protection des données (en France, la CNIL). Pour exercer vos droits : <a href="mailto:support@mysocialvsl.com">support@mysocialvsl.com</a>.',
    ]},
    { h: '9. Sécurité', p: [
      'Les mots de passe sont hachés, le trafic est chiffré en transit (HTTPS) et l’accès aux données est restreint. Aucune méthode de transmission n’est sûre à 100 %, mais nous prenons des mesures raisonnables pour protéger vos données.',
    ]},
    { h: '10. Condition d’âge', p: [
      'Le Service est destiné exclusivement aux adultes et est <strong>strictement réservé aux personnes âgées de 18 ans ou plus</strong>. Nous ne collectons pas sciemment de données de mineurs.',
    ]},
    { h: '11. Modifications', p: [
      'Nous pouvons mettre à jour cette politique. Les changements importants seront notifiés via le Service ou par email. La poursuite de l’utilisation vaut acceptation de la version mise à jour.',
    ]},
    { h: '12. Contact', p: [
      'Questions sur cette politique : <a href="mailto:support@mysocialvsl.com">support@mysocialvsl.com</a>.',
    ]},
  ],
}
</script>
