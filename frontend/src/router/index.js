import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

// ── Role-based access configuration ─────────────────────────────────────────
// null  = unrestricted (admin/manager)
// array = allowed path prefixes for that role
const ALLOWED_PREFIXES = {
  admin:        null,
  manager:      null,
  chef:         ['/chef', '/staff/reports', '/staff/leave'],
  housekeeping: ['/housekeeping', '/staff/reports', '/staff/leave'],
  security:     ['/rooms', '/staff/reports', '/staff/leave'],
  front_desk:   ['/reservations', '/rooms', '/pos', '/inventory', '/events', '/staff/reports', '/staff/leave'],
}

// Paths every authenticated user may always visit
const UNIVERSAL_PATHS = ['/profile', '/dashboard', '/unauthorized']

const ROLE_HOME = {
  admin:        '/dashboard',
  manager:      '/dashboard',
  chef:         '/chef/orders',
  housekeeping: '/housekeeping',
  security:     '/rooms',
  front_desk:   '/reservations',
  guest:        '/',
}

function isAllowed(role, path) {
  if (!role || role === 'admin' || role === 'manager') return true
  if (UNIVERSAL_PATHS.some(p => path === p || path.startsWith(p + '/'))) return true
  const prefixes = ALLOWED_PREFIXES[role]
  if (!prefixes) return true
  return prefixes.some(p => path === p || path.startsWith(p + '/'))
}

const routes = [
  { path: '/', name: 'Homepage', component: () => import('../pages/Homepage.vue') },
  { path: '/login', name: 'Login', component: () => import('../pages/Login.vue') },
  { path: '/register', name: 'Register', component: () => import('../pages/Register.vue') },

  // Dashboard
  { path: '/dashboard', name: 'Dashboard', component: () => import('../pages/Dashboard.vue'), meta: { requiresAuth: true } },

  // ── Staff Management ──
  { path: '/staff', name: 'Staff', component: () => import('../pages/Staff.vue'), meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/staff/attendance', name: 'Attendance', component: () => import('../pages/Attendance.vue'), meta: { requiresAuth: true, requiresAdmin: true, title: 'Attendance' } },
  { path: '/staff/payroll', name: 'Payroll', component: () => import('../pages/Payroll.vue'), meta: { requiresAuth: true, requiresAdmin: true, title: 'Payroll' } },
  { path: '/staff/reports', name: 'StaffReports', component: () => import('../pages/StaffReports.vue'), meta: { requiresAuth: true, title: 'Staff Reports' } },
  { path: '/staff/archived', name: 'StaffArchived', component: () => import('../pages/ArchivedStaff.vue'), meta: { requiresAuth: true, requiresAdmin: true, title: 'Archived Staff' } },
  { path: '/staff/leave/request', name: 'LeaveRequest', component: () => import('../pages/LeaveRequest.vue'), meta: { requiresAuth: true, title: 'Request Leave' } },
  { path: '/staff/leave/history', name: 'LeaveHistory', component: () => import('../pages/LeaveHistory.vue'), meta: { requiresAuth: true, title: 'Leave History' } },
  { path: '/staff/leave/approvals', name: 'LeaveApprovals', component: () => import('../pages/LeaveApprovals.vue'), meta: { requiresAuth: true, requiresAdmin: true, title: 'Leave Approvals' } },

  // ── Reservation Management ──
  { path: '/reservations', name: 'Reservations', component: () => import('../pages/ReservationHistory.vue'), meta: { requiresAuth: true } },
  { path: '/reservations/new', name: 'NewReservation', component: () => import('../pages/NewReservation.vue'), meta: { requiresAuth: true } },
  { path: '/reservations/approvals', name: 'ReservationApprovals', component: () => import('../pages/ReservationApprovals.vue'), meta: { requiresAuth: true, requiresAdmin: true, title: 'Reservation Approval' } },

  // Room
  { path: '/rooms', name: 'Rooms', component: () => import('../pages/Reservations.vue'), meta: { requiresAuth: true } },
  { path: '/rooms/edit', name: 'EditRoom', component: () => import('../pages/Rooms.vue'), meta: { requiresAuth: true, requiresAdmin: true, title: 'Edit Room' } },
  { path: '/rooms/archived', name: 'RoomsArchived', component: () => import('../pages/RoomsArchived.vue'), meta: { requiresAuth: true, requiresAdmin: true, title: 'Archived Rooms' } },

  // Event
  { path: '/events', name: 'Events', component: () => import('../pages/Events.vue'), meta: { requiresAuth: true, title: 'Event Management' } },
  { path: '/events/edit', name: 'EditEvent', component: () => import('../pages/EventPackages.vue'), meta: { requiresAuth: true, requiresAdmin: true, title: 'Manage Event Packages' } },
  { path: '/events/archived', name: 'EventsArchived', component: () => import('../pages/EventsArchived.vue'), meta: { requiresAuth: true, requiresAdmin: true, title: 'Archived Events' } },

  // ── Housekeeping ──
  { path: '/housekeeping', name: 'Housekeeping', component: () => import('../pages/Housekeeping.vue'), meta: { requiresAuth: true } },

  // ── Inventory Management ──
  { path: '/inventory', name: 'Inventory', component: () => import('../pages/Inventory.vue'), meta: { requiresAuth: true } },
  { path: '/inventory/archived', name: 'InventoryArchived', component: () => import('../pages/InventoryArchived.vue'), meta: { requiresAuth: true, requiresAdmin: true, title: 'Archived Inventory' } },

  // ── POS ──
  { path: '/pos', name: 'POS', component: () => import('../pages/POS.vue'), meta: { requiresAuth: true } },
  { path: '/pos/orders', name: 'OrderHistory', component: () => import('../pages/OrderHistory.vue'), meta: { requiresAuth: true, title: 'Order History' } },
  { path: '/pos/archived', name: 'POSArchived', component: () => import('../pages/ArchivedCafe.vue'), meta: { requiresAuth: true, requiresAdmin: true, title: 'Archived Café Menu' } },
  { path: '/restaurant', name: 'Restaurant', component: () => import('../pages/Restaurant.vue'), meta: { requiresAuth: true } },

  // ── Chef Dashboard ──
  { path: '/chef/orders', name: 'ChefOrders', component: () => import('../pages/ChefDashboard.vue'), meta: { requiresAuth: true, title: 'Chef Dashboard' } },

  // ── Bills ──
  { path: '/billing', name: 'Billing', component: () => import('../pages/Billing.vue'), meta: { requiresAuth: true } },

  // ── Settings ──
  { path: '/settings', name: 'Settings', component: () => import('../pages/Admin.vue'), meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/settings/export', name: 'Export', component: () => import('../pages/Export.vue'), meta: { requiresAuth: true, requiresAdmin: true, title: 'Export Data' } },
  { path: '/admin', name: 'Admin', component: () => import('../pages/Admin.vue'), meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/profile', name: 'Profile', component: () => import('../pages/Profile.vue'), meta: { requiresAuth: true, title: 'My Profile' } },

  // ── Access Denied ──
  { path: '/unauthorized', name: 'Unauthorized', component: () => import('../pages/Unauthorized.vue') },
  { path: '/:pathMatch(.*)*', redirect: '/unauthorized' },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // Verify token with the server exactly once per session (on first navigation)
  if (!authStore.tokenVerified) {
    await authStore.verifyToken()
  }

  // 1. Not logged in — redirect to login for protected routes
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
    return
  }

  // 2. Already logged in but visiting login/register/homepage — send to role home
  if (authStore.isAuthenticated && (to.path === '/login' || to.path === '/register' || to.path === '/')) {
    next(authStore.roleHomePath)
    return
  }

  // 3. Admin-only page guard — 'admin' and 'manager' may access these routes
  if (to.meta.requiresAdmin) {
    const role = authStore.userRole
    if (role !== 'admin' && role !== 'manager') {
      next('/unauthorized')
      return
    }
  }

  // 4. Role-based access check for authenticated users on protected routes
  if (authStore.isAuthenticated && to.meta.requiresAuth) {
    const role = authStore.userRole
    if (!isAllowed(role, to.path)) {
      next(ROLE_HOME[role] || '/unauthorized')
      return
    }
  }

  next()
})

export default router
