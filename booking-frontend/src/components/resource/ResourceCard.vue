<template>
  <RouterLink
    :to="`/resources/${resource.id}`"
    class="group bg-white rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden transition-all duration-300 hover:-translate-y-1 block"
    :class="{ 'md:col-span-1 ring-2 ring-blue-200': featured }"
  >
    <!-- Image -->
    <div class="relative h-52 overflow-hidden"
         :class="featured ? 'bg-gradient-to-br from-blue-500 to-indigo-600' : 'bg-gradient-to-br from-blue-100 to-blue-200'"
    >
      <div class="absolute inset-0 flex items-center justify-center">
        <span class="text-7xl opacity-80">🏨</span>
      </div>

      <!-- Badge featured -->
      <div v-if="featured" class="absolute top-3 left-3">
        <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full">
          ⭐ Coup de cœur
        </span>
      </div>

      <!-- Badge catégorie -->
      <div class="absolute top-3 right-3">
        <span class="bg-white/90 backdrop-blur-sm text-gray-700 text-xs font-semibold px-3 py-1 rounded-full">
          {{ resource.category?.name }}
        </span>
      </div>

      <!-- Overlay hover -->
      <div class="absolute inset-0 bg-blue-900/0 group-hover:bg-blue-900/10 transition-colors duration-300" />
    </div>

    <div class="p-5">
      <div class="flex items-start justify-between gap-2">
        <div class="flex-1 min-w-0">
          <h3 class="text-base font-bold text-gray-900 group-hover:text-blue-600 transition-colors truncate">
            {{ resource.name }}
          </h3>
          <p class="text-gray-400 text-sm mt-0.5 flex items-center gap-1">
            <span>📍</span>
            <span>{{ resource.location }}</span>
          </p>
        </div>
        <div class="text-right shrink-0">
          <p class="text-lg font-bold text-blue-600">{{ resource.price_per_night }}€</p>
          <p class="text-xs text-gray-400">/ nuit</p>
        </div>
      </div>

      <!-- Équipements -->
      <div class="flex flex-wrap gap-1.5 mt-4">
        <span
          v-for="amenity in resource.amenities?.slice(0, 3)"
          :key="amenity"
          class="bg-gray-50 text-gray-500 text-xs px-2.5 py-1 rounded-lg border border-gray-100"
        >
          {{ amenity }}
        </span>
        <span
          v-if="resource.amenities?.length > 3"
          class="bg-gray-50 text-gray-400 text-xs px-2.5 py-1 rounded-lg border border-gray-100"
        >
          +{{ resource.amenities.length - 3 }}
        </span>
      </div>

      <!-- Footer -->
      <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-50">
        <div class="flex items-center gap-1.5 text-gray-500 text-sm">
          <span>👥</span>
          <span>{{ resource.capacity }} pers. max</span>
        </div>
        <span class="text-xs font-semibold text-blue-600 group-hover:underline">
          Voir →
        </span>
      </div>
    </div>
  </RouterLink>
</template>

<script setup>
defineProps({
  resource: { type: Object, required: true },
  featured: { type: Boolean, default: false },
})
</script>
