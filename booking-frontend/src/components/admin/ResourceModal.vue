<template>
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
    <div class="bg-white rounded-2xl w-full max-w-lg p-8 shadow-xl">

      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">
          {{ resource ? 'Modifier la ressource' : 'Nouvelle ressource' }}
        </h2>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-4">

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
          <input v-model="form.name" type="text" required
                 class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
          <select v-model="form.category_id" required
                  class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prix / nuit (€)</label>
            <input v-model="form.price_per_night" type="number" min="0" step="0.01" required
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Capacité</label>
            <input v-model="form.capacity" type="number" min="1" required
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Localisation</label>
          <input v-model="form.location" type="text"
                 class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
          <textarea v-model="form.description" rows="3"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Équipements (séparés par des virgules)
          </label>
          <input v-model="amenitiesInput" type="text" placeholder="WiFi, TV, Climatisation"
                 class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div class="flex items-center gap-2">
          <input v-model="form.is_active" type="checkbox" id="is_active" class="accent-blue-600" />
          <label for="is_active" class="text-sm text-gray-700">Ressource active</label>
        </div>

        <div v-if="resource">
          <label class="block text-sm font-medium text-gray-700 mb-2">Images</label>
          <ImageUploader
            :resource-id="resource.id"
            @updated="$emit('saved')"
          />
        </div>
        <p v-else class="text-xs text-gray-400 bg-gray-50 rounded-xl p-3 text-center">
          💡 Sauvegardez d'abord la ressource pour ajouter des images
        </p>

        <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>

        <div class="flex gap-3 pt-2">
          <button type="button" @click="$emit('close')"
                  class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors">
            Annuler
          </button>
          <button type="submit" :disabled="loading"
                  class="flex-1 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white py-2.5 rounded-xl text-sm font-medium transition-colors">
            {{ loading ? 'Enregistrement...' : 'Enregistrer' }}
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAdminStore } from '@/stores/admin'
import { useResourcesStore } from '@/stores/resources'
import ImageUploader from '@/components/admin/ImageUploader.vue'

const props = defineProps({
  resource: { type: Object, default: null },
})
const emit = defineEmits(['close', 'saved'])

const adminStore     = useAdminStore()
const resourcesStore = useResourcesStore()
const loading        = ref(false)
const error          = ref(null)
const categories     = ref([])

const form = ref({
  name:            props.resource?.name || '',
  category_id:     props.resource?.category?.id || '',
  price_per_night: props.resource?.price_per_night || '',
  capacity:        props.resource?.capacity || 1,
  location:        props.resource?.location || '',
  description:     props.resource?.description || '',
  is_active:       props.resource?.is_active ?? true,
})

const amenitiesInput = ref(props.resource?.amenities?.join(', ') || '')

async function handleSubmit() {
  loading.value = true
  error.value   = null
  try {
    const payload = {
      ...form.value,
      amenities: amenitiesInput.value.split(',').map(a => a.trim()).filter(Boolean),
    }
    if (props.resource) {
      await adminStore.updateResource(props.resource.id, payload)
    } else {
      await adminStore.createResource(payload)
    }
    emit('saved')
  } catch (e) {
    error.value = e.response?.data?.message || 'Une erreur est survenue.'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await resourcesStore.fetchCategories()
  categories.value = resourcesStore.categories
})
</script>
