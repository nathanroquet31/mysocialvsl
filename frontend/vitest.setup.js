// jsdom in this toolchain doesn't expose a Web Storage implementation, so the
// stores (auth/theme) and legalLang that read localStorage at import time would
// blow up. Install a tiny in-memory shim on both global and window.
class MemoryStorage {
  #store = new Map()
  getItem(key) { return this.#store.has(key) ? this.#store.get(key) : null }
  setItem(key, value) { this.#store.set(key, String(value)) }
  removeItem(key) { this.#store.delete(key) }
  clear() { this.#store.clear() }
  key(i) { return [...this.#store.keys()][i] ?? null }
  get length() { return this.#store.size }
}

const storage = new MemoryStorage()
Object.defineProperty(globalThis, 'localStorage', { value: storage, configurable: true, writable: true })
if (typeof window !== 'undefined') {
  Object.defineProperty(window, 'localStorage', { value: storage, configurable: true, writable: true })
}
