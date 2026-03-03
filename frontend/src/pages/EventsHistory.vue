<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <!-- Page Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-semibold text-green-800">Event Reservation History</h1>
      <p class="text-sm text-gray-500 font-light mt-1">Completed event reservations with full payment records</p>
    </div>

    <!-- Stats row -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
        </div>
        <div>
          <p class="text-xs text-gray-400 font-light">Total Completed</p>
          <p class="text-2xl font-semibold text-green-800">{{ events.length }}</p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-xs text-gray-400 font-light">Fully Paid</p>
          <p class="text-2xl font-semibold text-green-600">{{ fullyPaidCount }}</p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-xs text-gray-400 font-light">Pending Balance</p>
          <p class="text-2xl font-semibold text-yellow-600">{{ pendingBalanceCount }}</p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-xs text-gray-400 font-light">Total Revenue</p>
          <p class="text-xl font-semibold text-green-800">&#x20B1;{{ formatMoney(totalRevenue) }}</p>
        </div>
      </div>
    </div>

    <!-- Search + Refresh -->
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
      <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input v-model="search" @input="fetchEvents" type="text" placeholder="Search by type, client, or venue..."
          class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white"/>
      </div>
      <router-link to="/events"
        class="flex items-center gap-2 border border-blue-700 text-green-800 hover:bg-green-50 px-4 py-2.5 rounded-lg text-sm font-light transition-colors whitespace-nowrap">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Active Events
      </router-link>
      <button @click="fetchEvents"
        class="flex items-center gap-2 border border-gray-300 text-gray-600 hover:bg-gray-50 px-4 py-2.5 rounded-lg text-sm font-light transition-colors">
        <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Refresh
      </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm">
      <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-base font-semibold text-green-800">Completed Reservations</h2>
        <span class="text-xs text-gray-400 font-light">{{ events.length }} record{{ events.length !== 1 ? 's' : '' }}</span>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50">
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Reference</th>
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Type</th>
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Client</th>
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Date</th>
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Guests</th>
              <th class="px-4 py-3 text-right font-semibold text-green-800 text-xs uppercase tracking-wide">Total Amount</th>
              <th class="px-4 py-3 text-right font-semibold text-green-800 text-xs uppercase tracking-wide">Amount Paid</th>
              <th class="px-4 py-3 text-right font-semibold text-green-800 text-xs uppercase tracking-wide">Remaining</th>
              <th class="px-4 py-3 text-center font-semibold text-green-800 text-xs uppercase tracking-wide">Payment Status</th>
              <th class="px-4 py-3 text-center font-semibold text-green-800 text-xs uppercase tracking-wide">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="10" class="px-4 py-12 text-center text-gray-400 font-light">
                <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-green-500" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                Loading history...
              </td>
            </tr>
            <tr v-else-if="events.length === 0">
              <td colspan="10" class="px-4 py-16 text-center">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-gray-400 font-light">No completed events found.</p>
              </td>
            </tr>
            <tr v-for="ev in events" :key="ev.id" class="border-t border-gray-50 hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 font-mono text-xs text-amber-600">{{ ev.reference_number }}</td>
              <td class="px-4 py-3 font-light text-gray-800">{{ ev.event_type }}</td>
              <td class="px-4 py-3 font-light text-gray-700">
                <p class="font-medium text-gray-800">{{ ev.client_name }}</p>
                <p class="text-xs text-gray-400">{{ ev.client_phone || '' }}</p>
              </td>
              <td class="px-4 py-3 font-light text-gray-600 whitespace-nowrap">{{ formatDate(ev.event_date) }}</td>
              <td class="px-4 py-3 font-light text-gray-600 text-center">{{ ev.number_of_guests }}</td>
              <td class="px-4 py-3 font-light text-gray-800 text-right whitespace-nowrap">&#x20B1;{{ formatMoney(ev.total_amount) }}</td>
              <td class="px-4 py-3 font-light text-green-700 text-right whitespace-nowrap">&#x20B1;{{ formatMoney(ev.down_payment) }}</td>
              <td class="px-4 py-3 font-light text-right whitespace-nowrap"
                :class="ev.remaining_balance > 0 ? 'text-red-600' : 'text-green-600'">
                &#x20B1;{{ formatMoney(ev.remaining_balance) }}
              </td>
              <td class="px-4 py-3 text-center">
                <span :class="paymentBadge(ev)" class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap">
                  {{ paymentLabel(ev) }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-1">
                  <!-- View -->
                  <button @click="viewEvent(ev)" title="View Details"
                    class="p-1.5 rounded-lg text-green-600 hover:bg-green-50 border border-green-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                  <!-- Print Contract -->
                  <button @click="printEvent(ev)" title="Print Contract"
                    class="p-1.5 rounded-lg text-amber-600 hover:bg-green-50 border border-green-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- VIEW MODAL -->
    <div v-if="showViewModal && viewingEvent" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white rounded-t-2xl">
          <div>
            <h3 class="text-base font-semibold text-green-800">Event Details</h3>
            <p class="text-xs text-gray-400 font-light">{{ viewingEvent.reference_number }}</p>
          </div>
          <button @click="showViewModal = false" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div class="p-6 space-y-4 text-sm">
          <div class="flex items-center justify-between">
            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
            <span class="text-xs text-gray-400 font-light">{{ formatDate(viewingEvent.event_date) }}</span>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-xs text-gray-400 font-light mb-0.5">Event Type</p>
              <p class="font-medium text-gray-800">{{ viewingEvent.event_type }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 font-light mb-0.5">Package</p>
              <p class="font-light text-gray-700">{{ viewingEvent.package_set || 'N/A' }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 font-light mb-0.5">Client</p>
              <p class="font-medium text-gray-800">{{ viewingEvent.client_name }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 font-light mb-0.5">Phone</p>
              <p class="font-light text-gray-700">{{ viewingEvent.client_phone || 'N/A' }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 font-light mb-0.5">Email</p>
              <p class="font-light text-gray-700 truncate">{{ viewingEvent.client_email || 'N/A' }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 font-light mb-0.5">Venue</p>
              <p class="font-light text-gray-700">{{ viewingEvent.venue || 'N/A' }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 font-light mb-0.5">Number of Guests</p>
              <p class="font-light text-gray-700">{{ viewingEvent.number_of_guests }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 font-light mb-0.5">Payment Method</p>
              <p class="font-light text-gray-700 capitalize">{{ viewingEvent.payment_method?.replace('_', ' ') || 'N/A' }}</p>
            </div>
          </div>

          <div class="bg-gray-50 rounded-xl p-4 space-y-2">
            <div class="flex justify-between text-sm">
              <span class="font-light text-gray-500">Total Amount</span>
              <span class="font-semibold text-gray-800">&#x20B1;{{ formatMoney(viewingEvent.total_amount) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="font-light text-gray-500">Amount Paid</span>
              <span class="font-light text-green-600">&#x20B1;{{ formatMoney(viewingEvent.down_payment) }}</span>
            </div>
            <div class="border-t border-gray-200 pt-2 flex justify-between text-sm">
              <span class="font-medium text-gray-700">Remaining Balance</span>
              <span class="font-semibold" :class="viewingEvent.remaining_balance > 0 ? 'text-red-600' : 'text-green-600'">
                &#x20B1;{{ formatMoney(viewingEvent.remaining_balance) }}
              </span>
            </div>
            <div class="flex justify-between text-sm pt-1">
              <span class="font-light text-gray-500">Payment Status</span>
              <span :class="paymentBadge(viewingEvent)" class="px-2 py-0.5 rounded-full text-xs font-medium">
                {{ paymentLabel(viewingEvent) }}
              </span>
            </div>
          </div>

          <div v-if="viewingEvent.notes">
            <p class="text-xs text-gray-400 font-light mb-1">Notes</p>
            <p class="font-light text-gray-700 text-sm leading-relaxed">{{ viewingEvent.notes }}</p>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
          <button @click="printEvent(viewingEvent)"
            class="px-4 py-2 border border-green-300 text-green-800 hover:bg-green-50 rounded-lg text-sm font-light transition-colors">
            Print Contract
          </button>
          <button @click="showViewModal = false"
            class="px-4 py-2 bg-green-800 hover:bg-green-900 text-white rounded-lg text-sm font-light transition-colors">
            Close
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'

const events   = ref([])
const loading  = ref(false)
const search   = ref('')

const showViewModal = ref(false)
const viewingEvent  = ref(null)

// Stats
const fullyPaidCount     = computed(() => events.value.filter(e => Number(e.remaining_balance) <= 0).length)
const pendingBalanceCount = computed(() => events.value.filter(e => Number(e.remaining_balance) > 0).length)
const totalRevenue       = computed(() => events.value.reduce((s, e) => s + Number(e.total_amount || 0), 0))

async function fetchEvents() {
  loading.value = true
  try {
    const params = new URLSearchParams({ status: 'completed' })
    if (search.value) params.set('search', search.value)
    const res = await api.get(`/events?${params}`)
    events.value = res.data.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

function viewEvent(ev) {
  viewingEvent.value = ev
  showViewModal.value = true
}

function paymentLabel(ev) {
  const remaining = Number(ev.remaining_balance ?? (ev.total_amount - ev.down_payment))
  if (remaining <= 0) return 'Fully Paid'
  if (Number(ev.down_payment) > 0) return 'Partial'
  return 'Unpaid'
}

function paymentBadge(ev) {
  const label = paymentLabel(ev)
  if (label === 'Fully Paid') return 'bg-green-100 text-green-700'
  if (label === 'Partial')    return 'bg-yellow-100 text-yellow-700'
  return 'bg-red-100 text-red-600'
}

function formatMoney(v) {
  return Number(v || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

function formatDate(d) {
  if (!d) return ''
  const dt = new Date(d + (d.includes('T') ? '' : 'T00:00:00'))
  return dt.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

function printEvent(ev) {
  const total   = Number(ev.total_amount || 0)
  const dp      = Number(ev.down_payment || 0)
  const balance = total - dp
  const today   = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
  const ref     = ev.reference_number || ('EVT-' + String(ev.id).padStart(5, '0'))
  const payMethod = ev.payment_method ? ev.payment_method.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase()) : '—'
  const win = window.open('', '_blank', 'width=850,height=1100')
  win.document.write(`<!DOCTYPE html><html><head>
  <title>Contract — ${ref}</title>
  <style>
    @page { size: A4; margin: 20mm 18mm; }
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; color: #111; }
    body { padding: 32px 36px; }
    .header { display: flex; align-items: center; gap: 16px; border-bottom: 3px solid #1d4ed8; padding-bottom: 16px; margin-bottom: 20px; }
    .header h1 { font-size: 22px; color: #1d4ed8; font-weight: 700; }
    .contract-title { text-align: center; font-size: 16px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #1d4ed8; margin-bottom: 16px; }
    .ref-badge { text-align: center; font-size: 13px; background: #eff6ff; color: #1d4ed8; padding: 6px 16px; border-radius: 20px; display: inline-block; margin: 0 auto 20px; font-family: monospace; font-weight: 700; }
    .section { margin-bottom: 18px; }
    .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; margin-bottom: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 24px; }
    .field label { font-size: 11px; color: #6b7280; }
    .field p { font-size: 13px; font-weight: 500; margin-top: 1px; }
    .amount-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 14px 18px; }
    .amount-row { display: flex; justify-content: space-between; font-size: 13px; padding: 4px 0; border-bottom: 1px solid #e5e7eb; }
    .amount-row.total { font-weight: 700; font-size: 15px; color: #1d4ed8; border-bottom: none; margin-top: 6px; }
    @media print { .print-btn { display: none !important; } }
  </style>
  </head><body>
  <div class="header"><div><h1>JOANNA'S NOOK BED &amp; BREAKFAST</h1><p>Official Event Contract &nbsp;|&nbsp; Generated ${today}</p></div></div>
  <p class="contract-title">Event Service Contract</p>
  <div style="text-align:center"><span class="ref-badge">${ref}</span></div>
  <div class="section">
    <div class="section-title">Client Information</div>
    <div class="grid-2">
      <div class="field"><label>Full Name</label><p>${ev.client_name || '—'}</p></div>
      <div class="field"><label>Phone</label><p>${ev.client_phone || '—'}</p></div>
      <div class="field"><label>Email</label><p>${ev.client_email || '—'}</p></div>
      <div class="field"><label>Address</label><p>${ev.client_address || '—'}</p></div>
    </div>
  </div>
  <div class="section">
    <div class="section-title">Event Details</div>
    <div class="grid-2">
      <div class="field"><label>Event Type</label><p>${ev.event_type || '—'}</p></div>
      <div class="field"><label>Event Name</label><p>${ev.event_name || '—'}</p></div>
      <div class="field"><label>Date</label><p>${new Date(ev.event_date+'T00:00:00').toLocaleDateString('en-US',{year:'numeric',month:'long',day:'numeric'})}</p></div>
      <div class="field"><label>Guests</label><p>${ev.number_of_guests}</p></div>
      <div class="field"><label>Venue</label><p>${ev.venue || 'Joanna\'s Nook Bed &amp; Breakfast'}</p></div>
      <div class="field"><label>Package / Set</label><p>${ev.package_set || '—'}</p></div>
    </div>
  </div>
  <div class="section">
    <div class="section-title">Payment Information</div>
    <div class="amount-box">
      <div class="amount-row"><span>Total Event Amount</span><span>&#x20B1;${total.toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>
      <div class="amount-row"><span>Amount Paid</span><span style="color:#16a34a">&#x20B1;${dp.toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>
      <div class="amount-row total"><span>Balance Due</span><span style="${balance <= 0 ? 'color:#16a34a' : ''}">&#x20B1;${balance.toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>
    </div>
    <div style="margin-top:8px" class="field"><label>Payment Method</label><p>${payMethod}</p></div>
  </div>
  <script>window.onload=function(){window.print()}<\/script>
  </body></html>`)
  win.document.close()
}

onMounted(() => fetchEvents())
</script>
