<template>
  <div class="p-4 lg:p-8 max-w-7xl mx-auto">

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-xl lg:text-3xl font-bold">Housekeeping Management</h1>

      <!-- Notification Bell -->
      <div class="relative">
        <button
          @click="toggleNotifPanel"
          class="relative p-2 rounded-full hover:bg-gray-100 transition-colors focus:outline-none"
          title="Cleaning Notifications"
        >
          <!-- Bell Icon -->
          <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          <!-- Badge -->
          <span
            v-if="notifications.length > 0"
            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full min-w-[1.2rem] h-5 flex items-center justify-center px-1 animate-pulse"
          >
            {{ notifications.length }}
          </span>
        </button>

        <!-- Notification Dropdown Panel -->
        <transition name="notif-slide">
          <div
            v-if="showNotifPanel"
            class="absolute right-0 mt-2 w-80 lg:w-96 bg-white border border-gray-200 rounded-xl shadow-2xl z-50"
          >
            <div class="flex items-center justify-between px-4 py-3 border-b bg-amber-50 rounded-t-xl">
              <h3 class="font-bold text-amber-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                Rooms Needing Cleaning
              </h3>
              <span v-if="notifLoading" class="text-xs text-gray-500 animate-spin">⟳</span>
            </div>

            <div class="max-h-72 overflow-y-auto">
              <div v-if="notifLoading && notifications.length === 0" class="p-4 text-center text-gray-500 text-sm">
                Loading...
              </div>
              <div v-else-if="notifications.length === 0" class="p-6 text-center text-green-600 text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto mb-2 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                All rooms are clean!
              </div>

              <div
                v-for="room in notifications"
                :key="room.id"
                class="flex items-center gap-3 px-4 py-3 border-b last:border-0 hover:bg-gray-50 transition-colors"
              >
                <!-- Room Icon -->
                <div :class="room.clean_reason === 'dirty' ? 'bg-orange-100 text-orange-600' : 'bg-green-100 text-amber-600'"
                  class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M9 6h6M9 18h6"/>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-semibold text-sm">Room {{ room.room_number }}</p>
                  <p class="text-xs text-gray-500 capitalize">{{ room.type }}</p>
                  <span
                    :class="room.clean_reason === 'dirty' ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-800'"
                    class="inline-block text-xs font-medium px-2 py-0.5 rounded-full mt-1 capitalize"
                  >
                    {{ room.clean_reason === 'dirty' ? '🧹 Dirty' : '🔑 Checked Out' }}
                  </span>
                </div>
                <button
                  @click="markClean(room)"
                  :disabled="cleaningRoomId === room.id"
                  class="flex-shrink-0 text-xs bg-green-500 hover:bg-green-600 text-white font-semibold px-3 py-1.5 rounded-lg transition-colors disabled:opacity-50"
                >
                  {{ cleaningRoomId === room.id ? '...' : 'Mark Clean' }}
                </button>
              </div>
            </div>

            <div class="px-4 py-2 bg-gray-50 rounded-b-xl text-xs text-gray-400 flex items-center justify-between">
              <span>Auto-refreshes every 30s</span>
              <button @click="loadNotifications" class="text-green-600 hover:underline">Refresh now</button>
            </div>
          </div>
        </transition>
      </div>
    </div>

    <!-- Cleaning Alert Banner (visible even when panel is closed) -->
    <transition name="fade">
      <div
        v-if="notifications.length > 0 && !showNotifPanel"
        class="mb-6 flex items-center gap-3 bg-amber-50 border border-amber-300 text-amber-800 rounded-xl px-4 py-3"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <span class="font-medium text-sm">
          {{ notifications.length }} room{{ notifications.length > 1 ? 's' : '' }} need{{ notifications.length === 1 ? 's' : '' }} cleaning.
        </span>
        <button @click="showNotifPanel = true" class="ml-auto text-sm font-semibold underline underline-offset-2 hover:text-amber-900">
          View &amp; Assign
        </button>
      </div>
    </transition>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- Cleaning Queue Panel -->
      <div class="lg:col-span-2">
        <div class="card">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Cleaning Queue</h2>
            <span
              class="text-xs font-semibold px-2 py-1 rounded-full"
              :class="notifications.length > 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'"
            >
              {{ notifications.length > 0 ? notifications.length + ' pending' : 'All clean' }}
            </span>
          </div>

          <div v-if="notifLoading && notifications.length === 0" class="text-center text-sm text-gray-400 py-4">Loading...</div>

          <div v-else-if="notifications.length === 0" class="text-center py-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-2 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-sm text-green-600 font-medium">All rooms are clean!</p>
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="room in notifications"
              :key="room.id"
              class="flex items-center justify-between gap-2 p-3 rounded-lg border"
              :class="room.clean_reason === 'dirty' ? 'border-orange-200 bg-orange-50' : 'border-green-200 bg-green-50'"
            >
              <div class="flex-1 min-w-0">
                <p class="font-bold text-sm">Room {{ room.room_number }}</p>
                <span
                  class="text-xs font-medium"
                  :class="room.clean_reason === 'dirty' ? 'text-orange-600' : 'text-amber-600'"
                >
                  {{ room.clean_reason === 'dirty' ? '🧹 Dirty' : '🔑 Checked Out' }}
                </span>
              </div>
              <button
                @click="markClean(room)"
                :disabled="cleaningRoomId === room.id"
                class="text-xs bg-green-500 hover:bg-green-600 text-white font-semibold px-3 py-1.5 rounded-lg transition-colors disabled:opacity-50 flex-shrink-0"
              >
                {{ cleaningRoomId === room.id ? 'Saving...' : '✓ Clean' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats & Cleaning Panel -->
      <div class="space-y-6">

        <!-- Quick Stats -->
        <div class="card h-fit">
          <h2 class="text-xl font-bold mb-4">Quick Stats</h2>
          <div class="space-y-4">
            <div class="border-b pb-4">
              <p class="text-gray-600">Pending Tasks</p>
              <p class="text-2xl font-bold">{{ pendingCount }}</p>
            </div>
            <div class="border-b pb-4">
              <p class="text-gray-600">Rooms Need Cleaning</p>
              <p class="text-2xl font-bold" :class="notifications.length > 0 ? 'text-amber-600' : 'text-green-600'">
                {{ notifications.length }}
              </p>
            </div>
            <div>
              <p class="text-gray-600">Total Rooms (in tasks)</p>
              <p class="text-2xl font-bold">{{ totalRooms }}</p>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Click-outside overlay for notification panel -->
    <div v-if="showNotifPanel" @click="showNotifPanel = false" class="fixed inset-0 z-40" />

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import api from '../services/api'
import { useToastStore } from '../stores/toast'

const toast = useToastStore()

// ── Tasks ────────────────────────────────────────────────────────────────────
const tasks       = ref([])
const loading     = ref(true)

const pendingCount = computed(() => tasks.value.filter(t => t.status === 'pending' || t.status === 'assigned').length)
const totalRooms   = computed(() => new Set(tasks.value.map(t => t.room_id)).size)

const getPriorityClass = (priority) => {
  const map = { low: 'badge-info', medium: 'badge-warning', high: 'badge-danger' }
  return map[priority] || 'badge-info'
}

const loadTasks = async () => {
  try {
    const res = await api.get('/housekeeping/my-tasks')
    tasks.value = res.data.data
  } catch (err) {
    console.error('Failed to load tasks:', err)
  } finally {
    loading.value = false
  }
}

const completeTask = async (taskId) => {
  try {
    await api.put(`/housekeeping/tasks/${taskId}/complete`)
    tasks.value = tasks.value.filter(t => t.id !== taskId)
    toast.success('Task marked as complete!')
    await loadNotifications()
  } catch (err) {
    toast.error('Failed to complete task.')
    console.error(err)
  }
}

// ── Notifications ─────────────────────────────────────────────────────────────
const notifications  = ref([])
const notifLoading   = ref(false)
const showNotifPanel = ref(false)
const cleaningRoomId = ref(null)
let   pollTimer      = null
let   prevNotifIds   = []

const toggleNotifPanel = () => {
  showNotifPanel.value = !showNotifPanel.value
}

const loadNotifications = async (showToastOnNew = false) => {
  notifLoading.value = true
  try {
    const res = await api.get('/housekeeping/notifications')
    const incoming = res.data.data || []

    if (showToastOnNew) {
      // Detect new rooms that weren't in previous poll
      const newRooms = incoming.filter(r => !prevNotifIds.includes(r.id))
      newRooms.forEach(r => {
        const reason = r.clean_reason === 'dirty' ? 'is dirty' : 'guest checked out'
        toast.warning(`🧹 Room ${r.room_number} ${reason} — needs cleaning!`, 7000)
      })
    }

    prevNotifIds  = incoming.map(r => r.id)
    notifications.value = incoming
  } catch (err) {
    console.error('Failed to load notifications:', err)
  } finally {
    notifLoading.value = false
  }
}

const markClean = async (room) => {
  cleaningRoomId.value = room.id
  try {
    await api.put(`/housekeeping/rooms/${room.id}/clean`)
    notifications.value = notifications.value.filter(n => n.id !== room.id)
    prevNotifIds = prevNotifIds.filter(id => id !== room.id)
    toast.success(`✅ Room ${room.room_number} marked as clean!`)
    await loadTasks()
  } catch (err) {
    toast.error(`Failed to mark room ${room.room_number} as clean.`)
    console.error(err)
  } finally {
    cleaningRoomId.value = null
  }
}

// ── Lifecycle ─────────────────────────────────────────────────────────────────
onMounted(async () => {
  await loadTasks()
  await loadNotifications(false)          // initial load – no toast
  pollTimer = setInterval(() => loadNotifications(true), 30_000)
})

onUnmounted(() => {
  if (pollTimer) clearInterval(pollTimer)
})
</script>

<style scoped>
/* Notification panel slide animation */
.notif-slide-enter-active,
.notif-slide-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.notif-slide-enter-from,
.notif-slide-leave-to {
  opacity: 0;
  transform: translateY(-8px) scale(0.97);
}

/* Banner fade animation */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
