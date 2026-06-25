import { describe, expect, it } from 'vitest'
import { nextTick } from 'vue'

import { legalLang } from '@/lib/legalLang'

// legalLang is a module-level singleton ref shared by the legal pages.
describe('legalLang', () => {
  it('defaults to a supported language code', () => {
    expect(['en', 'fr']).toContain(legalLang.value)
  })

  it('persists changes to localStorage', async () => {
    const next = legalLang.value === 'fr' ? 'en' : 'fr'

    legalLang.value = next
    await nextTick()

    expect(localStorage.getItem('legalLang')).toBe(next)
  })
})
