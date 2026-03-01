<template>
  <div>

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-600 to-blue-800 text-white py-20 px-4">
      <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-5xl font-bold mb-4">Trouvez votre hébergement idéal</h1>
        <p class="text-xl text-blue-100 mb-10">
          Réservez parmi nos meilleures chambres, suites et appartements
        </p>

        <!-- Formulaire de recherche rapide -->
        <div class="bg-white rounded-2xl p-6 shadow-xl text-gray-800">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Localisation</label>
              <input
                v-model="search.location"
                type="text"
                placeholder="Paris, Lyon..."
                class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Arrivée</label>
              <input
                v-model="search.check_in"
                type="date"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Départ</label>
              <input
                v-model="search.check_out"
                type="date"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Voyageurs</label>
              <input
                v-model="search.capacity"
                type="number"
                min="1"
                placeholder="2"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>
          <button
            @click="handleSearch"
            class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition-colors"
          >
            🔍 Rechercher
          </button>
        </div>
      </div>
    </section>

    <!-- Catégories -->
    <section class="max-w-7xl mx-auto px-4 py-16">
      <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Nos catégories</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <button
          v-for="cat in store.categories"
          :key="cat.id"
          @click="goToCategory(cat.slug)"
          class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md border border-gray-100 text-center transition-all hover:-translate-y-1"
        >
          <div class="text-4xl mb-3">
            {{ getCategoryEmoji(cat.icon) }}
          </div>
          <h3 class="font-semibold text-gray-800">{{ cat.name }}</h3>
          <p class="text-sm text-gray-500 mt-1">{{ cat.description }}</p>
        </button>
      </div>
    </section>

    <!-- Ressources Featured -->
    <section class="bg-gray-50 py-16 px-4">
      <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Nos meilleurs hébergements</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <ResourceCard
            v-for="resource in store.resources.slice(0, 3)"
            :key="resource.id"
            :resource="resource"
          />
        </div>
        <div class="text-center mt-10">
          <RouterLink
            to="/resources"
            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-semibold transition-colors inline-block"
          >
            Voir tous les hébergements →
          </RouterLink>
        </div>
      </div>
    </section>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useResourcesStore } from '@/stores/resources'
import ResourceCard from '@/components/resource/ResourceCard.vue'

const store  = useResourcesStore()
const router = useRouter()

const search = ref({
  location: '',
  check_in: '',
  check_out: '',
  capacity: '',
})

const emojiMap = {
  bed: '🛏️', star: '⭐', briefcase: '💼', home: '🏠',
}

function getCategoryEmoji(icon) {
  return emojiMap[icon] || '🏨'
}

function handleSearch() {
  router.push({ path: '/resources', query: search.value })
}

function goToCategory(slug) {
  router.push({ path: '/resources', query: { category: slug } })
}

onMounted(async () => {
  await store.fetchCategories()
  await store.fetchResources()
})
</script>
