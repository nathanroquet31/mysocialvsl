<template>
  <div class="auth-root">

    <SmokeyBackground />

    <div class="auth-card">

      <RouterLink to="/" class="brand">
        <LogoMark :size="34" />
        <span class="brand-name">MySocialVSL</span>
      </RouterLink>

      <div class="auth-head">
        <h2>Reset your password</h2>
        <p>Enter your email and we'll send you a reset link</p>
      </div>

      <div v-if="error" class="auth-error">{{ error }}</div>

      <div v-if="sent" class="auth-success">
        If an account exists for <strong>{{ form.email }}</strong>, a reset link is on its way. Check your inbox.
      </div>

      <form v-else class="auth-form" @submit.prevent="submit">
        <div class="field">
          <input v-model="form.email" type="email" id="forgot_email" class="field-input" placeholder=" " required />
          <label for="forgot_email" class="field-label"><i class="bi bi-envelope"></i> Email Address</label>
        </div>

        <button type="submit" :disabled="loading" class="auth-submit">
          {{ loading ? 'Sending...' : 'Send reset link' }}
          <i v-if="!loading" class="bi bi-arrow-right"></i>
        </button>
      </form>

      <p class="auth-foot">
        Remembered it?
        <RouterLink to="/login">Back to sign in</RouterLink>
      </p>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/lib/axios'
import SmokeyBackground from '@/components/SmokeyBackground.vue'
import LogoMark from '@/components/LogoMark.vue'

const form = ref({ email: '' })
const error = ref('')
const loading = ref(false)
const sent = ref(false)

async function submit() {
  loading.value = true
  error.value = ''
  try {
    await api.post('/forgot-password', { email: form.value.email })
    sent.value = true
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Something went wrong. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped src="@/assets/auth.css"></style>
