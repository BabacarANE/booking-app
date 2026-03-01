import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useResourcesStore = defineStore('resources', () => {
  const resources   = ref([])
  const resource    = ref(null)
  const categories  = ref([])
  const pagination  = ref(null)
  const loading     = ref(false)

  // ✅ Récupérer les catégories
  async function fetchCategories() {
    const { data } = await api.get('/categories')
    categories.value = data.data
  }

  // ✅ Récupérer les ressources avec filtres
  async function fetchResources(filters = {}) {
    loading.value = true
    try {
      const { data } = await api.get('/resources', { params: filters })
      resources.value = data.data
      pagination.value = data.meta
    } finally {
      loading.value = false
    }
  }

  // ✅ Récupérer une ressource
  async function fetchResource(id) {
    loading.value = true
    try {
      const { data } = await api.get(`/resources/${id}`)
      resource.value = data.data // ✅ data.data au lieu de data
    } finally {
      loading.value = false
    }
  }

  // ✅ Disponibilités
  async function fetchAvailability(id, month, year) {
    const { data } = await api.get(`/resources/${id}/availability`, {
      params: { month, year }
    })
    return data
  }

  return {
    resources,
    resource,
    categories,
    pagination,
    loading,
    fetchCategories,
    fetchResources,
    fetchResource,
    fetchAvailability,
  }
})
