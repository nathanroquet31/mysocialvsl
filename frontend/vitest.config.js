import { defineConfig } from 'vitest/config'
import { fileURLToPath, URL } from 'node:url'

// Standalone test config (no vue/tailwind plugins needed for the store/helper
// unit tests). jsdom gives us localStorage + document for the theme store.
export default defineConfig({
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  test: {
    environment: 'jsdom',
    globals: true,
    include: ['src/**/*.spec.js'],
    setupFiles: ['./vitest.setup.js'],
  },
})
