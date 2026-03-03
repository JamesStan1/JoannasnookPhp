<template>
  <div class="p-4 lg:p-8 max-w-4xl mx-auto">
    <h1 class="text-xl lg:text-3xl font-bold mb-6">New Reservation</h1>

    <div class="card">
      <form @submit.prevent="submitReservation" class="space-y-6">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-2">Check-in Date</label>
            <input v-model="form.checkInDate" type="date" required class="input-field" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Check-out Date</label>
            <input v-model="form.checkOutDate" type="date" required class="input-field" />
          </div>
        </div>

        <button v-if="!showRooms" type="button" @click="searchRooms" class="btn-primary">
          Check Availability
        </button>

        <div v-if="showRooms">
          <h2 class="text-xl font-bold mb-4">Available Rooms</h2>

          <div v-if="loading" class="text-center py-8">Loading...</div>

          <div v-else-if="availableRooms.length === 0" class="card bg-yellow-50">
            <p>No rooms available for the selected dates</p>
          </div>

          <div v-else class="grid gap-4">
            <div v-for="room in availableRooms" :key="room.id" @click="selectRoom(room)" class="card cursor-pointer hover:shadow-lg transition" :class="{ 'border-2 border-blue-600': form.roomId === room.id }">
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="text-lg font-bold">Room {{ room.room_number }}</h3>
                  <p class="text-gray-600">{{ room.type }} - Capacity: {{ room.capacity }} guests</p>
                  <p class="text-gray-600">{{ room.description }}</p>
                </div>
                <div class="text-right">
                  <div class="text-2xl font-bold text-green-600">${{ room.price }}</div>
                  <p class="text-sm text-gray-600">per night</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="form.roomId">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-2">Number of Guests</label>
              <input v-model="form.numberOfGuests" type="number" min="1" required class="input-field" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">Down Payment ($)</label>
              <input v-model="form.downPayment" type="number" step="0.01" min="0" class="input-field" />
            </div>
          </div>

          <div class="mt-4">
            <label class="block text-sm font-medium mb-2">Special Requests</label>
            <textarea v-model="form.specialRequests" rows="4" class="input-field"></textarea>
          </div>

          <button type="submit" :disabled="loading" class="btn-primary w-full mt-6">
            {{ loading ? 'Processing...' : 'Complete Reservation' }}
          </button>
        </div>
      </form>
    </div>

    <div v-if="error" class="mt-4 p-4 bg-red-100 text-red-800 rounded">{{ error }}</div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useReservationStore } from '../stores/reservation'

const router = useRouter()
const reservationStore = useReservationStore()

const form = reactive({
  checkInDate: '',
  checkOutDate: '',
  roomId: null,
  numberOfGuests: 1,
  downPayment: 0,
  specialRequests: '',
})

const showRooms = ref(false)
const loading = ref(false)
const error = ref('')
const availableRooms = computed(() => reservationStore.availableRooms)

const searchRooms = async () => {
  if (!form.checkInDate || !form.checkOutDate) {
    error.value = 'Please select both check-in and check-out dates'
    return
  }

  loading.value = true
  error.value = ''
  await reservationStore.fetchAvailableRooms(form.checkInDate, form.checkOutDate)
  showRooms.value = true
  loading.value = false
}

const selectRoom = (room) => {
  form.roomId = room.id
}

const submitReservation = async () => {
  loading.value = true
  error.value = ''

  try {
    const reservationId = await reservationStore.createReservation(
      form.roomId,
      form.checkInDate,
      form.checkOutDate,
      form.numberOfGuests,
      form.specialRequests
    )
    router.push(`/reservations/${reservationId}`)
  } catch (err) {
    error.value = 'Failed to create reservation'
  } finally {
    loading.value = false
  }
}
</script>
