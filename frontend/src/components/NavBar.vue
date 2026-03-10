<template>
  <nav class="fixed top-0 left-0 right-0 z-50 h-20 bg-gray-950 border-b border-gray-800 flex items-center px-4 lg:px-8 shadow-xl">
    <!-- Hamburger (mobile only) -->
    <button
      @click="$emit('toggle-sidebar')"
      class="lg:hidden flex items-center justify-center w-10 h-10 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition mr-2 shrink-0"
      aria-label="Toggle menu"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>

    <!-- Brand -->
    <div class="flex items-center gap-3 shrink-0 lg:w-64">
      <img src="/Joannaslogo.png" alt="Joanna's Logo" class="h-14 w-14 sm:h-16 sm:w-16 rounded-xl object-contain" />
      <div class="leading-tight hidden sm:block">
        <div class="text-white font-semibold tracking-widest text-base">Joanna's Nook</div>
        <div class="text-gray-400 text-xs tracking-wide">BED &amp; BREAKFAST</div>
      </div>
    </div>

    <!-- Spacer -->
    <div class="flex-1"></div>

    <!-- Right -->
    <div class="flex items-center gap-2 sm:gap-5">
      <!-- Online indicator -->
      <div class="hidden sm:flex items-center gap-2 text-xs text-gray-400">
        <span class="w-2 h-2 rounded-full bg-green-500 inline-block animate-pulse"></span>
        Online
      </div>

      <div class="hidden sm:block h-7 w-px bg-gray-700"></div>
      <div v-if="prStore.canApprove()" class="relative" ref="bellRef">
        <button
          @click="toggleBell"
          class="relative flex items-center justify-center w-10 h-10 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition duration-150"
          aria-label="Pending reservation notifications"
        >
          <!-- Bell icon -->
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
          <!-- Badge -->
          <span
            v-if="prStore.pendingCount > 0"
            class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-red-500 rounded-full text-white text-[10px] font-bold flex items-center justify-center shadow-lg animate-pulse"
          >
            {{ prStore.pendingCount > 99 ? '99+' : prStore.pendingCount }}
          </span>
        </button>

        <!-- Bell dropdown -->
        <Teleport to="body">
          <transition name="dropdown">
            <div
              v-if="prStore.bellOpen"
              ref="bellDropdownRef"
              :style="bellDropdownStyle"
              class="fixed bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl overflow-hidden"
              style="z-index: 9999;"
            >
              <!-- Header -->
              <div class="px-4 py-3 border-b border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                  </svg>
                  <span class="text-sm font-semibold text-white">Pending Reservations</span>
                </div>
                <span
                  v-if="prStore.pendingCount > 0"
                  class="min-w-[20px] h-5 px-1.5 bg-red-500 rounded-full text-white text-[10px] font-bold flex items-center justify-center"
                >
                  {{ prStore.pendingCount }}
                </span>
              </div>

              <!-- List -->
              <div v-if="prStore.loading" class="px-4 py-6 text-center">
                <svg class="w-5 h-5 animate-spin text-gray-400 mx-auto" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
              </div>
              <div v-else-if="prStore.pendingList.length === 0" class="px-4 py-6 text-center">
                <svg class="w-8 h-8 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-gray-400">No pending reservations</p>
              </div>
              <div v-else class="max-h-72 overflow-y-auto divide-y divide-gray-800">
                <div
                  v-for="r in prStore.pendingList.slice(0, 8)"
                  :key="r.id"
                  class="px-4 py-3 hover:bg-gray-800 transition cursor-pointer"
                  @click="goToApprovals"
                >
                  <div class="flex items-start gap-2">
                    <div class="w-2 h-2 rounded-full bg-amber-400 mt-1.5 shrink-0"></div>
                    <div class="min-w-0 flex-1">
                      <p class="text-sm text-white font-medium truncate">{{ r.guest_name }}</p>
                      <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-[11px] text-gray-400 capitalize">{{ r.reservation_type }}</span>
                        <span class="text-[11px] text-gray-600">�</span>
                        <span class="text-[11px] text-gray-400">{{ r.reference_number }}</span>
                      </div>
                      <p v-if="r.check_in_date" class="text-[11px] text-gray-500 mt-0.5">
                        Check-in: {{ formatBellDate(r.check_in_date) }}
                      </p>
                      <p v-else-if="r.event_date" class="text-[11px] text-gray-500 mt-0.5">
                        Event: {{ formatBellDate(r.event_date) }}
                      </p>
                    </div>
                    <span class="text-[10px] text-amber-500 font-medium bg-amber-500/10 px-2 py-0.5 rounded-full shrink-0">pending</span>
                  </div>
                </div>
                <div v-if="prStore.pendingList.length > 8" class="px-4 py-2 text-center text-xs text-gray-500">
                  +{{ prStore.pendingList.length - 8 }} more reservations
                </div>
              </div>

              <!-- Footer link -->
              <div class="px-4 py-3 border-t border-gray-700">
                <button
                  @click="goToApprovals"
                  class="w-full flex items-center justify-center gap-2 text-sm text-green-500 hover:text-green-400 font-medium transition"
                >
                  View all pending approvals
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                  </svg>
                </button>
              </div>
            </div>
          </transition>
        </Teleport>
      </div>

      <div v-if="prStore.canApprove()" class="hidden sm:block h-7 w-px bg-gray-700"></div>

      <!-- User dropdown -->
      <div class="relative" ref="dropdownRef">
        <button
          @click="toggleDropdown"
          class="relative flex items-center gap-3 rounded-xl px-3 py-2 hover:bg-gray-800 transition duration-150 group"
        >
          <!-- Notification badge -->
          <span
            v-if="unreadCount > 0"
            class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] px-1 bg-red-500 rounded-full text-white text-[10px] font-medium flex items-center justify-center z-10 shadow"
          >
            {{ unreadCount > 9 ? '9+' : unreadCount }}
          </span>
          <!-- Avatar -->
          <div class="w-11 h-11 rounded-full overflow-hidden bg-green-700 flex items-center justify-center text-white text-base font-semibold shadow shrink-0 ring-2 ring-blue-500/30">
            <img v-if="navAvatarSrc" :src="navAvatarSrc" alt="avatar" class="w-full h-full object-cover" />
            <span v-else>{{ user?.name?.charAt(0)?.toUpperCase() ?? '?' }}</span>
          </div>
          <div class="text-left hidden sm:block">
            <div class="text-sm text-white font-medium leading-tight group-hover:text-green-500 transition">{{ user?.name ?? 'User' }}</div>
            <div class="text-xs text-gray-400 capitalize leading-tight">{{ user?.role?.replace(/_/g, ' ') ?? 'Staff' }}</div>
          </div>
          <svg
            class="w-4 h-4 text-gray-500 group-hover:text-green-500 transition ml-1"
            :class="dropdownOpen ? 'rotate-180' : ''"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>

        <!-- Teleported dropdown -->
        <Teleport to="body">
          <transition name="dropdown">
            <div
              v-if="dropdownOpen"
              ref="dropdownMenuRef"
              :style="userDropdownStyle"
              class="fixed bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl overflow-hidden"
              style="z-index: 9999;"
            >
              <!-- Header -->
              <div class="px-4 py-3 border-b border-gray-700 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full overflow-hidden bg-green-700 flex items-center justify-center text-white text-sm font-semibold shrink-0 ring-2 ring-blue-500/30">
                  <img v-if="navAvatarSrc" :src="navAvatarSrc" alt="avatar" class="w-full h-full object-cover" />
                  <span v-else>{{ user?.name?.charAt(0)?.toUpperCase() ?? '?' }}</span>
                </div>
                <div class="min-w-0">
                  <div class="text-sm text-white font-medium truncate">{{ user?.name ?? 'User' }}</div>
                  <div class="text-xs text-gray-400 capitalize truncate">{{ user?.role?.replace(/_/g, ' ') ?? 'Staff' }}</div>
                </div>
              </div>

              <!-- Profile -->
              <button
                @click="goToProfile"
                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-800 hover:text-white transition"
              >
                <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profile
              </button>

              <div class="h-px bg-gray-700 mx-3"></div>

              <!-- Logout -->
              <button
                @click="handleLogout"
                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-400 hover:bg-red-600/20 hover:text-red-300 transition"
              >
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
              </button>
            </div>
          </transition>
        </Teleport>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed, onMounted, onBeforeUnmount, ref } from 'vue'
import { useRouter } from 'vue-router'

defineEmits(['toggle-sidebar'])
import { useAuthStore } from '../stores/auth'
import { useLeavesStore } from '../stores/leaves'
import { usePendingReservationsStore } from '../stores/pendingReservations'

const router      = useRouter()
const authStore   = useAuthStore()
const leavesStore = useLeavesStore()
const prStore     = usePendingReservationsStore()

const user        = computed(() => authStore.user)
const unreadCount = computed(() => leavesStore.unreadRejected.length)

const dropdownOpen  = ref(false)
const dropdownRef     = ref(null)
const dropdownMenuRef = ref(null)
const dropdownTop   = ref(0)
const dropdownRight = ref(0)
const navAvatarSrc  = ref(null)

// Bell dropdown position refs
const bellRef           = ref(null)
const bellDropdownRef   = ref(null)
const bellDropdownTop  = ref(0)
const bellDropdownRight = ref(0)
const isMobileBell     = ref(false)
const isMobileUser     = ref(false)

// Responsive dropdown styles
const MOBILE_BREAKPOINT = 640
const MOBILE_PANEL_MARGIN = 8

const bellDropdownStyle = computed(() => {
  if (isMobileBell.value) {
    // Full-width panel on mobile: 8px from each edge
    return {
      top: bellDropdownTop.value + 'px',
      left: MOBILE_PANEL_MARGIN + 'px',
      right: MOBILE_PANEL_MARGIN + 'px',
      width: 'auto',
    }
  }
  return {
    top: bellDropdownTop.value + 'px',
    right: bellDropdownRight.value + 'px',
    width: '320px',
  }
})

const userDropdownStyle = computed(() => {
  if (isMobileUser.value) {
    // Full-width panel on mobile
    return {
      top: dropdownTop.value + 'px',
      left: MOBILE_PANEL_MARGIN + 'px',
      right: MOBILE_PANEL_MARGIN + 'px',
      width: 'auto',
    }
  }
  return {
    top: dropdownTop.value + 'px',
    right: dropdownRight.value + 'px',
    width: '224px',
  }
})

const formatBellDate = (d) =>
  d ? new Date(d).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' }) : ''

const toggleBell = () => {
  if (!prStore.bellOpen && bellRef.value) {
    const rect = bellRef.value.getBoundingClientRect()
    isMobileBell.value = window.innerWidth < MOBILE_BREAKPOINT
    bellDropdownTop.value   = rect.bottom + 8
    bellDropdownRight.value = Math.max(MOBILE_PANEL_MARGIN, window.innerWidth - rect.right)
  }
  prStore.toggleBell()
}

const goToApprovals = () => {
  prStore.closeBell()
  router.push('/reservations/approvals')
}

const loadNavAvatar = () => {
  // Prefer server-stored avatar from user object, then fall back to localStorage
  const serverAvatar = authStore.user?.avatar
  if (serverAvatar) {
    const _apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
    const _base = _apiUrl.startsWith('http') ? _apiUrl.replace(/\/api$/, '') : _apiUrl
    navAvatarSrc.value = serverAvatar.startsWith('http') ? serverAvatar : `${_base}${serverAvatar}`
    return
  }
  const id = authStore.user?.id ?? 'guest'
  navAvatarSrc.value = localStorage.getItem(`avatar_${id}`) || null
}

const toggleDropdown = () => {
  if (!dropdownOpen.value && dropdownRef.value) {
    const rect = dropdownRef.value.getBoundingClientRect()
    isMobileUser.value  = window.innerWidth < MOBILE_BREAKPOINT
    dropdownTop.value   = rect.bottom + 8
    dropdownRight.value = Math.max(MOBILE_PANEL_MARGIN, window.innerWidth - rect.right)
  }
  dropdownOpen.value = !dropdownOpen.value
}

const goToProfile = () => {
  dropdownOpen.value = false
  router.push('/profile')
}

const handleLogout = async () => {
  dropdownOpen.value = false
  await authStore.logout()
  router.push('/login')
}

const handleClickOutside = (e) => {
  if (
    dropdownRef.value && !dropdownRef.value.contains(e.target) &&
    dropdownMenuRef.value && !dropdownMenuRef.value.contains(e.target)
  ) {
    dropdownOpen.value = false
  }
  if (
    bellRef.value && !bellRef.value.contains(e.target) &&
    bellDropdownRef.value && !bellDropdownRef.value.contains(e.target)
  ) {
    prStore.closeBell()
  }
}

const onAvatarUpdated = (e) => { navAvatarSrc.value = e.detail }

onMounted(() => {
  if (authStore.isAuthenticated) {
    leavesStore.fetchMyLeaves()
    prStore.startPolling(30000)
  }
  loadNavAvatar()
  window.addEventListener('avatar-updated', onAvatarUpdated)
  document.addEventListener('mousedown', handleClickOutside)
})

onBeforeUnmount(() => {
  prStore.stopPolling()
  window.removeEventListener('avatar-updated', onAvatarUpdated)
  document.removeEventListener('mousedown', handleClickOutside)
})
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>
