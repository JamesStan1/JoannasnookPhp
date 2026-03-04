<template>
  <div class="bg-gray-50 min-h-full">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
      <div class="p-4 lg:p-8 max-w-5xl mx-auto flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl lg:text-4xl font-light text-gray-900 tracking-tight">My Leave History</h1>
          <p class="text-gray-500 font-light mt-1">All your leave requests and their status</p>
        </div>
        <router-link
          to="/staff/leave/request"
          class="flex items-center gap-2 bg-green-800 hover:bg-green-900 text-white text-sm font-light px-5 py-2.5 rounded-lg transition"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          New Request
        </router-link>
      </div>
    </div>

    <div class="p-4 lg:p-8 max-w-5xl mx-auto space-y-6">

      <!-- Stats row -->
      <div class="grid grid-cols-4 gap-4">
        <div v-for="stat in stats" :key="stat.label" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
          <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0" :class="stat.bg">
            <span class="text-lg font-light" :class="stat.color">{{ stat.count }}</span>
          </div>
          <div>
            <p class="text-xs text-gray-400 font-light">{{ stat.label }}</p>
            <p class="text-sm font-medium" :class="stat.color">{{ stat.days }} days</p>
          </div>
        </div>
      </div>

      <!-- Filter tabs -->
      <div class="flex items-center gap-2">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          @click="activeTab = tab.value"
          class="px-4 py-1.5 rounded-full text-sm font-light transition"
          :class="activeTab === tab.value
            ? 'bg-green-800 text-white'
            : 'bg-white border border-gray-200 text-gray-600 hover:border-green-400'"
        >
          {{ tab.label }}
          <span v-if="tab.count !== undefined" class="ml-1.5 text-xs opacity-75">({{ tab.count }})</span>
        </button>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div v-if="loading" class="flex items-center justify-center py-20">
          <div class="w-7 h-7 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        </div>

        <div v-else-if="filteredLeaves.length === 0" class="flex flex-col items-center justify-center py-20 text-gray-400">
          <svg class="w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          <p class="text-sm font-light">No {{ activeTab !== 'all' ? activeTab : '' }} leave requests found</p>
          <router-link to="/staff/leave/request" class="mt-3 text-xs text-amber-600 hover:underline">Submit a request ?</router-link>
        </div>

        <div v-else class="overflow-x-auto -mx-px">

        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Period</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Days</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Reason</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Submitted</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="leave in filteredLeaves"
              :key="leave.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-6 py-4">
                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium capitalize" :class="typeBadge(leave.leave_type)">
                  {{ leave.leave_type?.replace(/_/g, ' ') }}
                </span>
              </td>
              <td class="px-6 py-4">
                <p class="text-sm font-light text-gray-900">{{ formatDate(leave.start_date) }}</p>
                <p class="text-xs text-gray-400 font-light">to {{ formatDate(leave.end_date) }}</p>
              </td>
              <td class="px-6 py-4">
                <span class="text-sm font-light text-gray-700">{{ leave.number_of_days }}d</span>
              </td>
              <td class="px-6 py-4 max-w-xs">
                <p class="text-sm font-light text-gray-600 truncate">{{ leave.reason || '—' }}</p>
              </td>
              <td class="px-6 py-4">
                <div>
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium" :class="statusBadge(leave.status)">
                    <span class="w-1.5 h-1.5 rounded-full" :class="statusDot(leave.status)"></span>
                    {{ leave.status }}
                  </span>
                  <!-- Rejection reason -->
                  <div v-if="leave.status === 'rejected' && leave.rejection_reason" class="mt-1.5 flex items-start gap-1.5">
                    <svg class="w-3.5 h-3.5 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-red-500 font-light italic">{{ leave.rejection_reason }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-xs text-gray-400 font-light">{{ formatDate(leave.created_at) }}</td>
            </tr>
          </tbody>
        </table></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'

const leaves = ref([])
const loading = ref(true)
const activeTab = ref('all')

const tabs = computed(() => [
  { label: 'All',      value: 'all',      count: leaves.value.length },
  { label: 'Pending',  value: 'pending',  count: leaves.value.filter(l => l.status === 'pending').length },
  { label: 'Approved', value: 'approved', count: leaves.value.filter(l => l.status === 'approved').length },
  { label: 'Rejected', value: 'rejected', count: leaves.value.filter(l => l.status === 'rejected').length },
])

const filteredLeaves = computed(() => {
  if (activeTab.value === 'all') return leaves.value
  return leaves.value.filter(l => l.status === activeTab.value)
})

const stats = computed(() => {
  const sumDays = (status) => leaves.value
    .filter(l => l.status === status)
    .reduce((s, l) => s + Number(l.number_of_days), 0)

  return [
    { label: 'Total Requests', count: leaves.value.length, days: leaves.value.reduce((s,l)=>s+Number(l.number_of_days),0), color: 'text-gray-700', bg: 'bg-gray-100' },
    { label: 'Pending',  count: leaves.value.filter(l=>l.status==='pending').length,  days: sumDays('pending'),  color: 'text-yellow-600', bg: 'bg-yellow-50' },
    { label: 'Approved', count: leaves.value.filter(l=>l.status==='approved').length, days: sumDays('approved'), color: 'text-green-600',  bg: 'bg-green-50'  },
    { label: 'Rejected', count: leaves.value.filter(l=>l.status==='rejected').length, days: sumDays('rejected'), color: 'text-red-500',   bg: 'bg-red-50'    },
  ]
})

const formatDate = (d) => {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' })
}

const typeBadge = (type) => ({
  sick:       'bg-red-50 text-red-700',
  casual:     'bg-green-50 text-green-800',
  annual:     'bg-green-50 text-green-700',
  emergency:  'bg-orange-50 text-orange-700',
  maternity:  'bg-pink-50 text-pink-700',
  paternity:  'bg-purple-50 text-purple-700',
}[type] ?? 'bg-gray-100 text-gray-600')

const statusBadge = (s) => ({
  pending:  'bg-yellow-50 text-yellow-700',
  approved: 'bg-green-50 text-green-700',
  rejected: 'bg-red-50 text-red-600',
}[s] ?? 'bg-gray-100 text-gray-600')

const statusDot = (s) => ({
  pending:  'bg-yellow-400',
  approved: 'bg-green-500',
  rejected: 'bg-red-400',
}[s] ?? 'bg-gray-400')

const load = async () => {
  loading.value = true
  try {
    const res = await api.get('/staff/leaves/my')
    leaves.value = res.data.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>
