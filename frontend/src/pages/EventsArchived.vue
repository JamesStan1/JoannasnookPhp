<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <!-- Page Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-semibold text-green-800">Archived Event Reservations</h1>
      <p class="text-sm text-gray-500 font-light mt-1">Deleted event reservations that can be restored or permanently removed</p>
    </div>

    <!-- Stats row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </div>
        <div>
          <p class="text-xs text-gray-400 font-light">Total Archived</p>
          <p class="text-2xl font-semibold text-red-500">{{ events.length }}</p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-xs text-gray-400 font-light">Pending (archived)</p>
          <p class="text-2xl font-semibold text-yellow-500">{{ pendingCount }}</p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-xs text-gray-400 font-light">Revenue (archived)</p>
          <p class="text-2xl font-semibold text-green-800">&#x20B1;{{ formatMoney(archivedRevenue) }}</p>
        </div>
      </div>
    </div>

    <!-- Search + Refresh -->
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
      <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input v-model="search" @input="fetchEvents" type="text" placeholder="Search by type, client, or venue..."
          class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white"/>
      </div>
      <button @click="fetchEvents"
        class="flex items-center gap-2 border border-blue-700 text-green-800 hover:bg-green-50 px-4 py-2.5 rounded-lg text-sm font-light transition-colors">
        <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Refresh
      </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm">
      <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-base font-semibold text-green-800">Archived Reservations</h2>
        <span class="text-xs text-gray-400 font-light">{{ events.length }} record{{ events.length !== 1 ? 's' : '' }}</span>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50">
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Reference</th>
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Type</th>
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Client</th>
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Date</th>
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Guests</th>
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Set</th>
              <th class="px-4 py-3 text-right font-semibold text-green-800 text-xs uppercase tracking-wide">Total</th>
              <th class="px-4 py-3 text-center font-semibold text-green-800 text-xs uppercase tracking-wide">Last Status</th>
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Archived On</th>
              <th class="px-4 py-3 text-center font-semibold text-green-800 text-xs uppercase tracking-wide">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="10" class="px-4 py-12 text-center text-gray-400 font-light">
                <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-green-500" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                Loading archived events...
              </td>
            </tr>
            <tr v-else-if="events.length === 0">
              <td colspan="10" class="px-4 py-16 text-center">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                <p class="text-sm text-gray-400 font-light">No archived event reservations found.</p>
              </td>
            </tr>
            <tr v-for="ev in events" :key="ev.id" class="border-t border-gray-50 hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 font-light text-gray-500 whitespace-nowrap text-xs">{{ ev.reference_number }}</td>
              <td class="px-4 py-3 font-light text-gray-800">{{ ev.event_type }}</td>
              <td class="px-4 py-3 font-light text-gray-600">
                <div>{{ ev.client_name }}</div>
                <div class="text-xs text-gray-400">{{ ev.client_phone || '' }}</div>
              </td>
              <td class="px-4 py-3 font-light text-gray-600 whitespace-nowrap">
                <div>{{ formatDate(ev.event_date) }}</div>
                <div class="text-xs text-gray-400">{{ formatTime(ev.event_time) }}</div>
              </td>
              <td class="px-4 py-3 font-light text-gray-600 text-center">{{ ev.number_of_guests }}</td>
              <td class="px-4 py-3 font-light text-gray-600">{{ ev.package_set || '\u2014' }}</td>
              <td class="px-4 py-3 font-light text-gray-800 text-right whitespace-nowrap">&#x20B1;{{ formatMoney(ev.total_amount) }}</td>
              <td class="px-4 py-3 text-center">
                <span :class="statusBadge(ev.status)" class="px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap">
                  {{ ev.status.charAt(0).toUpperCase() + ev.status.slice(1) }}
                </span>
              </td>
              <td class="px-4 py-3 font-light text-gray-500 whitespace-nowrap text-xs">{{ formatDateTime(ev.deleted_at) }}</td>
              <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-2">
                  <!-- Restore -->
                  <button @click="restorePrompt(ev)" title="Restore"
                    class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green-50 text-green-800 hover:bg-green-100 text-xs font-medium transition-colors whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Restore
                  </button>
                  <!-- Delete permanently -->
                  <button @click="deletePrompt(ev)" title="Delete permanently"
                    class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-xs font-medium transition-colors whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ===================== RESTORE CONFIRM ===================== -->
    <div v-if="showRestoreModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-gray-800">Restore Event</h3>
            <p class="text-sm text-gray-500 font-light">
              Restore <strong>{{ actionTarget?.event_type }}</strong> for {{ actionTarget?.client_name }}?
              It will be moved back to active events.
            </p>
          </div>
        </div>
        <div class="flex justify-end gap-3">
          <button @click="showRestoreModal = false"
            class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-light text-gray-600 hover:bg-gray-50">Cancel</button>
          <button @click="confirmRestore" :disabled="saving"
            class="px-4 py-2 bg-green-800 hover:bg-green-900 text-white rounded-lg text-sm font-light transition-colors disabled:opacity-50">
            {{ saving ? 'Restoring...' : 'Restore' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ===================== FORCE DELETE CONFIRM ===================== -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-gray-800">Permanently Delete</h3>
            <p class="text-sm text-gray-500 font-light">
              Permanently delete <strong>{{ actionTarget?.event_type }}</strong> for {{ actionTarget?.client_name }}?
              This <span class="text-red-500 font-medium">cannot be undone</span>.
            </p>
          </div>
        </div>
        <!-- Type confirmation input -->
        <div class="mb-4">
          <label class="block text-xs text-gray-500 font-light mb-1.5">
            Type <span class="font-semibold text-gray-700">{{ actionTarget?.event_type }}</span> to confirm:
          </label>
          <input v-model="deleteConfirmText" type="text" placeholder="Type event name to confirm..."
            class="w-full px-3 py-2.5 border border-red-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-red-200"/>
        </div>
        <div class="flex justify-end gap-3">
          <button @click="showDeleteModal = false; deleteConfirmText = ''"
            class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-light text-gray-600 hover:bg-gray-50">Cancel</button>
          <button @click="confirmForceDelete" :disabled="saving || deleteConfirmText !== actionTarget?.event_type"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-light transition-colors disabled:opacity-50">
            {{ saving ? 'Deleting...' : 'Delete Permanently' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'

// ---- State ----
const events   = ref([])
const loading  = ref(false)
const saving   = ref(false)
const search   = ref('')

const showRestoreModal = ref(false)
const showDeleteModal  = ref(false)
const actionTarget     = ref(null)
const deleteConfirmText = ref('')

// ---- Computed stats ----
const pendingCount = computed(() =>
  events.value.filter(e => e.status === 'pending').length
)
const archivedRevenue = computed(() =>
  events.value
    .filter(e => ['confirmed', 'completed'].includes(e.status))
    .reduce((sum, e) => sum + Number(e.total_amount || 0), 0)
)

// ---- Formatters ----
function formatMoney(v) {
  return Number(v || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

function formatDate(d) {
  if (!d) return ''
  const dt = new Date(d + (d.includes('T') ? '' : 'T00:00:00'))
  return dt.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

function formatTime(t) {
  if (!t) return ''
  const [h, m] = t.split(':')
  const hr = parseInt(h)
  const ampm = hr >= 12 ? 'PM' : 'AM'
  return `${hr % 12 || 12}:${m} ${ampm}`
}

function formatDateTime(dt) {
  if (!dt) return ''
  const d = new Date(dt)
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) +
    ' ' + d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
}

function statusBadge(s) {
  const map = {
    pending:   'bg-yellow-100 text-yellow-700',
    confirmed: 'bg-green-100 text-green-700',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-600'
  }
  return map[s] || 'bg-gray-100 text-gray-600'
}

// ---- Fetch ----
async function fetchEvents() {
  loading.value = true
  try {
    const params = search.value ? `?search=${encodeURIComponent(search.value)}` : ''
    const res = await api.get(`/events/archived${params}`)
    events.value = res.data.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

// ---- Actions ----
function restorePrompt(ev) {
  actionTarget.value = ev
  showRestoreModal.value = true
}

function deletePrompt(ev) {
  actionTarget.value = ev
  deleteConfirmText.value = ''
  showDeleteModal.value = true
}

async function confirmRestore() {
  if (!actionTarget.value) return
  saving.value = true
  try {
    await api.put(`/events/${actionTarget.value.id}/restore`, {})
    showRestoreModal.value = false
    actionTarget.value = null
    await fetchEvents()
  } catch (e) {
    console.error(e)
  } finally {
    saving.value = false
  }
}

async function confirmForceDelete() {
  if (!actionTarget.value) return
  if (deleteConfirmText.value !== actionTarget.value.event_type) return
  saving.value = true
  try {
    await api.delete(`/events/${actionTarget.value.id}/force`)
    showDeleteModal.value = false
    deleteConfirmText.value = ''
    actionTarget.value = null
    await fetchEvents()
  } catch (e) {
    console.error(e)
  } finally {
    saving.value = false
  }
}

onMounted(fetchEvents)
</script>
