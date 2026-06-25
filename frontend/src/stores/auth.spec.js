import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'

// The store talks to the API through @/lib/axios — mock it so we test the
// store's own token/user/localStorage bookkeeping, not the network.
vi.mock('@/lib/axios', () => ({
  default: { post: vi.fn(), get: vi.fn() },
}))

import api from '@/lib/axios'
import { useAuthStore } from '@/stores/auth'

describe('auth store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    localStorage.clear()
    vi.clearAllMocks()
  })

  it('starts logged out when there is no token', () => {
    const auth = useAuthStore()
    expect(auth.isLoggedIn()).toBe(false)
    expect(auth.token).toBeNull()
  })

  it('login stores the token + user and persists the token', async () => {
    api.post.mockResolvedValue({ data: { token: 'tok-123', user: { id: 1, name: 'Karine' } } })
    const auth = useAuthStore()

    await auth.login('k@example.com', 'secret')

    expect(api.post).toHaveBeenCalledWith('/login', { email: 'k@example.com', password: 'secret' })
    expect(auth.token).toBe('tok-123')
    expect(auth.user).toEqual({ id: 1, name: 'Karine' })
    expect(localStorage.getItem('token')).toBe('tok-123')
    expect(auth.isLoggedIn()).toBe(true)
  })

  it('register forwards the affiliate ref code', async () => {
    api.post.mockResolvedValue({ data: { token: 't', user: {} } })
    const auth = useAuthStore()

    await auth.register('Karine', 'k@example.com', 'pw', 'pw', 'COACH123')

    expect(api.post).toHaveBeenCalledWith('/register', {
      name: 'Karine',
      email: 'k@example.com',
      password: 'pw',
      password_confirmation: 'pw',
      ref: 'COACH123',
    })
  })

  it('logout clears state even when the server revoke fails', async () => {
    api.post.mockRejectedValue(new Error('401'))
    localStorage.setItem('token', 'stale')
    const auth = useAuthStore()
    auth.user = { id: 1 }

    await auth.logout()

    expect(auth.token).toBeNull()
    expect(auth.user).toBeNull()
    expect(localStorage.getItem('token')).toBeNull()
  })

  it('fetchMe is a no-op without a token', async () => {
    const auth = useAuthStore()
    await auth.fetchMe()
    expect(api.get).not.toHaveBeenCalled()
  })
})
