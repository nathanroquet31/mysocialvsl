<template>
  <div class="auth-root">

    <SmokeyBackground />

    <!-- Glassmorphism sign-up card -->
    <div class="auth-card">

      <RouterLink to="/" class="brand">
        <LogoMark :size="34" />
        <span class="brand-name">MySocialVSL</span>
      </RouterLink>

      <div class="auth-head">
        <h2>Create your account</h2>
        <p>Start converting in minutes</p>
      </div>

      <div v-if="error" class="auth-error">{{ error }}</div>

      <form class="auth-form" @submit.prevent="submit">
        <div class="field">
          <input v-model="form.name" type="text" id="reg_name" class="field-input" placeholder=" " required />
          <label for="reg_name" class="field-label"><i class="bi bi-person"></i> Name</label>
        </div>

        <div class="field">
          <input v-model="form.email" type="email" id="reg_email" class="field-input" placeholder=" " required />
          <label for="reg_email" class="field-label"><i class="bi bi-envelope"></i> Email Address</label>
        </div>

        <div class="field">
          <input v-model="form.password" type="password" id="reg_password" class="field-input" placeholder=" " required />
          <label for="reg_password" class="field-label"><i class="bi bi-lock"></i> Password</label>
        </div>

        <div class="field">
          <input v-model="form.password_confirmation" type="password" id="reg_password2" class="field-input" placeholder=" " required />
          <label for="reg_password2" class="field-label"><i class="bi bi-shield-lock"></i> Confirm Password</label>
        </div>

        <button type="submit" :disabled="loading" class="auth-submit">
          {{ loading ? 'Creating...' : 'Create my account' }}
          <i v-if="!loading" class="bi bi-arrow-right"></i>
        </button>
      </form>

      <p class="auth-foot">
        Already have an account?
        <RouterLink to="/login">Sign in</RouterLink>
      </p>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import SmokeyBackground from '@/components/SmokeyBackground.vue'
import LogoMark from '@/components/LogoMark.vue'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()
// Affiliate/coach code from /register?ref=… — drives attribution + beta trial.
const refCode = route.query.ref || null
const form = ref({ name: '', email: '', password: '', password_confirmation: '' })
const error = ref('')
const loading = ref(false)

async function submit() {
  loading.value = true
  error.value = ''
  try {
    await auth.register(form.value.name, form.value.email, form.value.password, form.value.password_confirmation, refCode)
    router.push('/dashboard/links')
  } catch (e) {
    const errors = e.response?.data?.errors
    error.value = errors ? Object.values(errors).flat().join(' ') : 'Something went wrong during sign up.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped src="@/assets/auth.css"></style>
