import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../services/api'
import { useAuthStore } from './auth'

export const usePendingReservationsStore = defineStore('pendingReservations', () => {
  const pendingList = ref([])
  const loading = ref(false)
  const bellOpen = ref(false)
  let pollTimer = null

  const pendingCount = computed(() => pendingList.value.length)

  // Only admins, managers and front_desk can approve reservations
  const canApprove = () => {
    const authStore = useAuthStore()
    return ['admin', 'manager', 'front_desk'].includes(authStore.userRole)
  }

  const fetchPending = async () => {
    if (!canApprove()) return
    loading.value = true
    try {
      const res = await api.get('/pending-reservations', { params: { status: 'pending' } })
      pendingList.value = res.data.data || []
    } catch {
      // silently fail – user may not have permission or network issue
    } finally {
      loading.value = false
    }
  }

  const startPolling = (intervalMs = 30000) => {
    if (!canApprove()) return
    fetchPending()
    if (pollTimer) clearInterval(pollTimer)
    pollTimer = setInterval(fetchPending, intervalMs)
  }

  const stopPolling = () => {
    if (pollTimer) {
      clearInterval(pollTimer)
      pollTimer = null
    }
  }

  const toggleBell = () => {
    bellOpen.value = !bellOpen.value
  }

  const closeBell = () => {
    bellOpen.value = false
  }

  return {
    pendingList,
    loading,
    bellOpen,
    pendingCount,
    fetchPending,
    startPolling,
    stopPolling,
    toggleBell,
    closeBell,
    canApprove,
  }
})
