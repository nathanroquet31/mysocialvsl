<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="w-full max-w-sm">
      <div class="text-center mb-8">
        <RouterLink to="/" class="inline-flex items-center gap-2 justify-center mb-6">
          <div class="w-8 h-8 bg-violet-600 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">V</span>
          </div>
          <span class="font-bold text-gray-900 text-lg">VSLpages</span>
        </RouterLink>
        <h1 class="text-2xl font-bold text-gray-900">Créer un compte</h1>
        <p class="text-gray-500 text-sm mt-1">Déjà inscrit ? <RouterLink to="/login" class="text-violet-600 font-medium">Se connecter</RouterLink></p>
      </div>

      <form @submit.prevent="submit" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
        <div v-if="error" class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3">{{ error }}</div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
          <input v-model="form.name" type="text" required placeholder="Ton nom"
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input v-model="form.email" type="email" required placeholder="ton@email.com"
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
          <input v-model="form.password" type="password" required placeholder="8 caractères min"
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
          <input v-model="form.password_confirmation" type="password" required placeholder="••••••••"
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500" />
        </div>
        <button type="submit" :disabled="loading"
          class="w-full bg-violet-600 text-white py-2.5 rounded-xl font-semibold text-sm hover:bg-violet-700 transition disabled:opacity-50">
          {{ loading ? 'Création...' : 'Créer mon compte' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()
const form = ref({ name: '', email: '', password: '', password_confirmation: '' })
const error = ref('')
const loading = ref(false)

async function submit() {
  loading.value = true
  error.value = ''
  try {
    await auth.register(form.value.name, form.value.email, form.value.password, form.value.password_confirmation)
    router.push('/dashboard')
  } catch (e) {
    const errors = e.response?.data?.errors
    error.value = errors ? Object.values(errors).flat().join(' ') : 'Erreur lors de l\'inscription.'
  } finally {
    loading.value = false
  }
}
</script>
