<template>
  <header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">

        <!-- Logo -->
        <RouterLink to="/" class="flex items-center gap-2">
          <span class="text-2xl">🏨</span>
          <span class="font-bold text-xl text-blue-600">BookingApp</span>
        </RouterLink>

        <!-- Navigation -->
        <nav class="hidden md:flex items-center gap-6">
          <RouterLink
            to="/resources"
            class="text-gray-600 hover:text-blue-600 font-medium transition-colors"
          >
            Nos Hébergements
          </RouterLink>
          <RouterLink
            v-if="auth.isAdmin || auth.isManager"
            to="/admin"
            class="text-gray-600 hover:text-blue-600 font-medium transition-colors"
          >
            Admin
          </RouterLink>
        </nav>

        <!-- Auth buttons -->
        <div class="flex items-center gap-3">
          <template v-if="auth.isAuthenticated">
            <RouterLink
              to="/dashboard"
              class="text-gray-600 hover:text-blue-600 font-medium transition-colors"
            >
              {{ auth.user?.name }}
            </RouterLink>
            <button
              @click="handleLogout"
              class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            >
              Déconnexion
            </button>
          </template>
          <template v-else>
            <RouterLink
              to="/login"
              class="text-gray-600 hover:text-blue-600 font-medium transition-colors"
            >
              Connexion
            </RouterLink>
            <RouterLink
              to="/register"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            >
              S'inscrire
            </RouterLink>
          </template>
        </div>

      </div>
    </div>
  </header>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth.js'
import { useRouter } from 'vue-router'

const auth   = useAuthStore()
const router = useRouter()

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}
</script>
