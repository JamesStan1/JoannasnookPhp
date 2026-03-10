<template>
  <div class="min-h-screen bg-gray-50">

    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
      <div class="px-4 lg:px-8 py-5 max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
          <h1 class="text-2xl font-light text-gray-900 tracking-tight">Front Desk Dashboard</h1>
          <p class="text-sm text-gray-500 font-light mt-0.5">Live overview · {{ today }}</p>
        </div>
        <button @click="loadAll" :disabled="loading"
          class="flex items-center gap-2 text-sm border border-gray-200 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg transition-colors disabled:opacity-50">
          <svg :class="['w-4 h-4', loading && 'animate-spin']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Refresh
        </button>
      </div>
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading && !data" class="flex items-center justify-center py-32">
      <div class="w-8 h-8 border-2 border-purple-600 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <div v-else class="p-4 lg:p-8 max-w-7xl mx-auto space-y-6">

      <!-- ══════════════════════════════════════════════════════════════════ -->
      <!-- STAT CARDS ROW                                                     -->
      <!-- ══════════════════════════════════════════════════════════════════ -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ── 1. UPCOMING EVENTS ── -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col overflow-hidden">
          <!-- Card Header -->
          <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <div class="flex items-center gap-3">
              <span class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4.5 h-4.5 w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </span>
              <div>
                <p class="text-sm font-semibold text-gray-800">Upcoming Events</p>
                <p class="text-xs text-gray-400 font-light">Today &amp; future</p>
              </div>
            </div>
            <span class="text-xl font-bold text-purple-600">{{ upcomingEvents.length }}</span>
          </div>

          <!-- Event List -->
          <div class="flex-1 overflow-y-auto max-h-72 divide-y divide-gray-50">
            <div v-if="upcomingEvents.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-400">
              <svg class="w-10 h-10 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <p class="text-sm font-light">No upcoming events</p>
            </div>

            <div v-for="event in upcomingEvents" :key="event.id"
              class="px-5 py-3 hover:bg-gray-50 transition-colors cursor-default">
              <div class="flex items-start justify-between gap-2 mb-1">
                <span class="text-sm font-medium text-gray-800 truncate">{{ event.client_name }}</span>
                <span :class="['text-[10px] font-semibold px-2 py-0.5 rounded-full flex-shrink-0', eventStatusClass(event.status)]">
                  {{ event.status }}
                </span>
              </div>
              <div class="text-xs text-gray-500 font-light flex flex-wrap gap-x-3 gap-y-0.5">
                <span class="flex items-center gap-1">
                  <svg class="w-3 h-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                  </svg>
                  {{ event.event_type }}
                </span>
                <span class="flex items-center gap-1">
                  <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  {{ formatDate(event.event_date) }}
                  <span v-if="event.event_time" class="text-gray-400">at {{ formatTime(event.event_time) }}</span>
                </span>
                <span v-if="event.venue" class="flex items-center gap-1">
                  <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  {{ event.venue }}
                </span>
                <span class="flex items-center gap-1">
                  <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  {{ event.number_of_guests }} guests
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- ── 2. FOOD READY ── -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col overflow-hidden">
          <!-- Card Header -->
          <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <div class="flex items-center gap-3">
              <span class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
              </span>
              <div>
                <p class="text-sm font-semibold text-gray-800">Food Ready</p>
                <p class="text-xs text-gray-400 font-light">Marked ready by chef</p>
              </div>
            </div>
            <span class="text-xl font-bold text-green-600">{{ totalReadyItems }}</span>
          </div>

          <!-- Ready Orders List -->
          <div class="flex-1 overflow-y-auto max-h-72 divide-y divide-gray-50">
            <div v-if="readyInvoices.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-400">
              <svg class="w-10 h-10 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
              </svg>
              <p class="text-sm font-light">No orders ready yet</p>
              <p class="text-xs mt-1 text-gray-300">Items will appear here when marked ready by chef</p>
            </div>

            <div v-for="inv in readyInvoices" :key="inv.invoice_id"
              class="px-5 py-3 hover:bg-green-50/30 transition-colors cursor-default">
              <!-- Invoice header -->
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-mono font-bold text-gray-700">{{ inv.invoice_id }}</span>
                <div class="text-right">
                  <p class="text-xs font-medium text-gray-800 truncate max-w-[120px]">{{ inv.customer_name || 'Guest' }}</p>
                  <p v-if="inv.room_number" class="text-[10px] text-amber-600 font-medium">Room {{ inv.room_number }}</p>
                </div>
              </div>
              <!-- Item rows -->
              <div class="space-y-1">
                <div v-for="item in inv.items" :key="item.id" class="flex items-center justify-between gap-2">
                  <div class="flex items-center gap-1.5 min-w-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0"></span>
                    <span class="text-xs text-gray-600 truncate">{{ item.item_name }}</span>
                  </div>
                  <span class="text-xs font-bold text-gray-700 bg-gray-100 px-1.5 py-0.5 rounded-full flex-shrink-0">×{{ item.quantity }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ── 3. ROOM STATUS ── -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col overflow-hidden">
          <!-- Card Header -->
          <div class="px-5 py-4 border-b border-gray-50">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                  </svg>
                </span>
                <div>
                  <p class="text-sm font-semibold text-gray-800">Room Status</p>
                  <p class="text-xs text-gray-400 font-light">{{ rooms.length }} rooms total</p>
                </div>
              </div>
            </div>
            <!-- Legend -->
            <div class="flex flex-wrap gap-x-4 gap-y-1">
              <span class="flex items-center gap-1.5 text-[10px] text-gray-500 font-medium uppercase tracking-wide">
                <span class="w-3 h-3 rounded-sm bg-green-500 inline-block"></span>Available
              </span>
              <span class="flex items-center gap-1.5 text-[10px] text-gray-500 font-medium uppercase tracking-wide">
                <span class="w-3 h-3 rounded-sm bg-red-500 inline-block"></span>Occupied/Reserved
              </span>
              <span class="flex items-center gap-1.5 text-[10px] text-gray-500 font-medium uppercase tracking-wide">
                <span class="w-3 h-3 rounded-sm bg-amber-700 inline-block"></span>Dirty
              </span>
              <span class="flex items-center gap-1.5 text-[10px] text-gray-500 font-medium uppercase tracking-wide">
                <span class="w-3 h-3 rounded-sm bg-yellow-400 inline-block"></span>Maintenance
              </span>
            </div>
          </div>

          <!-- Room Grid -->
          <div class="flex-1 overflow-y-auto max-h-60 p-4">
            <div v-if="rooms.length === 0" class="flex flex-col items-center justify-center py-10 text-gray-400">
              <p class="text-sm font-light">No rooms found</p>
            </div>
            <div v-else class="grid grid-cols-4 sm:grid-cols-5 gap-2">
              <button
                v-for="room in rooms"
                :key="room.id"
                @click="openRoomModal(room)"
                :class="['rounded-lg p-2 flex flex-col items-center justify-center gap-0.5 transition-all hover:scale-105 hover:shadow-md border border-white/20 cursor-pointer', roomColorClass(room)]"
                :title="roomTitle(room)"
              >
                <span class="text-xs font-bold text-white leading-tight">{{ room.room_number }}</span>
                <span class="text-[8px] text-white/80 font-light leading-tight truncate w-full text-center">{{ roomLabel(room) }}</span>
              </button>
            </div>
          </div>
        </div>

      </div>

    </div>

    <!-- ══════════════════════════════════════════════════════════════════ -->
    <!-- ROOM DETAIL MODAL                                                   -->
    <!-- ══════════════════════════════════════════════════════════════════ -->
    <Transition name="modal-fade">
      <div v-if="selectedRoom"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        @click.self="selectedRoom = null">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

          <!-- Modal Header -->
          <div :class="['px-6 py-4 flex items-center justify-between', roomModalHeaderClass(selectedRoom)]">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
              </div>
              <div>
                <p class="text-lg font-bold text-white">Room {{ selectedRoom.room_number }}</p>
                <p class="text-xs text-white/80 font-light capitalize">{{ selectedRoom.type }}</p>
              </div>
            </div>
            <button @click="selectedRoom = null"
              class="w-8 h-8 rounded-lg bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Modal Body -->
          <div class="px-6 py-5 space-y-4">

            <!-- Room Info -->
            <div class="grid grid-cols-2 gap-3">
              <div class="bg-gray-50 rounded-xl p-3">
                <p class="text-[10px] text-gray-400 uppercase tracking-wide font-medium mb-1">Room Type</p>
                <p class="text-sm font-semibold text-gray-800 capitalize">{{ selectedRoom.type || '—' }}</p>
              </div>
              <div class="bg-gray-50 rounded-xl p-3">
                <p class="text-[10px] text-gray-400 uppercase tracking-wide font-medium mb-1">Price / Night</p>
                <p class="text-sm font-semibold text-gray-800">₱{{ formatMoney(selectedRoom.price) }}</p>
              </div>
              <div class="bg-gray-50 rounded-xl p-3">
                <p class="text-[10px] text-gray-400 uppercase tracking-wide font-medium mb-1">Capacity</p>
                <p class="text-sm font-semibold text-gray-800">{{ selectedRoom.capacity || '—' }} guests</p>
              </div>
              <div class="bg-gray-50 rounded-xl p-3">
                <p class="text-[10px] text-gray-400 uppercase tracking-wide font-medium mb-1">Status</p>
                <span :class="['text-xs font-bold px-2 py-0.5 rounded-full', roomStatusBadge(selectedRoom)]">
                  {{ roomDisplayStatus(selectedRoom) }}
                </span>
              </div>
            </div>

            <!-- Reservation Details (for occupied/reserved rooms) -->
            <template v-if="isRoomReserved(selectedRoom)">
              <div class="border-t border-gray-100 pt-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Reservation Details</p>
                <div class="bg-red-50 border border-red-100 rounded-xl p-4 space-y-3">

                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                      <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-800">{{ selectedRoom.guest_name || 'Unknown Guest' }}</p>
                      <p v-if="selectedRoom.guest_email" class="text-xs text-gray-500 font-light">{{ selectedRoom.guest_email }}</p>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                      <p class="text-gray-400 font-medium mb-0.5">Check-in</p>
                      <p class="text-gray-800 font-semibold">{{ formatDate(selectedRoom.check_in_date) }}</p>
                    </div>
                    <div>
                      <p class="text-gray-400 font-medium mb-0.5">Check-out</p>
                      <p class="text-gray-800 font-semibold">{{ formatDate(selectedRoom.check_out_date) }}</p>
                    </div>
                    <div>
                      <p class="text-gray-400 font-medium mb-0.5">Reference</p>
                      <p class="font-mono text-gray-800 font-semibold">{{ selectedRoom.res_reference || '—' }}</p>
                    </div>
                    <div>
                      <p class="text-gray-400 font-medium mb-0.5">Guests</p>
                      <p class="text-gray-800 font-semibold">{{ selectedRoom.res_guests || '—' }}</p>
                    </div>
                  </div>

                  <div v-if="selectedRoom.special_requests" class="bg-white rounded-lg p-2.5 border border-red-100">
                    <p class="text-[10px] text-gray-400 font-medium uppercase mb-1">Special Requests</p>
                    <p class="text-xs text-gray-700 font-light">{{ selectedRoom.special_requests }}</p>
                  </div>

                  <div class="flex items-center justify-between text-xs pt-1 border-t border-red-100">
                    <span class="text-gray-500 font-medium">Reservation Status</span>
                    <span :class="['font-bold px-2 py-0.5 rounded-full', resStatusBadge(selectedRoom.res_status)]">
                      {{ (selectedRoom.res_status || '').replace('_', ' ') }}
                    </span>
                  </div>
                </div>
              </div>
            </template>

          </div>

          <!-- Modal Footer -->
          <div class="px-6 pb-5">
            <button @click="selectedRoom = null"
              class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition-colors">
              Close
            </button>
          </div>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import api from '../services/api'

// ── State ──────────────────────────────────────────────────────────────────────
const loading     = ref(true)
const data        = ref(null)
const selectedRoom = ref(null)

// ── Computed data ──────────────────────────────────────────────────────────────
const upcomingEvents = computed(() => data.value?.upcoming_events ?? [])
const readyOrdersRaw = computed(() => data.value?.ready_orders ?? [])
const rooms          = computed(() => data.value?.rooms ?? [])

// Group ready orders by invoice_id
const readyInvoices = computed(() => {
  const map = {}
  for (const item of readyOrdersRaw.value) {
    if (!map[item.invoice_id]) {
      map[item.invoice_id] = {
        invoice_id:    item.invoice_id,
        customer_name: item.customer_name,
        room_number:   item.room_number,
        items:         [],
      }
    }
    map[item.invoice_id].items.push(item)
  }
  return Object.values(map)
})

const totalReadyItems = computed(() => readyOrdersRaw.value.length)

// ── Helpers ────────────────────────────────────────────────────────────────────
const today = computed(() => new Date().toLocaleDateString('en-US', {
  weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
}))

function formatDate(dateStr) {
  if (!dateStr) return '—'
  const d = new Date(dateStr)
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

function formatTime(timeStr) {
  if (!timeStr) return ''
  const [h, m] = timeStr.split(':')
  const hour = parseInt(h)
  const ampm = hour >= 12 ? 'PM' : 'AM'
  return `${hour % 12 || 12}:${m} ${ampm}`
}

function formatMoney(val) {
  return parseFloat(val || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

// ── Room display logic ─────────────────────────────────────────────────────────
function isRoomReserved(room) {
  if (!room) return false
  return room.status === 'occupied' || (room.res_id && room.res_status)
}

function roomColorClass(room) {
  const s = room.status
  const hasReservation = room.res_id && room.res_status
  if (s === 'occupied' || hasReservation) return 'bg-red-500 hover:bg-red-600'
  if (s === 'dirty')       return 'bg-amber-700 hover:bg-amber-800'
  if (s === 'maintenance') return 'bg-yellow-400 hover:bg-yellow-500'
  return 'bg-green-500 hover:bg-green-600'  // available
}

function roomModalHeaderClass(room) {
  if (!room) return 'bg-gray-700'
  const s = room.status
  const hasReservation = room.res_id && room.res_status
  if (s === 'occupied' || hasReservation) return 'bg-red-500'
  if (s === 'dirty')       return 'bg-amber-700'
  if (s === 'maintenance') return 'bg-yellow-500'
  return 'bg-green-600'
}

function roomStatusBadge(room) {
  const s = room.status
  const hasReservation = room.res_id && room.res_status
  if (s === 'occupied' || hasReservation) return 'bg-red-100 text-red-700'
  if (s === 'dirty')       return 'bg-amber-100 text-amber-800'
  if (s === 'maintenance') return 'bg-yellow-100 text-yellow-800'
  return 'bg-green-100 text-green-700'
}

function roomDisplayStatus(room) {
  if (!room) return ''
  const s = room.status
  const hasReservation = room.res_id && room.res_status
  if (s === 'occupied') return 'Occupied'
  if (hasReservation && room.res_status === 'approved') return 'Reserved'
  if (hasReservation && room.res_status === 'checked_in') return 'Occupied'
  return s.charAt(0).toUpperCase() + s.slice(1)
}

function roomLabel(room) {
  return roomDisplayStatus(room)
}

function roomTitle(room) {
  const s = roomDisplayStatus(room)
  if (room.guest_name && isRoomReserved(room)) return `${s} – ${room.guest_name}`
  return s
}

function resStatusBadge(status) {
  if (status === 'checked_in') return 'bg-red-100 text-red-700'
  if (status === 'approved')   return 'bg-blue-100 text-blue-700'
  return 'bg-gray-100 text-gray-600'
}

function eventStatusClass(status) {
  if (status === 'confirmed') return 'bg-green-100 text-green-700'
  if (status === 'pending')   return 'bg-yellow-100 text-yellow-700'
  return 'bg-gray-100 text-gray-600'
}

// ── Data Loading ───────────────────────────────────────────────────────────────
async function loadAll() {
  loading.value = true
  try {
    const res = await api.get('/front-desk/dashboard')
    data.value = res.data?.data ?? res.data
  } catch (e) {
    console.error('Front desk dashboard load error:', e)
  } finally {
    loading.value = false
  }
}

function openRoomModal(room) {
  selectedRoom.value = room
}

// ── Auto-refresh every 30 seconds ─────────────────────────────────────────────
let refreshTimer = null

onMounted(() => {
  loadAll()
  refreshTimer = setInterval(loadAll, 30000)
})

onBeforeUnmount(() => {
  if (refreshTimer) clearInterval(refreshTimer)
})
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
