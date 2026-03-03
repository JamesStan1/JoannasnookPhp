<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-3xl shadow-xl max-w-md w-full p-10 text-center">

      <!-- Icon -->
      <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
      </div>

      <h1 class="text-2xl font-bold text-gray-800 mb-2">Access Denied</h1>
      <p class="text-gray-500 text-sm mb-1">You don't have permission to view this page.</p>
      <p class="text-gray-400 text-xs mb-8">
        Your role <span class="font-semibold text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded">{{ roleName }}</span>
        is not authorized to access this area.
      </p>

      <button @click="goHome"
        class="w-full py-3 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-xl transition text-sm shadow-md shadow-blue-200">
        Go to My Dashboard
      </button>

      <button @click="authStore.logout().then(() => router.push('/login'))"
        class="mt-3 w-full py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-medium rounded-xl transition">
        Sign Out
      </button>

    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router    = useRouter()
const authStore = useAuthStore()

const roleName = computed(() => {
  const r = authStore.userRole
  const labels = {
    admin:        'Admin',
    manager:      'Manager',
    chef:         'Chef',
    housekeeping: 'Housekeeping',
    security:     'Security',
    front_desk:   'Front Desk',
    guest:        'Guest',
  }
  return labels[r] || r || 'Unknown'
})

function goHome() {
  router.push(authStore.roleHomePath)
}
</script>
