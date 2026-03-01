<template>
  <div>

    <!-- Hero -->
    <section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-700 text-white overflow-hidden">
      <!-- Background decoration -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/5 rounded-full" />
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-white/5 rounded-full" />
      </div>

      <div class="relative max-w-5xl mx-auto px-4 py-24 text-center">
        <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-2 text-sm mb-8">
          <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse" />
          <span>{{ store.resources.length }} hébergements disponibles</span>
        </div>

        <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
          Trouvez votre<br />
          <span class="text-yellow-300">séjour idéal</span>
        </h1>
        <p class="text-xl text-blue-100 mb-12 max-w-2xl mx-auto">
          Réservez parmi nos meilleures chambres, suites et appartements en quelques clics
        </p>

        <!-- Search form -->
        <div class="bg-white rounded-2xl p-3 shadow-2xl text-gray-800 max-w-4xl mx-auto">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 transition-colors">
              <span class="text-gray-400 text-xl">📍</span>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Destination</p>
                <input
                  v-model="search.location"
                  type="text"
                  placeholder="Paris, Lyon..."
                  class="w-full text-sm font-medium bg-transparent focus:outline-none placeholder-gray-300"
                />
              </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 transition-colors border-l border-gray-100">
              <span class="text-gray-400 text-xl">📅</span>
              <div class="flex-1">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Arrivée</p>
                <input
                  v-model="search.check_in"
                  type="date"
                  class="w-full text-sm font-medium bg-transparent focus:outline-none"
                />
              </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 transition-colors border-l border-gray-100">
              <span class="text-gray-400 text-xl">📅</span>
              <div class="flex-1">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Départ</p>
                <input
                  v-model="search.check_out"
                  type="date"
                  class="w-full text-sm font-medium bg-transparent focus:outline-none"
                />
              </div>
            </div>
            <button
              @click="handleSearch"
              class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-xl transition-colors flex items-center justify-center gap-2 shadow-lg"
            >
              <span>🔍</span>
              <span>Rechercher</span>
            </button>
          </div>
        </div>

        <!-- Stats -->
        <div class="flex items-center justify-center gap-8 mt-12 text-sm text-blue-100">
          <div class="flex items-center gap-2">
            <span class="text-2xl font-bold text-white">{{ store.resources.length }}+</span>
            <span>Hébergements</span>
          </div>
          <div class="w-px h-8 bg-white/20" />
          <div class="flex items-center gap-2">
            <span class="text-2xl font-bold text-white">{{ store.categories.length }}</span>
            <span>Catégories</span>
          </div>
          <div class="w-px h-8 bg-white/20" />
          <div class="flex items-center gap-2">
            <span class="text-2xl font-bold text-white">100%</span>
            <span>Sécurisé</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Catégories -->
    <section class="max-w-7xl mx-auto px-4 py-20">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900">Explorez nos catégories</h2>
        <p class="text-gray-500 mt-2">Trouvez l'hébergement qui correspond à vos besoins</p>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <button
          v-for="cat in store.categories"
          :key="cat.id"
          @click="goToCategory(cat.slug)"
          class="group bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg border border-gray-100 text-center transition-all duration-300 hover:-translate-y-2"
        >
          <div class="w-14 h-14 bg-blue-50 group-hover:bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
            <span class="text-2xl">{{ getCategoryEmoji(cat.icon) }}</span>
          </div>
          <h3 class="font-bold text-gray-800 group-hover:text-blue-600 transition-colors">{{ cat.name }}</h3>
          <p class="text-xs text-gray-400 mt-1">{{ cat.description }}</p>
        </button>
      </div>
    </section>

    <!-- Featured ressources -->
    <section class="bg-gray-50 py-20 px-4">
      <div class="max-w-7xl mx-auto">
        <div class="flex items-end justify-between mb-12">
          <div>
            <h2 class="text-3xl font-bold text-gray-900">Nos coups de cœur</h2>
            <p class="text-gray-500 mt-2">Les hébergements les plus appréciés de nos clients</p>
          </div>
          <RouterLink
            to="/resources"
            class="hidden md:flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors"
          >
            Voir tout →
          </RouterLink>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <ResourceCard
            v-for="(resource, index) in store.resources.slice(0, 3)"
            :key="resource.id"
            :resource="resource"
            :featured="index === 0"
          />
        </div>

        <div class="text-center mt-10 md:hidden">
          <RouterLink
            to="/resources"
            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-semibold transition-colors inline-block"
          >
            Voir tous les hébergements →
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- Pourquoi nous choisir -->
    <section class="max-w-7xl mx-auto px-4 py-20">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900">Pourquoi BookingApp ?</h2>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div v-for="feature in features" :key="feature.title" class="text-center">
          <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <span class="text-3xl">{{ feature.icon }}</span>
          </div>
          <h3 class="text-lg font-bold text-gray-900 mb-2">{{ feature.title }}</h3>
          <p class="text-gray-500 text-sm leading-relaxed">{{ feature.description }}</p>
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

const search = ref({ location: '', check_in: '', check_out: '', capacity: '' })

const emojiMap = { bed: '🛏️', star: '⭐', briefcase: '💼', home: '🏠' }

const features = [
  { icon: '🔐', title: 'Paiement sécurisé',    description: 'Vos paiements sont sécurisés via Stripe, leader mondial du paiement en ligne.' },
  { icon: '📅', title: 'Réservation instantanée', description: 'Confirmez votre réservation en quelques clics, disponibilités en temps réel.' },
  { icon: '💬', title: 'Support réactif',       description: 'Notre équipe est disponible pour vous accompagner à chaque étape.' },
]

function getCategoryEmoji(icon) { return emojiMap[icon] || '🏨' }
function handleSearch()         { router.push({ path: '/resources', query: search.value }) }
function goToCategory(slug)     { router.push({ path: '/resources', query: { category: slug } }) }

onMounted(async () => {
  await store.fetchCategories()
  await store.fetchResources()
})
</script>
