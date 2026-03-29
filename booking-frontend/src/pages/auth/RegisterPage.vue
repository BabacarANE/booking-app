<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 via-white to-teal-50 py-12 px-4">
    <!-- Décor background -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-0 left-0 w-80 h-80 bg-gradient-to-br from-teal-100/30 to-transparent rounded-full blur-3xl" />
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-tl from-coral-100/20 to-transparent rounded-full blur-3xl" />
    </div>

    <div class="max-w-md w-full bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/50 p-8 relative z-10">

      <!-- Header -->
      <div class="text-center mb-9">
        <div class="flex justify-center mb-4">
          <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg">
            <img
              src="/favicon.ico"
              alt="BookingApp"
              class="w-8 h-8"
            />
          </div>
        </div>

        <h1 class="text-3xl font-bold bg-gradient-to-r from-teal-600 to-teal-500 bg-clip-text text-transparent">S'inscrire</h1>
        <p class="text-gray-500 mt-2 leading-relaxed">
          Créez votre compte <span class="font-semibold text-gray-700">BookingApp</span><br>
          <span class="text-xs text-gray-400">Rejoignez des millions de voyageurs</span>
        </p>
      </div>

      <!-- Divider -->
      <div class="w-12 h-1 bg-gradient-to-r from-teal-500 to-coral-500 rounded-full mx-auto mb-8" />

      <!-- Progress indicator -->
      <div class="flex justify-center gap-2 mb-8">
        <div v-for="i in 5" :key="i" :class="['h-1 rounded-full transition-all', i <= filledFields ? 'bg-teal-500 flex-1' : 'bg-gray-200 flex-1']"/>
      </div>

      <form @submit.prevent="handleRegister" class="space-y-5">

        <!-- Nom -->
        <div class="group">
          <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
            <span class="text-teal-500">👤</span>
            Nom complet
          </label>
          <input
            v-model="form.name"
            @input="updateProgress"
            type="text"
            placeholder="John Doe"
            class="w-full border-2 border-gray-200 bg-white/50 rounded-xl px-4 py-3 text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-teal-500 focus:bg-white transition-all duration-200 group-hover:border-gray-300"
            required
          />
        </div>

        <!-- Email -->
        <div class="group">
          <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
            <span class="text-teal-500">📧</span>
            Email
          </label>
          <input
            v-model="form.email"
            @input="updateProgress"
            type="email"
            placeholder="votre@email.com"
            class="w-full border-2 border-gray-200 bg-white/50 rounded-xl px-4 py-3 text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-teal-500 focus:bg-white transition-all duration-200 group-hover:border-gray-300"
            required
          />
        </div>

        <!-- Téléphone -->
        <div class="group">
          <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
            <span class="text-teal-500">📱</span>
            Téléphone
          </label>
          <input
            v-model="form.phone"
            @input="updateProgress"
            type="tel"
            placeholder="+33600000000"
            class="w-full border-2 border-gray-200 bg-white/50 rounded-xl px-4 py-3 text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-teal-500 focus:bg-white transition-all duration-200 group-hover:border-gray-300"
          />
        </div>

        <!-- Mot de passe -->
        <div class="group">
          <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
            <span class="text-teal-500">🔐</span>
            Mot de passe
          </label>
          <input
            v-model="form.password"
            @input="updateProgress"
            type="password"
            placeholder="••••••••"
            class="w-full border-2 border-gray-200 bg-white/50 rounded-xl px-4 py-3 text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-teal-500 focus:bg-white transition-all duration-200 group-hover:border-gray-300"
            required
          />
          <p class="text-xs text-gray-500 mt-1.5 flex items-center gap-1">
            <span v-if="form.password.length >= 8" class="text-green-500">✅</span>
            <span v-else class="text-gray-400">➖</span>
            Min. 8 caractères
          </p>
        </div>

        <!-- Confirmer mot de passe -->
        <div class="group">
          <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
            <span class="text-teal-500">🔒</span>
            Confirmer
          </label>
          <input
            v-model="form.password_confirmation"
            @input="updateProgress"
            type="password"
            placeholder="••••••••"
            class="w-full border-2 border-gray-200 bg-white/50 rounded-xl px-4 py-3 text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-teal-500 focus:bg-white transition-all duration-200 group-hover:border-gray-300"
            required
          />
          <p v-if="form.password && form.password_confirmation" :class="['text-xs mt-1.5 flex items-center gap-1', form.password === form.password_confirmation ? 'text-green-500' : 'text-red-500']"
          >
            <span>{{ form.password === form.password_confirmation ? '✅' : '❌' }}</span>
            {{ form.password === form.password_confirmation ? 'Les mots de passe correspondent' : 'Les mots de passe ne correspondent pas' }}
          </p>
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
          :disabled="loading || !isFormValid"
          class="w-full bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold py-3.5 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-2"
        >
          <span v-if="loading" class="animate-spin">⏳</span>
          <span>{{ loading ? 'Inscription...' : '🎉 S\'inscrire' }}</span>
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
          Vous avez déjà un compte ?<br>
          <RouterLink 
            to="/login" 
            class="text-teal-600 hover:text-teal-700 font-bold mt-1 inline-block hover:underline transition-colors"
          >
            Se connecter →
          </RouterLink>
        </p>
      </div>

      <!-- Terms -->
      <div class="mt-8 pt-6 border-t border-gray-100 text-center">
        <p class="text-xs text-gray-500 leading-relaxed">
          En s'inscrivant, vous acceptez nos<br>
          <span class="text-gray-600 font-medium">Conditions d'utilisation</span> et <span class="text-gray-600 font-medium">Politique de confidentialité</span>
        </p>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth    = useAuthStore()
const router  = useRouter()
const loading = ref(false)
const error   = ref(null)
const filledFields = ref(0)

const form = ref({
  name:                  '',
  email:                 '',
  phone:                 '',
  password:              '',
  password_confirmation: '',
})

const isFormValid = computed(() => {
  return (
    form.value.name &&
    form.value.email &&
    form.value.phone &&
    form.value.password &&
    form.value.password_confirmation &&
    form.value.password === form.value.password_confirmation &&
    form.value.password.length >= 8
  )
})

function updateProgress() {
  let count = 0
  if (form.value.name) count++
  if (form.value.email) count++
  if (form.value.phone) count++
  if (form.value.password) count++
  if (form.value.password_confirmation) count++
  filledFields.value = count
}

async function handleRegister() {
  loading.value = true
  error.value   = null
  try {
    await auth.register(form.value)
    router.push('/')
  } catch (e) {
    error.value = e.response?.data?.message || 'Une erreur est survenue.'
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
