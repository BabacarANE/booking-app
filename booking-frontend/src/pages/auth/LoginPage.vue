<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 via-white to-coral-50 py-12 px-4">
    <!-- Décor background -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-0 right-0 w-80 h-80 bg-gradient-to-bl from-coral-100/30 to-transparent rounded-full blur-3xl" />
      <div class="absolute bottom-0 left-0 w-96 h-96 bg-gradient-to-tr from-teal-100/20 to-transparent rounded-full blur-3xl" />
    </div>

    <div class="max-w-md w-full bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/50 p-8 relative z-10">

      <!-- Header -->
      <div class="text-center mb-9">
        <div class="flex justify-center mb-4">
          <div class="w-16 h-16 bg-gradient-to-br from-coral-500 to-coral-600 rounded-2xl flex items-center justify-center shadow-lg">
            <img
              src="/favicon.ico"
              alt="BookingApp"
              class="w-8 h-8"
            />
          </div>
        </div>

        <h1 class="text-3xl font-bold bg-gradient-to-r from-coral-600 to-coral-500 bg-clip-text text-transparent">Connexion</h1>

        <p class="text-gray-500 mt-2 leading-relaxed">
          Bienvenue sur <span class="font-semibold text-gray-700">BookingApp</span><br>
          <span class="text-xs text-gray-400">Trouvez votre prochain séjour parfait</span>
        </p>
      </div>

      <!-- Divider -->
      <div class="w-12 h-1 bg-gradient-to-r from-coral-500 to-teal-500 rounded-full mx-auto mb-8" />

      <form @submit.prevent="handleLogin" class="space-y-6">

        <!-- Email -->
        <div class="group">
          <label class="block text-sm font-semibold text-gray-700 mb-2.5 flex items-center gap-2">
            <span class="text-coral-500">📧</span>
            Email
          </label>
          <div class="relative">
            <input
              v-model="form.email"
              type="email"
              placeholder="votre@email.com"
              class="w-full border-2 border-gray-200 bg-white/50 rounded-xl px-4 py-3 text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-coral-500 focus:bg-white transition-all duration-200 group-hover:border-gray-300"
              required
            />
            <span class="absolute right-3 top-3.5 text-gray-300">🔍</span>
          </div>
        </div>

        <!-- Mot de passe -->
        <div class="group">
          <label class="block text-sm font-semibold text-gray-700 mb-2.5 flex items-center gap-2">
            <span class="text-coral-500">🔐</span>
            Mot de passe
          </label>
          <div class="relative">
            <input
              v-model="form.password"
              type="password"
              placeholder="••••••••"
              class="w-full border-2 border-gray-200 bg-white/50 rounded-xl px-4 py-3 text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-coral-500 focus:bg-white transition-all duration-200 group-hover:border-gray-300"
              required
            />
            <span class="absolute right-3 top-3.5 text-gray-300">🔒</span>
          </div>
        </div>

        <!-- Erreur -->
        <transition name="fade">
          <div v-if="error" class="bg-red-50 border-l-4 border-red-500 rounded-xl p-4 flex gap-3 items-start">
            <span class="text-2xl">⚠️</span>
            <p class="text-red-700 text-sm font-medium leading-relaxed">{{ error }}</p>
          </div>
        </transition>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-gradient-to-r from-coral-500 to-coral-600 hover:from-coral-600 hover:to-coral-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold py-3.5 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-2"
        >
          <span v-if="loading" class="animate-spin">⏳</span>
          <span>{{ loading ? 'Connexion...' : '🚀 Se connecter' }}</span>
        </button>

      </form>

      <!-- Divider -->
      <div class="flex items-center gap-4 my-8">
        <div class="flex-1 h-px bg-gradient-to-r from-gray-200 to-transparent" />
        <span class="text-xs text-gray-400 font-medium">OU</span>
        <div class="flex-1 h-px bg-gradient-to-l from-gray-200 to-transparent" />
      </div>

      <!-- Link -->
      <div class="text-center">
        <p class="text-sm text-gray-600">
          Pas encore de compte ?<br>
          <RouterLink 
            to="/register" 
            class="text-coral-600 hover:text-coral-700 font-bold mt-1 inline-block hover:underline transition-colors"
          >
            S'inscrire maintenant →
          </RouterLink>
        </p>
      </div>

      <!-- Security badge -->
      <div class="mt-8 pt-6 border-t border-gray-100 text-center">
        <p class="text-xs text-gray-500 flex items-center justify-center gap-1.5">
          <span class="text-green-500">✅</span>
          Connexion 100% sécurisée
        </p>
      </div>

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

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

input::placeholder {
  color: #d1d5db;
}
</style>
