<template>
  <div class="min-h-screen bg-gray-100 p-6">
    <!-- Header card -->
    <div class="bg-white rounded-xl shadow-sm p-5 mb-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-0.5">
            <router-link to="/inventory"
              class="text-sm text-green-600 hover:text-green-800 font-light flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Inventory
            </router-link>
            <span class="text-gray-300">/</span>
            <span class="text-sm text-gray-500 font-light">Archived</span>
          </div>
          <h1 class="text-xl font-semibold text-green-800">Archived Inventory</h1>
          <p class="text-sm text-gray-500 font-light mt-0.5">Deleted items that can be restored or permanently removed</p>
        </div>
        <div class="flex items-center gap-2">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input v-model="search" type="text" placeholder="Search archived items..."
              class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200 bg-gray-50 w-56"/>
          </div>
          <span class="px-3 py-1.5 bg-gray-100 text-gray-500 text-sm font-light rounded-lg">
            {{ filteredItems.length }} item{{ filteredItems.length !== 1 ? 's' : '' }}
          </span>
        </div>
      </div>
    </div>

    <!-- Table card -->
    <div class="bg-white rounded-xl shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100">
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Category</th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Quantity</th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Unit</th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Supplier</th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Deleted</th>
              <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="8" class="px-5 py-12 text-center text-gray-400 font-light">
                <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-green-500" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                Loading archived items...
              </td>
            </tr>
            <tr v-else-if="filteredItems.length === 0">
              <td colspan="8" class="px-5 py-16 text-center">
                <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                <p class="text-sm font-medium text-gray-400">No archived items</p>
                <p class="text-xs text-gray-400 font-light mt-0.5">Items you delete from inventory will appear here</p>
              </td>
            </tr>
            <tr v-for="item in filteredItems" :key="item.id"
              class="border-t border-gray-50 hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3.5 font-medium text-gray-700">{{ item.name }}</td>
              <td class="px-5 py-3.5 font-light text-gray-600">{{ item.category }}</td>
              <td class="px-5 py-3.5 font-light text-gray-700">{{ item.current_stock }}</td>
              <td class="px-5 py-3.5 font-light text-gray-500">{{ item.unit || 'piece' }}</td>
              <td class="px-5 py-3.5">
                <span :class="statusBadge(item.status)" class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap">
                  {{ item.status }}
                </span>
              </td>
              <td class="px-5 py-3.5 font-light text-gray-500">{{ item.supplier || '—' }}</td>
              <td class="px-5 py-3.5 font-light text-gray-400 text-xs">{{ formatDate(item.deleted_at) }}</td>
              <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-2">
                  <!-- Restore -->
                  <button @click="restorePrompt(item)"
                    title="Restore"
                    class="flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Restore
                  </button>
                  <!-- Force Delete -->
                  <button @click="forceDeletePrompt(item)"
                    title="Permanently Delete"
                    class="flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-500 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
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

    <!-- ===== RESTORE CONFIRM ===== -->
    <div v-if="showRestoreModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-start gap-3 mb-5">
          <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-gray-800">Restore Item</h3>
            <p class="text-sm text-gray-500 font-light mt-0.5">
              Restore <strong class="text-gray-700">{{ targetItem?.name }}</strong> back to active inventory?
            </p>
          </div>
        </div>
        <div class="flex justify-end gap-3">
          <button @click="showRestoreModal = false"
            class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-light text-gray-600 hover:bg-gray-50">
            Cancel
          </button>
          <button @click="confirmRestore" :disabled="saving"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
            {{ saving ? 'Restoring...' : 'Restore' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ===== FORCE DELETE CONFIRM ===== -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-start gap-3 mb-5">
          <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-gray-800">Permanently Delete</h3>
            <p class="text-sm text-gray-500 font-light mt-0.5">
              Delete <strong class="text-gray-700">{{ targetItem?.name }}</strong> permanently? This cannot be undone.
            </p>
          </div>
        </div>
        <div class="flex justify-end gap-3">
          <button @click="showDeleteModal = false"
            class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-light text-gray-600 hover:bg-gray-50">
            Cancel
          </button>
          <button @click="confirmForceDelete" :disabled="saving"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
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

const items   = ref([])
const loading = ref(false)
const saving  = ref(false)
const search  = ref('')

const showRestoreModal = ref(false)
const showDeleteModal  = ref(false)
const targetItem       = ref(null)

const filteredItems = computed(() => {
  if (!search.value) return items.value
  const q = search.value.toLowerCase()
  return items.value.filter(i =>
    i.name.toLowerCase().includes(q) ||
    i.category.toLowerCase().includes(q) ||
    (i.supplier || '').toLowerCase().includes(q)
  )
})

function statusBadge(s) {
  if (s === 'In Stock')     return 'bg-green-100 text-green-800'
  if (s === 'Low Stock')    return 'bg-yellow-100 text-yellow-700'
  if (s === 'Out of Stock') return 'bg-red-100 text-red-600'
  return 'bg-gray-100 text-gray-600'
}

function formatDate(dt) {
  if (!dt) return '—'
  const d = new Date(dt)
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

async function fetchArchived() {
  loading.value = true
  try {
    const res = await api.get('/inventory/archived')
    items.value = res.data.data || []
  } catch (e) { console.error(e) }
  finally { loading.value = false }
}

function restorePrompt(item)      { targetItem.value = item; showRestoreModal.value = true }
function forceDeletePrompt(item)  { targetItem.value = item; showDeleteModal.value  = true }

async function confirmRestore() {
  if (!targetItem.value) return
  saving.value = true
  try {
    await api.put(`/inventory/${targetItem.value.id}/restore`)
    showRestoreModal.value = false
    targetItem.value = null
    await fetchArchived()
  } catch (e) { console.error(e) }
  finally { saving.value = false }
}

async function confirmForceDelete() {
  if (!targetItem.value) return
  saving.value = true
  try {
    await api.delete(`/inventory/${targetItem.value.id}/force`)
    showDeleteModal.value = false
    targetItem.value = null
    await fetchArchived()
  } catch (e) { console.error(e) }
  finally { saving.value = false }
}

onMounted(fetchArchived)
</script>
