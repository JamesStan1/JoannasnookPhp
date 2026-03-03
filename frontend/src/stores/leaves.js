import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../services/api'

export const useLeavesStore = defineStore('leaves', () => {
  const myLeaves = ref([])
  const readIds = ref(new Set(JSON.parse(localStorage.getItem('readLeaveNotifs') || '[]')))

  const unreadRejected = computed(() =>
    myLeaves.value.filter(l => l.status === 'rejected' && !readIds.value.has(Number(l.id)))
  )

  const fetchMyLeaves = async () => {
    try {
      const res = await api.get('/staff/leaves/my')
      myLeaves.value = res.data.data || []
    } catch (e) {
      // Not authenticated yet or network error — silently fail
    }
  }

  const markAllRead = () => {
    myLeaves.value
      .filter(l => l.status === 'rejected')
      .forEach(l => readIds.value.add(Number(l.id)))
    localStorage.setItem('readLeaveNotifs', JSON.stringify([...readIds.value]))
  }

  return { myLeaves, unreadRejected, fetchMyLeaves, markAllRead }
})
