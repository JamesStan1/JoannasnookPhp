import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'

export const useReservationStore = defineStore('reservation', () => {
  const reservations = ref([])
  const currentReservation = ref(null)
  const availableRooms = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchAvailableRooms = async (checkInDate, checkOutDate) => {
    loading.value = true
    try {
      const response = await api.post('/reservations/available-rooms', {
        check_in_date: checkInDate,
        check_out_date: checkOutDate,
      })
      availableRooms.value = response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch available rooms'
    } finally {
      loading.value = false
    }
  }

  const createReservation = async (roomId, checkInDate, checkOutDate, numberOfGuests, specialRequests) => {
    loading.value = true
    try {
      const response = await api.post('/reservations', {
        room_id: roomId,
        check_in_date: checkInDate,
        check_out_date: checkOutDate,
        number_of_guests: numberOfGuests,
        special_requests: specialRequests,
      })
      return response.data.data.reservation_id
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create reservation'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchMyReservations = async () => {
    loading.value = true
    try {
      const response = await api.get('/reservations/guest/my-reservations')
      reservations.value = response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch reservations'
    } finally {
      loading.value = false
    }
  }

  const getReservation = async (id) => {
    loading.value = true
    try {
      const response = await api.get(`/reservations/${id}`)
      currentReservation.value = response.data.data
      return response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch reservation'
    } finally {
      loading.value = false
    }
  }

  const cancelReservation = async (id) => {
    loading.value = true
    try {
      await api.put(`/reservations/${id}/cancel`)
      return true
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to cancel reservation'
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    reservations,
    currentReservation,
    availableRooms,
    loading,
    error,
    fetchAvailableRooms,
    createReservation,
    fetchMyReservations,
    getReservation,
    cancelReservation,
  }
})
