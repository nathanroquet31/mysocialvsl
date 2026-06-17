<template>
  <div class="legal-root">

    <!-- Top bar -->
    <header class="legal-bar">
      <RouterLink to="/" class="legal-brand">
        <LogoMark :size="30" />
        <span class="legal-brandname">MySocialVSL</span>
      </RouterLink>

      <div class="legal-langswitch">
        <button :class="['lang-btn', { active: legalLang === 'en' }]" @click="legalLang = 'en'">EN</button>
        <button :class="['lang-btn', { active: legalLang === 'fr' }]" @click="legalLang = 'fr'">FR</button>
      </div>
    </header>

    <main class="legal-main">
      <h1 class="legal-title">{{ title[legalLang] }}</h1>
      <p class="legal-updated">
        {{ legalLang === 'fr' ? 'Dernière mise à jour' : 'Last updated' }} : {{ updated }}
      </p>

      <!-- Template disclaimer -->
      <div class="legal-disclaimer">
        <i class="bi bi-info-circle"></i>
        <span>{{ legalLang === 'fr'
          ? "Ces documents sont fournis à titre informatif ; une relecture par un juriste est recommandée, en particulier au vu de la nature du service."
          : "These documents are provided for information; a review by a lawyer is recommended, especially given the nature of the service." }}</span>
      </div>

      <article class="legal-content">
        <section v-for="(s, i) in sections[legalLang]" :key="i">
          <h2 v-if="s.h">{{ s.h }}</h2>
          <p v-for="(p, j) in (s.p || [])" :key="j" v-html="p"></p>
          <ul v-if="s.list">
            <li v-for="(li, k) in s.list" :key="k" v-html="li"></li>
          </ul>
        </section>
      </article>
    </main>

    <!-- Footer nav between legal pages -->
    <footer class="legal-foot">
      <RouterLink to="/privacy">{{ legalLang === 'fr' ? 'Confidentialité' : 'Privacy' }}</RouterLink>
      <RouterLink to="/terms">{{ legalLang === 'fr' ? 'CGU' : 'Terms' }}</RouterLink>
      <RouterLink to="/cookies">Cookies</RouterLink>
      <RouterLink to="/">{{ legalLang === 'fr' ? "Accueil" : 'Home' }}</RouterLink>
    </footer>
  </div>
</template>

<script setup>
import { legalLang } from '@/lib/legalLang'
import LogoMark from '@/components/LogoMark.vue'

defineProps({
  title: { type: Object, required: true },     // { en, fr }
  updated: { type: String, required: true },
  sections: { type: Object, required: true },   // { en: [{h, p?, list?}], fr: [...] }
})
</script>

<style scoped>
.legal-root {
  min-height: 100vh;
  background: #0a0a0f;
  color: #fff;
  font-family: 'Inter', sans-serif;
}

.legal-bar {
  position: sticky;
  top: 0;
  z-index: 20;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
  padding: 0 24px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.07);
  background: rgba(0, 0, 0, 0.85);
  backdrop-filter: blur(20px);
}
.legal-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
.legal-logo {
  width: 30px; height: 30px;
  background: linear-gradient(135deg, #6D4EE8, #8B6FF0);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 0 16px rgba(109, 78, 232, 0.4);
}
.legal-logo span { color: #fff; font-weight: 900; font-size: 9px; letter-spacing: -0.05em; }
.legal-brandname { font-weight: 700; color: #fff; font-size: 15px; letter-spacing: -0.02em; }

.legal-langswitch { display: flex; gap: 4px; background: rgba(255, 255, 255, 0.06); border-radius: 10px; padding: 3px; }
.lang-btn {
  border: none;
  background: transparent;
  color: rgba(255, 255, 255, 0.5);
  font-size: 12px;
  font-weight: 700;
  padding: 5px 12px;
  border-radius: 7px;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.15s, color 0.15s;
}
.lang-btn.active { background: #6D4EE8; color: #fff; }

.legal-main { max-width: 760px; margin: 0 auto; padding: 56px 24px 40px; }
.legal-title { font-size: 34px; font-weight: 800; margin: 0 0 8px; letter-spacing: -0.02em; }
.legal-updated { font-size: 13px; color: rgba(255, 255, 255, 0.4); margin: 0 0 28px; }

.legal-disclaimer {
  display: flex; align-items: flex-start; gap: 10px;
  background: rgba(245, 158, 11, 0.08);
  border: 1px solid rgba(245, 158, 11, 0.25);
  border-radius: 12px;
  padding: 14px 16px;
  margin-bottom: 36px;
  font-size: 13px;
  color: #FBBF24;
  line-height: 1.6;
}
.legal-disclaimer i { margin-top: 1px; }

.legal-content section { margin-bottom: 28px; }
.legal-content h2 { font-size: 18px; font-weight: 700; color: #fff; margin: 0 0 12px; }
.legal-content p { font-size: 14px; line-height: 1.75; color: rgba(255, 255, 255, 0.7); margin: 0 0 12px; }
.legal-content ul { margin: 0 0 12px; padding-left: 22px; }
.legal-content li { font-size: 14px; line-height: 1.75; color: rgba(255, 255, 255, 0.7); margin-bottom: 6px; }
.legal-content :deep(a) { color: #A78BFA; text-decoration: none; }
.legal-content :deep(a:hover) { text-decoration: underline; }
.legal-content :deep(strong) { color: rgba(255, 255, 255, 0.92); }

.legal-foot {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 22px;
  padding: 28px 24px 48px;
  border-top: 1px solid rgba(255, 255, 255, 0.06);
}
.legal-foot a { font-size: 13px; color: rgba(255, 255, 255, 0.4); text-decoration: none; transition: color 0.15s; }
.legal-foot a:hover { color: #A78BFA; }
</style>
