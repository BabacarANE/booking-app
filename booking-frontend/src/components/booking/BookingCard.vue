<template>
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex flex-col md:flex-row">

      <!-- Image -->
      <div class="bg-gradient-to-br from-blue-100 to-blue-200 w-full md:w-48 h-40 md:h-auto flex items-center justify-center shrink-0">
        <span class="text-5xl">🏨</span>
      </div>

      <!-- Infos -->
      <div class="p-6 flex-1">
        <div class="flex items-start justify-between">
          <div>
            <h3 class="font-bold text-gray-900 text-lg">
              {{ booking.resource?.name }}
            </h3>
            <p class="text-gray-500 text-sm mt-1">
              📍 {{ booking.resource?.location }}
            </p>
          </div>

          <!-- Status badge -->
          <span :class="statusClass" class="text-xs font-semibold px-3 py-1 rounded-full">
            {{ statusLabel }}
          </span>
        </div>

        <!-- Dates -->
        <div class="flex gap-6 mt-4 text-sm text-gray-600">
          <div>
            <p class="text-xs text-gray-400 uppercase font-medium">Arrivée</p>
            <p class="font-semibold mt-1">{{ formatDate(booking.check_in_date) }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-400 uppercase font-medium">Départ</p>
            <p class="font-semibold mt-1">{{ formatDate(booking.check_out_date) }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-400 uppercase font-medium">Total</p>
            <p class="font-semibold text-blue-600 mt-1">{{ booking.total_price }}€</p>
          </div>
        </div>

        <!-- Actions -->
        <div v-if="!readonly && booking.status === 'pending'" class="flex gap-3 mt-5">
          <button
            @click="cancelBooking"
            :disabled="cancelling"
            class="bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium px-4 py-2 rounded-xl transition-colors disabled:opacity-50"
          >
            {{ cancelling ? 'Annulation...' : 'Annuler la réservation' }}
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import api from '@/services/api'

const props = defineProps({
  booking:  { type: Object, required: true },
  readonly: { type: Boolean, default: false },
})

const emit      = defineEmits(['cancelled'])
const cancelling = ref(false)

const statusMap = {
  pending:   { label: 'En attente',  class: 'bg-yellow-100 text-yellow-700' },
  confirmed: { label: 'Confirmée',   class: 'bg-green-100 text-green-700'  },
  cancelled: { label: 'Annulée',     class: 'bg-red-100 text-red-600'      },
  completed: { label: 'Terminée',    class: 'bg-gray-100 text-gray-600'    },
}

const statusLabel = computed(() => statusMap[props.booking.status]?.label || props.booking.status)
const statusClass = computed(() => statusMap[props.booking.status]?.class || '')

function formatDate(date) {
  return new Date(date).toLocaleDateString('fr-FR', {
    day: 'numeric', month: 'long', year: 'numeric'
  })
}

async function cancelBooking() {
  if (!confirm('Confirmer l\'annulation de cette réservation ?')) return
  cancelling.value = true
  try {
    await api.delete(`/bookings/${props.booking.id}`)
    emit('cancelled')
  } catch (e) {
    alert('Erreur lors de l\'annulation.')
  } finally {
    cancelling.value = false
  }
}
</script>
