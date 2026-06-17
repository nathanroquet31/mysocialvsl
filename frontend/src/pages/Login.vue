<template>
  <div class="auth-root">

    <SmokeyBackground />

    <!-- Glassmorphism sign-in card -->
    <div class="auth-card">

      <RouterLink to="/" class="brand">
        <LogoMark :size="34" />
        <span class="brand-name">MySocialVSL</span>
      </RouterLink>

      <div class="auth-head">
        <h2>Welcome back</h2>
        <p>Sign in to continue</p>
      </div>

      <div v-if="error" class="auth-error">{{ error }}</div>

      <form class="auth-form" @submit.prevent="submit">
        <div class="field">
          <input v-model="form.email" type="email" id="login_email" class="field-input" placeholder=" " required />
          <label for="login_email" class="field-label"><i class="bi bi-envelope"></i> Email Address</label>
        </div>

        <div class="field">
          <input v-model="form.password" type="password" id="login_password" class="field-input" placeholder=" " required />
          <label for="login_password" class="field-label"><i class="bi bi-lock"></i> Password</label>
        </div>

        <button type="submit" :disabled="loading" class="auth-submit">
          {{ loading ? 'Signing in...' : 'Sign in' }}
          <i v-if="!loading" class="bi bi-arrow-right"></i>
        </button>
      </form>

      <p class="auth-foot">
        No account yet?
        <RouterLink to="/register">Sign up free</RouterLink>
      </p>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import SmokeyBackground from '@/components/SmokeyBackground.vue'
import LogoMark from '@/components/LogoMark.vue'

const auth = useAuthStore()
const router = useRouter()
const form = ref({ email: '', password: '' })
const error = ref('')
const loading = ref(false)

async function submit() {
  loading.value = true
  error.value = ''
  try {
    await auth.login(form.value.email, form.value.password)
    router.push('/dashboard')
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Incorrect email or password.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped src="@/assets/auth.css"></style>
