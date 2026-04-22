<template>
  <div class="min-h-screen bg-gray-50">

    <!-- Header Admin -->
    <div class="bg-gradient-to-r from-neutral-900 to-neutral-800 text-white px-3 md:px-6 py-6 md:py-8">
      <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-0">
          <div class="min-w-0">
            <h1 class="text-xl md:text-2xl font-bold">Panel Administration</h1>
            <p class="text-gray-400 mt-1 text-xs md:text-sm">Gestion complète de la plateforme</p>
          </div>
          <div class="flex items-center gap-2 bg-white/10 rounded-xl px-3 md:px-4 py-2 w-fit">
            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse" />
            <span class="text-xs md:text-sm text-gray-300">En ligne</span>
          </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-1 mt-6 md:mt-8 overflow-x-auto pb-2">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'flex items-center gap-1 md:gap-2 px-3 md:px-5 py-2 md:py-2.5 rounded-xl text-xs md:text-sm font-medium transition-all whitespace-nowrap shrink-0',
              activeTab === tab.id
                ? 'bg-white text-gray-900 shadow-sm'
                : 'text-gray-400 hover:text-white hover:bg-white/10'
            ]"
          >
            <span>{{ tab.icon }}</span>
            <span class="hidden sm:inline">{{ tab.label }}</span>
          </button>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-3 md:px-4 py-6 md:py-8">

      <!-- Dashboard Stats -->
      <div v-if="activeTab === 'stats'">

        <!-- KPIs -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 md:gap-4 mb-6 md:mb-8">
          <div
            v-for="kpi in kpis"
            :key="kpi.label"
            class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-gray-100"
          >
            <div class="flex items-center justify-between mb-2 md:mb-3">
              <div :class="`w-10 h-10 ${kpi.bg} rounded-xl flex items-center justify-center text-lg md:text-xl`">
                {{ kpi.icon }}
              </div>
            </div>
            <p class="text-xl md:text-2xl font-bold" :class="kpi.color">{{ kpi.value }}</p>
            <p class="text-xs md:text-sm text-gray-500 mt-1">{{ kpi.label }}</p>
          </div>
        </div>

        <!-- Graphique réservations par mois -->
        <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-gray-100 mb-6 md:mb-8">
          <h2 class="font-bold text-gray-900 text-base md:text-lg mb-4 md:mb-6">Réservations par mois</h2>
          <div class="flex items-end gap-2 md:gap-3 h-40 md:h-48 overflow-x-auto pb-2">
            <div
              v-for="item in store.stats?.bookings_by_month"
              :key="item.month"
              class="flex flex-col items-center gap-2 flex-1"
            >
              <span class="text-sm font-bold text-coral-500">{{ item.count }}</span>
              <div
                class="w-full bg-gradient-to-t from-coral-600 to-coral-400 rounded-t-xl transition-all duration-500"
                :style="`height: ${Math.max(20, (item.count / maxBookings) * 160)}px`"
              />
              <span class="text-xs text-gray-400 font-medium">{{ item.month }}</span>
            </div>
          </div>
        </div>

        <!-- Dernières réservations -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="px-4 md:px-6 py-4 md:py-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-900 text-base md:text-lg">Dernières réservations</h2>
            <button
              @click="activeTab = 'bookings'"
              class="text-coral-500 hover:text-coral-700 text-xs md:text-sm font-medium"
            >
              Voir tout →
            </button>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-xs md:text-sm">
              <thead class="bg-gray-50">
              <tr>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-medium text-gray-500">Client</th>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-medium text-gray-500 hidden md:table-cell">Hébergement</th>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-medium text-gray-500">Total</th>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-medium text-gray-500">Statut</th>
              </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
              <tr v-for="booking in store.bookings.slice(0, 5)" :key="booking.id" class="hover:bg-gray-50">
                <td class="px-4 md:px-6 py-3 md:py-4">
                  <p class="font-medium text-gray-900 text-xs md:text-sm">{{ booking.user?.name }}</p>
                  <p class="text-gray-400 text-xs hidden md:inline">{{ booking.user?.email }}</p>
                </td>
                <td class="px-4 md:px-6 py-3 md:py-4 text-gray-600 hidden md:table-cell">{{ booking.resource?.name }}</td>
                <td class="px-4 md:px-6 py-3 md:py-4 font-bold text-coral-500">{{ booking.total_price }}€</td>
                <td class="px-4 md:px-6 py-3 md:py-4">
                    <span :class="statusClass(booking.status)" class="text-xs font-semibold px-2 md:px-3 py-1 md:py-1.5 rounded-full">
                      {{ statusLabel(booking.status) }}
                    </span>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Gestion Ressources -->
      <div v-if="activeTab === 'resources'">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
          <div>
            <h2 class="text-lg md:text-xl font-bold text-gray-900">Ressources</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">{{ store.resources.length }} hébergements</p>
          </div>
          <button
            @click="showResourceModal = true"
            class="bg-coral-500 hover:bg-coral-600 text-white px-4 md:px-5 py-2 md:py-2.5 rounded-xl text-xs md:text-sm font-medium transition-colors shadow-sm flex items-center gap-2 justify-center sm:justify-start"
          >
            <span>+</span> <span class="hidden sm:inline">Ajouter</span>
          </button>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
          <div class="overflow-x-auto">
            <table class="w-full text-xs md:text-sm">
              <thead class="bg-gray-50 border-b border-gray-100">
              <tr>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-semibold text-gray-600">Hébergement</th>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-semibold text-gray-600 hidden lg:table-cell">Catégorie</th>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-semibold text-gray-600">Prix</th>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-semibold text-gray-600 hidden md:table-cell">Capacité</th>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-semibold text-gray-600">Statut</th>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-semibold text-gray-600">Actions</th>
              </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
              <tr v-for="resource in store.resources" :key="resource.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-4 md:px-6 py-3 md:py-4">
                  <div class="flex items-center gap-2 md:gap-3">
                    <div class="w-8 md:w-10 h-8 md:h-10 bg-coral-100 rounded-lg md:rounded-xl flex items-center justify-center flex-shrink-0">
                      <span class="text-lg md:text-xl">🏨</span>
                    </div>
                    <div class="min-w-0">
                      <p class="font-semibold text-gray-900 truncate">{{ resource.name }}</p>
                      <p class="text-gray-400 text-xs truncate">📍 {{ resource.location }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-4 md:px-6 py-3 md:py-4 hidden lg:table-cell">
                    <span class="bg-coral-50 text-coral-700 text-xs font-semibold px-2 md:px-3 py-1 rounded">
                      {{ resource.category?.name }}
                    </span>
                </td>
                <td class="px-4 md:px-6 py-3 md:py-4 font-bold text-coral-500 text-xs md:text-sm">{{ resource.price_per_night }}€</td>
                <td class="px-4 md:px-6 py-3 md:py-4 text-gray-500 hidden md:table-cell">{{ resource.capacity }} pers.</td>
                <td class="px-4 md:px-6 py-3 md:py-4">
                    <span
                      :class="resource.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                      class="text-xs font-semibold px-2 md:px-3 py-1 rounded"
                    >
                      {{ resource.is_active ? '✓' : '✗' }} <span class="hidden md:inline">{{ resource.is_active ? 'Actif' : 'Inactif' }}</span>
                    </span>
                </td>
                <td class="px-4 md:px-6 py-3 md:py-4">
                  <div class="flex gap-1 md:gap-2">
                    <button
                      @click="editResource(resource)"
                      class="bg-coral-50 hover:bg-coral-100 text-coral-500 text-xs font-medium px-2 md:px-3 py-1 md:py-1.5 rounded transition-colors"
                    >
                      Modifier
                    </button>
                    <button
                      @click="store.toggleResource(resource.id)"
                      :class="resource.is_active
                          ? 'bg-red-50 hover:bg-red-100 text-red-500'
                          : 'bg-green-50 hover:bg-green-100 text-green-600'"
                      class="text-xs font-medium px-2 md:px-3 py-1 md:py-1.5 rounded transition-colors"
                    >
                      {{ resource.is_active ? 'Dés.' : 'Act.' }} <span class="hidden md:inline">{{ resource.is_active ? 'activer' : 'tiver' }}</span>
                    </button>
                  </div>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Gestion Réservations -->
      <div v-if="activeTab === 'bookings'">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
          <div>
            <h2 class="text-lg md:text-xl font-bold text-gray-900">Réservations</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">{{ store.bookings.length }} réservation(s)</p>
          </div>
          <select
            v-model="bookingFilter"
            @change="store.fetchBookings({ status: bookingFilter || undefined })"
            class="border border-gray-200 rounded-lg md:rounded-xl px-3 md:px-4 py-2 md:py-2.5 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-coral-400 bg-white"
          >
            <option value="">Tous les statuts</option>
            <option value="pending">En attente</option>
            <option value="confirmed">Confirmées</option>
            <option value="cancelled">Annulées</option>
            <option value="completed">Terminées</option>
          </select>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
          <div class="overflow-x-auto">
            <table class="w-full text-xs md:text-sm">
              <thead class="bg-gray-50 border-b border-gray-100">
              <tr>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-semibold text-gray-600">Client</th>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-semibold text-gray-600 hidden md:table-cell">Hébergement</th>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-semibold text-gray-600 hidden lg:table-cell">Dates</th>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-semibold text-gray-600">Total</th>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-semibold text-gray-600">Statut</th>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-semibold text-gray-600">Action</th>
              </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
              <tr v-for="booking in store.bookings" :key="booking.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-4 md:px-6 py-3 md:py-4">
                  <p class="font-semibold text-gray-900 text-xs md:text-sm">{{ booking.user?.name }}</p>
                  <p class="text-gray-400 text-xs hidden md:inline">{{ booking.user?.email }}</p>
                </td>
                <td class="px-4 md:px-6 py-3 md:py-4 text-gray-600 text-xs md:text-sm hidden md:table-cell">{{ booking.resource?.name }}</td>
                <td class="px-4 md:px-6 py-3 md:py-4 text-gray-500 text-xs hidden lg:table-cell">
                  <p>{{ booking.check_in_date }}</p>
                  <p>→ {{ booking.check_out_date }}</p>
                </td>
                <td class="px-4 md:px-6 py-3 md:py-4 font-bold text-coral-500 text-xs md:text-sm">{{ booking.total_price }}€</td>
                <td class="px-4 md:px-6 py-3 md:py-4">
                    <span :class="statusClass(booking.status)" class="text-xs font-semibold px-2 md:px-3 py-1 rounded">
                      {{ statusLabel(booking.status) }}
                    </span>
                </td>
                <td class="px-4 md:px-6 py-3 md:py-4">
                  <select
                    :value="booking.status"
                    @change="store.updateBookingStatus(booking.id, $event.target.value)"
                    class="border border-gray-200 rounded px-2 md:px-2.5 py-1 md:py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-coral-400 bg-white"
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
      </div>

      <!-- Gestion Utilisateurs -->
      <div v-if="activeTab === 'users'">
        <div class="mb-6">
          <h2 class="text-lg md:text-xl font-bold text-gray-900">Utilisateurs</h2>
          <p class="text-gray-500 text-xs md:text-sm mt-1">{{ store.users.length }} utilisateur(s)</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
          <div class="overflow-x-auto">
            <table class="w-full text-xs md:text-sm">
              <thead class="bg-gray-50 border-b border-gray-100">
              <tr>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-semibold text-gray-600">Utilisateur</th>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-semibold text-gray-600 hidden md:table-cell">Téléphone</th>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-semibold text-gray-600">Rôle</th>
                <th class="text-left px-4 md:px-6 py-3 md:py-4 font-semibold text-gray-600">Action</th>
              </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
              <tr v-for="user in store.users" :key="user.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-4 md:px-6 py-3 md:py-4">
                  <div class="flex items-center gap-2 md:gap-3">
                    <div class="w-8 md:w-9 h-8 md:h-9 bg-gradient-to-br from-coral-400 to-coral-600 rounded-lg md:rounded-xl flex items-center justify-center text-white text-xs md:text-sm font-bold flex-shrink-0">
                      {{ user.name?.charAt(0).toUpperCase() }}
                    </div>
                    <div class="min-w-0">
                      <p class="font-semibold text-gray-900 text-xs md:text-sm truncate">{{ user.name }}</p>
                      <p class="text-gray-400 text-xs truncate hidden md:inline-block">{{ user.email }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-4 md:px-6 py-3 md:py-4 text-gray-500 hidden md:table-cell">{{ user.phone || '—' }}</td>
                <td class="px-4 md:px-6 py-3 md:py-4">
                    <span :class="roleClass(user.role)" class="text-xs font-semibold px-2 md:px-3 py-1 rounded">
                      {{ user.role }}
                    </span>
                </td>
                <td class="px-4 md:px-6 py-3 md:py-4">
                  <select
                    :value="user.role"
                    @change="store.updateUserRole(user.id, $event.target.value)"
                    class="border border-gray-200 rounded px-2 md:px-2.5 py-1 md:py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-coral-400 bg-white"
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
  { id: 'stats',     icon: '📊', label: 'Dashboard'    },
  { id: 'resources', icon: '🏨', label: 'Ressources'   },
  { id: 'bookings',  icon: '📅', label: 'Réservations' },
  { id: 'users',     icon: '👥', label: 'Utilisateurs' },
]

const maxBookings = computed(() => {
  const counts = store.stats?.bookings_by_month?.map(b => b.count) || [1]
  return Math.max(...counts)
})

const kpis = computed(() => [
  { icon: '📅', label: 'Réservations',  value: store.stats?.total_bookings  || 0, color: 'text-coral-500',   bg: 'bg-coral-50'   },
  { icon: '💰', label: 'Revenus',       value: `${store.stats?.total_revenue || 0}€`, color: 'text-green-600', bg: 'bg-green-50' },
  { icon: '⏳', label: 'Actives',       value: store.stats?.active_bookings  || 0, color: 'text-yellow-600', bg: 'bg-yellow-50' },
  { icon: '🏨', label: 'Ressources',   value: store.stats?.total_resources  || 0, color: 'text-teal-500', bg: 'bg-teal-50' },
  { icon: '👥', label: 'Clients',      value: store.stats?.total_users      || 0, color: 'text-gray-700',   bg: 'bg-gray-100'  },
])

const statusMap = {
  pending:   { label: 'En attente', class: 'bg-yellow-100 text-yellow-700' },
  confirmed: { label: 'Confirmée',  class: 'bg-green-100 text-green-700'  },
  cancelled: { label: 'Annulée',    class: 'bg-red-100 text-red-600'      },
  completed: { label: 'Terminée',   class: 'bg-gray-100 text-gray-600'    },
}

function statusLabel(status) { return statusMap[status]?.label || status }
function statusClass(status) { return statusMap[status]?.class || '' }

function roleClass(role) {
  return { admin: 'bg-teal-100 text-teal-700', manager: 'bg-coral-100 text-coral-700', client: 'bg-gray-100 text-gray-600' }[role] || ''
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
