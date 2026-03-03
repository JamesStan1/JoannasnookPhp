<template>
  <div class="bg-gray-50 min-h-full">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
      <div class="p-4 lg:p-8 max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-sm text-gray-400 font-light mb-1">
            <router-link to="/staff" class="hover:text-amber-600 transition">Staff List</router-link>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-600">Archived Staff</span>
          </div>
          <h1 class="text-2xl lg:text-4xl font-light text-gray-900 tracking-tight">Archived Staff</h1>
          <p class="text-gray-500 font-light mt-1">Deactivated accounts that are no longer active</p>
        </div>
        <router-link
          to="/staff"
          class="flex items-center gap-2 border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-light px-5 py-2.5 rounded-lg transition"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          View Active Staff
        </router-link>
      </div>
    </div>

    <div class="p-4 lg:p-8 max-w-7xl mx-auto space-y-6">

      <!-- Summary card -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex items-center gap-5">
        <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
          </svg>
        </div>
        <div>
          <p class="text-2xl font-light text-gray-700">{{ users.length }} <span class="text-base text-gray-400">archived {{ users.length === 1 ? 'account' : 'accounts' }}</span></p>
          <p class="text-xs text-gray-400 font-light mt-0.5">These users cannot log in. You can restore or permanently delete them.</p>
        </div>
      </div>

      <!-- Search + Filter -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-wrap items-center gap-4">
        <div class="relative flex-1 min-w-48">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
          </svg>
          <input
            v-model="search"
            type="text"
            placeholder="Search by name or email..."
            class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition"
          />
        </div>
        <select
          v-model="roleFilter"
          class="border border-gray-200 rounded-lg px-3 py-2 text-sm font-light text-gray-700 focus:outline-none focus:border-blue-600 transition"
        >
          <option value="">All Roles</option>
          <option value="admin">Admin</option>
          <option value="manager">Manager</option>
          <option value="front_desk">Front Desk</option>
          <option value="housekeeping">Housekeeping</option>
          <option value="chef">Chef</option>
          <option value="accountant">Accountant</option>
          <option value="guest">Guest</option>
        </select>
        <span class="ml-auto text-xs text-gray-400 font-light">
          {{ filteredUsers.length }} {{ filteredUsers.length === 1 ? 'result' : 'results' }}
        </span>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div v-if="loading" class="flex items-center justify-center py-20">
          <div class="w-7 h-7 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        </div>

        <div v-else-if="filteredUsers.length === 0" class="flex flex-col items-center justify-center py-20 text-gray-400">
          <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
          </svg>
          <p class="text-sm font-light">
            {{ search || roleFilter ? 'No archived users match your search' : 'No archived staff found' }}
          </p>
          <p class="text-xs text-gray-300 font-light mt-1">
            Deactivated accounts from the Staff List will appear here
          </p>
        </div>

        <div v-else class="overflow-x-auto -mx-px">

        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">#</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">User</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Phone</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Role</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Archived On</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="(u, i) in filteredUsers"
              :key="u.id"
              class="hover:bg-gray-50 transition-colors duration-100"
            >
              <td class="px-6 py-4 text-sm text-gray-400 font-light">{{ i + 1 }}</td>

              <!-- Avatar + Name -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-medium shrink-0 opacity-60"
                    :style="{ backgroundColor: avatarColor(u.name) }"
                  >
                    {{ u.name?.charAt(0)?.toUpperCase() }}
                  </div>
                  <div>
                    <p class="text-sm font-light text-gray-700">{{ u.name }}</p>
                    <p class="text-xs text-gray-400 font-light">#{{ u.id }}</p>
                  </div>
                </div>
              </td>

              <td class="px-6 py-4 text-sm font-light text-gray-500">{{ u.email }}</td>
              <td class="px-6 py-4 text-sm font-light text-gray-400">{{ u.phone || '—' }}</td>

              <!-- Role badge -->
              <td class="px-6 py-4">
                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium capitalize opacity-75" :class="roleBadge(u.role)">
                  {{ u.role?.replace(/_/g, ' ') }}
                </span>
              </td>

              <!-- Archived date -->
              <td class="px-6 py-4">
                <p class="text-xs font-light text-gray-500">{{ formatDate(u.archived_at) }}</p>
                <p class="text-xs text-gray-300 font-light">{{ timeAgo(u.archived_at) }}</p>
              </td>

              <!-- Actions -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <!-- Restore -->
                  <button
                    @click="confirmRestore(u)"
                    :disabled="actionId === u.id"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 text-xs font-medium rounded-lg transition disabled:opacity-50"
                    title="Restore user"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ actionId === u.id ? '...' : 'Restore' }}
                  </button>

                  <!-- Permanently delete -->
                  <button
                    @click="confirmDelete(u)"
                    :disabled="actionId === u.id"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-lg transition disabled:opacity-50"
                    title="Permanently delete"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table></div>
      </div>
    </div>

    <!-- Restore confirm modal -->
    <transition name="fade">
      <div v-if="showRestoreModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-4 lg:p-8 text-center">
          <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
          </div>
          <h3 class="text-lg font-light text-gray-900 mb-2">Restore User?</h3>
          <p class="text-sm text-gray-500 font-light mb-6">
            <strong class="text-gray-700">{{ actionTarget?.name }}</strong> will be reactivated and will be able to log in again.
          </p>
          <div class="flex gap-3">
            <button @click="doRestore" :disabled="actionId !== null" class="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 text-white font-light py-2.5 rounded-lg transition text-sm">
              {{ actionId !== null ? 'Restoring...' : 'Yes, Restore' }}
            </button>
            <button @click="showRestoreModal = false" class="flex-1 border border-gray-200 hover:bg-gray-50 text-gray-700 font-light py-2.5 rounded-lg transition text-sm">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Delete confirm modal -->
    <transition name="fade">
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-4 lg:p-8 text-center">
          <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
          </div>
          <h3 class="text-lg font-light text-gray-900 mb-2">Permanently Delete?</h3>
          <p class="text-sm text-gray-500 font-light mb-2">
            This will <strong class="text-red-600">permanently remove</strong> <strong class="text-gray-700">{{ actionTarget?.name }}</strong> from the system.
          </p>
          <p class="text-xs text-red-400 font-light mb-6">This action cannot be undone.</p>
          <div class="flex gap-3">
            <button @click="doDelete" :disabled="actionId !== null" class="flex-1 bg-red-600 hover:bg-red-700 disabled:bg-gray-300 text-white font-light py-2.5 rounded-lg transition text-sm">
              {{ actionId !== null ? 'Deleting...' : 'Yes, Delete' }}
            </button>
            <button @click="showDeleteModal = false" class="flex-1 border border-gray-200 hover:bg-gray-50 text-gray-700 font-light py-2.5 rounded-lg transition text-sm">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Toast -->
    <transition name="slide-down">
      <div v-if="toast.show" class="fixed top-20 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg text-sm font-light"
        :class="toast.type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'">
        <svg v-if="toast.type === 'success'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <svg v-else class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{ toast.message }}
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import api from '../services/api'

const users           = ref([])
const loading         = ref(true)
const search          = ref('')
const roleFilter      = ref('')
const actionId        = ref(null)
const actionTarget    = ref(null)
const showRestoreModal = ref(false)
const showDeleteModal  = ref(false)
const toast = reactive({ show: false, message: '', type: 'success' })

// -- Computed ------------------------------------------
const filteredUsers = computed(() => {
  return users.value.filter(u => {
    const q = search.value.toLowerCase()
    const matchSearch = !q || u.name?.toLowerCase().includes(q) || u.email?.toLowerCase().includes(q)
    const matchRole   = !roleFilter.value || u.role === roleFilter.value
    return matchSearch && matchRole
  })
})

// -- Helpers -------------------------------------------
const avatarColors = ['#b45309','#0369a1','#4f46e5','#059669','#dc2626','#9333ea','#0891b2']
const avatarColor  = (name) => avatarColors[(name?.charCodeAt(0) ?? 0) % avatarColors.length]

const roleBadge = (role) => ({
  admin:        'bg-red-50 text-red-700',
  manager:      'bg-green-50 text-green-800',
  front_desk:   'bg-cyan-50 text-cyan-700',
  housekeeping: 'bg-green-50 text-green-700',
  chef:         'bg-orange-50 text-orange-700',
  accountant:   'bg-purple-50 text-purple-700',
  guest:        'bg-gray-100 text-gray-600',
}[role] ?? 'bg-gray-100 text-gray-600')

const formatDate = (d) => {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' })
}

const timeAgo = (d) => {
  if (!d) return ''
  const diff = Date.now() - new Date(d).getTime()
  const days = Math.floor(diff / 86400000)
  if (days === 0) return 'Today'
  if (days === 1) return '1 day ago'
  if (days < 30)  return `${days} days ago`
  if (days < 365) return `${Math.floor(days / 30)} months ago`
  return `${Math.floor(days / 365)} years ago`
}

const showToast = (message, type = 'success') => {
  toast.message = message; toast.type = type; toast.show = true
  setTimeout(() => { toast.show = false }, 2800)
}

// -- Load data -----------------------------------------
const loadUsers = async () => {
  loading.value = true
  try {
    const res = await api.get('/admin/users/archived')
    users.value = res.data.data || []
  } catch (e) {
    console.error('Failed to load archived users', e)
  } finally {
    loading.value = false
  }
}

// -- Restore -------------------------------------------
const confirmRestore = (u) => {
  actionTarget.value = u
  showRestoreModal.value = true
}

const doRestore = async () => {
  actionId.value = actionTarget.value.id
  try {
    await api.put(`/admin/users/${actionTarget.value.id}/restore`)
    showToast(`${actionTarget.value.name} has been restored`)
    showRestoreModal.value = false
    await loadUsers()
  } catch (e) {
    showToast(e.response?.data?.message || 'Failed to restore user', 'error')
  } finally {
    actionId.value = null
  }
}

// -- Delete --------------------------------------------
const confirmDelete = (u) => {
  actionTarget.value = u
  showDeleteModal.value = true
}

const doDelete = async () => {
  actionId.value = actionTarget.value.id
  try {
    await api.delete(`/admin/users/${actionTarget.value.id}`)
    showToast(`${actionTarget.value.name} permanently deleted`)
    showDeleteModal.value = false
    await loadUsers()
  } catch (e) {
    showToast(e.response?.data?.message || 'Failed to delete user', 'error')
  } finally {
    actionId.value = null
  }
}

onMounted(loadUsers)
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s ease; }
.slide-down-enter-from { opacity: 0; transform: translateX(-50%) translateY(-12px); }
.slide-down-leave-to { opacity: 0; transform: translateX(-50%) translateY(-8px); }
</style>
