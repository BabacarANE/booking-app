<template>
  <div class="max-w-7xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Nos hébergements</h1>

    <div class="flex gap-8">

      <!-- Sidebar Filtres -->
      <aside class="w-72 shrink-0">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
          <h2 class="font-bold text-gray-800 text-lg mb-5">Filtres</h2>

          <!-- Localisation -->
          <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Localisation</label>
            <input
              v-model="filters.location"
              type="text"
              placeholder="Paris, Lyon..."
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <!-- Dates -->
          <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Arrivée</label>
            <input
              v-model="filters.check_in"
              type="date"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Départ</label>
            <input
              v-model="filters.check_out"
              type="date"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <!-- Capacité -->
          <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Voyageurs : {{ filters.capacity }}
            </label>
            <input
              v-model="filters.capacity"
              type="range"
              min="1"
              max="20"
              class="w-full accent-blue-600"
            />
          </div>

          <!-- Prix max -->
          <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Prix max : {{ filters.price_max }}€ / nuit
            </label>
            <input
              v-model="filters.price_max"
              type="range"
              min="50"
              max="1000"
              step="10"
              class="w-full accent-blue-600"
            />
          </div>

          <!-- Catégorie -->
          <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
            <div class="space-y-2">
              <label
                v-for="cat in store.categories"
                :key="cat.id"
                class="flex items-center gap-2 cursor-pointer"
              >
                <input
                  type="radio"
                  :value="cat.slug"
                  v-model="filters.category"
                  class="accent-blue-600"
                />
                <span class="text-sm text-gray-700">{{ cat.name }}</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  type="radio"
                  value=""
                  v-model="filters.category"
                  class="accent-blue-600"
                />
                <span class="text-sm text-gray-700">Toutes</span>
              </label>
            </div>
          </div>

          <!-- Boutons -->
          <button
            @click="applyFilters"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-xl transition-colors"
          >
            Appliquer
          </button>
          <button
            @click="resetFilters"
            class="w-full mt-2 text-gray-500 hover:text-gray-700 text-sm py-2"
          >
            Réinitialiser
          </button>
        </div>
      </aside>

      <!-- Résultats -->
      <div class="flex-1">

        <!-- Tri -->
        <div class="flex items-center justify-between mb-6">
          <p class="text-gray-500 text-sm">
            {{ store.pagination?.total || 0 }} hébergement(s) trouvé(s)
          </p>
          <select
            v-model="sortBy"
            @change="applyFilters()"
            class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="price_per_night_asc">Prix croissant</option>
            <option value="price_per_night_desc">Prix décroissant</option>
            <option value="capacity_asc">Capacité</option>
          </select>
        </div>

        <!-- Loading -->
        <div v-if="store.loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="n in 6"
            :key="n"
            class="bg-gray-100 rounded-2xl h-72 animate-pulse"
          />
        </div>

        <!-- Résultats -->
        <div v-else-if="store.resources.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <ResourceCard
            v-for="resource in store.resources"
            :key="resource.id"
            :resource="resource"
          />
        </div>

        <!-- Aucun résultat -->
        <div v-else class="text-center py-20 text-gray-400">
          <p class="text-5xl mb-4">🔍</p>
          <p class="text-xl font-medium">Aucun hébergement trouvé</p>
          <p class="text-sm mt-2">Essayez de modifier vos filtres</p>
        </div>

        <!-- Pagination -->
        <div
          v-if="store.pagination && store.pagination.last_page > 1"
          class="flex justify-center gap-2 mt-10"
        >
          <button
            v-for="page in store.pagination.last_page"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
              page === store.pagination.current_page
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'
            ]"
          >
            {{ page }}
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useResourcesStore } from '@/stores/resources'
import ResourceCard from '@/components/resource/ResourceCard.vue'

const store = useResourcesStore()
const route = useRoute()

const filters = ref({
  location:  '',
  check_in:  '',
  check_out: '',
  capacity:  1,
  price_max: 1000,
  category:  '',
})

const sortBy = ref('price_per_night_asc')

function initFromQuery() {
  if (route.query.location) filters.value.location = route.query.location
  if (route.query.check_in)  filters.value.check_in  = route.query.check_in
  if (route.query.check_out) filters.value.check_out = route.query.check_out
  if (route.query.capacity)  filters.value.capacity  = route.query.capacity
  if (route.query.category)  filters.value.category  = route.query.category
}

function applyFilters(page = 1) {
  // ✅ Parsing simplifié du tri
  const sortMap = {
    'price_per_night_asc':  { sort_by: 'price_per_night', sort_dir: 'asc' },
    'price_per_night_desc': { sort_by: 'price_per_night', sort_dir: 'desc' },
    'capacity_asc':         { sort_by: 'capacity',        sort_dir: 'asc' },
  }

  const sort = sortMap[sortBy.value] || { sort_by: 'price_per_night', sort_dir: 'asc' }

  store.fetchResources({
    ...filters.value,
    ...sort,
    page,
  })
}

function resetFilters() {
  filters.value = {
    location: '', check_in: '', check_out: '',
    capacity: 1, price_max: 1000, category: '',
  }
  sortBy.value = 'price_per_night_asc'
  applyFilters()
}

function goToPage(page) {
  applyFilters(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(async () => {
  await store.fetchCategories()
  initFromQuery()
  applyFilters()
})
</script>
