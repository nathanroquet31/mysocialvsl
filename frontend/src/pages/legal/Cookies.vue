<template>
  <LegalLayout :title="title" :updated="updated" :sections="sections" />
</template>

<script setup>
import LegalLayout from '@/components/LegalLayout.vue'

const title = { en: 'Cookie Policy', fr: 'Politique cookies' }
const updated = '2026-06-17'

const sections = {
  en: [
    { h: '1. What are cookies', p: [
      'Cookies are small files stored on your device. We also use similar technologies such as local storage. This policy explains how MySocialVSL (the "Service") uses them.',
    ]},
    { h: '2. Cookies we use', list: [
      '<strong>Essential</strong> — required to run the Service: authentication/session token, security, load balancing. These cannot be disabled.',
      '<strong>Preferences</strong> — remember your choices (e.g. theme, language) for a better experience.',
      '<strong>Analytics</strong> — help us understand how the Service is used (clicks, page views) to improve it. On your public VSL pages, visit analytics are aggregated and approximate.',
    ]},
    { h: '3. Third parties', p: [
      'Some providers may set their own cookies, in particular <strong>Stripe</strong> (payment and fraud prevention) and <strong>Cloudflare</strong> (security and performance).',
    ]},
    { h: '4. Managing cookies', p: [
      'You can control or delete cookies through your browser settings. Blocking essential cookies may prevent parts of the Service from working. Where required, we ask for your consent before setting non-essential cookies.',
    ]},
    { h: '5. Contact', p: [
      'Questions about this policy: <a href="mailto:support@mysocialvsl.com">support@mysocialvsl.com</a>. See also our <a href="/privacy">Privacy Policy</a>.',
    ]},
  ],
  fr: [
    { h: '1. Qu’est-ce qu’un cookie', p: [
      'Les cookies sont de petits fichiers stockés sur votre appareil. Nous utilisons aussi des technologies similaires comme le stockage local. Cette politique explique comment MySocialVSL (le « Service ») les utilise.',
    ]},
    { h: '2. Cookies utilisés', list: [
      '<strong>Essentiels</strong> — nécessaires au fonctionnement du Service : jeton d’authentification/session, sécurité, répartition de charge. Ils ne peuvent pas être désactivés.',
      '<strong>Préférences</strong> — mémorisent vos choix (ex. thème, langue) pour une meilleure expérience.',
      '<strong>Analytics</strong> — nous aident à comprendre l’usage du Service (clics, vues) pour l’améliorer. Sur vos pages VSL publiques, les statistiques de visite sont agrégées et approximatives.',
    ]},
    { h: '3. Tiers', p: [
      'Certains prestataires peuvent déposer leurs propres cookies, notamment <strong>Stripe</strong> (paiement et lutte anti-fraude) et <strong>Cloudflare</strong> (sécurité et performance).',
    ]},
    { h: '4. Gérer les cookies', p: [
      'Vous pouvez contrôler ou supprimer les cookies via les paramètres de votre navigateur. Bloquer les cookies essentiels peut empêcher certaines parties du Service de fonctionner. Lorsque la loi l’exige, nous demandons votre consentement avant de déposer des cookies non essentiels.',
    ]},
    { h: '5. Contact', p: [
      'Questions sur cette politique : <a href="mailto:support@mysocialvsl.com">support@mysocialvsl.com</a>. Voir aussi notre <a href="/privacy">Politique de confidentialité</a>.',
    ]},
  ],
}
</script>
