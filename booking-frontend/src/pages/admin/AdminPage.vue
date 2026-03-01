<template>
  <div class="min-h-screen bg-gray-50">

    <!-- Header Admin -->
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 text-white px-6 py-8">
      <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold">Panel Administration</h1>
            <p class="text-gray-400 mt-1 text-sm">Gestion complète de la plateforme</p>
          </div>
          <div class="flex items-center gap-2 bg-white/10 rounded-xl px-4 py-2">
            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse" />
            <span class="text-sm text-gray-300">En ligne</span>
          </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-1 mt-8">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium transition-all',
              activeTab === tab.id
                ? 'bg-white text-gray-900 shadow-sm'
                : 'text-gray-400 hover:text-white hover:bg-white/10'
            ]"
          >
            <span>{{ tab.icon }}</span>
            <span>{{ tab.label }}</span>
          </button>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8">

      <!-- Dashboard Stats -->
      <div v-if="activeTab === 'stats'">

        <!-- KPIs -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
          <div
            v-for="kpi in kpis"
            :key="kpi.label"
            class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100"
          >
            <div class="flex items-center justify-between mb-3">
              <div :class="`w-10 h-10 ${kpi.bg} rounded-xl flex items-center justify-center text-xl`">
                {{ kpi.icon }}
              </div>
            </div>
            <p class="text-2xl font-bold" :class="kpi.color">{{ kpi.value }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ kpi.label }}</p>
          </div>
        </div>

        <!-- Graphique réservations par mois -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-8">
          <h2 class="font-bold text-gray-900 text-lg mb-6">Réservations par mois</h2>
          <div class="flex items-end gap-3 h-48">
            <div
              v-for="item in store.stats?.bookings_by_month"
              :key="item.month"
              class="flex flex-col items-center gap-2 flex-1"
            >
              <span class="text-sm font-bold text-blue-600">{{ item.count }}</span>
              <div
                class="w-full bg-gradient-to-t from-blue-600 to-blue-400 rounded-t-xl transition-all duration-500"
                :style="`height: ${Math.max(20, (item.count / maxBookings) * 160)}px`"
              />
              <span class="text-xs text-gray-400 font-medium">{{ item.month }}</span>
            </div>
          </div>
        </div>

        <!-- Dernières réservations -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Dernières réservations</h2>
            <button
              @click="activeTab = 'bookings'"
              class="text-blue-600 hover:text-blue-800 text-sm font-medium"
            >
              Voir tout →
            </button>
          </div>
          <table class="w-full text-sm">
            <thead class="bg-gray-50">
            <tr>
              <th class="text-left px-6 py-3 font-medium text-gray-500">Client</th>
              <th class="text-left px-6 py-3 font-medium text-gray-500">Hébergement</th>
              <th class="text-left px-6 py-3 font-medium text-gray-500">Total</th>
              <th class="text-left px-6 py-3 font-medium text-gray-500">Statut</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
            <tr v-for="booking in store.bookings.slice(0, 5)" :key="booking.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">
                <p class="font-medium text-gray-900">{{ booking.user?.name }}</p>
                <p class="text-gray-400 text-xs">{{ booking.user?.email }}</p>
              </td>
              <td class="px-6 py-4 text-gray-600">{{ booking.resource?.name }}</td>
              <td class="px-6 py-4 font-bold text-blue-600">{{ booking.total_price }}€</td>
              <td class="px-6 py-4">
                  <span :class="statusClass(booking.status)" class="text-xs font-semibold px-3 py-1 rounded-full">
                    {{ statusLabel(booking.status) }}
                  </span>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Gestion Ressources -->
      <div v-if="activeTab === 'resources'">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h2 class="text-xl font-bold text-gray-900">Ressources</h2>
            <p class="text-gray-500 text-sm mt-1">{{ store.resources.length }} hébergements</p>
          </div>
          <button
            @click="showResourceModal = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition-colors shadow-sm flex items-center gap-2"
          >
            <span>+</span> Ajouter
          </button>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
              <th class="text-left px-6 py-4 font-semibold text-gray-600">Hébergement</th>
              <th class="text-left px-6 py-4 font-semibold text-gray-600">Catégorie</th>
              <th class="text-left px-6 py-4 font-semibold text-gray-600">Prix</th>
              <th class="text-left px-6 py-4 font-semibold text-gray-600">Capacité</th>
              <th class="text-left px-6 py-4 font-semibold text-gray-600">Statut</th>
              <th class="text-left px-6 py-4 font-semibold text-gray-600">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
            <tr v-for="resource in store.resources" :key="resource.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span>🏨</span>
                  </div>
                  <div>
                    <p class="font-semibold text-gray-900">{{ resource.name }}</p>
                    <p class="text-gray-400 text-xs">📍 {{ resource.location }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                  <span class="bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full">
                    {{ resource.category?.name }}
                  </span>
              </td>
              <td class="px-6 py-4 font-bold text-blue-600">{{ resource.price_per_night }}€</td>
              <td class="px-6 py-4 text-gray-500">{{ resource.capacity }} pers.</td>
              <td class="px-6 py-4">
                  <span
                    :class="resource.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                    class="text-xs font-semibold px-3 py-1 rounded-full"
                  >
                    {{ resource.is_active ? '✓ Actif' : '✗ Inactif' }}
                  </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex gap-2">
                  <button
                    @click="editResource(resource)"
                    class="bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors"
                  >
                    Modifier
                  </button>
                  <button
                    @click="store.toggleResource(resource.id)"
                    :class="resource.is_active
                        ? 'bg-red-50 hover:bg-red-100 text-red-500'
                        : 'bg-green-50 hover:bg-green-100 text-green-600'"
                    class="text-xs font-medium px-3 py-1.5 rounded-lg transition-colors"
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
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-xl font-bold text-gray-900">Réservations</h2>
            <p class="text-gray-500 text-sm mt-1">{{ store.bookings.length }} réservation(s)</p>
          </div>
          <select
            v-model="bookingFilter"
            @change="store.fetchBookings({ status: bookingFilter || undefined })"
            class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
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
              <th class="text-left px-6 py-4 font-semibold text-gray-600">Client</th>
              <th class="text-left px-6 py-4 font-semibold text-gray-600">Hébergement</th>
              <th class="text-left px-6 py-4 font-semibold text-gray-600">Dates</th>
              <th class="text-left px-6 py-4 font-semibold text-gray-600">Total</th>
              <th class="text-left px-6 py-4 font-semibold text-gray-600">Statut</th>
              <th class="text-left px-6 py-4 font-semibold text-gray-600">Action</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
            <tr v-for="booking in store.bookings" :key="booking.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <p class="font-semibold text-gray-900">{{ booking.user?.name }}</p>
                <p class="text-gray-400 text-xs">{{ booking.user?.email }}</p>
              </td>
              <td class="px-6 py-4 text-gray-600">{{ booking.resource?.name }}</td>
              <td class="px-6 py-4 text-gray-500 text-xs">
                <p>{{ booking.check_in_date }}</p>
                <p>→ {{ booking.check_out_date }}</p>
              </td>
              <td class="px-6 py-4 font-bold text-blue-600">{{ booking.total_price }}€</td>
              <td class="px-6 py-4">
                  <span :class="statusClass(booking.status)" class="text-xs font-semibold px-3 py-1 rounded-full">
                    {{ statusLabel(booking.status) }}
                  </span>
              </td>
              <td class="px-6 py-4">
                <select
                  :value="booking.status"
                  @change="store.updateBookingStatus(booking.id, $event.target.value)"
                  class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
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
        <div class="mb-6">
          <h2 class="text-xl font-bold text-gray-900">Utilisateurs</h2>
          <p class="text-gray-500 text-sm mt-1">{{ store.users.length }} utilisateur(s)</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
              <th class="text-left px-6 py-4 font-semibold text-gray-600">Utilisateur</th>
              <th class="text-left px-6 py-4 font-semibold text-gray-600">Téléphone</th>
              <th class="text-left px-6 py-4 font-semibold text-gray-600">Rôle</th>
              <th class="text-left px-6 py-4 font-semibold text-gray-600">Action</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
            <tr v-for="user in store.users" :key="user.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white text-sm font-bold">
                    {{ user.name?.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <p class="font-semibold text-gray-900">{{ user.name }}</p>
                    <p class="text-gray-400 text-xs">{{ user.email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-gray-500">{{ user.phone || '—' }}</td>
              <td class="px-6 py-4">
                  <span :class="roleClass(user.role)" class="text-xs font-semibold px-3 py-1 rounded-full">
                    {{ user.role }}
                  </span>
              </td>
              <td class="px-6 py-4">
                <select
                  :value="user.role"
                  @change="store.updateUserRole(user.id, $event.target.value)"
                  class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
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
  { icon: '📅', label: 'Réservations',  value: store.stats?.total_bookings  || 0, color: 'text-blue-600',   bg: 'bg-blue-50'   },
  { icon: '💰', label: 'Revenus',       value: `${store.stats?.total_revenue || 0}€`, color: 'text-green-600', bg: 'bg-green-50' },
  { icon: '⏳', label: 'Actives',       value: store.stats?.active_bookings  || 0, color: 'text-yellow-600', bg: 'bg-yellow-50' },
  { icon: '🏨', label: 'Ressources',   value: store.stats?.total_resources  || 0, color: 'text-purple-600', bg: 'bg-purple-50' },
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
  return { admin: 'bg-purple-100 text-purple-700', manager: 'bg-blue-100 text-blue-700', client: 'bg-gray-100 text-gray-600' }[role] || ''
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
