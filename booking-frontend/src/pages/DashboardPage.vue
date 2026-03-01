<template>
  <div class="max-w-5xl mx-auto px-4 py-10">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Mon espace</h1>
        <p class="text-gray-500 mt-1">Bonjour, {{ auth.user?.name }} 👋</p>
      </div>
    </div>

    <!-- Stats rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500">Réservations actives</p>
        <p class="text-3xl font-bold text-blue-600 mt-1">{{ activeBookings.length }}</p>
      </div>
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500">Réservations terminées</p>
        <p class="text-3xl font-bold text-green-600 mt-1">{{ completedBookings.length }}</p>
      </div>
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500">Réservations annulées</p>
        <p class="text-3xl font-bold text-red-500 mt-1">{{ cancelledBookings.length }}</p>
      </div>
    </div>

    <!-- Réservations actives -->
    <div class="mb-10">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Réservations actives</h2>

      <div v-if="loading" class="space-y-4">
        <div v-for="n in 2" :key="n" class="bg-gray-100 rounded-2xl h-32 animate-pulse" />
      </div>

      <div v-else-if="activeBookings.length === 0" class="bg-white rounded-2xl p-10 text-center border border-gray-100 text-gray-400">
        <p class="text-4xl mb-3">📅</p>
        <p class="font-medium">Aucune réservation active</p>
        <RouterLink to="/resources" class="text-blue-600 hover:underline text-sm mt-2 inline-block">
          Parcourir les hébergements →
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
    <div>
      <h2 class="text-xl font-bold text-gray-900 mb-4">Historique</h2>

      <div v-if="historyBookings.length === 0" class="bg-white rounded-2xl p-10 text-center border border-gray-100 text-gray-400">
        <p class="text-4xl mb-3">🕓</p>
        <p class="font-medium">Aucun historique</p>
      </div>

      <div v-else class="space-y-4">
        <BookingCard
          v-for="booking in historyBookings"
          :key="booking.id"
          :booking="booking"
          :readonly="true"
        />
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import BookingCard from '@/components/booking/BookingCard.vue'
import api from '@/services/api'

const auth    = useAuthStore()
const loading = ref(false)
const bookings = ref([])

const activeBookings    = computed(() => bookings.value.filter(b => ['pending', 'confirmed'].includes(b.status)))
const completedBookings = computed(() => bookings.value.filter(b => b.status === 'completed'))
const cancelledBookings = computed(() => bookings.value.filter(b => b.status === 'cancelled'))
const historyBookings   = computed(() => bookings.value.filter(b => ['completed', 'cancelled'].includes(b.status)))

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
