<template>
  <div class="bg-gray-50 min-h-full">

    <!-- Cover + Avatar Hero -->
    <div class="relative bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-900 h-44">
      <!-- Decorative dot pattern -->
      <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 20% 40%, white 1px, transparent 1px),radial-gradient(circle at 80% 70%, white 1px, transparent 1px); background-size: 32px 32px;"></div>

      <!-- Avatar anchored to bottom-left -->
      <div class="absolute -bottom-12 left-8">
        <div class="relative group">
          <div class="w-24 h-24 rounded-2xl border-4 border-white shadow-xl overflow-hidden bg-green-700 flex items-center justify-center">
            <img v-if="avatarSrc" :src="avatarSrc" alt="Avatar" class="w-full h-full object-cover" />
            <span v-else class="text-white text-xl lg:text-3xl font-semibold">{{ profile?.name?.charAt(0)?.toUpperCase() ?? '?' }}</span>
          </div>
          <label class="absolute inset-0 rounded-2xl bg-black/50 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer" title="Upload photo">
            <template v-if="avatarUploading">
              <svg class="w-6 h-6 text-white animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
              <span class="text-white text-[10px] font-medium mt-0.5">Uploading</span>
            </template>
            <template v-else>
              <svg class="w-6 h-6 text-white mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
              <span class="text-white text-[10px] font-medium">Upload</span>
            </template>
            <input type="file" accept="image/*" class="hidden" :disabled="avatarUploading" @change="onAvatarChange" />
          </label>
        </div>
      </div>
    </div>

    <!-- Name / role bar -->
    <div class="pl-4 sm:pl-40 pr-4 sm:pr-8 pt-14 sm:pt-3 pb-4 bg-white border-b border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 min-h-[72px]">
      <div>
        <h1 class="text-xl font-semibold text-gray-900 leading-tight">{{ profile?.name ?? '-' }}</h1>
        <p class="text-sm text-gray-500 capitalize font-light">{{ profile?.role?.replace(/_/g, ' ') }} &middot; {{ profile?.email }}</p>
      </div>
      <div class="flex items-center gap-2 mb-1">
        <span class="inline-flex items-center gap-1.5 text-xs text-green-600 font-medium bg-green-50 border border-green-200 rounded-full px-3 py-1">
          <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
        </span>
        <span class="text-xs text-gray-400 font-light">ID #{{ profile?.id }}</span>
      </div>
    </div>

    <div class="p-4 lg:p-8 max-w-4xl mx-auto space-y-6">

      <!-- Rejected leave notifications banner -->
      <transition name="fade">
        <div v-if="unreadRejected.length > 0" class="bg-red-50 border border-red-200 rounded-xl p-5">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <div class="flex-1">
              <p class="text-sm font-medium text-red-800">
                {{ unreadRejected.length === 1 ? '1 leave request was rejected' : `${unreadRejected.length} leave requests were rejected` }}
              </p>
              <div class="mt-3 space-y-2">
                <div v-for="leave in unreadRejected" :key="leave.id" class="bg-white border border-red-200 rounded-lg p-3">
                  <div class="flex items-center gap-2">
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium capitalize" :class="typeBadge(leave.leave_type)">{{ leave.leave_type }}</span>
                    <span class="text-xs text-gray-500 font-light">{{ formatDate(leave.start_date) }} &ndash; {{ formatDate(leave.end_date) }} &bull; {{ leave.number_of_days }}d</span>
                  </div>
                  <p v-if="leave.rejection_reason" class="text-sm text-red-600 font-light mt-1.5 italic">
                    "{{ leave.rejection_reason }}"
                  </p>
                  <p v-else class="text-xs text-gray-400 font-light mt-1">No reason provided</p>
                </div>
              </div>
              <button @click="dismissNotifications" class="mt-3 text-xs text-red-600 hover:text-red-800 font-medium">
                Mark all as read ?
              </button>
            </div>
          </div>
        </div>
      </transition>

      <!-- Main 3-col grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left col: forms -->
        <div class="lg:col-span-2 space-y-5">

          <!-- Profile Information -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              </div>
              <h2 class="text-sm font-semibold text-gray-800">Profile Information</h2>
            </div>
            <div class="p-6">
              <form @submit.prevent="saveProfile" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Full Name</label>
                    <input v-model="editForm.name" type="text" required class="form-input" />
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Phone</label>
                    <input v-model="editForm.phone" type="text" placeholder="09XXXXXXXXX" class="form-input" />
                  </div>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1.5">Email
                    <span v-if="profile?.role !== 'admin'" class="text-gray-400 font-light">(contact admin to change)</span>
                  </label>
                  <input v-if="profile?.role === 'admin'" v-model="editForm.email" type="email" required class="form-input" />
                  <input v-else :value="profile?.email" type="email" disabled class="form-input opacity-60 cursor-not-allowed bg-gray-50" />
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1.5">Role</label>
                  <input :value="profile?.role?.replace(/_/g,' ')" type="text" disabled class="form-input opacity-60 cursor-not-allowed bg-gray-50 capitalize" />
                </div>
                <transition name="fade">
                  <div v-if="profileMsg" class="p-3 rounded-lg text-sm font-light flex items-center gap-2"
                    :class="profileMsg.type === 'success' ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700'">
                    <svg v-if="profileMsg.type==='success'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ profileMsg.text }}
                  </div>
                </transition>

                <button type="submit" :disabled="profileLoading"
                  class="w-full bg-green-800 hover:bg-green-900 disabled:bg-gray-300 text-white font-medium py-2.5 rounded-xl transition text-sm flex items-center justify-center gap-2">
                  <svg v-if="profileLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                  {{ profileLoading ? 'Saving...' : 'Save Changes' }}
                </button>
              </form>
            </div>
          </div>

          <!-- Change Password -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              </div>
              <h2 class="text-sm font-semibold text-gray-800">Change Password</h2>
            </div>
            <div class="p-6">
            <form @submit.prevent="changePassword" class="space-y-4">
              <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Current Password</label>
                <div class="relative">
                  <input v-model="pwForm.current" :type="showCurrent ? 'text' : 'password'" required class="form-input pr-10" placeholder="••••••••" />
                  <button type="button" @click="showCurrent = !showCurrent" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg v-if="!showCurrent" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                  </button>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1.5">New Password</label>
                  <div class="relative">
                    <input v-model="pwForm.new_password" :type="showNew ? 'text' : 'password'" required class="form-input pr-10" placeholder="Min. 6 characters" minlength="6" />
                    <button type="button" @click="showNew = !showNew" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                      <svg v-if="!showNew" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                      <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                  </div>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1.5">Confirm Password</label>
                  <div class="relative">
                    <input v-model="pwForm.confirm" :type="showConfirm ? 'text' : 'password'" required class="form-input pr-10" :class="pwForm.confirm && pwForm.confirm !== pwForm.new_password ? 'border-red-400' : ''" placeholder="Re-enter password" />
                    <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                      <svg v-if="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                      <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                  </div>
                  <p v-if="pwForm.confirm && pwForm.confirm !== pwForm.new_password" class="text-xs text-red-500 mt-1">Passwords do not match</p>
                </div>
              </div>
              <transition name="fade">
                <div v-if="pwMsg" class="p-3 rounded-lg text-sm font-light"
                  :class="pwMsg.type === 'success' ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700'">
                  {{ pwMsg.text }}
                </div>
              </transition>
              <button type="submit" :disabled="pwLoading || pwForm.confirm !== pwForm.new_password"
                class="w-full bg-gray-800 hover:bg-gray-900 disabled:bg-gray-300 text-white font-medium py-2.5 rounded-xl transition text-sm flex items-center justify-center gap-2">
                <svg v-if="pwLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                {{ pwLoading ? 'Updating...' : 'Update Password' }}
              </button>
            </form>
            </div>
          </div>
        </div>

        <!-- Right col -->
        <div class="space-y-4">

          <!-- Avatar card -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <div class="relative inline-block group mx-auto">
              <div class="w-20 h-20 rounded-2xl overflow-hidden bg-green-700 flex items-center justify-center mx-auto shadow">
                <img v-if="avatarSrc" :src="avatarSrc" alt="Avatar" class="w-full h-full object-cover" />
                <span v-else class="text-white text-2xl font-semibold">{{ profile?.name?.charAt(0)?.toUpperCase() ?? '?' }}</span>
              </div>
              <label class="absolute inset-0 rounded-2xl bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer">
                <template v-if="avatarUploading">
                  <svg class="w-5 h-5 text-white animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                </template>
                <template v-else>
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </template>
                <input type="file" accept="image/*" class="hidden" :disabled="avatarUploading" @change="onAvatarChange" />
              </label>
            </div>
            <p class="text-sm font-semibold text-gray-800 mt-3">{{ profile?.name ?? '-' }}</p>
            <p class="text-xs text-gray-400 capitalize">{{ profile?.role?.replace(/_/g, ' ') }}</p>
            <button v-if="avatarSrc" @click="removeAvatar" :disabled="avatarUploading"
              class="mt-3 text-xs text-red-500 hover:text-red-700 transition font-light disabled:opacity-40">Remove photo</button>
          </div>

          <!-- Account info card -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Account Info
            </h3>
            <div class="space-y-2.5">
              <div class="flex justify-between text-sm">
                <span class="text-gray-400 font-light">User ID</span>
                <span class="text-gray-700 font-medium">#{{ profile?.id }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-400 font-light">Status</span>
                <span class="inline-flex items-center gap-1.5 text-green-600 text-xs font-medium">
                  <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                </span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-400 font-light">Last Login</span>
                <span class="text-gray-600 font-light text-xs">{{ profile?.last_login ? formatDate(profile.last_login) : 'N/A' }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-400 font-light">Member Since</span>
                <span class="text-gray-600 font-light text-xs">{{ profile?.created_at ? formatDate(profile.created_at) : 'N/A' }}</span>
              </div>
            </div>
          </div>

          <!-- Leave summary card -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                My Leaves
              </h3>
              <router-link to="/staff/leave/history" class="text-xs text-amber-600 hover:underline">View all</router-link>
            </div>
            <div v-if="leavesLoading" class="text-xs text-gray-400 font-light">Loading...</div>
            <div v-else class="space-y-2">
              <div v-for="lstat in leaveStats" :key="lstat.label" class="flex items-center justify-between">
                <span class="inline-flex items-center gap-1.5 text-xs font-light" :class="lstat.color">
                  <span class="w-2 h-2 rounded-full" :class="lstat.dot"></span>
                  {{ lstat.label }}
                </span>
                <span class="text-sm font-medium text-gray-700">{{ lstat.count }}</span>
              </div>
            </div>
            <router-link
              to="/staff/leave/request"
              class="block w-full text-center text-xs text-green-800 border border-green-300 hover:bg-green-50 py-2 rounded-xl transition font-medium"
            >
              + Request Leave
            </router-link>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import api from '../services/api'
import { useAuthStore } from '../stores/auth'
import { useLeavesStore } from '../stores/leaves'

const authStore   = useAuthStore()
const leavesStore = useLeavesStore()

const profile        = ref(null)
const editForm       = reactive({ name: '', phone: '', email: '' })
const pwForm         = reactive({ current: '', new_password: '', confirm: '' })
const profileLoading  = ref(false)
const pwLoading       = ref(false)
const profileMsg      = ref(null)
const pwMsg           = ref(null)
const showCurrent     = ref(false)
const showNew         = ref(false)
const showConfirm     = ref(false)
const leavesLoading   = ref(false)
const avatarSrc       = ref(null)
const avatarUploading = ref(false)

const unreadRejected = computed(() => leavesStore.unreadRejected)

const leaveStats = computed(() => {
  const leaves = leavesStore.myLeaves
  return [
    { label: 'Total',    count: leaves.length,                                   color: 'text-gray-600', dot: 'bg-gray-400' },
    { label: 'Pending',  count: leaves.filter(l=>l.status==='pending').length,   color: 'text-yellow-600', dot: 'bg-yellow-400' },
    { label: 'Approved', count: leaves.filter(l=>l.status==='approved').length,  color: 'text-green-600',  dot: 'bg-green-500' },
    { label: 'Rejected', count: leaves.filter(l=>l.status==='rejected').length,  color: 'text-red-500',    dot: 'bg-red-400' },
  ]
})

const formatDate  = (d) => d ? new Date(d).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' }) : '-'
const typeBadge   = (t) => ({ sick: 'bg-red-50 text-red-700', casual: 'bg-green-50 text-green-800', annual: 'bg-green-50 text-green-700', emergency: 'bg-orange-50 text-orange-700', maternity: 'bg-pink-50 text-pink-700', paternity: 'bg-purple-50 text-purple-700' }[t] ?? 'bg-gray-100 text-gray-600')

const avatarKey = () => `avatar_${authStore.user?.id ?? 'guest'}`

const resolveAvatarUrl = (path) => {
  if (!path) return null
  if (path.startsWith('http')) return path
  const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
  // Absolute URL (local dev): strip /api to get server root → http://localhost:8000
  // Relative URL (production): keep as-is → /api  so uploads resolve to /api/uploads/...
  const base = apiUrl.startsWith('http') ? apiUrl.replace(/\/api$/, '') : apiUrl
  return `${base}${path}`
}

const loadAvatar = () => {
  // Prefer server-stored avatar from user object or profile
  const serverAvatar = authStore.user?.avatar || profile.value?.avatar
  if (serverAvatar) {
    avatarSrc.value = resolveAvatarUrl(serverAvatar)
    // Cache locally for offline/navBar use
    localStorage.setItem(avatarKey(), avatarSrc.value)
    return
  }
  // Fall back to local cache
  avatarSrc.value = localStorage.getItem(avatarKey()) || null
}

const onAvatarChange = async (e) => {
  const file = e.target.files?.[0]
  if (!file) return
  e.target.value = ''

  avatarUploading.value = true
  try {
    const formData = new FormData()
    formData.append('avatar', file)
    const res = await api.post('/auth/avatar', formData)
    const avatarPath = res.data.data.avatar
    const avatarUrl  = resolveAvatarUrl(avatarPath)
    avatarSrc.value  = avatarUrl
    // Update the auth store user so the NavBar picks it up immediately
    authStore.setUser({ ...authStore.user, avatar: avatarPath })
    // Cache locally
    localStorage.setItem(avatarKey(), avatarUrl)
    window.dispatchEvent(new CustomEvent('avatar-updated', { detail: avatarUrl }))
  } catch (err) {
    // Fall back to local-only preview on upload failure
    const reader = new FileReader()
    reader.onload = (ev) => {
      avatarSrc.value = ev.target.result
      localStorage.setItem(avatarKey(), ev.target.result)
      window.dispatchEvent(new CustomEvent('avatar-updated', { detail: ev.target.result }))
    }
    reader.readAsDataURL(file)
  } finally {
    avatarUploading.value = false
  }
}

const removeAvatar = async () => {
  try {
    await api.delete('/auth/avatar')
  } catch { /* ignore if server fails */ }
  avatarSrc.value = null
  authStore.setUser({ ...authStore.user, avatar: null })
  localStorage.removeItem(avatarKey())
  window.dispatchEvent(new CustomEvent('avatar-updated', { detail: null }))
}

const dismissNotifications = () => leavesStore.markAllRead()

const loadProfile = async () => {
  try {
    const res = await api.get('/auth/profile')
    profile.value = res.data.data
    editForm.name  = profile.value.name  ?? ''
    editForm.phone = profile.value.phone ?? ''
    editForm.email = profile.value.email ?? ''
    // Re-load avatar now that we have the server profile (includes avatar path)
    loadAvatar()
  } catch (e) { console.error(e) }
}

const saveProfile = async () => {
  profileLoading.value = true
  profileMsg.value = null
  try {
    const payload = { name: editForm.name, phone: editForm.phone }
    if (profile.value?.role === 'admin') payload.email = editForm.email
    const res = await api.put('/auth/profile', payload)
    profile.value = res.data.data
    authStore.setUser({ ...authStore.user, name: editForm.name })
    profileMsg.value = { type: 'success', text: 'Profile updated successfully!' }
  } catch (e) {
    profileMsg.value = { type: 'error', text: e.response?.data?.message || 'Failed to update profile' }
  } finally {
    profileLoading.value = false
    setTimeout(() => { profileMsg.value = null }, 3000)
  }
}

const changePassword = async () => {
  if (pwForm.new_password !== pwForm.confirm) return
  pwLoading.value = true
  pwMsg.value = null
  try {
    await api.put('/auth/profile', {
      current_password: pwForm.current,
      new_password:     pwForm.new_password,
    })
    pwMsg.value = { type: 'success', text: 'Password updated successfully!' }
    Object.assign(pwForm, { current: '', new_password: '', confirm: '' })
  } catch (e) {
    pwMsg.value = { type: 'error', text: e.response?.data?.message || 'Failed to update password' }
  } finally {
    pwLoading.value = false
    setTimeout(() => { pwMsg.value = null }, 3000)
  }
}

onMounted(async () => {
  // loadProfile will also call loadAvatar once profile data (including server avatar) is available
  loadAvatar()   // initial fast load from localStorage/authStore cache
  await loadProfile()
  leavesLoading.value = true
  await leavesStore.fetchMyLeaves()
  leavesLoading.value = false
})
</script>

<style scoped>
.form-input {
  @apply w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-light focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition bg-white;
}
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
