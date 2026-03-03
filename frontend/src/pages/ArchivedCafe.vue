<template>
  <div class="min-h-screen bg-gray-100 p-6">

    <!-- Header card -->
    <div class="bg-white rounded-xl shadow-sm p-5 mb-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-0.5">
            <router-link to="/restaurant"
              class="text-sm text-green-600 hover:text-green-800 font-light flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Café Menu
            </router-link>
            <span class="text-gray-300">/</span>
            <span class="text-sm text-gray-500 font-light">Archived</span>
          </div>
          <h1 class="text-xl font-semibold text-green-800">Archived Café Menu</h1>
          <p class="text-sm text-gray-500 font-light mt-0.5">Removed dishes that can be restored or permanently deleted</p>
        </div>

        <div class="flex items-center gap-2">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input v-model="search" type="text" placeholder="Search archived dishes..."
              class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200 bg-gray-50 w-60"/>
          </div>
          <span class="px-3 py-1.5 bg-gray-100 text-gray-500 text-sm font-light rounded-lg whitespace-nowrap">
            {{ filtered.length }} dish{{ filtered.length !== 1 ? 'es' : '' }}
          </span>
        </div>
      </div>
    </div>

    <!-- Table card -->
    <div class="bg-white rounded-xl shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">#</th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Dish Name</th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Category</th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Price</th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Archived On</th>
              <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
            </tr>
          </thead>
          <tbody>
            <!-- Loading -->
            <tr v-if="loading">
              <td colspan="7" class="px-5 py-12 text-center text-gray-400 font-light">
                <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-green-500" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                Loading archived dishes...
              </td>
            </tr>

            <!-- Empty -->
            <tr v-else-if="filtered.length === 0">
              <td colspan="7" class="px-5 py-16 text-center">
                <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                <p class="text-sm font-medium text-gray-400">No archived dishes</p>
                <p class="text-xs text-gray-400 font-light mt-0.5">Dishes you remove from the café menu will appear here</p>
              </td>
            </tr>

            <!-- Rows -->
            <tr v-for="(item, idx) in filtered" :key="item.id"
              class="border-t border-gray-50 hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3.5 text-gray-400 font-light">{{ idx + 1 }}</td>
              <td class="px-5 py-3.5 font-medium text-gray-700">{{ item.name }}</td>
              <td class="px-5 py-3.5">
                <span v-if="item.category"
                  class="px-2 py-0.5 bg-green-50 text-green-600 text-xs font-medium rounded-full border border-green-100">
                  {{ item.category }}
                </span>
                <span v-else class="text-gray-400 font-light">—</span>
              </td>
              <td class="px-5 py-3.5 font-semibold text-gray-700">&#x20B1;{{ formatMoney(item.price) }}</td>
              <td class="px-5 py-3.5 text-gray-500 font-light max-w-xs truncate">{{ item.description || '—' }}</td>
              <td class="px-5 py-3.5 text-gray-400 text-xs font-light whitespace-nowrap">{{ formatDate(item.updated_at) }}</td>
              <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-2">
                  <button @click="openRestore(item)"
                    class="flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Restore
                  </button>
                  <button @click="openForceDelete(item)"
                    class="flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-500 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-10 0h14"/>
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


    <!-- ══ RESTORE MODAL ══ -->
    <div v-if="restoreTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-start gap-3 mb-5">
          <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-gray-800">Restore Dish</h3>
            <p class="text-sm text-gray-500 font-light mt-0.5">
              Restore <strong class="text-gray-700">{{ restoreTarget.name }}</strong> back to the active café menu?
            </p>
          </div>
        </div>
        <div class="flex justify-end gap-3">
          <button @click="restoreTarget = null"
            class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
            Cancel
          </button>
          <button @click="confirmRestore" :disabled="saving"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
            {{ saving ? 'Restoring...' : 'Restore' }}
          </button>
        </div>
      </div>
    </div>


    <!-- ══ FORCE DELETE MODAL ══ -->
    <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-start gap-3 mb-5">
          <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-gray-800">Permanently Delete</h3>
            <p class="text-sm text-gray-500 font-light mt-0.5">
              Delete <strong class="text-gray-700">{{ deleteTarget.name }}</strong> permanently? This action <span class="text-red-500 font-medium">cannot be undone</span>.
            </p>
          </div>
        </div>
        <div class="flex justify-end gap-3">
          <button @click="deleteTarget = null"
            class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
            Cancel
          </button>
          <button @click="confirmForceDelete" :disabled="saving"
            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
            {{ saving ? 'Deleting...' : 'Delete Forever' }}
          </button>
        </div>
      </div>
    </div>


    <!-- Toast -->
    <transition name="fade">
      <div v-if="toast" class="fixed bottom-6 right-6 z-50 bg-green-600 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium">
        {{ toast }}
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'

const items        = ref([])
const loading      = ref(true)
const search       = ref('')
const saving       = ref(false)
const toast        = ref('')
const restoreTarget = ref(null)
const deleteTarget  = ref(null)

// ── Computed ──────────────────────────────────────────────
const filtered = computed(() => {
  if (!search.value.trim()) return items.value
  const q = search.value.toLowerCase()
  return items.value.filter(i =>
    i.name.toLowerCase().includes(q) ||
    (i.category && i.category.toLowerCase().includes(q))
  )
})

// ── Helpers ───────────────────────────────────────────────
function formatMoney(v) {
  return Number(v || 0).toFixed(2)
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-PH', {
    year: 'numeric', month: 'short', day: 'numeric'
  })
}

function showToast(msg) {
  toast.value = msg
  setTimeout(() => { toast.value = '' }, 3000)
}

// ── Actions ───────────────────────────────────────────────
function openRestore(item)      { restoreTarget.value = item }
function openForceDelete(item)  { deleteTarget.value = item }

async function confirmRestore() {
  if (!restoreTarget.value) return
  saving.value = true
  try {
    await api.put(`/restaurant/menu-items/${restoreTarget.value.id}/restore`)
    items.value = items.value.filter(i => i.id !== restoreTarget.value.id)
    showToast(`"${restoreTarget.value.name}" restored to the café menu.`)
    restoreTarget.value = null
  } catch (e) {
    console.error(e)
  } finally {
    saving.value = false
  }
}

async function confirmForceDelete() {
  if (!deleteTarget.value) return
  saving.value = true
  try {
    await api.delete(`/restaurant/menu-items/${deleteTarget.value.id}/force`)
    items.value = items.value.filter(i => i.id !== deleteTarget.value.id)
    showToast(`"${deleteTarget.value.name}" permanently deleted.`)
    deleteTarget.value = null
  } catch (e) {
    console.error(e)
  } finally {
    saving.value = false
  }
}

// ── Load ──────────────────────────────────────────────────
onMounted(async () => {
  try {
    const res = await api.get('/restaurant/menu-items/archived')
    items.value = res.data.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
