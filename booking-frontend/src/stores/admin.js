import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useAdminStore = defineStore('admin', () => {
  const stats     = ref(null)
  const resources = ref([])
  const bookings  = ref([])
  const users     = ref([])
  const loading   = ref(false)

  async function fetchStats() {
    const { data } = await api.get('/admin/stats')
    stats.value = data
  }

  async function fetchResources() {
    loading.value = true
    try {
      const { data } = await api.get('/admin/resources')
      resources.value = data.data
    } finally {
      loading.value = false
    }
  }

  async function fetchBookings(filters = {}) {
    loading.value = true
    try {
      const { data } = await api.get('/admin/bookings', { params: filters })
      bookings.value = data.data
    } finally {
      loading.value = false
    }
  }

  async function fetchUsers() {
    const { data } = await api.get('/admin/users')
    users.value = data.data
  }

  async function updateBookingStatus(id, status) {
    const { data } = await api.patch(`/admin/bookings/${id}`, { status })
    const index = bookings.value.findIndex(b => b.id === id)
    if (index !== -1) bookings.value[index] = data.data // ✅ data.data
  }

  async function updateUserRole(id, role) {
    const { data } = await api.patch(`/admin/users/${id}/role`, { role })
    const index = users.value.findIndex(u => u.id === id)
    if (index !== -1) users.value[index] = data.data // ✅ data.data
  }

  async function createResource(payload) {
    const { data } = await api.post('/admin/resources', payload)
    resources.value.unshift(data)
    return data
  }

  async function updateResource(id, payload) {
    const { data } = await api.put(`/admin/resources/${id}`, payload)
    const index = resources.value.findIndex(r => r.id === id)
    if (index !== -1) resources.value[index] = data
    return data
  }

  async function toggleResource(id) {
    await api.delete(`/admin/resources/${id}`)
    await fetchResources()
  }

  return {
    stats, resources, bookings, users, loading,
    fetchStats, fetchResources, fetchBookings, fetchUsers,
    updateBookingStatus, updateUserRole,
    createResource, updateResource, toggleResource,
  }
})
