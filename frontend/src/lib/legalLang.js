import { ref, watch } from 'vue'

// Shared language state for the public legal pages (Privacy / Terms / Cookies).
// Persisted so the choice sticks while navigating between legal pages.
const stored = typeof localStorage !== 'undefined' ? localStorage.getItem('legalLang') : null
const initial = stored || (typeof navigator !== 'undefined' && navigator.language?.startsWith('fr') ? 'fr' : 'en')

export const legalLang = ref(initial)

watch(legalLang, (v) => {
  if (typeof localStorage !== 'undefined') localStorage.setItem('legalLang', v)
})
