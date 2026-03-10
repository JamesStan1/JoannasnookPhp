import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)
  const loading = ref(false)
  const error = ref(null)
  const tokenVerified = ref(false)

  // Hydrate user immediately from localStorage on store creation
  const _savedUser = localStorage.getItem('user')
  if (_savedUser) {
    try { user.value = JSON.parse(_savedUser) } catch { /* ignore malformed */ }
  }

  const isAuthenticated = computed(() => !!token.value)

  // ── Role helpers ────────────────────────────────────────────────────────────
  const ROLE_HOME = {
    admin:        '/dashboard',
    manager:      '/dashboard',
    chef:         '/chef/orders',
    housekeeping: '/housekeeping',
    security:     '/rooms',
    front_desk:   '/front-desk',
    guest:        '/',
  }

  const userRole = computed(() => {
    const u = user.value || JSON.parse(localStorage.getItem('user') || '{}')
    return (u?.role || 'guest').toLowerCase()
  })

  const roleHomePath = computed(() => ROLE_HOME[userRole.value] || '/dashboard')

  const isFullAccess = computed(() => ['admin', 'manager'].includes(userRole.value))

  // True only for the Admin role — used to gate admin-only pages & actions
  const isAdmin = computed(() => userRole.value === 'admin')

  const login = async (email, password) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.post('/auth/login', { email, password })
      token.value = response.data.data.token
      user.value = response.data.data.user
      localStorage.setItem('token', token.value)
      localStorage.setItem('user', JSON.stringify(user.value))
      return true
    } catch (err) {
      error.value = err.response?.data?.message || 'Login failed'
      return false
    } finally {
      loading.value = false
    }
  }

  const register = async (name, email, password, phone) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.post('/auth/register', {
        name,
        email,
        password,
        phone,
        role: 'guest',
      })
      return true
    } catch (err) {
      error.value = err.response?.data?.message || 'Registration failed'
      return false
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    try {
      await api.post('/auth/logout')
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    }
  }

  const verifyToken = async () => {
    if (!token.value) {
      tokenVerified.value = true
      return false
    }

    try {
      const response = await api.post('/auth/verify-token')
      user.value = response.data.data
      // Persist updated user data (including avatar) to localStorage
      localStorage.setItem('user', JSON.stringify(user.value))
      tokenVerified.value = true
      return true
    } catch (err) {
      logout()
      tokenVerified.value = true
      return false
    }
  }

  const initializeAuth = () => {
    const savedToken = localStorage.getItem('token')
    const savedUser = localStorage.getItem('user')

    if (savedToken && savedUser) {
      token.value = savedToken
      user.value = JSON.parse(savedUser)
    }
  }

  return {
    user,
    token,
    loading,
    error,
    tokenVerified,
    isAuthenticated,
    userRole,
    roleHomePath,
    isFullAccess,
    isAdmin,
    login,
    register,
    logout,
    verifyToken,
    initializeAuth,
    setUser: (u) => {
      user.value = u
      localStorage.setItem('user', JSON.stringify(u))
    },
  }
})
