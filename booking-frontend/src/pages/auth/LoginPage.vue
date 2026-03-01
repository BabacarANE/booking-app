<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-md p-8">

      <div class="text-center mb-8">
        <span class="text-4xl">🏨</span>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Connexion</h1>
        <p class="text-gray-500 mt-1">Bienvenue sur BookingApp</p>
      </div>

      <form @submit.prevent="handleLogin" class="space-y-5">

        <!-- Email -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Email
          </label>
          <input
            v-model="form.email"
            type="email"
            placeholder="votre@email.com"
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>

        <!-- Mot de passe -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Mot de passe
          </label>
          <input
            v-model="form.password"
            type="password"
            placeholder="••••••••"
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>

        <!-- Erreur -->
        <p v-if="error" class="text-red-500 text-sm text-center">
          {{ error }}
        </p>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-medium py-2.5 rounded-lg transition-colors"
        >
          {{ loading ? 'Connexion...' : 'Se connecter' }}
        </button>

      </form>

      <p class="text-center text-sm text-gray-500 mt-6">
        Pas encore de compte ?
        <RouterLink to="/register" class="text-blue-600 hover:underline font-medium">
          S'inscrire
        </RouterLink>
      </p>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth    = useAuthStore()
const router  = useRouter()
const loading = ref(false)
const error   = ref(null)

const form = ref({
  email:    '',
  password: '',
})

async function handleLogin() {
  loading.value = true
  error.value   = null
  try {
    await auth.login(form.value)
    router.push('/')
  } catch (e) {
    error.value = e.response?.data?.message || 'Identifiants incorrects.'
  } finally {
    loading.value = false
  }
}
</script>
