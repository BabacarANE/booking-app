import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const user  = ref(null)
  const token = ref(localStorage.getItem('token') || null)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin         = computed(() => user.value?.role === 'admin')
  const isManager       = computed(() => user.value?.role === 'manager')

  // ✅ Connexion
  async function login(credentials) {
    const { data } = await api.post('/login', credentials)
    token.value = data.token
    user.value  = data.user
    localStorage.setItem('token', data.token)
    return data
  }

  // ✅ Inscription
  async function register(userData) {
    const { data } = await api.post('/register', userData)
    token.value = data.token
    user.value  = data.user
    localStorage.setItem('token', data.token)
    return data
  }

  // ✅ Déconnexion
  async function logout() {
    await api.post('/logout')
    token.value = null
    user.value  = null
    localStorage.removeItem('token')
  }

  // ✅ Récupérer l'utilisateur connecté
  async function fetchUser() {
    if (!token.value) return
    try {
      const { data } = await api.get('/user')
      user.value = data
    } catch {
      token.value = null
      localStorage.removeItem('token')
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    isAdmin,
    isManager,
    login,
    register,
    logout,
    fetchUser,
  }
})
