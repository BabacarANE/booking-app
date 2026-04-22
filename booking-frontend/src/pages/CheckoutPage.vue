<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">

      <!-- Header -->
      <div class="text-center mb-10">
        <div class="inline-flex items-center gap-2 bg-white border border-gray-100 rounded-full px-5 py-2 shadow-sm mb-4">
          <span class="w-2 h-2 bg-green-500 rounded-full" />
          <span class="text-sm text-gray-600 font-medium">Paiement sécurisé</span>
          <img src="https://cdn.brandfetch.io/idxAg10C0L/w/400/h/400/theme/light/icon.jpeg?k=id64Mup7ac&t=1707326480706" class="h-5 ml-1" alt="Stripe" />
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Finaliser la réservation</h1>
      </div>

      <!-- Stepper -->
      <div class="flex items-center justify-center gap-1 md:gap-3 mb-8 md:mb-10 overflow-x-auto px-2">
        <div v-for="(step, index) in steps" :key="step.id" class="flex items-center gap-1 md:gap-3 shrink-0">
          <div class="flex items-center gap-1 md:gap-2">
            <div
              :class="[
                'w-7 h-7 md:w-8 md:h-8 rounded-full flex items-center justify-center text-xs md:text-sm font-bold transition-all',
                currentStep > index
                  ? 'bg-green-500 text-white'
                  : currentStep === index
                    ? 'bg-coral-500 text-white'
                    : 'bg-gray-200 text-gray-400'
              ]"
            >
              <span v-if="currentStep > index">✓</span>
              <span v-else>{{ index + 1 }}</span>
            </div>
            <span
              :class="[
                'text-xs md:text-sm font-medium hidden sm:inline-block',
                currentStep === index ? 'text-coral-500' : 'text-gray-400'
              ]"
            >
              {{ step.label }}
            </span>
          </div>
          <div v-if="index < steps.length - 1" class="w-8 md:w-16 h-px bg-gray-200 shrink-0" />
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">

        <!-- Colonne principale -->
        <div class="lg:col-span-2">

          <!-- Étape 1 — Récapitulatif réservation -->
          <div v-if="currentStep === 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-6">Votre réservation</h2>

            <div class="flex gap-3 md:gap-5 mb-6 flex-col md:flex-row md:items-start">
              <div class="w-20 md:w-24 h-20 md:h-24 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center text-3xl md:text-4xl shrink-0">
                🏨
              </div>
              <div class="min-w-0">
                <h3 class="font-bold text-gray-900 text-base md:text-lg break-words">{{ booking.resource?.name }}</h3>
                <p class="text-gray-500 mt-1 text-sm break-words">📍 {{ booking.resource?.location }}</p>
                <p class="text-gray-500 text-xs md:text-sm mt-2">
                  {{ booking.resource?.category?.name }}
                </p>
              </div>
            </div>

            <div class="bg-gray-50 rounded-2xl p-5 space-y-3">
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">📅 Arrivée</span>
                <span class="font-semibold">{{ formatDate(booking.check_in_date) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">📅 Départ</span>
                <span class="font-semibold">{{ formatDate(booking.check_out_date) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">🌙 Nuits</span>
                <span class="font-semibold">{{ nights }}</span>
              </div>
              <div v-if="booking.special_requests" class="flex justify-between text-sm">
                <span class="text-gray-500">💬 Demandes</span>
                <span class="font-semibold text-right max-w-48">{{ booking.special_requests }}</span>
              </div>
              <div class="flex justify-between font-bold text-gray-900 pt-3 border-t border-gray-200">
                <span>Total à payer</span>
                <span class="text-coral-500 text-lg">{{ booking.total_price }}€</span>
              </div>
            </div>

            <div class="flex gap-2 md:gap-3 mt-6 flex-col md:flex-row">
              <RouterLink
                :to="`/resources/${booking.resource_id}`"
                class="flex-1 border border-gray-200 text-gray-600 py-2.5 md:py-3 rounded-xl text-xs md:text-sm font-medium hover:bg-gray-50 transition-colors text-center"
              >
                ← Modifier
              </RouterLink>
              <button
                @click="proceedToPayment"
                :disabled="loadingIntent"
                class="flex-1 bg-coral-500 hover:bg-coral-600 disabled:opacity-50 text-white py-2.5 md:py-3 rounded-xl text-xs md:text-sm font-bold transition-colors"
              >
                {{ loadingIntent ? '⏳ Chargement...' : 'Procéder au paiement →' }}
              </button>
            </div>
          </div>

          <!-- Étape 2 — Formulaire paiement Stripe -->
          <div v-if="currentStep === 1" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-2">Informations de paiement</h2>
            <p class="text-gray-400 text-xs md:text-sm mb-6">
              🔒 Vos données sont chiffrées et sécurisées par Stripe
            </p>

            <!-- Stripe Elements sera monté ici -->
            <div id="payment-element" class="mb-6" />

            <p v-if="paymentError" class="text-red-500 text-xs md:text-sm bg-red-50 rounded-xl p-3 mb-4">
              ⚠️ {{ paymentError }}
            </p>

            <div v-if="currentStep === 1" class="flex items-center gap-2 bg-orange-50 border border-orange-200 rounded-xl px-3 md:px-4 py-2 md:py-3 mb-4 text-xs md:text-sm">
              <span class="text-orange-500 text-lg md:text-base">⏱️</span>
              <p class="text-orange-700">
                Votre réservation est réservée pendant
                <strong>{{ timerDisplay }}</strong> — complétez le paiement avant expiration.
              </p>
            </div>

            <div class="flex gap-2 md:gap-3 flex-col md:flex-row">
              <button
                @click="currentStep = 0"
                class="flex-1 border border-gray-200 text-gray-600 py-2.5 md:py-3 rounded-xl text-xs md:text-sm font-medium hover:bg-gray-50 transition-colors"
              >
                ← Retour
              </button>
              <button
                @click="handlePayment"
                :disabled="processingPayment"
                class="flex-1 bg-teal-500 hover:bg-teal-600 disabled:opacity-50 text-white py-2.5 md:py-3 rounded-xl text-xs md:text-sm font-bold transition-colors flex items-center justify-center gap-2"
              >
                <span v-if="processingPayment">⏳ Traitement...</span>
                <span v-else>🔒 Payer {{ booking.total_price }}€</span>
              </button>
            </div>

            <!-- Logos cartes -->
            <div class="flex items-center justify-center gap-2 md:gap-3 mt-5 opacity-50 flex-wrap">
              <span class="text-xs text-gray-400">Accepté :</span>
              <span class="text-xs md:text-sm">💳 Visa</span>
              <span class="text-xs md:text-sm">💳 Mastercard</span>
              <span class="text-xs md:text-sm">💳 Amex</span>
            </div>
          </div>

          <!-- Étape 3 — Confirmation -->
          <div v-if="currentStep === 2" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-12 text-center">
            <div class="w-20 h-20 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <span class="text-4xl">✅</span>
            </div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-3">Paiement confirmé !</h2>
            <p class="text-gray-500 text-sm md:text-base mb-2">
              Votre réservation <strong>#{{ booking.id }}</strong> est confirmée.
            </p>
            <p class="text-gray-400 text-xs md:text-sm mb-8">
              Un email de confirmation a été envoyé à <strong>{{ auth.user?.email }}</strong>
            </p>

            <div class="bg-teal-50 rounded-2xl p-4 md:p-5 text-left space-y-2 mb-8 max-w-xs mx-auto text-xs md:text-sm">
              <div class="flex justify-between">
                <span class="text-gray-500">Hébergement</span>
                <span class="font-semibold">{{ booking.resource?.name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Arrivée</span>
                <span class="font-semibold">{{ formatDate(booking.check_in_date) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Départ</span>
                <span class="font-semibold">{{ formatDate(booking.check_out_date) }}</span>
              </div>
              <div class="flex justify-between font-bold text-teal-600 pt-2 border-t border-teal-200">
                <span>Montant payé</span>
                <span>{{ booking.total_price }}€</span>
              </div>
            </div>

            <div class="flex gap-2 md:gap-3 justify-center flex-col md:flex-row">
              <RouterLink
                to="/dashboard"
                class="bg-coral-500 hover:bg-coral-600 text-white px-6 md:px-8 py-2.5 md:py-3 rounded-xl font-semibold transition-colors text-center text-xs md:text-sm"
              >
                Voir mes réservations
              </RouterLink>
              <RouterLink
                to="/resources"
                class="border border-gray-200 text-gray-600 px-6 md:px-8 py-2.5 md:py-3 rounded-xl font-medium hover:bg-gray-50 transition-colors text-center text-xs md:text-sm"
              >
                Continuer à explorer
              </RouterLink>
            </div>
          </div>

        </div>

        <!-- Récapitulatif latéral -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6 sticky top-4 md:top-24">
            <h3 class="font-bold text-gray-900 mb-4 text-sm md:text-base">Récapitulatif</h3>

            <div class="space-y-2 md:space-y-3 text-xs md:text-sm">
              <div class="flex justify-between text-gray-600 break-words">
                <span>{{ booking.resource?.name }}</span>
              </div>
              <div class="flex justify-between text-gray-600">
                <span>{{ booking.resource?.price_per_night }}€ × {{ nights }} nuits</span>
                <span>{{ booking.total_price }}€</span>
              </div>
              <div class="flex justify-between text-gray-500 text-xs">
                <span>Taxes incluses</span>
                <span>—</span>
              </div>
              <div class="flex justify-between font-bold text-gray-900 pt-2 md:pt-3 border-t text-sm md:text-base">
                <span>Total</span>
                <span class="text-coral-500">{{ booking.total_price }}€</span>
              </div>
            </div>

            <!-- Badge sécurité -->
            <div class="mt-4 md:mt-6 bg-gray-50 rounded-xl p-3 md:p-4 space-y-2">
              <div class="flex items-center gap-2 text-xs text-gray-500">
                <span class="text-sm">🔒</span> <span>Paiement 100% sécurisé</span>
              </div>
              <div class="flex items-center gap-2 text-xs text-gray-500">
                <span class="text-sm">✅</span> <span>Confirmation instantanée</span>
              </div>
              <div class="flex items-center gap-2 text-xs text-gray-500">
                <span class="text-sm">📧</span> <span>Email de confirmation</span>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { loadStripe } from '@stripe/stripe-js'
import { useBookingStore } from '@/stores/booking'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const route        = useRoute()
const router       = useRouter()
const bookingStore = useBookingStore()
const auth         = useAuthStore()

const currentStep      = ref(0)
const loadingIntent    = ref(false)
const processingPayment = ref(false)
const paymentError     = ref(null)
const booking          = ref({})
const timeLeft   = ref(15 * 60)


let timerInterval = null
let stripe        = null
let elements      = null
let paymentElement = null

const steps = [
  { id: 'recap',    label: 'Récapitulatif' },
  { id: 'payment',  label: 'Paiement'      },
  { id: 'confirm',  label: 'Confirmation'  },
]

const nights = computed(() => {
  if (!booking.value.check_in_date || !booking.value.check_out_date) return 0
  const diff = new Date(booking.value.check_out_date) - new Date(booking.value.check_in_date)
  return Math.floor(diff / (1000 * 60 * 60 * 24))
})

const timerDisplay = computed(() => {
  const minutes = Math.floor(timeLeft.value / 60)
  const seconds = timeLeft.value % 60
  return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
})

function startTimer() {
  timerInterval = setInterval(async () => {
    timeLeft.value--
    if (timeLeft.value <= 0) {
      clearInterval(timerInterval)
      // Annuler la réservation automatiquement
      await api.delete(`/bookings/${booking.value.id}`)
      alert('⏱️ Temps écoulé ! Votre réservation a été annulée.')
      router.push('/resources')
    }
  }, 1000)
}

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR', {
    day: 'numeric', month: 'long', year: 'numeric'
  })
}

async function proceedToPayment() {
  loadingIntent.value = true
  paymentError.value  = null
  try {
    const { client_secret } = await bookingStore.createPaymentIntent(booking.value.id)



    currentStep.value = 1
    await new Promise(r => setTimeout(r, 300))
    await mountStripeElement(client_secret)
  } catch (e) {
    console.error('❌ Erreur complète:', e) // ← Debug
    console.error('❌ Response:', e.response?.data) // ← Debug
    paymentError.value = e.response?.data?.message || e.message || 'Erreur lors de l\'initialisation du paiement.'
  } finally {
    loadingIntent.value = false
  }
}

async function mountStripeElement(clientSecret) {
  stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY)

  elements = stripe.elements({
    clientSecret,
    appearance: {
      theme: 'stripe',
      variables: {
        colorPrimary:    '#FF5A5F',
        colorBackground: '#ffffff',
        colorText:       '#484848',
        borderRadius:    '12px',
        fontFamily:      'system-ui, sans-serif',
      },
    },
  })

  paymentElement = elements.create('payment')
  paymentElement.mount('#payment-element')
}

async function handlePayment() {
  if (!stripe || !elements) return
  processingPayment.value = true
  paymentError.value      = null

  try {
    // Confirme le paiement côté Stripe
    const { error, paymentIntent } = await stripe.confirmPayment({
      elements,
      redirect: 'if_required',
    })

    if (error) {
      paymentError.value = error.message
      return
    }

    if (paymentIntent.status === 'succeeded') {
      // Confirme côté backend
      await bookingStore.confirmPayment(booking.value.id, paymentIntent.id)
      currentStep.value = 2

      // Recharge la réservation avec le nouveau statut
      const { data } = await api.get(`/bookings/${booking.value.id}`)
      booking.value = data.data
    }
  } catch (e) {
    paymentError.value = e.response?.data?.message || 'Erreur lors du paiement.'
  } finally {
    processingPayment.value = false
  }
}

onMounted(async () => {
  // Récupère la réservation depuis les query params
  const bookingId = route.query.booking_id
  if (!bookingId) {
    router.push('/dashboard')
    return
  }

  try {
    const { data } = await api.get(`/bookings/${bookingId}`)
    booking.value = data.data
  } catch {
    router.push('/dashboard')
  }
  startTimer()
})

onUnmounted(() => {
  clearInterval(timerInterval)
  if (paymentElement) paymentElement.unmount()
})
</script>
