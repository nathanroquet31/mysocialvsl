<template>
  <div class="auth-root">

    <SmokeyBackground />

    <div class="auth-card">

      <RouterLink to="/" class="brand">
        <LogoMark :size="34" />
        <span class="brand-name">MySocialVSL</span>
      </RouterLink>

      <div class="auth-head">
        <h2>Choose a new password</h2>
        <p>For {{ email || 'your account' }}</p>
      </div>

      <div v-if="error" class="auth-error">{{ error }}</div>

      <form class="auth-form" @submit.prevent="submit">
        <div class="field">
          <input v-model="form.password" type="password" id="reset_password" class="field-input" placeholder=" " required minlength="8" />
          <label for="reset_password" class="field-label"><i class="bi bi-lock"></i> New Password</label>
        </div>

        <div class="field">
          <input v-model="form.password_confirmation" type="password" id="reset_password2" class="field-input" placeholder=" " required minlength="8" />
          <label for="reset_password2" class="field-label"><i class="bi bi-shield-lock"></i> Confirm Password</label>
        </div>

        <button type="submit" :disabled="loading" class="auth-submit">
          {{ loading ? 'Saving...' : 'Reset password' }}
          <i v-if="!loading" class="bi bi-arrow-right"></i>
        </button>
      </form>

      <p class="auth-foot">
        <RouterLink to="/login">Back to sign in</RouterLink>
      </p>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/lib/axios'
import SmokeyBackground from '@/components/SmokeyBackground.vue'
import LogoMark from '@/components/LogoMark.vue'

const route = useRoute()
const router = useRouter()

const token = String(route.query.token ?? '')
const email = String(route.query.email ?? '')

const form = ref({ password: '', password_confirmation: '' })
const error = ref('')
const loading = ref(false)

async function submit() {
  error.value = ''
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'The two passwords do not match.'
    return
  }
  if (!token || !email) {
    error.value = 'This reset link is invalid or has expired. Request a new one.'
    return
  }
  loading.value = true
  try {
    await api.post('/reset-password', {
      token,
      email,
      password: form.value.password,
      password_confirmation: form.value.password_confirmation,
    })
    router.push({ path: '/login', query: { reset: '1' } })
  } catch (e) {
    error.value = e.response?.data?.message
      ?? e.response?.data?.errors?.email?.[0]
      ?? 'This reset link is invalid or has expired. Request a new one.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped src="@/assets/auth.css"></style>
