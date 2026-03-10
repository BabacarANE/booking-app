<template>
  <header class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">

        <!-- Logo -->

        <RouterLink to="/" class="flex items-center gap-2 group">
          <img
            src="/favicon.ico"
            alt="BookingApp"
            class="w-9 h-9 rounded-xl group-hover:scale-105 transition-transform"
          />
          <span class="font-bold text-xl text-gray-900">Booking<span class="text-coral-500">App</span></span>
        </RouterLink>

        <!-- Navigation centre -->
        <nav class="hidden md:flex items-center gap-1">
          <RouterLink
            to="/"
            class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:text-coral-500 hover:bg-coral-50 transition-all"
            :class="{ 'text-coral-500 bg-coral-50': $route.path === '/' }"
          >
            Accueil
          </RouterLink>
          <RouterLink
            to="/resources"
            class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:text-coral-500 hover:bg-coral-50 transition-all"
            :class="{ 'text-coral-500 bg-coral-50': $route.path.startsWith('/resources') }"
          >
            Hébergements
          </RouterLink>
          <RouterLink
            v-if="auth.isAdmin || auth.isManager"
            to="/admin"
            class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:text-coral-500 hover:bg-coral-50 transition-all"
            :class="{ 'text-coral-500 bg-coral-50': $route.path.startsWith('/admin') }"
          >
            Admin
          </RouterLink>
        </nav>

        <!-- Auth -->
        <div class="flex items-center gap-3">
          <template v-if="auth.isAuthenticated">
            <!-- User menu -->
            <RouterLink
              to="/dashboard"
              class="hidden md:flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-gray-50 transition-colors"
            >
              <div class="w-8 h-8 bg-coral-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                {{ auth.user?.name?.charAt(0).toUpperCase() }}
              </div>
              <span class="text-sm font-medium text-gray-700">{{ auth.user?.name }}</span>
            </RouterLink>
            <button
              @click="handleLogout"
              class="flex items-center gap-1 px-4 py-2 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 transition-colors"
            >
              <span>Déconnexion</span>
            </button>
          </template>
          <template v-else>
            <RouterLink
              to="/login"
              class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:text-coral-500 hover:bg-coral-50 transition-all"
            >
              Connexion
            </RouterLink>
            <RouterLink
              to="/register"
              class="px-4 py-2 rounded-xl text-sm font-medium bg-coral-500 text-white hover:bg-coral-600 transition-colors shadow-sm"
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
import { useAuthStore } from '@/stores/auth'
import { useRouter, useRoute } from 'vue-router'

const auth   = useAuthStore()
const router = useRouter()
const route  = useRoute()

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}
</script>
