<template>
  <div class="bg-gray-50 min-h-full">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
      <div class="p-4 lg:p-8 max-w-7xl mx-auto">
        <h1 class="text-2xl lg:text-4xl font-light text-gray-900 tracking-tight">Leave Approvals</h1>
        <p class="text-gray-500 font-light mt-1">Review and action staff leave requests</p>
      </div>
    </div>

    <div class="p-4 lg:p-8 max-w-7xl mx-auto space-y-6">

      <!-- Summary row -->
      <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex items-center gap-4">
          <div class="w-10 h-10 rounded-full bg-yellow-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <p class="text-xs text-gray-400 font-light uppercase tracking-wide">Pending</p>
            <p class="text-2xl font-light text-yellow-600">{{ pendingCount }}</p>
          </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex items-center gap-4">
          <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <p class="text-xs text-gray-400 font-light uppercase tracking-wide">Approved</p>
            <p class="text-2xl font-light text-green-600">{{ approvedCount }}</p>
          </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex items-center gap-4">
          <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <p class="text-xs text-gray-400 font-light uppercase tracking-wide">Rejected</p>
            <p class="text-2xl font-light text-red-500">{{ rejectedCount }}</p>
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
          <span v-if="tab.count" class="ml-1.5 text-xs opacity-75">({{ tab.count }})</span>
        </button>
        <!-- Search -->
        <div class="relative ml-auto">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
          </svg>
          <input
            v-model="search"
            type="text"
            placeholder="Search staff..."
            class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:border-blue-600 transition"
          />
        </div>
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
          <p class="text-sm font-light">No {{ activeTab !== 'all' ? activeTab : '' }} leave requests</p>
        </div>

        <div v-else class="overflow-x-auto -mx-px">

        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Staff</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Period</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Days</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Reason</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="leave in filteredLeaves"
              :key="leave.id"
              class="hover:bg-gray-50 transition-colors"
              :class="leave.status === 'pending' ? 'border-l-2 border-yellow-400' : ''"
            >
              <!-- Staff -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-medium shrink-0" :style="{ backgroundColor: avatarColor(leave.staff_name) }">
                    {{ leave.staff_name?.charAt(0)?.toUpperCase() }}
                  </div>
                  <div>
                    <p class="text-sm font-light text-gray-900">{{ leave.staff_name }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ leave.staff_role?.replace(/_/g, ' ') }}</p>
                  </div>
                </div>
              </td>
              <!-- Type -->
              <td class="px-6 py-4">
                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium capitalize" :class="typeBadge(leave.leave_type)">
                  {{ leave.leave_type?.replace(/_/g, ' ') }}
                </span>
              </td>
              <!-- Period -->
              <td class="px-6 py-4">
                <p class="text-sm font-light text-gray-900">{{ formatDate(leave.start_date) }}</p>
                <p class="text-xs text-gray-400">to {{ formatDate(leave.end_date) }}</p>
              </td>
              <!-- Days -->
              <td class="px-6 py-4 text-sm font-light text-gray-700">{{ leave.number_of_days }}d</td>
              <!-- Reason -->
              <td class="px-6 py-4 max-w-48">
                <p class="text-sm font-light text-gray-600 line-clamp-2">{{ leave.reason || '—' }}</p>
                <!-- Rejection reason -->
                <p v-if="leave.status === 'rejected' && leave.rejection_reason" class="text-xs text-red-500 italic mt-1">
                  Rejected: {{ leave.rejection_reason }}
                </p>
              </td>
              <!-- Status -->
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium" :class="statusBadge(leave.status)">
                  <span class="w-1.5 h-1.5 rounded-full" :class="statusDot(leave.status)"></span>
                  {{ leave.status }}
                </span>
              </td>
              <!-- Actions -->
              <td class="px-6 py-4">
                <div v-if="leave.status === 'pending'" class="flex items-center gap-2">
                  <button
                    @click="approve(leave)"
                    :disabled="actionId === leave.id"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 text-xs font-medium rounded-lg transition disabled:opacity-50"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ actionId === leave.id ? '...' : 'Approve' }}
                  </button>
                  <button
                    @click="openReject(leave)"
                    :disabled="actionId === leave.id"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-lg transition disabled:opacity-50"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reject
                  </button>
                </div>
                <span v-else class="text-xs text-gray-400 font-light">—</span>
              </td>
            </tr>
          </tbody>
        </table></div>
      </div>
    </div>

    <!-- Reject modal -->
    <transition name="fade">
      <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-4 lg:p-8">
          <h3 class="text-xl font-light text-gray-900 mb-1">Reject Leave Request</h3>
          <p class="text-sm text-gray-500 font-light mb-5">
            Rejecting <strong class="text-gray-700">{{ rejectTarget?.staff_name }}</strong>'s
            {{ rejectTarget?.leave_type }} leave ({{ rejectTarget?.number_of_days }} days).
            The staff will be notified.
          </p>
          <div class="mb-5">
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Reason for Rejection <span class="text-gray-400 font-light normal-case">(optional)</span></label>
            <textarea
              v-model="rejectReason"
              rows="3"
              placeholder="Briefly explain why the leave is being rejected..."
              class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400 transition resize-none"
            ></textarea>
          </div>
          <div class="flex gap-3">
            <button
              @click="confirmReject"
              :disabled="actionId !== null"
              class="flex-1 bg-red-600 hover:bg-red-700 disabled:bg-gray-300 text-white font-light py-2.5 rounded-lg transition text-sm"
            >
              {{ actionId !== null ? 'Rejecting...' : 'Confirm Rejection' }}
            </button>
            <button @click="showRejectModal = false" class="flex-1 border border-gray-200 hover:bg-gray-50 text-gray-700 font-light py-2.5 rounded-lg transition text-sm">
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
        {{ toast.message }}
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import api from '../services/api'

const leaves   = ref([])
const loading  = ref(true)
const activeTab = ref('pending')
const search   = ref('')
const actionId = ref(null)
const showRejectModal = ref(false)
const rejectTarget = ref(null)
const rejectReason = ref('')
const toast = reactive({ show: false, message: '', type: 'success' })

const tabs = computed(() => [
  { label: 'Pending',  value: 'pending',  count: leaves.value.filter(l=>l.status==='pending').length },
  { label: 'Approved', value: 'approved', count: leaves.value.filter(l=>l.status==='approved').length },
  { label: 'Rejected', value: 'rejected', count: leaves.value.filter(l=>l.status==='rejected').length },
  { label: 'All',      value: 'all' },
])

const pendingCount  = computed(() => leaves.value.filter(l=>l.status==='pending').length)
const approvedCount = computed(() => leaves.value.filter(l=>l.status==='approved').length)
const rejectedCount = computed(() => leaves.value.filter(l=>l.status==='rejected').length)

const filteredLeaves = computed(() => {
  let list = activeTab.value === 'all' ? leaves.value : leaves.value.filter(l=>l.status===activeTab.value)
  if (search.value) {
    const q = search.value.toLowerCase()
    list = list.filter(l => l.staff_name?.toLowerCase().includes(q) || l.staff_role?.includes(q))
  }
  return list
})

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-PH', { month:'short', day:'numeric', year:'numeric' }) : '—'
const avatarColors = ['#b45309','#0369a1','#4f46e5','#059669','#dc2626','#9333ea','#0891b2']
const avatarColor  = (name) => avatarColors[(name?.charCodeAt(0)??0) % avatarColors.length]
const typeBadge    = (t) => ({ sick:'bg-red-50 text-red-700', casual:'bg-green-50 text-green-800', annual:'bg-green-50 text-green-700', emergency:'bg-orange-50 text-orange-700', maternity:'bg-pink-50 text-pink-700', paternity:'bg-purple-50 text-purple-700' }[t] ?? 'bg-gray-100 text-gray-600')
const statusBadge  = (s) => ({ pending:'bg-yellow-50 text-yellow-700', approved:'bg-green-50 text-green-700', rejected:'bg-red-50 text-red-600' }[s] ?? 'bg-gray-100 text-gray-600')
const statusDot    = (s) => ({ pending:'bg-yellow-400', approved:'bg-green-500', rejected:'bg-red-400' }[s] ?? 'bg-gray-400')

const showToast = (message, type = 'success') => {
  toast.message = message; toast.type = type; toast.show = true
  setTimeout(() => { toast.show = false }, 2800)
}

const load = async () => {
  loading.value = true
  try {
    const res = await api.get('/staff/leaves/all')
    leaves.value = res.data.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const approve = async (leave) => {
  actionId.value = leave.id
  try {
    await api.put(`/staff/leaves/${leave.id}/approve`)
    showToast(`${leave.staff_name}'s leave approved`)
    await load()
  } catch (e) {
    showToast(e.response?.data?.message || 'Failed to approve', 'error')
  } finally {
    actionId.value = null
  }
}

const openReject = (leave) => {
  rejectTarget.value = leave
  rejectReason.value = ''
  showRejectModal.value = true
}

const confirmReject = async () => {
  actionId.value = rejectTarget.value.id
  try {
    await api.put(`/staff/leaves/${rejectTarget.value.id}/reject`, { reason: rejectReason.value })
    showToast(`${rejectTarget.value.staff_name}'s leave rejected`)
    showRejectModal.value = false
    await load()
  } catch (e) {
    showToast(e.response?.data?.message || 'Failed to reject', 'error')
  } finally {
    actionId.value = null
  }
}

onMounted(load)
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s ease; }
.slide-down-enter-from { opacity: 0; transform: translateX(-50%) translateY(-12px); }
.slide-down-leave-to { opacity: 0; transform: translateX(-50%) translateY(-8px); }
</style>
