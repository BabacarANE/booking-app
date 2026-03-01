<template>
  <RouterLink
    :to="`/resources/${resource.id}`"
    class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 overflow-hidden transition-all hover:-translate-y-1 block"
  >
    <!-- Image placeholder -->
    <div class="bg-gradient-to-br from-blue-100 to-blue-200 h-48 flex items-center justify-center">
      <span class="text-6xl">🏨</span>
    </div>

    <div class="p-5">
      <!-- Catégorie -->
      <span class="text-xs font-semibold text-blue-600 uppercase tracking-wide">
        {{ resource.category?.name }}
      </span>

      <!-- Nom -->
      <h3 class="text-lg font-bold text-gray-900 mt-1">{{ resource.name }}</h3>

      <!-- Localisation -->
      <p class="text-gray-500 text-sm mt-1">📍 {{ resource.location }}</p>

      <!-- Équipements -->
      <div class="flex flex-wrap gap-1 mt-3">
        <span
          v-for="amenity in resource.amenities?.slice(0, 3)"
          :key="amenity"
          class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full"
        >
          {{ amenity }}
        </span>
      </div>

      <!-- Prix et capacité -->
      <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
        <div>
          <span class="text-2xl font-bold text-blue-600">{{ resource.price_per_night }}€</span>
          <span class="text-gray-400 text-sm"> / nuit</span>
        </div>
        <span class="text-gray-500 text-sm">👥 {{ resource.capacity }} pers.</span>
      </div>
    </div>
  </RouterLink>
</template>

<script setup>
defineProps({
  resource: {
    type: Object,
    required: true,
  },
})
</script>
