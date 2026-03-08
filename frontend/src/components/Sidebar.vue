<template>
  <aside
    class="fixed top-20 left-0 w-72 max-w-[85vw] bg-gray-950 border-r border-gray-800 h-[calc(100vh-80px)] overflow-y-auto z-40 flex flex-col transition-transform duration-300 ease-in-out"
    :class="props.open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
  >
    <!-- Mobile close button -->
    <div class="lg:hidden flex items-center justify-between px-4 py-3 border-b border-gray-800">
      <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Menu</span>
      <button
        @click="emit('close')"
        class="flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition"
        aria-label="Close menu"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <nav class="flex-1 py-3 px-2 space-y-0.5">

      <!-- Dashboard -->
      <router-link v-if="show.dashboard"
        to="/dashboard"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-light transition-all duration-150"
        :class="isActive('/dashboard') ? 'bg-green-800 text-white shadow-sm' : 'text-gray-300 hover:bg-gray-800 hover:text-white'"
      >
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
      </router-link>

      <!-- -- STAFF MANAGEMENT -- -->
      <div v-if="show.staff">
        <button @click="toggle('staff')" class="section-btn" :class="{ 'section-open': sections.staff }">
          <span class="flex items-center gap-3">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Staff Management
          </span>
          <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': sections.staff }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <transition name="slide">
          <div v-show="sections.staff" class="sub-section">
            <router-link v-if="show.staffList" to="/staff" class="sub-link" :class="{ 'sub-active': isActive('/staff', true) }">Staff List</router-link>
            <router-link v-if="show.staffAttendance" to="/staff/attendance" class="sub-link" :class="{ 'sub-active': isActive('/staff/attendance', true) }">Attendance</router-link>
            <router-link v-if="show.staffPayroll" to="/staff/payroll" class="sub-link" :class="{ 'sub-active': isActive('/staff/payroll', true) }">Payroll</router-link>
            <router-link v-if="show.staffReports" to="/staff/reports" class="sub-link" :class="{ 'sub-active': isActive('/staff/reports', true) }">Reports</router-link>
            <router-link v-if="show.staffArchived" to="/staff/archived" class="sub-link" :class="{ 'sub-active': isActive('/staff/archived', true) }">Archived</router-link>
            <!-- Leave nested -->
            <div>
              <button @click.stop="toggle('leave')" class="nested-btn" :class="{ 'nested-open': sections.leave }">
                Leave
                  <svg class="w-3 h-3 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': sections.leave }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
              <transition name="slide">
                <div v-show="sections.leave" class="nested-section">
                  <router-link v-if="show.staffLeaveRequest" to="/staff/leave/request" class="nested-link" :class="{ 'sub-active': isActive('/staff/leave/request', true) }">Request Leave</router-link>
                  <router-link v-if="show.staffLeaveHistory" to="/staff/leave/history" class="nested-link" :class="{ 'sub-active': isActive('/staff/leave/history', true) }">Leave History</router-link>
                  <router-link v-if="show.staffLeaveApprovals" to="/staff/leave/approvals" class="nested-link" :class="{ 'sub-active': isActive('/staff/leave/approvals', true) }">Leave Approvals</router-link>
                </div>
              </transition>
            </div>
          </div>
        </transition>
      </div>

      <!-- -- RESERVATION MANAGEMENT -- -->
      <div v-if="show.reservation">
        <button @click="toggle('reservations')" class="section-btn" :class="{ 'section-open': sections.reservations }">
          <span class="flex items-center gap-3">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Reservation Management
          </span>
          <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': sections.reservations }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <transition name="slide">
          <div v-show="sections.reservations" class="sub-section">
            <!-- Room nested -->
            <div>
              <button @click.stop="toggle('rooms')" class="nested-btn" :class="{ 'nested-open': sections.rooms }">
                Room
                  <svg class="w-3 h-3 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': sections.rooms }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
              <transition name="slide">
                <div v-show="sections.rooms" class="nested-section">
                  <router-link v-if="show.roomManagement" to="/rooms" class="nested-link" :class="{ 'sub-active': isActive('/rooms', true) }">Management</router-link>
                  <router-link v-if="show.roomEdit" to="/rooms/edit" class="nested-link" :class="{ 'sub-active': isActive('/rooms/edit', true) }">Edit Room</router-link>
                  <router-link v-if="show.roomArchived" to="/rooms/archived" class="nested-link" :class="{ 'sub-active': isActive('/rooms/archived', true) }">Archived</router-link>
                </div>
              </transition>
            </div>
            <!-- Event nested -->
            <div v-if="show.events">
              <button @click.stop="toggle('events')" class="nested-btn" :class="{ 'nested-open': sections.events }">
                Event
                  <svg class="w-3 h-3 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': sections.events }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
              <transition name="slide">
                <div v-show="sections.events" class="nested-section">
                  <router-link to="/events" class="nested-link" :class="{ 'sub-active': isActive('/events', true) }">Management</router-link>
                  <router-link v-if="show.roomEdit" to="/events/edit" class="nested-link" :class="{ 'sub-active': isActive('/events/edit', true) }">Edit Package</router-link>
                  <router-link v-if="show.roomEdit" to="/events/archived" class="nested-link" :class="{ 'sub-active': isActive('/events/archived', true) }">Archived</router-link>
                </div>
              </transition>
            </div>
            <router-link v-if="show.reservationHistory" to="/reservations" class="sub-link" :class="{ 'sub-active': isActive('/reservations', true) }">Reservation History</router-link>
            <router-link v-if="show.reservationApproval" to="/reservations/approvals" class="sub-link" :class="{ 'sub-active': isActive('/reservations/approvals', true) }">Reservation Approval</router-link>
          </div>
        </transition>
      </div>

      <!-- -- HOUSEKEEPING -- -->
      <div v-if="show.housekeeping">
        <button @click="toggle('housekeeping')" class="section-btn" :class="{ 'section-open': sections.housekeeping }">
          <span class="flex items-center gap-3">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Housekeeping
          </span>
          <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': sections.housekeeping }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <transition name="slide">
          <div v-show="sections.housekeeping" class="sub-section">
            <router-link to="/housekeeping" class="sub-link" :class="{ 'sub-active': isActive('/housekeeping', true) }">Tasks</router-link>
          </div>
        </transition>
      </div>

      <!-- -- INVENTORY MANAGEMENT -- -->
      <div v-if="show.inventory">
        <button @click="toggle('inventory')" class="section-btn" :class="{ 'section-open': sections.inventory }">
          <span class="flex items-center gap-3">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Inventory Management
          </span>
          <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': sections.inventory }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <transition name="slide">
          <div v-show="sections.inventory" class="sub-section">
            <router-link to="/inventory" class="sub-link" :class="{ 'sub-active': isActive('/inventory', true) }">Management</router-link>
            <router-link to="/inventory/archived" class="sub-link" :class="{ 'sub-active': isActive('/inventory/archived', true) }">Archived</router-link>
          </div>
        </transition>
      </div>

      <!-- -- POS -- -->
      <div v-if="show.pos">
        <button @click="toggle('pos')" class="section-btn" :class="{ 'section-open': sections.pos }">
          <span class="flex items-center gap-3">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            POS
          </span>
          <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': sections.pos }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <transition name="slide">
          <div v-show="sections.pos" class="sub-section">
            <router-link v-if="show.posPage" to="/pos" class="sub-link" :class="{ 'sub-active': isActive('/pos', true) }">POS</router-link>
            <router-link v-if="show.posOrders" to="/pos/orders" class="sub-link" :class="{ 'sub-active': isActive('/pos/orders', true) }">Order History</router-link>
            <router-link v-if="show.restaurantMenu" to="/restaurant" class="sub-link" :class="{ 'sub-active': isActive('/restaurant', true) }">Café Menu</router-link>
            <router-link v-if="show.chefDashboard" to="/chef/orders" class="sub-link" :class="{ 'sub-active': isActive('/chef/orders', true) }">Chef Dashboard</router-link>
            <router-link v-if="show.posArchived" to="/pos/archived" class="sub-link" :class="{ 'sub-active': isActive('/pos/archived', true) }">Archived</router-link>
          </div>
        </transition>
      </div>

      <!-- -- BILLS -- -->
      <div v-if="show.bills">
        <button @click="toggle('bills')" class="section-btn" :class="{ 'section-open': sections.bills }">
          <span class="flex items-center gap-3">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Bills
          </span>
          <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': sections.bills }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <transition name="slide">
          <div v-show="sections.bills" class="sub-section">
            <router-link to="/billing" class="sub-link" :class="{ 'sub-active': isActive('/billing', true) }">Bills &amp; Invoices</router-link>
          </div>
        </transition>
      </div>

      <!-- -- SETTINGS -- -->
      <div v-if="show.settings">
        <button @click="toggle('settings')" class="section-btn" :class="{ 'section-open': sections.settings }">
          <span class="flex items-center gap-3">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Settings
          </span>
          <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': sections.settings }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <transition name="slide">
          <div v-show="sections.settings" class="sub-section">
            <router-link to="/settings" class="sub-link" :class="{ 'sub-active': isActive('/settings', true) }">General</router-link>
            <router-link to="/settings/export" class="sub-link" :class="{ 'sub-active': isActive('/settings/export', true) }">Export</router-link>
          </div>
        </transition>
      </div>

    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-gray-800">
      <p class="text-xs text-gray-600 font-light text-center">Joanna's Nook Bed & Breakfast &copy; 2026</p>
    </div>
  </aside>
</template>

<script setup>
import { reactive, watch, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
const props = defineProps({ open: { type: Boolean, default: false } })
const emit  = defineEmits(['close'])

const route     = useRoute()
const authStore = useAuthStore()
// Renamed from `open` to `sections` to avoid shadowing the `open` prop
const sections = reactive({
  staff: false, leave: false,
  reservations: false, rooms: false, events: false,
  inventory: false, housekeeping: false, pos: false, bills: false, settings: false,
})

const toggle = (key) => { sections[key] = !sections[key] }

const isActive = (path, exact = false) => {
  if (exact) return route.path === path
  return route.path.startsWith(path)
}

// -- Role helpers --------------------------------------------------------------
const role        = computed(() => authStore.userRole)
const isAdmin     = computed(() => role.value === 'admin')
const isManager   = computed(() => role.value === 'manager')
const isAdminOrManager = computed(() => isAdmin.value || isManager.value)
const isChef      = computed(() => role.value === 'chef')
const isSecurity  = computed(() => role.value === 'security')
const isHouseRole = computed(() => role.value === 'housekeeping')
const isFrontDesk = computed(() => role.value === 'front_desk')

// Section / item visibility
const show = computed(() => ({
  dashboard:           isAdminOrManager.value || isFrontDesk.value,
  // Staff Management: visible to all authenticated users
  staff:               !!role.value,
  // Admin/manager see all staff sub-items; others only see Reports and Leave
  staffList:           isAdminOrManager.value,
  staffAttendance:     isAdminOrManager.value,
  staffPayroll:        isAdminOrManager.value,
  staffReports:        !!role.value,
  staffArchived:       isAdminOrManager.value,
  staffLeaveRequest:   !!role.value,
  staffLeaveHistory:   !!role.value,
  staffLeaveApprovals: isAdminOrManager.value,
  reservation:         isAdminOrManager.value || isFrontDesk.value || isSecurity.value,
  roomManagement:      isAdminOrManager.value || isFrontDesk.value || isSecurity.value,
  roomEdit:            isAdminOrManager.value,
  roomArchived:        isAdminOrManager.value,
  events:              isAdminOrManager.value || isFrontDesk.value,
  reservationHistory:  isAdminOrManager.value || isFrontDesk.value,
  reservationApproval: isAdminOrManager.value || isFrontDesk.value,
  housekeeping:        isAdminOrManager.value || isHouseRole.value,
  inventory:           isAdminOrManager.value || isFrontDesk.value,
  pos:                 isAdminOrManager.value || isFrontDesk.value || isChef.value,
  posPage:             isAdminOrManager.value || isFrontDesk.value,
  posOrders:           isAdminOrManager.value || isFrontDesk.value,
  restaurantMenu:      isAdminOrManager.value,
  chefDashboard:       isAdminOrManager.value || isChef.value,
  posArchived:         isAdminOrManager.value,
  bills:               isAdminOrManager.value,
  settings:            isAdminOrManager.value,
}))

const autoOpen = (path) => {
  if (path.startsWith('/staff')) {
    sections.staff = true
    if (path.startsWith('/staff/leave')) sections.leave = true
  }
  if (path.startsWith('/reservations') || path.startsWith('/rooms') || path.startsWith('/events')) {
    sections.reservations = true
    if (path.startsWith('/rooms')) sections.rooms = true
    if (path.startsWith('/events')) sections.events = true
  }
  if (path.startsWith('/inventory')) sections.inventory = true
  if (path.startsWith('/housekeeping')) sections.housekeeping = true
  if (path.startsWith('/pos') || path.startsWith('/restaurant') || path.startsWith('/chef')) sections.pos = true
  if (path.startsWith('/billing')) sections.bills = true
  if (path.startsWith('/settings') || path.startsWith('/admin')) sections.settings = true
}

onMounted(() => autoOpen(route.path))
watch(() => route.path, (path) => {
  autoOpen(path)
  emit('close')  // close sidebar on mobile when navigating
})
</script>

<style scoped>
.section-btn {
  @apply w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-light text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-150 cursor-pointer;
}
.section-open {
  @apply bg-gray-800 text-white;
}
.sub-section {
  @apply ml-3 mt-0.5 pl-3 border-l border-gray-800 pb-1 space-y-0.5;
}
.sub-link {
  @apply block px-3 py-2 rounded-md text-xs font-light text-gray-400 hover:bg-gray-800 hover:text-white transition-all duration-150;
}
.sub-active {
  @apply bg-green-800/20 text-green-500 !important;
}
.nested-btn {
  @apply w-full flex items-center justify-between px-3 py-2 rounded-md text-xs font-light text-gray-400 hover:bg-gray-800 hover:text-white transition-all duration-150 cursor-pointer;
}
.nested-open {
  @apply text-white;
}
.nested-section {
  @apply ml-3 pl-2 border-l border-gray-800/60 mt-0.5 pb-0.5 space-y-0.5;
}
.nested-link {
  @apply block px-3 py-1.5 rounded-md text-xs font-light text-gray-500 hover:bg-gray-800 hover:text-white transition-all duration-150;
}
/* Slide animation */
.slide-enter-active, .slide-leave-active {
  transition: all 0.2s ease;
  overflow: hidden;
}
.slide-enter-from, .slide-leave-to {
  max-height: 0;
  opacity: 0;
  transform: translateY(-4px);
}
.slide-enter-to, .slide-leave-from {
  max-height: 600px;
  opacity: 1;
  transform: translateY(0);
}
</style>
