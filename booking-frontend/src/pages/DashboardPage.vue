<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 py-10">

      <!-- Header profil -->
      <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 text-white mb-8 shadow-lg">
        <div class="flex items-center gap-5">
          <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center text-3xl font-bold backdrop-blur-sm">
            {{ auth.user?.name?.charAt(0).toUpperCase() }}
          </div>
          <div>
            <h1 class="text-2xl font-bold">Bonjour, {{ auth.user?.name }} 👋</h1>
            <p class="text-blue-100 mt-1">{{ auth.user?.email }}</p>
            <span class="inline-flex items-center gap-1 bg-white/20 rounded-full px-3 py-0.5 text-xs font-medium mt-2">
              {{ auth.user?.role }}
            </span>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
          <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-2xl">📅</div>
          <div>
            <p class="text-2xl font-bold text-blue-600">{{ activeBookings.length }}</p>
            <p class="text-sm text-gray-500">Réservations actives</p>
          </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
          <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-2xl">✅</div>
          <div>
            <p class="text-2xl font-bold text-green-600">{{ completedBookings.length }}</p>
            <p class="text-sm text-gray-500">Séjours terminés</p>
          </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
          <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-2xl">💰</div>
          <div>
            <p class="text-2xl font-bold text-purple-600">{{ totalSpent }}€</p>
            <p class="text-sm text-gray-500">Total dépensé</p>
          </div>
        </div>
      </div>

      <!-- Réservations actives -->
      <div class="mb-10">
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
          <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse" />
          Réservations actives
        </h2>

        <div v-if="loading" class="space-y-4">
          <div v-for="n in 2" :key="n" class="bg-gray-100 rounded-2xl h-36 animate-pulse" />
        </div>

        <div v-else-if="activeBookings.length === 0"
             class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
          <p class="text-5xl mb-4">🏖️</p>
          <p class="font-bold text-gray-700 text-lg">Aucune réservation active</p>
          <p class="text-gray-400 text-sm mt-1 mb-5">Commencez à planifier votre prochain séjour !</p>
          <RouterLink to="/resources"
                      class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition-colors inline-block">
            Explorer les hébergements
          </RouterLink>
        </div>

        <div v-else class="space-y-4">
          <BookingCard
            v-for="booking in activeBookings"
            :key="booking.id"
            :booking="booking"
            @cancelled="fetchBookings"
          />
        </div>
      </div>

      <!-- Historique -->
      <div v-if="historyBookings.length > 0">
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
          <span>🕓</span> Historique
        </h2>
        <div class="space-y-4">
          <BookingCard
            v-for="booking in historyBookings"
            :key="booking.id"
            :booking="booking"
            :readonly="true"
          />
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import BookingCard from '@/components/booking/BookingCard.vue'
import api from '@/services/api'

const auth     = useAuthStore()
const loading  = ref(false)
const bookings = ref([])

const activeBookings    = computed(() => bookings.value.filter(b => ['pending', 'confirmed'].includes(b.status)))
const completedBookings = computed(() => bookings.value.filter(b => b.status === 'completed'))
const cancelledBookings = computed(() => bookings.value.filter(b => b.status === 'cancelled'))
const historyBookings   = computed(() => bookings.value.filter(b => ['completed', 'cancelled'].includes(b.status)))
const totalSpent        = computed(() => {
  return bookings.value
    .filter(b => b.status !== 'cancelled')
    .reduce((sum, b) => sum + parseFloat(b.total_price), 0)
    .toFixed(2)
})

async function fetchBookings() {
  loading.value = true
  try {
    const { data } = await api.get('/bookings')
    bookings.value = data.data
  } finally {
    loading.value = false
  }
}

onMounted(fetchBookings)
</script>
