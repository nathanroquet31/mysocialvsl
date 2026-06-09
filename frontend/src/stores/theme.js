import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

export const useThemeStore = defineStore('theme', () => {
  const dark = ref(localStorage.getItem('theme') === 'dark')

  function toggle() {
    dark.value = !dark.value
    localStorage.setItem('theme', dark.value ? 'dark' : 'light')
    apply()
  }

  function apply() {
    const root = document.documentElement
    if (dark.value) {
      root.style.setProperty('--bg', '#0d0b1e')
      root.style.setProperty('--bg2', '#16132b')
      root.style.setProperty('--surface', 'rgba(255,255,255,0.04)')
      root.style.setProperty('--border', 'rgba(255,255,255,0.08)')
      root.style.setProperty('--text', '#ffffff')
      root.style.setProperty('--text2', 'rgba(255,255,255,0.5)')
      root.style.setProperty('--sidebar-bg', '#100e22')
      root.style.setProperty('--sidebar-border', 'rgba(255,255,255,0.06)')
      root.style.setProperty('--input-bg', 'rgba(255,255,255,0.06)')
      root.style.setProperty('--input-border', 'rgba(255,255,255,0.12)')
      root.style.setProperty('--card-bg', 'rgba(255,255,255,0.03)')
      root.setAttribute('data-theme', 'dark')
    } else {
      root.style.setProperty('--bg', '#F9FAFB')
      root.style.setProperty('--bg2', '#ffffff')
      root.style.setProperty('--surface', '#ffffff')
      root.style.setProperty('--border', '#E5E7EB')
      root.style.setProperty('--text', '#111827')
      root.style.setProperty('--text2', '#6B7280')
      root.style.setProperty('--sidebar-bg', '#ffffff')
      root.style.setProperty('--sidebar-border', '#E5E7EB')
      root.style.setProperty('--input-bg', '#ffffff')
      root.style.setProperty('--input-border', '#E5E7EB')
      root.style.setProperty('--card-bg', '#ffffff')
      root.setAttribute('data-theme', 'light')
    }
  }

  return { dark, toggle, apply }
})
