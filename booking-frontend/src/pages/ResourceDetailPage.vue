<template>
  <div class="min-h-screen bg-gray-50">

    <!-- Loading skeleton -->
    <div v-if="store.loading" class="max-w-6xl mx-auto px-4 py-10 animate-pulse space-y-6">
      <div class="bg-gray-200 rounded-2xl h-80" />
      <div class="grid grid-cols-3 gap-8">
        <div class="col-span-2 space-y-4">
          <div class="bg-gray-200 rounded-xl h-10 w-2/3" />
          <div class="bg-gray-200 rounded-xl h-6 w-1/3" />
          <div class="bg-gray-200 rounded-xl h-32" />
        </div>
        <div class="bg-gray-200 rounded-2xl h-80" />
      </div>
    </div>

    <div v-else-if="store.resource">

      <!-- Hero image / Galerie carrousel -->
      <div class="relative h-96 overflow-hidden bg-gradient-to-br from-blue-600 to-indigo-600">

        <!-- Images -->
        <transition-group name="fade">
          <div
            v-for="(img, index) in store.resource.images_list"
            :key="img.id"
            v-show="currentImageIndex === index"
            class="absolute inset-0"
          >
            <img
              :src="img.url"
              :alt="store.resource.name"
              class="w-full h-full object-cover"
            />
          </div>
        </transition-group>

        <!-- Fallback si pas d'images -->
        <div
          v-if="!store.resource.images_list?.length"
          class="absolute inset-0 flex items-center justify-center"
        >
          <span class="text-9xl opacity-20">🏨</span>
        </div>

        <!-- Overlay gradient -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent" />

        <!-- Boutons navigation -->
        <template v-if="store.resource.images_list?.length > 1">
          <button
            @click="prevImage"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white rounded-full flex items-center justify-center transition-all hover:scale-110"
          >
            ←
          </button>
          <button
            @click="nextImage"
            class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white rounded-full flex items-center justify-center transition-all hover:scale-110"
          >
            →
          </button>
        </template>

        <!-- Back button -->
        <div class="absolute top-6 left-6">
          <RouterLink
            to="/resources"
            class="flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-white/30 transition-colors"
          >
            ← Retour
          </RouterLink>
        </div>

        <!-- Badge catégorie -->
        <div class="absolute top-6 right-6">
        <span class="bg-white/20 backdrop-blur-sm text-white text-sm font-semibold px-4 py-2 rounded-xl">
          {{ store.resource.category?.name }}
        </span>
        </div>

        <!-- Titre -->
        <div class="absolute bottom-16 left-6 right-6">
          <h1 class="text-3xl font-bold text-white">{{ store.resource.name }}</h1>
          <p class="text-white/80 mt-1">📍 {{ store.resource.location }}</p>
        </div>

        <!-- Miniatures + indicateurs -->
        <div class="absolute bottom-4 left-0 right-0 flex items-center justify-center gap-2">
          <template v-if="store.resource.images_list?.length > 1">
            <!-- Miniatures cliquables -->
            <div class="flex gap-2 bg-black/30 backdrop-blur-sm rounded-2xl p-2">
              <button
                v-for="(img, index) in store.resource.images_list"
                :key="img.id"
                @click="goToImage(index)"
                class="relative w-12 h-12 rounded-xl overflow-hidden transition-all duration-300"
                :class="currentImageIndex === index
            ? 'ring-2 ring-white scale-110'
            : 'opacity-60 hover:opacity-100'"
              >
                <img :src="img.url" class="w-full h-full object-cover" />
              </button>
            </div>
          </template>

          <!-- Compteur -->
          <div
            v-if="store.resource.images_list?.length > 1"
            class="bg-black/30 backdrop-blur-sm text-white text-xs px-3 py-1.5 rounded-full font-medium"
          >
            {{ currentImageIndex + 1 }} / {{ store.resource.images_list.length }}
          </div>
        </div>
      </div>

      <div class="max-w-6xl mx-auto px-4 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

          <!-- Colonne gauche -->
          <div class="lg:col-span-2 space-y-8">

            <!-- Description -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
              <h2 class="text-xl font-bold text-gray-900 mb-3">Description</h2>
              <p class="text-gray-600 leading-relaxed">{{ store.resource.description }}</p>

              <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t border-gray-100">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                    <span>👥</span>
                  </div>
                  <div>
                    <p class="text-xs text-gray-400">Capacité</p>
                    <p class="font-semibold text-gray-800">{{ store.resource.capacity }} personnes</p>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                    <span>💰</span>
                  </div>
                  <div>
                    <p class="text-xs text-gray-400">Prix</p>
                    <p class="font-semibold text-gray-800">{{ store.resource.price_per_night }}€ / nuit</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Équipements -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Équipements inclus</h2>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div
                  v-for="amenity in store.resource.amenities"
                  :key="amenity"
                  class="flex items-center gap-3 bg-green-50 rounded-xl px-4 py-3"
                >
                  <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center shrink-0">
                    <span class="text-white text-xs">✓</span>
                  </div>
                  <span class="text-gray-700 text-sm font-medium">{{ amenity }}</span>
                </div>
              </div>
            </div>

            <!-- Calendrier -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
              <h2 class="text-xl font-bold text-gray-900 mb-6">Disponibilités</h2>

              <!-- Navigation mois -->
              <div class="flex items-center justify-between mb-6">
                <button
                  @click="prevMonth"
                  class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-gray-100 transition-colors text-gray-600"
                >←</button>
                <h3 class="font-bold text-gray-800 text-lg">{{ monthName }} {{ currentYear }}</h3>
                <button
                  @click="nextMonth"
                  class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-gray-100 transition-colors text-gray-600"
                >→</button>
              </div>

              <!-- Jours semaine -->
              <div class="grid grid-cols-7 gap-1 mb-2">
                <div
                  v-for="day in ['L', 'M', 'M', 'J', 'V', 'S', 'D']"
                  :key="day"
                  class="text-center text-xs font-bold text-gray-400 py-2"
                >{{ day }}</div>
              </div>

              <!-- Jours -->
              <div class="grid grid-cols-7 gap-1">
                <div
                  v-for="(day, index) in calendarDays"
                  :key="index"
                  class="aspect-square flex items-center justify-center rounded-xl text-sm font-medium cursor-pointer transition-all"
                  :class="getDayClass(day)"
                  @click="selectDay(day)"
                >
                  {{ day?.date }}
                </div>
              </div>

              <!-- Légende -->
              <div class="flex gap-6 mt-6 pt-4 border-t border-gray-100">
                <div class="flex items-center gap-2 text-xs text-gray-500">
                  <div class="w-4 h-4 bg-gray-100 rounded-lg" /> Passé
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                  <div class="w-4 h-4 bg-red-100 rounded-lg" /> Indisponible
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                  <div class="w-4 h-4 bg-blue-600 rounded-lg" /> Sélectionné
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                  <div class="w-4 h-4 bg-blue-100 rounded-lg" /> Dans la plage
                </div>
              </div>
            </div>

          </div>

          <!-- Colonne droite — Bloc réservation sticky -->
          <div class="lg:col-span-1">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-lg sticky top-24 overflow-hidden">

              <!-- Header prix -->
              <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white text-center">
                <p class="text-sm text-blue-100 mb-1">À partir de</p>
                <div class="flex items-end justify-center gap-1">
                  <span class="text-4xl font-bold">{{ store.resource.price_per_night }}</span>
                  <span class="text-xl mb-1">€</span>
                </div>
                <p class="text-blue-100 text-sm">par nuit</p>
              </div>

              <div class="p-6 space-y-4">

                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                    📅 Arrivée
                  </label>
                  <input
                    v-model="booking.check_in"
                    type="date"
                    :min="today"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>

                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                    📅 Départ
                  </label>
                  <input
                    v-model="booking.check_out"
                    type="date"
                    :min="booking.check_in || today"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>

                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                    💬 Demandes spéciales
                  </label>
                  <textarea
                    v-model="booking.special_requests"
                    rows="2"
                    placeholder="Chambre non-fumeur, lit bébé..."
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                  />
                </div>

                <!-- Récap prix -->
                <div v-if="nights > 0" class="bg-blue-50 rounded-xl p-4 space-y-2">
                  <div class="flex justify-between text-sm text-gray-600">
                    <span>{{ store.resource.price_per_night }}€ × {{ nights }} nuit(s)</span>
                    <span class="font-medium">{{ totalPrice }}€</span>
                  </div>
                  <div class="flex justify-between font-bold text-gray-900 pt-2 border-t border-blue-100">
                    <span>Total</span>
                    <span class="text-blue-600 text-lg">{{ totalPrice }}€</span>
                  </div>
                </div>

                <p v-if="error" class="text-red-500 text-sm text-center bg-red-50 rounded-xl p-3">
                  ⚠️ {{ error }}
                </p>

                <button
                  @click="handleBooking"
                  :disabled="!nights || loading"
                  class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold py-3.5 rounded-xl transition-all shadow-lg hover:shadow-xl"
                >
                  {{ loading ? '⏳ Réservation...' : nights ? `Réserver — ${totalPrice}€` : 'Sélectionnez des dates' }}
                </button>

                <p class="text-center text-xs text-gray-400">
                  Annulation gratuite sous conditions
                </p>

              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted , onUnmounted} from 'vue'
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
const activeImage = ref(null)

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

    // ✅ data.data.id au lieu de data.id
    router.push({
      name:  'checkout',
      query: { booking_id: data.data.id }
    })
  } catch (e) {
    error.value = e.response?.data?.message || 'Une erreur est survenue.'
  } finally {
    loading.value = false
  }
}

// ✅ Carrousel
const currentImageIndex = ref(0)
let autoplayInterval    = null

function nextImage() {
  const total = store.resource?.images_list?.length || 0
  if (total === 0) return
  currentImageIndex.value = (currentImageIndex.value + 1) % total
}

function prevImage() {
  const total = store.resource?.images_list?.length || 0
  if (total === 0) return
  currentImageIndex.value = (currentImageIndex.value - 1 + total) % total
}

function goToImage(index) {
  currentImageIndex.value = index
  resetAutoplay()
}

function startAutoplay() {
  const total = store.resource?.images_list?.length || 0
  if (total <= 1) return
  autoplayInterval = setInterval(() => {
    nextImage()
  }, 4000) // ✅ Change d'image toutes les 4 secondes
}

function resetAutoplay() {
  clearInterval(autoplayInterval)
  startAutoplay()
}

function stopAutoplay() {
  clearInterval(autoplayInterval)
}

onMounted(async () => {
  await store.fetchResource(route.params.id)
  await loadAvailability()
  startAutoplay() // ✅ Lance le défilement automatique
})

onUnmounted(() => {
  stopAutoplay() // ✅ Nettoie l'intervalle quand on quitte la page
})


</script>
<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.6s ease;
  position: absolute;
  inset: 0;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
