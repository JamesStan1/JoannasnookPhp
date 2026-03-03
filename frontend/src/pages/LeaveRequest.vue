<template>
  <div class="bg-gray-50 min-h-full">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
      <div class="p-4 lg:p-8 max-w-3xl mx-auto">
        <h1 class="text-2xl lg:text-4xl font-light text-gray-900 tracking-tight">Request Leave</h1>
        <p class="text-gray-500 font-light mt-1">Submit a new leave application</p>
      </div>
    </div>

    <div class="p-4 lg:p-8 max-w-3xl mx-auto space-y-6">

      <!-- Success state -->
      <transition name="fade">
        <div v-if="submitted" class="bg-green-50 border border-green-200 rounded-xl p-6 flex items-start gap-4">
          <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
          <div>
            <h3 class="font-medium text-green-800">Leave Request Submitted!</h3>
            <p class="text-sm text-green-700 font-light mt-0.5">Your request is pending approval from management. You'll be notified once a decision is made.</p>
            <div class="flex gap-3 mt-4">
              <button @click="submitted = false; resetForm()" class="text-sm text-green-700 font-medium border border-green-300 px-4 py-1.5 rounded-lg hover:bg-green-100 transition">
                New Request
              </button>
              <router-link to="/staff/leave/history" class="text-sm text-white font-medium bg-green-600 px-4 py-1.5 rounded-lg hover:bg-green-700 transition">
                View History
              </router-link>
            </div>
          </div>
        </div>
      </transition>

      <!-- Form -->
      <div v-if="!submitted" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 lg:p-8">
        <form @submit.prevent="submitLeave" class="space-y-5">

          <!-- Leave type -->
          <div>
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Leave Type</label>
            <select v-model="form.leave_type" required class="form-input">
              <option value="">Select leave type...</option>
              <option value="sick">Sick Leave (Paid)</option>
              <option value="casual">Casual Leave (Paid)</option>
              <option value="annual">Annual Leave (Paid)</option>
              <option value="emergency">Emergency Leave (Paid)</option>
              <option value="maternity">Maternity Leave (Paid)</option>
              <option value="paternity">Paternity Leave (Paid)</option>
              <option value="unpaid">Unpaid Leave</option>
            </select>
          </div>

          <!-- Date range -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Start Date</label>
              <input v-model="form.start_date" type="date" required :min="today" @change="calcDays" class="form-input" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">End Date</label>
              <input v-model="form.end_date" type="date" required :min="form.start_date || today" @change="calcDays" class="form-input" />
            </div>
          </div>

          <!-- Days calculated -->
          <transition name="fade">
            <div v-if="days > 0" class="flex items-center gap-2 px-4 py-2.5 bg-green-50 border border-green-200 rounded-lg">
              <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <span class="text-sm text-amber-700 font-light">
                <strong class="font-medium">{{ days }}</strong> {{ days === 1 ? 'day' : 'days' }} of leave requested
              </span>
            </div>
          </transition>

          <!-- Reason -->
          <div>
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">
              Reason <span class="normal-case font-light text-gray-400">(optional)</span>
            </label>
            <textarea
              v-model="form.reason"
              rows="4"
              placeholder="Briefly describe the reason for your leave..."
              class="form-input resize-none"
            ></textarea>
          </div>

          <!-- Error -->
          <div v-if="error" class="p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg font-light">{{ error }}</div>

          <!-- Actions -->
          <div class="flex gap-3 pt-2">
            <button
              type="submit"
              :disabled="loading || days <= 0"
              class="flex-1 bg-green-800 hover:bg-green-900 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-light py-2.5 rounded-lg transition text-sm"
            >
              {{ loading ? 'Submitting...' : 'Submit Leave Request' }}
            </button>
            <router-link
              to="/staff/leave/history"
              class="px-6 py-2.5 border border-gray-200 hover:bg-gray-50 text-gray-600 font-light rounded-lg transition text-sm text-center"
            >
              View History
            </router-link>
          </div>
        </form>
      </div>

      <!-- Leave type info -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-medium text-gray-700 mb-4">Leave Types</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <div v-for="lt in leaveTypes" :key="lt.value" class="p-3 rounded-lg border border-gray-100 bg-gray-50">
            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium mb-1 capitalize" :class="lt.color">{{ lt.label }}</span>
            <p class="text-xs text-gray-400 font-light mt-0.5">{{ lt.desc }}</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import api from '../services/api'
import { useLeavesStore } from '../stores/leaves'

const leavesStore = useLeavesStore()
const today = new Date().toISOString().split('T')[0]
const submitted = ref(false)
const loading = ref(false)
const error = ref('')
const days = ref(0)

const form = reactive({ leave_type: '', start_date: '', end_date: '', reason: '' })

const leaveTypes = [
  { value: 'sick',      label: 'Sick',      color: 'bg-red-50 text-red-700',    desc: 'Medical or health-related absence' },
  { value: 'casual',   label: 'Casual',    color: 'bg-green-50 text-green-800',   desc: 'Personal errands or short notice' },
  { value: 'annual',   label: 'Annual',    color: 'bg-green-50 text-green-700', desc: 'Planned vacation or rest' },
  { value: 'emergency',label: 'Emergency', color: 'bg-orange-50 text-orange-700',desc: 'Unforeseen urgent matters' },
  { value: 'maternity',label: 'Maternity', color: 'bg-pink-50 text-pink-700',   desc: 'Childbirth / newborn care' },
  { value: 'paternity',label: 'Paternity', color: 'bg-purple-50 text-purple-700',desc: 'Father support leave' },
]

const calcDays = () => {
  if (form.start_date && form.end_date) {
    const start = new Date(form.start_date)
    const end   = new Date(form.end_date)
    const diff  = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1
    days.value = diff > 0 ? diff : 0
  } else {
    days.value = 0
  }
}

const resetForm = () => {
  Object.assign(form, { leave_type: '', start_date: '', end_date: '', reason: '' })
  days.value = 0; error.value = ''
}

const submitLeave = async () => {
  error.value = ''
  loading.value = true
  try {
    await api.post('/staff/leaves', {
      leave_type: form.leave_type,
      start_date: form.start_date,
      end_date:   form.end_date,
      reason:     form.reason,
    })
    submitted.value = true
    await leavesStore.fetchMyLeaves()
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to submit leave request'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.form-input {
  @apply w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition;
}
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
