<template>
  <div class="min-h-screen bg-gray-50 p-4 lg:p-8">
    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm -mx-4 lg:-mx-8 px-4 lg:px-8 py-5 mb-6">
      <div class="max-w-4xl mx-auto flex items-center gap-3">
        <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <div>
          <h1 class="text-xl lg:text-2xl font-light text-gray-900 tracking-tight">New Reservation</h1>
          <p class="text-sm text-gray-500 font-light mt-0.5">Search available rooms and complete a booking</p>
        </div>
      </div>
    </div>

    <div class="max-w-4xl mx-auto space-y-6">

      <!-- H9 – Error Recovery: error shown at the top with icon and specific hint -->
      <transition name="fade-error">
        <div v-if="error" class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl shadow-sm">
          <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <div>
            <p class="text-sm font-medium">{{ error }}</p>
            <p class="text-xs text-red-600 mt-0.5">Please review the form and try again.</p>
          </div>
          <button @click="error = ''" class="ml-auto text-red-400 hover:text-red-600 transition" aria-label="Dismiss error">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </transition>

      <!-- Step 1: Date Selection -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-medium uppercase tracking-widest text-gray-400 mb-5">Step 1 — Select Dates</h2>
        <form @submit.prevent="searchRooms" class="space-y-5">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5" for="checkInDate">
                Check-in Date <span class="text-red-500">*</span>
              </label>
              <input
                id="checkInDate"
                v-model="form.checkInDate"
                type="date"
                required
                :min="today"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-700/20 focus:border-green-700 transition"
                @change="validateDates"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5" for="checkOutDate">
                Check-out Date <span class="text-red-500">*</span>
              </label>
              <input
                id="checkOutDate"
                v-model="form.checkOutDate"
                type="date"
                required
                :min="form.checkInDate || today"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-700/20 focus:border-green-700 transition"
                :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-200': dateError }"
                @change="validateDates"
              />
              <!-- H5 – Error Prevention: inline date validation message -->
              <p v-if="dateError" class="mt-1 text-xs text-red-600 flex items-center gap-1">
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                </svg>
                {{ dateError }}
              </p>
            </div>
          </div>

          <!-- H3 – User Control: clear form button to recover from mistakes -->
          <div class="flex items-center gap-3 pt-1">
            <button
              type="submit"
              :disabled="loading || !!dateError || !form.checkInDate || !form.checkOutDate"
              class="flex items-center gap-2 bg-green-700 hover:bg-green-800 disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-sm font-medium px-5 py-2.5 rounded-lg transition"
            >
              <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              {{ loading ? 'Searching…' : 'Check Availability' }}
            </button>
            <button
              v-if="showRooms || form.checkInDate || form.checkOutDate"
              type="button"
              @click="resetForm"
              class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 border border-gray-200 px-4 py-2.5 rounded-lg transition hover:bg-gray-50"
              aria-label="Clear form and start over"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M4 9a9 9 0 0115.8-4.8M20 15a9 9 0 01-15.8 4.8"/>
              </svg>
              Clear
            </button>
          </div>
        </form>
      </div>

      <!-- Step 2: Room Selection -->
      <div v-if="showRooms" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-medium uppercase tracking-widest text-gray-400 mb-5">Step 2 — Choose a Room</h2>

        <!-- H1 – Visibility: loading spinner with context -->
        <div v-if="loading" class="flex items-center justify-center py-12">
          <svg class="w-6 h-6 animate-spin text-green-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
          </svg>
          <span class="ml-3 text-sm text-gray-500 font-light">Checking availability…</span>
        </div>

        <div v-else-if="availableRooms.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
          <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
          <p class="text-sm font-medium text-gray-600">No rooms available for the selected dates</p>
          <p class="text-xs text-gray-400 mt-1">Try different dates or check back later.</p>
        </div>

        <div v-else class="grid gap-3">
          <div
            v-for="room in availableRooms"
            :key="room.id"
            @click="selectRoom(room)"
            class="flex items-start justify-between p-4 rounded-xl border-2 cursor-pointer transition-all"
            :class="form.roomId === room.id
              ? 'border-green-600 bg-green-50/60 shadow-sm'
              : 'border-gray-100 hover:border-green-300 hover:bg-gray-50'"
            :aria-selected="form.roomId === room.id"
            role="option"
          >
            <div class="flex items-start gap-3">
              <!-- H1 – Selected indicator -->
              <div class="mt-0.5 w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition"
                :class="form.roomId === room.id ? 'border-green-600 bg-green-600' : 'border-gray-300'">
                <svg v-if="form.roomId === room.id" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-800">Room {{ room.room_number }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ room.type }} · Capacity: {{ room.capacity }} guests</p>
                <p v-if="room.description" class="text-xs text-gray-400 mt-0.5">{{ room.description }}</p>
              </div>
            </div>
            <!-- H2 – Match Real World: use ₱ (Philippine Peso) not $ -->
            <div class="text-right shrink-0 ml-4">
              <p class="text-lg font-semibold text-green-700">₱{{ Number(room.price).toLocaleString() }}</p>
              <p class="text-xs text-gray-400">per night</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Step 3: Guest Details & Submit -->
      <div v-if="form.roomId" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-medium uppercase tracking-widest text-gray-400 mb-5">Step 3 — Guest Details</h2>
        <form @submit.prevent="submitReservation" class="space-y-5">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5" for="numGuests">
                Number of Guests <span class="text-red-500">*</span>
              </label>
              <input
                id="numGuests"
                v-model.number="form.numberOfGuests"
                type="number"
                min="1"
                required
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-700/20 focus:border-green-700 transition"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5" for="downPayment">
                Down Payment (₱)
              </label>
              <input
                id="downPayment"
                v-model.number="form.downPayment"
                type="number"
                step="0.01"
                min="0"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-700/20 focus:border-green-700 transition"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5" for="specialRequests">
              Special Requests <span class="text-xs font-normal text-gray-400">(optional)</span>
            </label>
            <textarea
              id="specialRequests"
              v-model="form.specialRequests"
              rows="3"
              placeholder="e.g. early check-in, extra pillows, dietary requirements…"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-700/20 focus:border-green-700 transition resize-none"
            ></textarea>
          </div>

          <div class="flex items-center gap-3 pt-1">
            <button
              type="submit"
              :disabled="loading"
              class="flex items-center gap-2 bg-green-700 hover:bg-green-800 disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-sm font-medium px-6 py-2.5 rounded-lg transition shadow-sm"
            >
              <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
              </svg>
              {{ loading ? 'Processing…' : 'Complete Reservation' }}
            </button>
            <button
              type="button"
              @click="form.roomId = null"
              class="text-sm text-gray-500 hover:text-gray-700 border border-gray-200 px-4 py-2.5 rounded-lg transition hover:bg-gray-50"
            >
              Back to Rooms
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useReservationStore } from '../stores/reservation'

const router = useRouter()
const reservationStore = useReservationStore()

// H5 – Error Prevention: today's date as the minimum selectable date
const today = new Date().toISOString().split('T')[0]

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
// H5 – inline date validation state
const dateError = ref('')

const availableRooms = computed(() => reservationStore.availableRooms)

// H5 – Error Prevention: validate that checkout is strictly after check-in
const validateDates = () => {
  dateError.value = ''
  if (form.checkInDate && form.checkOutDate) {
    if (form.checkOutDate <= form.checkInDate) {
      dateError.value = 'Check-out date must be after check-in date.'
    }
  }
}

const searchRooms = async () => {
  validateDates()
  if (dateError.value) return
  if (!form.checkInDate || !form.checkOutDate) {
    error.value = 'Please select both check-in and check-out dates.'
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

// H3 – User Control and Freedom: clear the entire form to start over
const resetForm = () => {
  form.checkInDate = ''
  form.checkOutDate = ''
  form.roomId = null
  form.numberOfGuests = 1
  form.downPayment = 0
  form.specialRequests = ''
  showRooms.value = false
  error.value = ''
  dateError.value = ''
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
    error.value = 'Failed to create reservation. Please check the details and try again.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.fade-error-enter-active, .fade-error-leave-active { transition: all 0.25s ease; }
.fade-error-enter-from, .fade-error-leave-to { opacity: 0; transform: translateY(-6px); }
</style>

