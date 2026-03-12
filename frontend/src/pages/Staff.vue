<template>
  <div class="bg-gray-50 min-h-full">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
      <div class="p-4 lg:p-8 max-w-7xl mx-auto flex items-center justify-between">
        <div>
          <h1 class="text-2xl lg:text-4xl font-light text-gray-900 tracking-tight">Staff List</h1>
          <p class="text-gray-500 font-light mt-1">All registered system users</p>
        </div>
        <button
          @click="showAddModal = true"
          class="flex items-center gap-2 bg-green-800 hover:bg-green-900 text-white text-sm font-light px-5 py-2.5 rounded-lg transition"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Add User
        </button>
      </div>
    </div>

    <div class="p-4 lg:p-8 max-w-7xl mx-auto space-y-6">

      <!-- Filters & Search -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-wrap items-center gap-4">
        <!-- Search -->
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
        <!-- Role Filter -->
        <select
          v-model="roleFilter"
          class="border border-gray-200 rounded-lg px-3 py-2 text-sm font-light text-gray-700 focus:outline-none focus:border-blue-600 transition"
        >
          <option value="">All Roles</option>
          <option value="admin">Admin</option>
          <option value="manager">Manager</option>
          <option value="it">IT</option>
          <option value="front_desk">Front Desk</option>
          <option value="housekeeping">Housekeeping</option>
          <option value="chef">Chef</option>
          <option value="accountant">Accountant</option>
        </select>
        <!-- Status Filter -->
        <select
          v-model="statusFilter"
          class="border border-gray-200 rounded-lg px-3 py-2 text-sm font-light text-gray-700 focus:outline-none focus:border-blue-600 transition"
        >
          <option value="">All Status</option>
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
        <!-- Count badge -->
        <span class="ml-auto text-xs text-gray-400 font-light">
          {{ filteredUsers.length }} {{ filteredUsers.length === 1 ? 'user' : 'users' }} found
        </span>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div v-if="loading" class="flex items-center justify-center py-20">
          <div class="w-7 h-7 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        </div>

        <div v-else-if="filteredUsers.length === 0" class="flex flex-col items-center justify-center py-20 text-gray-400">
          <svg class="w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <p class="text-sm font-light">No users found</p>
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
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Last Login</th>
              <th v-if="canSetRate" class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Hourly Rate</th>
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
                  <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-medium shrink-0" :style="{ backgroundColor: avatarColor(u.name) }">
                    {{ u.name?.charAt(0)?.toUpperCase() }}
                  </div>
                  <span class="text-sm font-light text-gray-900">{{ u.name }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm font-light text-gray-600">{{ u.email }}</td>
              <td class="px-6 py-4 text-sm font-light text-gray-500">{{ u.phone || '—' }}</td>
              <!-- Role badge -->
              <td class="px-6 py-4">
                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium capitalize" :class="roleBadge(u.role)">
                  {{ u.role?.replace(/_/g, ' ') }}
                </span>
              </td>
              <!-- Status -->
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 text-xs font-medium" :class="u.active == 1 ? 'text-green-600' : 'text-red-500'">
                  <span class="w-1.5 h-1.5 rounded-full" :class="u.active == 1 ? 'bg-green-500' : 'bg-red-400'"></span>
                  {{ u.active == 1 ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 text-xs font-light text-gray-400">{{ u.last_login ? formatDate(u.last_login) : 'Never' }}</td>
              <!-- Hourly Rate -->
              <td v-if="canSetRate" class="px-6 py-4 text-sm font-light text-gray-600">
                <span v-if="u.hourly_rate != null && u.hourly_rate !== ''" class="text-green-700 font-medium">₱{{ Number(u.hourly_rate).toFixed(2) }}/hr</span>
                <span v-else class="text-gray-300 text-xs">Not set</span>
              </td>
              <!-- Actions -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <!-- QR Code -->
                  <button
                    @click="openQrCode(u)"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition"
                    title="View QR Code"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                  </button>
                  <!-- Set Hourly Rate (Manager / Admin only) -->
                  <button
                    v-if="canSetRate"
                    @click="openRateModal(u)"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-green-700 hover:bg-green-50 transition"
                    title="Set Hourly Rate"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </button>
                  <button
                    @click="openEdit(u)"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-green-50 transition"
                    title="Edit"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </button>
                  <button
                    @click="toggleStatus(u)"
                    class="p-1.5 rounded-lg transition"
                    :class="u.active == 1 ? 'text-gray-400 hover:text-orange-500 hover:bg-orange-50' : 'text-gray-400 hover:text-green-500 hover:bg-green-50'"
                    :title="u.active == 1 ? 'Deactivate' : 'Activate'"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                  </button>
                  <button
                    @click="confirmDelete(u)"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition"
                    title="Delete"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table></div>
      </div>
    </div>

    <!-- Add / Edit Modal -->
    <transition name="fade">
      <div v-if="showAddModal || showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-4 lg:p-8" @click.stop>
          <h2 class="text-2xl font-light text-gray-900 mb-6">{{ showEditModal ? 'Edit User' : 'Add New User' }}</h2>
          <form @submit.prevent="showEditModal ? saveEdit() : saveAdd()" class="space-y-4">
            <div>
              <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Full Name</label>
              <input v-model="form.name" type="text" required placeholder="e.g. Juan dela Cruz" class="form-input" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Email</label>
              <input v-model="form.email" type="email" required placeholder="user@hotel.com" class="form-input" />
            </div>
            <div v-if="!showEditModal">
              <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Password</label>
              <div class="relative">
                <input v-model="form.password" :type="showFormPassword ? 'text' : 'password'" required placeholder="••••••••" class="form-input pr-10" />
                <button type="button" @click="showFormPassword = !showFormPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                  <svg v-if="!showFormPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
              </div>
            </div>
            <div v-if="showEditModal && isIT">
              <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">New Password <span class="text-gray-400 font-light normal-case">(leave blank to keep current)</span></label>
              <div class="relative">
                <input v-model="form.password" :type="showFormPassword ? 'text' : 'password'" placeholder="••••••••" class="form-input pr-10" />
                <button type="button" @click="showFormPassword = !showFormPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                  <svg v-if="!showFormPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
              </div>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Phone</label>
              <input v-model="form.phone" type="text" placeholder="09XXXXXXXXX" class="form-input" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Role</label>
              <select v-model="form.role" required class="form-input">
                <option value="">Select role</option>
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
                <option v-if="isIT" value="it">IT</option>
                <option value="front_desk">Front Desk</option>
                <option value="housekeeping">Housekeeping</option>
                <option value="chef">Chef</option>
                <option value="accountant">Accountant</option>
              </select>
            </div>
            <div v-if="formError" class="p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg font-light">{{ formError }}</div>
            <div class="flex gap-3 pt-2">
              <button type="submit" :disabled="formLoading" class="flex-1 bg-green-800 hover:bg-green-900 disabled:bg-gray-300 text-white font-light py-2.5 rounded-lg transition text-sm">
                {{ formLoading ? 'Saving...' : (showEditModal ? 'Save Changes' : 'Add User') }}
              </button>
              <button type="button" @click="closeModal" class="flex-1 border border-gray-200 hover:bg-gray-50 text-gray-700 font-light py-2.5 rounded-lg transition text-sm">
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>

    <!-- Delete Confirm Modal -->
    <transition name="fade">
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-4 lg:p-8 text-center">
          <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
          </div>
          <h3 class="text-lg font-light text-gray-900 mb-2">Delete User?</h3>
          <p class="text-sm text-gray-500 font-light mb-6">Are you sure you want to delete <strong class="text-gray-700">{{ deleteTarget?.name }}</strong>? This action cannot be undone.</p>
          <div class="flex gap-3">
            <button @click="deleteUser" :disabled="formLoading" class="flex-1 bg-red-600 hover:bg-red-700 disabled:bg-gray-300 text-white font-light py-2.5 rounded-lg transition text-sm">
              {{ formLoading ? 'Deleting...' : 'Yes, Delete' }}
            </button>
            <button @click="showDeleteModal = false" class="flex-1 border border-gray-200 hover:bg-gray-50 text-gray-700 font-light py-2.5 rounded-lg transition text-sm">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Set Hourly Rate Modal -->
    <transition name="fade">
      <div v-if="showRateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" @click.self="showRateModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 lg:p-8" @click.stop>
          <h2 class="text-xl font-light text-gray-900 mb-1">Set Hourly Rate</h2>
          <p class="text-sm text-gray-500 font-light mb-5">{{ rateTarget?.name }}</p>
          <form @submit.prevent="saveRate" class="space-y-4">
            <div>
              <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Hourly Rate (₱)</label>
              <input
                v-model="rateForm.rate"
                type="number"
                min="0"
                step="0.01"
                required
                placeholder="e.g. 75.00"
                class="form-input"
              />
            </div>
            <div v-if="rateError" class="p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg font-light">{{ rateError }}</div>
            <div class="flex gap-3 pt-2">
              <button type="submit" :disabled="rateLoading" class="flex-1 bg-green-800 hover:bg-green-900 disabled:bg-gray-300 text-white font-light py-2.5 rounded-lg transition text-sm">
                {{ rateLoading ? 'Saving...' : 'Save Rate' }}
              </button>
              <button type="button" @click="showRateModal = false" class="flex-1 border border-gray-200 hover:bg-gray-50 text-gray-700 font-light py-2.5 rounded-lg transition text-sm">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </transition>

    <!-- QR Code Modal -->
    <transition name="fade">
      <div v-if="showQrModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" @click.self="showQrModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 lg:p-8 text-center">
          <h2 class="text-xl font-light text-gray-900 mb-1">Attendance QR Code</h2>
          <p class="text-sm text-gray-500 font-light mb-5">{{ qrTarget?.name }}</p>

          <div class="flex justify-center mb-5">
            <div v-if="qrLoading" class="w-10 h-10 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
            <canvas v-else ref="qrCanvas" class="rounded-lg border border-gray-100 shadow-sm"></canvas>
          </div>

          <p class="text-xs text-gray-400 font-light mb-5">Staff scans this code at the attendance kiosk to clock in / clock out.</p>

          <div class="flex gap-3">
            <button @click="downloadQr" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-light py-2.5 rounded-lg transition text-sm">Download</button>
            <button @click="showQrModal = false" class="flex-1 border border-gray-200 hover:bg-gray-50 text-gray-700 font-light py-2.5 rounded-lg transition text-sm">Close</button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, nextTick } from 'vue'
import QRCode from 'qrcode'
import api from '../services/api'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const isIT = computed(() => authStore.isIT)
const isManager = computed(() => authStore.isManager)
const canSetRate = computed(() => authStore.isAdmin || isManager.value)

const users = ref([])
const loading = ref(true)
const search = ref('')
const roleFilter = ref('')
const statusFilter = ref('')
const showAddModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const deleteTarget = ref(null)
const formLoading = ref(false)
const formError = ref('')
const editId = ref(null)

// QR Code state
const showQrModal = ref(false)
const qrTarget   = ref(null)
const qrLoading  = ref(false)
const qrCanvas   = ref(null)

const form = reactive({ name: '', email: '', password: '', phone: '', role: '' })
const showFormPassword = ref(false)

const filteredUsers = computed(() => {
  return users.value.filter(u => {
    if (u.role === 'guest') return false
    const q = search.value.toLowerCase()
    const matchSearch = !q || u.name?.toLowerCase().includes(q) || u.email?.toLowerCase().includes(q)
    const matchRole = !roleFilter.value || u.role === roleFilter.value
    const matchStatus = statusFilter.value === '' || String(u.active) === statusFilter.value
    return matchSearch && matchRole && matchStatus
  })
})

// Hourly rate modal state
const showRateModal = ref(false)
const rateTarget = ref(null)
const rateForm = reactive({ rate: '' })
const rateLoading = ref(false)
const rateError = ref('')

const openRateModal = (u) => {
  rateTarget.value = u
  rateForm.rate = u.hourly_rate ?? ''
  rateError.value = ''
  showRateModal.value = true
}

const saveRate = async () => {
  rateLoading.value = true
  rateError.value = ''
  try {
    await api.post('/admin/payroll/rate', { user_id: rateTarget.value.id, rate: parseFloat(rateForm.rate) })
    await loadUsers()
    showRateModal.value = false
  } catch (e) {
    rateError.value = e.response?.data?.message || 'Failed to update hourly rate'
  } finally {
    rateLoading.value = false
  }
}

const loadUsers = async () => {
  loading.value = true
  try {
    const res = await api.get('/admin/users')
    users.value = res.data.data || []
  } catch (e) {
    console.error('Failed to load users', e)
  } finally {
    loading.value = false
  }
}

const formatDate = (d) => {
  if (!d) return 'Never'
  return new Date(d).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' })
}

const avatarColors = ['#b45309','#0369a1','#4f46e5','#059669','#dc2626','#9333ea','#0891b2']
const avatarColor = (name) => avatarColors[(name?.charCodeAt(0) ?? 0) % avatarColors.length]

const roleBadge = (role) => ({
  admin:       'bg-red-50 text-red-700',
  manager:     'bg-green-50 text-green-800',
  it:          'bg-blue-50 text-blue-700',
  front_desk:  'bg-cyan-50 text-cyan-700',
  housekeeping:'bg-green-50 text-green-700',
  chef:        'bg-orange-50 text-orange-700',
  accountant:  'bg-purple-50 text-purple-700',
  guest:       'bg-gray-100 text-gray-600',
}[role] ?? 'bg-gray-100 text-gray-600')

const openEdit = (u) => {
  editId.value = u.id
  form.name = u.name; form.email = u.email; form.phone = u.phone ?? ''; form.role = u.role; form.password = ''
  showFormPassword.value = false
  formError.value = ''
  showEditModal.value = true
}

const closeModal = () => {
  showAddModal.value = false; showEditModal.value = false
  formError.value = ''; editId.value = null
  Object.assign(form, { name: '', email: '', password: '', phone: '', role: '' })
  showFormPassword.value = false
}

const saveAdd = async () => {
  formLoading.value = true; formError.value = ''
  try {
    await api.post('/admin/users', { ...form })
    await loadUsers(); closeModal()
  } catch (e) {
    formError.value = e.response?.data?.message || 'Failed to add user'
  } finally { formLoading.value = false }
}

const saveEdit = async () => {
  formLoading.value = true; formError.value = ''
  try {
    const payload = { name: form.name, email: form.email, phone: form.phone, role: form.role }
    if (isIT.value && form.password) payload.password = form.password
    await api.put(`/admin/users/${editId.value}`, payload)
    await loadUsers(); closeModal()
  } catch (e) {
    formError.value = e.response?.data?.message || 'Failed to update user'
  } finally { formLoading.value = false }
}

const toggleStatus = async (u) => {
  try {
    await api.put(`/admin/users/${u.id}`, { active: u.active == 1 ? 0 : 1 })
    await loadUsers()
  } catch (e) { console.error(e) }
}

const confirmDelete = (u) => { deleteTarget.value = u; showDeleteModal.value = true }

const openQrCode = async (u) => {
  qrTarget.value    = u
  qrLoading.value   = true
  showQrModal.value = true
  try {
    const res   = await api.get(`/admin/users/${u.id}/qr-code`)
    const token = res.data.data?.qr_token

    // Hide spinner first so the <canvas> is rendered, then wait for it
    qrLoading.value = false
    await nextTick()

    if (qrCanvas.value && token) {
      await QRCode.toCanvas(qrCanvas.value, token, {
        width: 220, margin: 2,
        color: { dark: '#1e293b', light: '#ffffff' },
      })
    }
  } catch (e) {
    console.error('Failed to load QR code', e)
    qrLoading.value = false
  }
}

const downloadQr = () => {
  if (!qrCanvas.value) return
  const link = document.createElement('a')
  link.download = `qr-${qrTarget.value?.name?.replace(/\s+/g, '_') ?? 'staff'}.png`
  link.href = qrCanvas.value.toDataURL('image/png')
  link.click()
}

const deleteUser = async () => {
  formLoading.value = true
  try {
    await api.delete(`/admin/users/${deleteTarget.value.id}`)
    await loadUsers(); showDeleteModal.value = false
  } catch (e) { console.error(e) }
  finally { formLoading.value = false }
}

onMounted(loadUsers)
</script>

<style scoped>
.form-input {
  @apply w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition;
}
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

