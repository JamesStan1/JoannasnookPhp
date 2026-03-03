<template>
  <div class="p-6 bg-gray-50 min-h-screen font-light">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-gray-800">Archived Rooms</h1>
        <p class="text-gray-500 text-sm mt-1">Restore archived rooms or permanently remove them</p>
      </div>
      <router-link to="/rooms/edit"
        class="flex items-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 bg-white hover:bg-gray-50 transition w-fit shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Edit Room
      </router-link>
    </div>

    <!-- Info Banner -->
    <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-6 flex items-center gap-3">
      <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
      <p class="text-sm text-amber-700">Archived rooms are hidden from reservations and room status. Restore to make them available again, or permanently delete to remove all data.</p>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
        <div class="p-2.5 bg-gray-100 rounded-xl">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Total Archived</p>
          <p class="text-2xl font-semibold text-gray-700">{{ archived.length }}</p>
        </div>
      </div>
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
        <div class="p-2.5 bg-green-50 rounded-xl">
          <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Restorable</p>
          <p class="text-2xl font-semibold text-green-600">{{ archived.length }}</p>
        </div>
      </div>
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
        <div class="p-2.5 bg-green-50 rounded-xl">
          <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div>
          <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Last Archived</p>
          <p class="text-sm font-medium text-amber-600">{{ lastArchivedDate }}</p>
        </div>
      </div>
    </div>

    <!-- Search -->
    <div class="relative mb-5 max-w-sm">
      <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      <input v-model="search" type="text" placeholder="Search by room number or type..."
        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-600 bg-white" />
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-20 text-gray-400">
      <svg class="w-8 h-8 mx-auto mb-3 animate-spin text-gray-300" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
      Loading archived rooms...
    </div>

    <!-- Table -->
    <div v-else class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-700 text-white">
              <th class="px-4 py-3 text-left font-medium text-xs">#</th>
              <th class="px-4 py-3 text-left font-medium text-xs">Room No.</th>
              <th class="px-4 py-3 text-left font-medium text-xs">Type</th>
              <th class="px-4 py-3 text-center font-medium text-xs">Capacity</th>
              <th class="px-4 py-3 text-right font-medium text-xs">Price/Night</th>
              <th class="px-4 py-3 text-left font-medium text-xs">Description</th>
              <th class="px-4 py-3 text-left font-medium text-xs">Last Status</th>
              <th class="px-4 py-3 text-left font-medium text-xs">Archived On</th>
              <th class="px-4 py-3 text-center font-medium text-xs">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filtered.length === 0">
              <td colspan="9" class="px-4 py-16 text-center">
                <div class="flex flex-col items-center text-gray-300">
                  <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                  <p class="text-sm">{{ search ? 'No rooms match your search' : 'No archived rooms' }}</p>
                </div>
              </td>
            </tr>
            <tr v-for="(room, idx) in filtered" :key="room.id"
              class="border-t border-gray-50 hover:bg-gray-50/60 transition-colors">
              <td class="px-4 py-3 text-gray-400 text-xs">{{ idx + 1 }}</td>
              <td class="px-4 py-3 font-semibold text-gray-600">{{ room.room_number }}</td>
              <td class="px-4 py-3 capitalize text-gray-500">{{ room.type }}</td>
              <td class="px-4 py-3 text-center text-gray-500">{{ room.capacity }}</td>
              <td class="px-4 py-3 text-right text-gray-500 font-medium">&#x20B1;{{ formatMoney(room.price) }}</td>
              <td class="px-4 py-3 text-gray-400 text-xs max-w-xs truncate">{{ room.description || '&mdash;' }}</td>
              <td class="px-4 py-3">
                <span :class="statusBadge(room.status)" class="px-2.5 py-1 rounded-full text-xs font-medium capitalize">
                  {{ room.status || 'unknown' }}
                </span>
              </td>
              <td class="px-4 py-3 text-gray-400 text-xs whitespace-nowrap">{{ formatDate(room.deleted_at) }}</td>
              <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-2">
                  <button @click="restoreRoom(room)"
                    class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg transition flex items-center gap-1.5 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Restore
                  </button>
                  <button @click="confirmForceDelete(room)"
                    class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition flex items-center gap-1.5 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete Forever
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="px-4 py-3 border-t border-gray-50 text-xs text-gray-400 flex items-center justify-between">
        <span>{{ filtered.length }} of {{ archived.length }} archived room{{ archived.length !== 1 ? 's' : '' }}</span>
        <span v-if="search" class="text-green-600 cursor-pointer hover:underline" @click="search = ''">Clear search</span>
      </div>
    </div>

    <!-- ====== RESTORE CONFIRM ====== -->
    <div v-if="restoreTarget" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
        <p class="text-sm font-semibold text-gray-800 mb-1">Restore Room {{ restoreTarget.room_number }}?</p>
        <p class="text-xs text-gray-500 mb-5">This room will be made available again and visible in reservations.</p>
        <div class="flex gap-3">
          <button @click="restoreTarget = null" class="flex-1 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
          <button @click="doRestore" :disabled="actionLoading"
            class="flex-1 py-2 text-sm bg-green-600 text-white rounded-xl hover:bg-green-700 transition disabled:opacity-60">
            {{ actionLoading ? 'Restoring...' : 'Restore' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ====== FORCE DELETE CONFIRM ====== -->
    <div v-if="deleteTarget" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        </div>
        <p class="text-sm font-semibold text-gray-800 mb-1">Permanently Delete Room {{ deleteTarget.room_number }}?</p>
        <p class="text-xs text-gray-500 mb-1">Type the room number to confirm:</p>
        <input v-model="deleteConfirmInput" :placeholder="deleteTarget.room_number"
          class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-red-400 mb-4" />
        <p class="text-xs text-red-500 mb-5">This action <strong>cannot be undone</strong>. All data for this room will be permanently lost.</p>
        <div class="flex gap-3">
          <button @click="deleteTarget = null; deleteConfirmInput = ''" class="flex-1 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
          <button @click="doForceDelete" :disabled="deleteConfirmInput !== deleteTarget.room_number || actionLoading"
            class="flex-1 py-2 text-sm bg-red-600 text-white rounded-xl hover:bg-red-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
            {{ actionLoading ? 'Deleting...' : 'Delete Forever' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'
import { useToastStore } from '../stores/toast'

const toast = useToastStore()

const archived = ref([])
const loading = ref(false)
const search = ref('')
const restoreTarget = ref(null)
const deleteTarget = ref(null)
const deleteConfirmInput = ref('')
const actionLoading = ref(false)

const filtered = computed(() => {
  if (!search.value.trim()) return archived.value
  const s = search.value.toLowerCase()
  return archived.value.filter(r =>
    r.room_number?.toLowerCase().includes(s) ||
    r.type?.toLowerCase().includes(s) ||
    r.description?.toLowerCase().includes(s)
  )
})

const lastArchivedDate = computed(() => {
  if (!archived.value.length) return 'None'
  const sorted = [...archived.value].sort((a, b) => new Date(b.deleted_at) - new Date(a.deleted_at))
  return formatDate(sorted[0].deleted_at)
})

async function loadArchived() {
  loading.value = true
  try {
    const res = await api.get('/rooms/archived')
    archived.value = res.data?.data ?? res.data ?? []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

function restoreRoom(room) { restoreTarget.value = room }
async function doRestore() {
  actionLoading.value = true
  try {
    await api.put(`/rooms/${restoreTarget.value.id}/restore`)
    archived.value = archived.value.filter(r => r.id !== restoreTarget.value.id)
    restoreTarget.value = null
    toast.success('Room restored successfully')
  } catch (e) {
    toast.error(e.response?.data?.message ?? 'Error restoring room')
  } finally { actionLoading.value = false }
}

function confirmForceDelete(room) {
  deleteTarget.value = room
  deleteConfirmInput.value = ''
}
async function doForceDelete() {
  actionLoading.value = true
  try {
    await api.delete(`/rooms/${deleteTarget.value.id}`)
    archived.value = archived.value.filter(r => r.id !== deleteTarget.value.id)
    deleteTarget.value = null
    deleteConfirmInput.value = ''
    toast.success('Room permanently deleted')
  } catch (e) {
    toast.error(e.response?.data?.message ?? 'Error deleting room')
  } finally { actionLoading.value = false }
}

function formatMoney(v) {
  return Number(v || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}
function formatDate(d) {
  if (!d) return '\u2014'
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
function statusBadge(status) {
  const map = {
    available: 'bg-green-100 text-green-700',
    occupied: 'bg-green-100 text-green-800',
    maintenance: 'bg-yellow-100 text-yellow-700',
    dirty: 'bg-red-100 text-red-600',
  }
  return map[status] ?? 'bg-gray-100 text-gray-500'
}

onMounted(loadArchived)
</script>
