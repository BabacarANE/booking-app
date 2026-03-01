import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useBookingStore = defineStore('booking', () => {
  const currentBooking = ref(null)
  const clientSecret   = ref(null)
  const loading        = ref(false)

  async function createBooking(payload) {
    loading.value = true
    try {
      const { data } = await api.post('/bookings', payload)
      currentBooking.value = data
      return data
    } finally {
      loading.value = false
    }
  }

  async function createPaymentIntent(bookingId) {
    const { data } = await api.post('/payments', { booking_id: bookingId })
    clientSecret.value = data.client_secret
    return data
  }

  async function confirmPayment(bookingId, paymentIntentId) {
    const { data } = await api.post('/payments/confirm', {
      booking_id:        bookingId,
      payment_intent_id: paymentIntentId,
    })
    return data
  }

  function reset() {
    currentBooking.value = null
    clientSecret.value   = null
  }

  return {
    currentBooking,
    clientSecret,
    loading,
    createBooking,
    createPaymentIntent,
    confirmPayment,
    reset,
  }
})
