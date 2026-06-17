<template>
  <LegalLayout :title="title" :updated="updated" :sections="sections" />
</template>

<script setup>
import LegalLayout from '@/components/LegalLayout.vue'

const title = { en: 'Terms of Service', fr: "Conditions générales d’utilisation" }
const updated = '2026-06-17'

const sections = {
  en: [
    { h: '1. Acceptance', p: [
      'By creating an account or using MySocialVSL (the "Service"), operated by <strong>Obsidian Creators LLC</strong> (30 N Gould St, Ste R, Sheridan, WY 82801, USA), you agree to these Terms. If you do not agree, do not use the Service.',
    ]},
    { h: '2. Eligibility', p: [
      'You must be at least <strong>18 years old</strong> and able to enter a binding contract. The Service is reserved to adults.',
    ]},
    { h: '3. Your account', p: [
      'You are responsible for your login credentials and for all activity under your account. Keep your password secure and notify us of any unauthorized use.',
    ]},
    { h: '4. Acceptable use', p: [
      'You are solely responsible for the content you publish through the Service and for ensuring it complies with all applicable laws and the terms of the platforms you link to.',
    ], list: [
      'No illegal content, content involving minors, non-consensual content, or content you do not have the rights to.',
      'No fraud, malware, phishing, or attempts to bypass the Service’s security.',
      'No use that violates the terms of third-party platforms (e.g. social networks) in a way that exposes us to liability.',
    ]},
    { h: '5. Subscriptions & billing', p: [
      'Paid plans are billed through <strong>Stripe</strong> on a recurring basis (monthly or as stated at checkout). Subscriptions <strong>renew automatically</strong> until cancelled. You can cancel anytime from your billing settings; access continues until the end of the paid period.',
      'Unless required by law, payments are non-refundable. Prices may change with prior notice.',
    ]},
    { h: '6. Intellectual property', p: [
      'You retain ownership of the content you upload. You grant us a limited licence to host and display it solely to operate the Service. The Service software, brand and design remain our property.',
    ]},
    { h: '7. Termination', p: [
      'You may stop using the Service and delete your account at any time. We may suspend or terminate accounts that violate these Terms or applicable law.',
    ]},
    { h: '8. Disclaimers & limitation of liability', p: [
      'The Service is provided "as is", without warranty of uninterrupted or error-free operation. To the maximum extent permitted by law, we are not liable for indirect or consequential damages, lost profits, or loss of data. We do not guarantee any specific conversion or revenue result.',
    ]},
    { h: '9. Governing law', p: [
      'These Terms are governed by the laws of <strong>the State of Wyoming, USA</strong>, and disputes are subject to the competent courts of <strong>Wyoming</strong>, without prejudice to mandatory consumer protections.',
    ]},
    { h: '10. Changes', p: [
      'We may update these Terms. Material changes will be notified through the Service or by email. Continued use means you accept the updated Terms.',
    ]},
    { h: '11. Contact', p: [
      'Questions about these Terms: <a href="mailto:support@mysocialvsl.com">support@mysocialvsl.com</a>.',
    ]},
  ],
  fr: [
    { h: '1. Acceptation', p: [
      'En créant un compte ou en utilisant MySocialVSL (le « Service »), exploité par <strong>Obsidian Creators LLC</strong> (30 N Gould St, Ste R, Sheridan, WY 82801, USA), vous acceptez les présentes conditions. Si vous n’êtes pas d’accord, n’utilisez pas le Service.',
    ]},
    { h: '2. Éligibilité', p: [
      'Vous devez être âgé d’au moins <strong>18 ans</strong> et capable de conclure un contrat. Le Service est réservé aux adultes.',
    ]},
    { h: '3. Votre compte', p: [
      'Vous êtes responsable de vos identifiants et de toute activité sur votre compte. Gardez votre mot de passe confidentiel et signalez-nous tout usage non autorisé.',
    ]},
    { h: '4. Usage acceptable', p: [
      'Vous êtes seul responsable du contenu que vous publiez via le Service et de sa conformité aux lois applicables et aux conditions des plateformes vers lesquelles vous renvoyez.',
    ], list: [
      'Aucun contenu illégal, impliquant des mineurs, non consenti, ou dont vous n’avez pas les droits.',
      'Aucune fraude, malware, phishing, ni tentative de contourner la sécurité du Service.',
      'Aucun usage violant les conditions de plateformes tierces (ex. réseaux sociaux) de manière à engager notre responsabilité.',
    ]},
    { h: '5. Abonnements & facturation', p: [
      'Les offres payantes sont facturées via <strong>Stripe</strong> de façon récurrente (mensuelle ou selon ce qui est indiqué au paiement). Les abonnements se <strong>renouvellent automatiquement</strong> jusqu’à résiliation. Vous pouvez résilier à tout moment depuis vos paramètres de facturation ; l’accès se poursuit jusqu’à la fin de la période payée.',
      'Sauf disposition légale contraire, les paiements ne sont pas remboursables. Les prix peuvent évoluer avec préavis.',
    ]},
    { h: '6. Propriété intellectuelle', p: [
      'Vous restez propriétaire du contenu que vous importez. Vous nous accordez une licence limitée pour l’héberger et l’afficher uniquement aux fins d’exploitation du Service. Le logiciel, la marque et le design du Service restent notre propriété.',
    ]},
    { h: '7. Résiliation', p: [
      'Vous pouvez cesser d’utiliser le Service et supprimer votre compte à tout moment. Nous pouvons suspendre ou résilier les comptes qui violent les présentes conditions ou la loi.',
    ]},
    { h: '8. Garanties & limitation de responsabilité', p: [
      'Le Service est fourni « en l’état », sans garantie de fonctionnement ininterrompu ou sans erreur. Dans la mesure permise par la loi, nous ne sommes pas responsables des dommages indirects, pertes de profits ou pertes de données. Nous ne garantissons aucun résultat de conversion ou de revenu particulier.',
    ]},
    { h: '9. Droit applicable', p: [
      'Les présentes conditions sont régies par le droit de <strong>l’État du Wyoming (États-Unis)</strong>, et les litiges relèvent des tribunaux compétents du <strong>Wyoming</strong>, sans préjudice des protections impératives des consommateurs.',
    ]},
    { h: '10. Modifications', p: [
      'Nous pouvons mettre à jour ces conditions. Les changements importants seront notifiés via le Service ou par email. La poursuite de l’utilisation vaut acceptation.',
    ]},
    { h: '11. Contact', p: [
      'Questions sur ces conditions : <a href="mailto:support@mysocialvsl.com">support@mysocialvsl.com</a>.',
    ]},
  ],
}
</script>
