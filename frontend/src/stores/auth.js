import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/lib/axios'

export const useAuthStore = defineStore('auth', () => {
  const user  = ref(null)
  const token = ref(localStorage.getItem('token'))

  async function login(email, password) {
    const { data } = await api.post('/login', { email, password })
    token.value = data.token
    user.value  = data.user
    localStorage.setItem('token', data.token)
  }

  async function register(name, email, password, password_confirmation, ref = null) {
    // `ref` is the affiliate/coach code from /register?ref=… — it attributes the
    // signup and, for beta-partner codes, grants the card-free trial server-side.
    const { data } = await api.post('/register', { name, email, password, password_confirmation, ref })
    token.value = data.token
    user.value  = data.user
    localStorage.setItem('token', data.token)
  }

  async function logout() {
    // Best-effort server revoke — never block local sign-out if the token is already invalid.
    try {
      await api.post('/logout')
    } catch {
      // ignore (e.g. 401 when the token was already revoked)
    }
    token.value = null
    user.value  = null
    localStorage.removeItem('token')
  }

  async function fetchMe() {
    if (!token.value) return
    const { data } = await api.get('/me')
    user.value = data
  }

  const isLoggedIn = () => !!token.value

  return { user, token, login, register, logout, fetchMe, isLoggedIn }
})
