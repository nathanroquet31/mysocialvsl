import { beforeEach, describe, expect, it } from 'vitest'
import { nextTick } from 'vue'
import { createPinia, setActivePinia } from 'pinia'

import { useThemeStore } from '@/stores/theme'

describe('theme store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    localStorage.clear()
    document.documentElement.removeAttribute('data-theme')
  })

  it('defaults to light when nothing is saved', () => {
    const theme = useThemeStore()
    expect(theme.dark).toBe(false)
    expect(document.documentElement.getAttribute('data-theme')).toBe('light')
  })

  it('restores a saved dark preference', () => {
    localStorage.setItem('theme', 'dark')
    const theme = useThemeStore()
    expect(theme.dark).toBe(true)
    expect(document.documentElement.getAttribute('data-theme')).toBe('dark')
  })

  it('toggle flips the theme, persists it and re-applies the CSS vars', async () => {
    const theme = useThemeStore()

    theme.toggle()
    await nextTick()

    expect(theme.dark).toBe(true)
    expect(localStorage.getItem('theme')).toBe('dark')
    expect(document.documentElement.getAttribute('data-theme')).toBe('dark')
    expect(document.documentElement.style.getPropertyValue('--bg')).toBe('#0d0b1e')

    theme.toggle()
    await nextTick()

    expect(localStorage.getItem('theme')).toBe('light')
    expect(document.documentElement.getAttribute('data-theme')).toBe('light')
    expect(document.documentElement.style.getPropertyValue('--text')).toBe('#111827')
  })
})
