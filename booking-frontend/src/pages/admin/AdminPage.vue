<template>
  <div class="max-w-7xl mx-auto px-4 py-10">

    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Panel Admin</h1>
      <p class="text-gray-500 mt-1">Gestion de la plateforme</p>
    </div>

    <!-- Tabs -->
    <div class="flex gap-2 mb-8 border-b border-gray-200">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        @click="activeTab = tab.id"
        :class="[
          'px-5 py-3 text-sm font-medium transition-colors border-b-2 -mb-px',
          activeTab === tab.id
            ? 'border-blue-600 text-blue-600'
            : 'border-transparent text-gray-500 hover:text-gray-700'
        ]"
      >
        {{ tab.label }}
      </button>
    </div>

    <!-- Dashboard Stats -->
    <div v-if="activeTab === 'stats'">
      <div v-if="store.stats" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
          <p class="text-3xl font-bold text-blue-600">{{ store.stats.total_bookings }}</p>
          <p class="text-sm text-gray-500 mt-1">Réservations</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
          <p class="text-3xl font-bold text-green-600">{{ store.stats.total_revenue }}€</p>
          <p class="text-sm text-gray-500 mt-1">Revenus</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
          <p class="text-3xl font-bold text-yellow-500">{{ store.stats.active_bookings }}</p>
          <p class="text-sm text-gray-500 mt-1">Actives</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
          <p class="text-3xl font-bold text-purple-600">{{ store.stats.total_resources }}</p>
          <p class="text-sm text-gray-500 mt-1">Ressources</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
          <p class="text-3xl font-bold text-gray-700">{{ store.stats.total_users }}</p>
          <p class="text-sm text-gray-500 mt-1">Clients</p>
        </div>
      </div>

      <!-- Bookings par mois -->
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h2 class="font-bold text-gray-900 mb-4">Réservations par mois</h2>
        <div class="flex items-end gap-4 h-40">
          <div
            v-for="item in store.stats?.bookings_by_month"
            :key="item.month"
            class="flex flex-col items-center gap-2 flex-1"
          >
            <span class="text-sm font-bold text-blue-600">{{ item.count }}</span>
            <div
              class="w-full bg-blue-500 rounded-t-lg"
              :style="`height: ${(item.count / maxBookings) * 100}px`"
            />
            <span class="text-xs text-gray-400">{{ item.month }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Gestion Ressources -->
    <div v-if="activeTab === 'resources'">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-900">Ressources</h2>
        <button
          @click="showResourceModal = true"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors"
        >
          + Ajouter une ressource
        </button>
      </div>

      <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-100">
          <tr>
            <th class="text-left px-6 py-4 font-medium text-gray-500">Nom</th>
            <th class="text-left px-6 py-4 font-medium text-gray-500">Catégorie</th>
            <th class="text-left px-6 py-4 font-medium text-gray-500">Prix/nuit</th>
            <th class="text-left px-6 py-4 font-medium text-gray-500">Capacité</th>
            <th class="text-left px-6 py-4 font-medium text-gray-500">Statut</th>
            <th class="text-left px-6 py-4 font-medium text-gray-500">Actions</th>
          </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
          <tr v-for="resource in store.resources" :key="resource.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium text-gray-900">{{ resource.name }}</td>
            <td class="px-6 py-4 text-gray-500">{{ resource.category?.name }}</td>
            <td class="px-6 py-4 text-blue-600 font-medium">{{ resource.price_per_night }}€</td>
            <td class="px-6 py-4 text-gray-500">{{ resource.capacity }} pers.</td>
            <td class="px-6 py-4">
                <span :class="resource.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                      class="text-xs font-semibold px-2 py-1 rounded-full">
                  {{ resource.is_active ? 'Actif' : 'Inactif' }}
                </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex gap-2">
                <button
                  @click="editResource(resource)"
                  class="text-blue-600 hover:text-blue-800 text-xs font-medium"
                >
                  Modifier
                </button>
                <button
                  @click="store.toggleResource(resource.id)"
                  class="text-red-500 hover:text-red-700 text-xs font-medium"
                >
                  {{ resource.is_active ? 'Désactiver' : 'Activer' }}
                </button>
              </div>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Gestion Réservations -->
    <div v-if="activeTab === 'bookings'">
      <div class="flex items-center gap-4 mb-6">
        <h2 class="text-xl font-bold text-gray-900">Réservations</h2>
        <select
          v-model="bookingFilter"
          @change="store.fetchBookings({ status: bookingFilter })"
          class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none"
        >
          <option value="">Tous les statuts</option>
          <option value="pending">En attente</option>
          <option value="confirmed">Confirmées</option>
          <option value="cancelled">Annulées</option>
          <option value="completed">Terminées</option>
        </select>
      </div>

      <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-100">
          <tr>
            <th class="text-left px-6 py-4 font-medium text-gray-500">Client</th>
            <th class="text-left px-6 py-4 font-medium text-gray-500">Ressource</th>
            <th class="text-left px-6 py-4 font-medium text-gray-500">Dates</th>
            <th class="text-left px-6 py-4 font-medium text-gray-500">Total</th>
            <th class="text-left px-6 py-4 font-medium text-gray-500">Statut</th>
            <th class="text-left px-6 py-4 font-medium text-gray-500">Actions</th>
          </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
          <tr v-for="booking in store.bookings" :key="booking.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <p class="font-medium text-gray-900">{{ booking.user?.name }}</p>
              <p class="text-gray-400 text-xs">{{ booking.user?.email }}</p>
            </td>
            <td class="px-6 py-4 text-gray-600">{{ booking.resource?.name }}</td>
            <td class="px-6 py-4 text-gray-500">
              {{ booking.check_in_date }} → {{ booking.check_out_date }}
            </td>
            <td class="px-6 py-4 text-blue-600 font-medium">{{ booking.total_price }}€</td>
            <td class="px-6 py-4">
                <span :class="statusClass(booking.status)"
                      class="text-xs font-semibold px-2 py-1 rounded-full">
                  {{ statusLabel(booking.status) }}
                </span>
            </td>
            <td class="px-6 py-4">
              <select
                :value="booking.status"
                @change="store.updateBookingStatus(booking.id, $event.target.value)"
                class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none"
              >
                <option value="pending">En attente</option>
                <option value="confirmed">Confirmer</option>
                <option value="cancelled">Annuler</option>
                <option value="completed">Terminer</option>
              </select>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Gestion Utilisateurs -->
    <div v-if="activeTab === 'users'">
      <h2 class="text-xl font-bold text-gray-900 mb-6">Utilisateurs</h2>
      <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-100">
          <tr>
            <th class="text-left px-6 py-4 font-medium text-gray-500">Nom</th>
            <th class="text-left px-6 py-4 font-medium text-gray-500">Email</th>
            <th class="text-left px-6 py-4 font-medium text-gray-500">Rôle</th>
            <th class="text-left px-6 py-4 font-medium text-gray-500">Actions</th>
          </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
          <tr v-for="user in store.users" :key="user.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium text-gray-900">{{ user.name }}</td>
            <td class="px-6 py-4 text-gray-500">{{ user.email }}</td>
            <td class="px-6 py-4">
                <span :class="roleClass(user.role)" class="text-xs font-semibold px-2 py-1 rounded-full">
                  {{ user.role }}
                </span>
            </td>
            <td class="px-6 py-4">
              <select
                :value="user.role"
                @change="store.updateUserRole(user.id, $event.target.value)"
                class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none"
              >
                <option value="client">Client</option>
                <option value="manager">Manager</option>
                <option value="admin">Admin</option>
              </select>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Ressource -->
    <ResourceModal
      v-if="showResourceModal"
      :resource="selectedResource"
      @close="closeModal"
      @saved="onResourceSaved"
    />

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAdminStore } from '@/stores/admin'
import ResourceModal from '@/components/admin/ResourceModal.vue'

const store = useAdminStore()

const activeTab         = ref('stats')
const showResourceModal = ref(false)
const selectedResource  = ref(null)
const bookingFilter     = ref('')

const tabs = [
  { id: 'stats',     label: '📊 Dashboard' },
  { id: 'resources', label: '🏨 Ressources' },
  { id: 'bookings',  label: '📅 Réservations' },
  { id: 'users',     label: '👥 Utilisateurs' },
]

const maxBookings = computed(() => {
  const counts = store.stats?.bookings_by_month?.map(b => b.count) || [1]
  return Math.max(...counts)
})

const statusMap = {
  pending:   { label: 'En attente', class: 'bg-yellow-100 text-yellow-700' },
  confirmed: { label: 'Confirmée',  class: 'bg-green-100 text-green-700'  },
  cancelled: { label: 'Annulée',    class: 'bg-red-100 text-red-600'      },
  completed: { label: 'Terminée',   class: 'bg-gray-100 text-gray-600'    },
}

function statusLabel(status) { return statusMap[status]?.label || status }
function statusClass(status) { return statusMap[status]?.class || '' }

function roleClass(role) {
  return {
    admin:   'bg-purple-100 text-purple-700',
    manager: 'bg-blue-100 text-blue-700',
    client:  'bg-gray-100 text-gray-600',
  }[role] || ''
}

function editResource(resource) {
  selectedResource.value  = resource
  showResourceModal.value = true
}

function closeModal() {
  showResourceModal.value = false
  selectedResource.value  = null
}

async function onResourceSaved() {
  closeModal()
  await store.fetchResources()
}

onMounted(async () => {
  await store.fetchStats()
  await store.fetchResources()
  await store.fetchBookings()
  await store.fetchUsers()
})
</script>
