<template>
  <div class="min-h-screen bg-gray-50 font-light">

    <!-- Header -->
    <div class="bg-white border-b border-gray-100 px-8 py-5">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-normal text-gray-800">Staff Reports</h1>
          <p class="text-sm text-gray-400 mt-0.5">Submit complaints, suggestions, or incidents</p>
        </div>
        <button
          v-if="activeTab !== 'submit'"
          @click="activeTab = 'submit'"
          class="flex items-center gap-2 bg-green-800 text-white text-sm px-4 py-2 rounded-lg hover:bg-green-900 transition"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
          </svg>
          New Report
        </button>
      </div>
    </div>

    <div class="px-8 py-6 max-w-6xl mx-auto">

      <!-- Tabs -->
      <div class="flex gap-1 bg-white border border-gray-100 rounded-xl p-1 mb-6 w-fit">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          @click="activeTab = tab.key"
          :class="[
            'px-4 py-2 rounded-lg text-sm transition',
            activeTab === tab.key
              ? 'bg-green-800 text-white font-normal shadow-sm'
              : 'text-gray-500 hover:text-gray-700'
          ]"
        >
          {{ tab.label }}
          <span
            v-if="tab.key === 'admin' && summary.pending"
            class="ml-1.5 bg-red-100 text-red-600 text-xs px-1.5 py-0.5 rounded-full"
          >{{ summary.pending }}</span>
        </button>
      </div>

      <!-- --- SUBMIT REPORT TAB --- -->
      <div v-if="activeTab === 'submit'">
        <div class="bg-white border border-gray-100 rounded-xl p-6 max-w-xl">
          <h2 class="text-base font-normal text-gray-800 mb-5">Submit a Report</h2>

          <!-- Success state -->
          <div v-if="submitSuccess" class="text-center py-10">
            <div class="w-14 h-14 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
            <p class="text-gray-700 font-normal">Report submitted successfully</p>
            <p class="text-sm text-gray-400 mt-1">We'll review it and respond as soon as possible.</p>
            <button @click="resetForm" class="mt-5 text-sm text-green-800 hover:underline">Submit another report</button>
          </div>

          <form v-else @submit.prevent="submitReport" class="space-y-4">
            <!-- Category -->
            <div>
              <label class="block text-sm text-gray-600 mb-1">Category <span class="text-red-400">*</span></label>
              <select v-model="form.category" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
                <option value="" disabled>Select a category</option>
                <option value="complaint">Complaint</option>
                <option value="suggestion">Suggestion</option>
                <option value="incident">Incident Report</option>
                <option value="harassment">Harassment / Misconduct</option>
                <option value="other">Other</option>
              </select>
            </div>

            <!-- Subject -->
            <div>
              <label class="block text-sm text-gray-600 mb-1">Subject <span class="text-red-400">*</span></label>
              <input
                v-model="form.subject"
                type="text"
                required
                maxlength="255"
                placeholder="Brief summary of your report"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200"
              />
            </div>

            <!-- Description -->
            <div>
              <label class="block text-sm text-gray-600 mb-1">Description <span class="text-red-400">*</span></label>
              <textarea
                v-model="form.description"
                required
                rows="5"
                placeholder="Provide detailed information..."
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200 resize-none"
              ></textarea>
            </div>

            <!-- Anonymous toggle -->
            <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3">
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="form.is_anonymous" class="sr-only peer">
                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:bg-green-800 transition-colors"></div>
                <div class="absolute left-0.5 top-0.5 bg-white w-4 h-4 rounded-full transition-transform peer-checked:translate-x-4"></div>
              </label>
              <div>
                <p class="text-sm text-gray-700">Submit anonymously</p>
                <p class="text-xs text-gray-400">Your name will not be shown to admin</p>
              </div>
            </div>

            <div v-if="submitError" class="text-red-500 text-sm">{{ submitError }}</div>

            <button
              type="submit"
              :disabled="submitting"
              class="w-full bg-green-800 text-white text-sm py-2.5 rounded-lg hover:bg-green-900 transition disabled:opacity-50"
            >
              {{ submitting ? 'Submitting...' : 'Submit Report' }}
            </button>
          </form>
        </div>
      </div>

      <!-- --- MY REPORTS TAB --- -->
      <div v-else-if="activeTab === 'my'">
        <div v-if="loadingMy" class="flex items-center justify-center py-20">
          <div class="w-8 h-8 border-2 border-blue-700 border-t-transparent rounded-full animate-spin"></div>
        </div>

        <div v-else-if="!myReports.length" class="bg-white border border-gray-100 rounded-xl py-20 text-center text-gray-400 text-sm">
          You haven't submitted any reports yet.
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="report in myReports"
            :key="report.id"
            class="bg-white border border-gray-100 rounded-xl p-5 hover:border-gray-200 transition"
          >
            <div class="flex items-start justify-between gap-4">
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1.5">
                  <span :class="categoryBadge(report.category)" class="text-xs px-2 py-0.5 rounded-full capitalize">
                    {{ report.category }}
                  </span>
                  <span :class="statusBadge(report.status)" class="text-xs px-2 py-0.5 rounded-full">
                    {{ statusLabel(report.status) }}
                  </span>
                  <span v-if="report.is_anonymous" class="text-xs text-gray-400 italic">Anonymous</span>
                </div>
                <p class="text-sm font-normal text-gray-800">{{ report.subject }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ formatDate(report.created_at) }}</p>
              </div>
            </div>

            <p class="text-sm text-gray-600 mt-3 leading-relaxed">{{ report.description }}</p>

            <!-- Admin response -->
            <div v-if="report.admin_response" class="mt-4 bg-green-50 border border-green-100 rounded-lg p-4">
              <p class="text-xs text-green-800 font-normal mb-1">
                Response from {{ report.responder_name || 'Admin' }}
                <span v-if="report.responded_at">· {{ formatDate(report.responded_at) }}</span>
              </p>
              <p class="text-sm text-gray-700 leading-relaxed">{{ report.admin_response }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- --- ADMIN TAB --- -->
      <div v-else-if="activeTab === 'admin'">
        <!-- Summary cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
          <div v-for="card in summaryCards" :key="card.label" class="bg-white border border-gray-100 rounded-xl p-4">
            <p class="text-2xl font-normal text-gray-800">{{ card.value }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ card.label }}</p>
          </div>
        </div>

        <!-- Filters -->
        <div class="flex gap-3 mb-4 flex-wrap">
          <select v-model="filterStatus" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-200">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="in_review">In Review</option>
            <option value="resolved">Resolved</option>
            <option value="dismissed">Dismissed</option>
          </select>
          <select v-model="filterCategory" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-200">
            <option value="">All Categories</option>
            <option value="complaint">Complaint</option>
            <option value="suggestion">Suggestion</option>
            <option value="incident">Incident</option>
            <option value="harassment">Harassment</option>
            <option value="other">Other</option>
          </select>
          <button @click="fetchAllReports" class="text-sm text-green-800 hover:underline px-2">Refresh</button>
        </div>

        <div v-if="loadingAdmin" class="flex items-center justify-center py-20">
          <div class="w-8 h-8 border-2 border-blue-700 border-t-transparent rounded-full animate-spin"></div>
        </div>

        <div v-else-if="!allReports.length" class="bg-white border border-gray-100 rounded-xl py-20 text-center text-gray-400 text-sm">
          No reports found.
        </div>

        <!-- Reports table -->
        <div v-else class="bg-white border border-gray-100 rounded-xl overflow-hidden">
          <div class="overflow-x-auto -mx-px">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left text-xs text-gray-400 font-normal px-5 py-3">#</th>
                <th class="text-left text-xs text-gray-400 font-normal px-5 py-3">Subject</th>
                <th class="text-left text-xs text-gray-400 font-normal px-5 py-3">Category</th>
                <th class="text-left text-xs text-gray-400 font-normal px-5 py-3">Reported By</th>
                <th class="text-left text-xs text-gray-400 font-normal px-5 py-3">Status</th>
                <th class="text-left text-xs text-gray-400 font-normal px-5 py-3">Date</th>
                <th class="text-left text-xs text-gray-400 font-normal px-5 py-3">Response</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr
                v-for="report in allReports"
                :key="report.id"
                @click="openViewModal(report)"
                class="hover:bg-green-50/40 cursor-pointer transition"
              >
                <td class="px-5 py-3.5 text-gray-400 text-xs">{{ report.id }}</td>
                <td class="px-5 py-3.5 max-w-[220px]">
                  <p class="text-gray-800 truncate font-normal">{{ report.subject }}</p>
                </td>
                <td class="px-5 py-3.5">
                  <span :class="categoryBadge(report.category)" class="text-xs px-2 py-0.5 rounded-full capitalize">
                    {{ report.category }}
                  </span>
                </td>
                <td class="px-5 py-3.5 text-gray-600">{{ report.reporter_display }}</td>
                <td class="px-5 py-3.5">
                  <span :class="statusBadge(report.status)" class="text-xs px-2 py-0.5 rounded-full">
                    {{ statusLabel(report.status) }}
                  </span>
                </td>
                <td class="px-5 py-3.5 text-gray-400 text-xs whitespace-nowrap">{{ formatDate(report.created_at) }}</td>
                <td class="px-5 py-3.5">
                  <span v-if="report.admin_response" class="text-xs text-green-600">✓ Responded</span>
                  <span v-else class="text-xs text-gray-300">—</span>
                </td>
              </tr>
            </tbody>
          </table></div>
        </div>
      </div>
    </div>

    <!-- --- REPORT DETAIL MODAL --- -->
    <div v-if="showViewModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="showViewModal = false">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh]">

        <!-- Modal header -->
        <div class="flex items-start justify-between px-6 pt-6 pb-4 border-b border-gray-100 shrink-0">
          <div class="flex-1 min-w-0 pr-4">
            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
              <span :class="categoryBadge(viewTarget?.category)" class="text-xs px-2 py-0.5 rounded-full capitalize">
                {{ viewTarget?.category }}
              </span>
              <span :class="statusBadge(viewTarget?.status)" class="text-xs px-2 py-0.5 rounded-full">
                {{ statusLabel(viewTarget?.status) }}
              </span>
              <span v-if="viewTarget?.is_anonymous" class="text-xs text-gray-400 italic">Anonymous</span>
            </div>
            <h2 class="text-base font-normal text-gray-800 leading-snug">{{ viewTarget?.subject }}</h2>
          </div>
          <button @click="showViewModal = false" class="text-gray-300 hover:text-gray-500 transition shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Scrollable body -->
        <div class="overflow-y-auto flex-1 px-6 py-4 space-y-5">

          <!-- Meta row -->
          <div class="flex items-center gap-4 text-xs text-gray-400">
            <span>Reported by <span class="text-gray-600 font-normal">{{ viewTarget?.reporter_display }}</span></span>
            <span>·</span>
            <span>{{ formatDate(viewTarget?.created_at) }}</span>
          </div>

          <!-- Description -->
          <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Description</p>
            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ viewTarget?.description }}</p>
          </div>

          <!-- Existing admin response (read-only display) -->
          <div v-if="viewTarget?.admin_response" class="bg-green-50 border border-green-100 rounded-xl p-4">
            <p class="text-xs text-green-800 font-normal mb-1">
              Response from {{ viewTarget?.responder_name || 'Admin' }}
              <span v-if="viewTarget?.responded_at">· {{ formatDate(viewTarget?.responded_at) }}</span>
            </p>
            <p class="text-sm text-gray-700 leading-relaxed">{{ viewTarget?.admin_response }}</p>
          </div>

          <!-- Respond form (visible when pending or in_review) -->
          <div v-if="viewTarget?.status === 'pending' || viewTarget?.status === 'in_review'" class="border-t border-gray-100 pt-4">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-3">{{ viewTarget?.admin_response ? 'Update Response' : 'Write a Response' }}</p>

            <div class="mb-3">
              <label class="block text-sm text-gray-600 mb-1">Set Status</label>
              <select v-model="respondForm.status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
                <option value="in_review">In Review</option>
                <option value="resolved">Resolved</option>
                <option value="dismissed">Dismissed</option>
              </select>
            </div>

            <div class="mb-1">
              <label class="block text-sm text-gray-600 mb-1">Response <span class="text-gray-400">(optional)</span></label>
              <textarea
                v-model="respondForm.response"
                rows="3"
                placeholder="Write your response to the staff member..."
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-200"
              ></textarea>
            </div>

            <div v-if="respondError" class="text-red-500 text-sm mt-1">{{ respondError }}</div>
          </div>

          <!-- Status-only update for resolved/dismissed -->
          <div v-else class="border-t border-gray-100 pt-4">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-3">Change Status</p>
            <div class="flex items-center gap-3">
              <select v-model="respondForm.status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
                <option value="pending">Pending</option>
                <option value="in_review">In Review</option>
                <option value="resolved">Resolved</option>
                <option value="dismissed">Dismissed</option>
              </select>
              <button
                @click="saveStatusOnly"
                :disabled="responding || respondForm.status === viewTarget?.status"
                class="text-sm bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition disabled:opacity-40"
              >
                Update
              </button>
            </div>
          </div>
        </div>

        <!-- Modal footer -->
        <div class="px-6 py-4 border-t border-gray-100 flex gap-3 shrink-0">
          <button @click="showViewModal = false" class="flex-1 border border-gray-200 text-gray-600 text-sm py-2 rounded-lg hover:bg-gray-50 transition">
            Close
          </button>
          <button
            v-if="viewTarget?.status === 'pending' || viewTarget?.status === 'in_review'"
            @click="sendResponse"
            :disabled="responding"
            class="flex-1 bg-green-800 text-white text-sm py-2 rounded-lg hover:bg-green-900 transition disabled:opacity-50"
          >
            {{ responding ? 'Saving...' : 'Submit Response' }}
          </button>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'
import { useToastStore } from '../stores/toast'

const toast = useToastStore()

const authStore = useAuthStore()
const isAdmin = computed(() => authStore.userRole === 'admin')

// Tabs
const allTabs = [
  { key: 'submit', label: 'Submit Report' },
  { key: 'my', label: 'My Reports' },
  { key: 'admin', label: 'All Reports' },
]
const tabs = computed(() => isAdmin.value ? allTabs : allTabs.filter(t => t.key !== 'admin'))
const activeTab = ref('submit')

onMounted(() => {
  if (isAdmin.value) {
    activeTab.value = 'admin'
    fetchAllReports()
  } else {
    fetchMyReports()
  }
})

// --- SUBMIT FORM ---
const form = ref({ category: '', subject: '', description: '', is_anonymous: false })
const submitting = ref(false)
const submitError = ref('')
const submitSuccess = ref(false)

async function submitReport() {
  submitting.value = true
  submitError.value = ''
  try {
    await api.post('/staff/reports', {
      ...form.value,
      is_anonymous: form.value.is_anonymous ? 1 : 0,
    })
    submitSuccess.value = true
    fetchMyReports()
  } catch (e) {
    submitError.value = e?.response?.data?.message || 'Failed to submit report'
  } finally {
    submitting.value = false
  }
}

function resetForm() {
  form.value = { category: '', subject: '', description: '', is_anonymous: false }
  submitSuccess.value = false
  submitError.value = ''
}

// --- MY REPORTS ---
const myReports = ref([])
const loadingMy = ref(false)

async function fetchMyReports() {
  loadingMy.value = true
  try {
    const res = await api.get('/staff/reports/my')
    myReports.value = res.data?.data ?? []
  } catch {
    myReports.value = []
  } finally {
    loadingMy.value = false
  }
}

// --- ALL REPORTS (ADMIN) ---
const allReports = ref([])
const loadingAdmin = ref(false)
const filterStatus = ref('')
const filterCategory = ref('')
const summary = ref({ total: 0, pending: 0, in_review: 0, resolved: 0, dismissed: 0 })

const summaryCards = computed(() => [
  { label: 'Total Reports', value: summary.value.total },
  { label: 'Pending', value: summary.value.pending },
  { label: 'In Review', value: summary.value.in_review },
  { label: 'Resolved', value: summary.value.resolved },
])

watch(activeTab, (val) => {
  if (val === 'my' && !myReports.value.length) fetchMyReports()
  if (val === 'admin' && !allReports.value.length) fetchAllReports()
})

watch([filterStatus, filterCategory], () => {
  if (activeTab.value === 'admin') fetchAllReports()
})

async function fetchAllReports() {
  loadingAdmin.value = true
  try {
    const params = {}
    if (filterStatus.value) params.status = filterStatus.value
    if (filterCategory.value) params.category = filterCategory.value
    const res = await api.get('/admin/reports', { params })
    const d = res.data?.data ?? {}
    allReports.value = d.reports ?? []
    summary.value = d.summary ?? { total: 0, pending: 0, in_review: 0, resolved: 0, dismissed: 0 }
  } catch {
    allReports.value = []
  } finally {
    loadingAdmin.value = false
  }
}

// --- VIEW / RESPOND MODAL ---
const showViewModal = ref(false)
const viewTarget = ref(null)
const respondForm = ref({ status: 'pending', response: '' })
const responding = ref(false)
const respondError = ref('')

function openViewModal(report) {
  viewTarget.value = report
  respondForm.value = { status: report.status, response: report.admin_response || '' }
  respondError.value = ''
  showViewModal.value = true
}

async function sendResponse() {
  responding.value = true
  respondError.value = ''
  try {
    await api.put(`/admin/reports/${viewTarget.value.id}/respond`, respondForm.value)
    showViewModal.value = false
    fetchAllReports()
  } catch (e) {
    respondError.value = e?.response?.data?.message || 'Failed to send response'
  } finally {
    responding.value = false
  }
}

async function saveStatusOnly() {
  responding.value = true
  try {
    await api.put(`/admin/reports/${viewTarget.value.id}/status`, { status: respondForm.value.status })
    showViewModal.value = false
    fetchAllReports()
  } catch (e) {
    toast.error(e?.response?.data?.message || 'Failed to update status')
  } finally {
    responding.value = false
  }
}

// --- HELPERS ---
function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

function categoryBadge(c) {
  return {
    complaint: 'bg-red-50 text-red-600',
    suggestion: 'bg-green-50 text-amber-600',
    incident: 'bg-orange-50 text-orange-600',
    harassment: 'bg-purple-50 text-purple-700',
    other: 'bg-gray-100 text-gray-600',
  }[c] ?? 'bg-gray-100 text-gray-600'
}

function statusBadge(s) {
  return {
    pending: 'bg-yellow-50 text-yellow-600',
    in_review: 'bg-green-50 text-amber-600',
    resolved: 'bg-green-50 text-green-600',
    dismissed: 'bg-gray-100 text-gray-500',
  }[s] ?? 'bg-gray-100 text-gray-500'
}

function statusLabel(s) {
  return { pending: 'Pending', in_review: 'In Review', resolved: 'Resolved', dismissed: 'Dismissed' }[s] ?? s
}
</script>
