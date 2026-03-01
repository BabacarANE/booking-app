<template>
  <div class="max-w-6xl mx-auto px-4 py-10">

    <!-- Loading -->
    <div v-if="store.loading" class="animate-pulse space-y-6">
      <div class="bg-gray-200 rounded-2xl h-80" />
      <div class="bg-gray-200 rounded-xl h-10 w-1/2" />
      <div class="bg-gray-200 rounded-xl h-6 w-1/3" />
    </div>

    <div v-else-if="store.resource">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        <!-- Colonne gauche -->
        <div class="lg:col-span-2 space-y-8">

          <!-- Image -->
          <div class="bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl h-80 flex items-center justify-center">
            <span class="text-8xl">🏨</span>
          </div>

          <!-- Infos principales -->
          <div>
            <span class="text-sm font-semibold text-blue-600 uppercase">
              {{ store.resource.category?.name }}
            </span>
            <h1 class="text-3xl font-bold text-gray-900 mt-1">
              {{ store.resource.name }}
            </h1>
            <p class="text-gray-500 mt-2">📍 {{ store.resource.location }}</p>
            <p class="text-gray-600 mt-4 leading-relaxed">
              {{ store.resource.description }}
            </p>
          </div>

          <!-- Équipements -->
          <div>
            <h2 class="text-xl font-bold text-gray-900 mb-4">Équipements</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <div
                v-for="amenity in store.resource.amenities"
                :key="amenity"
                class="flex items-center gap-2 bg-gray-50 rounded-xl px-4 py-3"
              >
                <span class="text-green-500">✓</span>
                <span class="text-gray-700 text-sm">{{ amenity }}</span>
              </div>
            </div>
          </div>

          <!-- Calendrier disponibilités -->
          <div>
            <h2 class="text-xl font-bold text-gray-900 mb-4">Disponibilités</h2>
            <div class="bg-white border border-gray-200 rounded-2xl p-6">

              <!-- Navigation mois -->
              <div class="flex items-center justify-between mb-6">
                <button
                  @click="prevMonth"
                  class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                  ←
                </button>
                <h3 class="font-semibold text-gray-800">
                  {{ monthName }} {{ currentYear }}
                </h3>
                <button
                  @click="nextMonth"
                  class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                  →
                </button>
              </div>

              <!-- Jours de la semaine -->
              <div class="grid grid-cols-7 gap-1 mb-2">
                <div
                  v-for="day in ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']"
                  :key="day"
                  class="text-center text-xs font-medium text-gray-400 py-2"
                >
                  {{ day }}
                </div>
              </div>

              <!-- Jours du mois -->
              <div class="grid grid-cols-7 gap-1">
                <div
                  v-for="(day, index) in calendarDays"
                  :key="index"
                  class="aspect-square flex items-center justify-center rounded-lg text-sm cursor-pointer transition-colors"
                  :class="getDayClass(day)"
                  @click="selectDay(day)"
                >
                  {{ day?.date }}
                </div>
              </div>

              <!-- Légende -->
              <div class="flex gap-4 mt-4 text-xs text-gray-500">
                <div class="flex items-center gap-1">
                  <div class="w-3 h-3 bg-green-100 rounded" /> Disponible
                </div>
                <div class="flex items-center gap-1">
                  <div class="w-3 h-3 bg-red-100 rounded" /> Indisponible
                </div>
                <div class="flex items-center gap-1">
                  <div class="w-3 h-3 bg-blue-500 rounded" /> Sélectionné
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Colonne droite — Bloc réservation -->
        <div class="lg:col-span-1">
          <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm sticky top-24">

            <div class="text-center mb-6">
              <span class="text-3xl font-bold text-blue-600">
                {{ store.resource.price_per_night }}€
              </span>
              <span class="text-gray-400"> / nuit</span>
            </div>

            <div class="space-y-4">

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Arrivée</label>
                <input
                  v-model="booking.check_in"
                  type="date"
                  :min="today"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Départ</label>
                <input
                  v-model="booking.check_out"
                  type="date"
                  :min="booking.check_in || today"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Demandes spéciales
                </label>
                <textarea
                  v-model="booking.special_requests"
                  rows="3"
                  placeholder="Chambre non-fumeur, lit bébé..."
                  class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                />
              </div>

              <!-- Récapitulatif prix -->
              <div v-if="nights > 0" class="bg-gray-50 rounded-xl p-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                  <span>{{ store.resource.price_per_night }}€ × {{ nights }} nuit(s)</span>
                  <span>{{ totalPrice }}€</span>
                </div>
                <div class="flex justify-between font-bold text-gray-900 pt-2 border-t">
                  <span>Total</span>
                  <span>{{ totalPrice }}€</span>
                </div>
              </div>

              <!-- Erreur -->
              <p v-if="error" class="text-red-500 text-sm text-center">{{ error }}</p>

              <!-- Bouton réserver -->
              <button
                @click="handleBooking"
                :disabled="!nights || loading"
                class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-semibold py-3 rounded-xl transition-colors"
              >
                {{ loading ? 'Réservation...' : 'Réserver maintenant' }}
              </button>

              <p class="text-center text-xs text-gray-400">
                👥 Capacité max : {{ store.resource.capacity }} personnes
              </p>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useResourcesStore } from '@/stores/resources'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const route  = useRoute()
const router = useRouter()
const store  = useResourcesStore()
const auth   = useAuthStore()

const today   = new Date().toISOString().split('T')[0]
const loading = ref(false)
const error   = ref(null)

const booking = ref({
  check_in:         '',
  check_out:        '',
  special_requests: '',
})

// ✅ Calendrier
const currentMonth = ref(new Date().getMonth() + 1)
const currentYear  = ref(new Date().getFullYear())
const availability = ref(null)

const monthNames = [
  'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
  'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
]

const monthName = computed(() => monthNames[currentMonth.value - 1])

const nights = computed(() => {
  if (!booking.value.check_in || !booking.value.check_out) return 0
  const diff = new Date(booking.value.check_out) - new Date(booking.value.check_in)
  return Math.max(0, Math.floor(diff / (1000 * 60 * 60 * 24)))
})

const totalPrice = computed(() => {
  return (nights.value * parseFloat(store.resource?.price_per_night || 0)).toFixed(2)
})

const calendarDays = computed(() => {
  const days     = []
  const firstDay = new Date(currentYear.value, currentMonth.value - 1, 1).getDay()
  const offset   = firstDay === 0 ? 6 : firstDay - 1
  const total    = new Date(currentYear.value, currentMonth.value, 0).getDate()

  for (let i = 0; i < offset; i++) days.push(null)
  for (let d = 1; d <= total; d++) {
    const dateStr = `${currentYear.value}-${String(currentMonth.value).padStart(2, '0')}-${String(d).padStart(2, '0')}`
    days.push({ date: d, dateStr, available: isAvailable(dateStr) })
  }
  return days
})

function isAvailable(dateStr) {
  if (!availability.value) return true
  const booked = availability.value.booked_dates?.some(b =>
    dateStr >= b.check_in_date && dateStr < b.check_out_date
  )
  const blocked = availability.value.unavailable_dates?.includes(dateStr)
  return !booked && !blocked
}

function getDayClass(day) {
  if (!day) return ''
  const isSelected = day.dateStr === booking.value.check_in || day.dateStr === booking.value.check_out
  const inRange    = booking.value.check_in && booking.value.check_out &&
    day.dateStr > booking.value.check_in && day.dateStr < booking.value.check_out
  const isPast     = day.dateStr < today

  if (isSelected) return 'bg-blue-600 text-white font-bold'
  if (inRange)     return 'bg-blue-100 text-blue-800'
  if (!day.available || isPast) return 'bg-red-50 text-red-300 cursor-not-allowed'
  return 'hover:bg-green-100 text-gray-700'
}

function selectDay(day) {
  if (!day || !day.available || day.dateStr < today) return
  if (!booking.value.check_in || (booking.value.check_in && booking.value.check_out)) {
    booking.value.check_in  = day.dateStr
    booking.value.check_out = ''
  } else if (day.dateStr > booking.value.check_in) {
    booking.value.check_out = day.dateStr
  } else {
    booking.value.check_in = day.dateStr
  }
}

async function loadAvailability() {
  availability.value = await store.fetchAvailability(
    route.params.id,
    currentMonth.value,
    currentYear.value
  )
}

async function prevMonth() {
  if (currentMonth.value === 1) { currentMonth.value = 12; currentYear.value-- }
  else currentMonth.value--
  await loadAvailability()
}

async function nextMonth() {
  if (currentMonth.value === 12) { currentMonth.value = 1; currentYear.value++ }
  else currentMonth.value++
  await loadAvailability()
}


async function handleBooking() {
  if (!auth.isAuthenticated) {
    router.push('/login')
    return
  }

  loading.value = true
  error.value   = null

  try {
    const { data } = await api.post('/bookings', {
      resource_id:      route.params.id,
      check_in_date:    booking.value.check_in,
      check_out_date:   booking.value.check_out,
      special_requests: booking.value.special_requests,
    })
    router.push('/dashboard')
  } catch (e) {
    error.value = e.response?.data?.message || 'Une erreur est survenue.'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await store.fetchResource(route.params.id)
  await loadAvailability()
})
</script>
