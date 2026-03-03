<template>
  <div class="p-6 bg-gray-50 min-h-screen">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-amber-600">Order History</h1>
      <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input v-model="search" type="text" placeholder="Search by Receipt ID or Customer"
          class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white w-72"/>
      </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div v-if="loading" class="flex justify-center items-center h-48">
        <svg class="w-6 h-6 animate-spin text-green-500" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg>
      </div>

      <div v-else class="overflow-x-auto -mx-px">

      <table class="w-full text-sm">
        <thead>
          <tr class="bg-green-700 text-white text-xs tracking-wide">
            <th class="px-5 py-3 text-left font-semibold">RECEIPT ID</th>
            <th class="px-5 py-3 text-left font-semibold">DATE &amp; TIME</th>
            <th class="px-5 py-3 text-left font-semibold">CUSTOMER</th>
            <th class="px-5 py-3 text-left font-semibold">CASHIER</th>
            <th class="px-5 py-3 text-left font-semibold">ITEMS</th>
            <th class="px-5 py-3 text-left font-semibold">TOTAL</th>
            <th class="px-5 py-3 text-left font-semibold">RECEIVED</th>
            <th class="px-5 py-3 text-left font-semibold">PAYMENT</th>
            <th class="px-5 py-3 text-left font-semibold">ACTION</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="pagedOrders.length === 0">
            <td colspan="9" class="text-center py-12 text-gray-400 text-sm">No orders found.</td>
          </tr>
          <tr v-for="order in pagedOrders" :key="order.id"
            class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
            <td class="px-5 py-4 text-gray-700">{{ order.id }}</td>
            <td class="px-5 py-4">
              <p class="text-gray-700">{{ formatDate(order.created_at) }}</p>
              <p class="text-gray-400 text-xs mt-0.5">{{ formatTime(order.created_at) }}</p>
            </td>
            <td class="px-5 py-4 text-gray-700">{{ order.customer_name || 'Guest' }}</td>
            <td class="px-5 py-4 text-gray-700">{{ order.cashier_name || '—' }}</td>
            <td class="px-5 py-4">
              <span class="bg-green-50 text-green-600 text-xs font-medium px-2.5 py-1 rounded-full">
                {{ order.item_count }} item{{ order.item_count != 1 ? 's' : '' }}
              </span>
            </td>
            <td class="px-5 py-4 font-semibold text-amber-600">&#x20B1;{{ formatMoney(order.total_amount) }}</td>
            <td class="px-5 py-4 text-gray-700">
              <span v-if="order.received_amount">&#x20B1;{{ formatMoney(order.received_amount) }}</span>
              <span v-else class="text-gray-400">-</span>
            </td>
            <td class="px-5 py-4">
              <span v-if="order.payment_method && order.payment_method !== 'bill'"
                class="bg-gray-100 text-gray-600 text-xs font-medium px-3 py-1 rounded-full capitalize">
                {{ order.payment_method }}
              </span>
              <span v-else class="bg-gray-100 text-gray-500 text-xs font-medium px-3 py-1 rounded-full">N/A</span>
            </td>
            <td class="px-5 py-4">
              <button @click="viewDetails(order.id)" class="text-amber-600 hover:text-amber-700 font-medium transition-colors">
                View Details
              </button>
            </td>
          </tr>
        </tbody>
      </table></div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-center gap-1 mt-6">
      <button @click="currentPage = Math.max(1, currentPage - 1)"
        :disabled="currentPage === 1"
        class="px-4 py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
        Previous
      </button>
      <button v-for="p in totalPages" :key="p" @click="currentPage = p"
        :class="[
          'w-9 h-9 text-sm rounded-lg border transition-colors',
          currentPage === p
            ? 'bg-green-700 text-white border-blue-600'
            : 'border-gray-200 text-gray-600 hover:bg-gray-100'
        ]">
        {{ p }}
      </button>
      <button @click="currentPage = Math.min(totalPages, currentPage + 1)"
        :disabled="currentPage === totalPages || totalPages === 0"
        class="px-4 py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
        Next
      </button>
    </div>

    <!-- View Details Modal -->
    <transition name="fade">
      <div v-if="detailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
          <!-- Modal Header -->
          <div class="bg-green-700 px-6 py-4 flex items-center justify-between">
            <h2 class="text-white font-semibold text-base">Receipt #{{ detailOrder?.id }}</h2>
            <button @click="detailModal = false" class="text-white/70 hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Modal Body -->
          <div v-if="detailLoading" class="flex justify-center items-center h-40">
            <svg class="w-6 h-6 animate-spin text-green-500" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
          </div>
          <div v-else-if="detailOrder" class="px-6 py-5 space-y-4 overflow-y-auto flex-1">
            <!-- Info grid -->
            <div class="grid grid-cols-2 gap-3 text-sm">
              <div>
                <p class="text-xs text-gray-400">Customer</p>
                <p class="font-medium text-gray-700">{{ detailOrder.customer_name || 'Guest' }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400">Cashier</p>
                <p class="font-medium text-gray-700">{{ detailOrder.cashier_name || '—' }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400">Date &amp; Time</p>
                <p class="font-medium text-gray-700">{{ formatDate(detailOrder.created_at) }} {{ formatTime(detailOrder.created_at) }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400">Payment</p>
                <p class="font-medium text-gray-700 capitalize">{{ detailOrder.payment_method || 'N/A' }}</p>
              </div>
              <div v-if="detailOrder.reference_number">
                <p class="text-xs text-gray-400">Reference #</p>
                <p class="font-medium text-gray-700">{{ detailOrder.reference_number }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400">Discount</p>
                <p class="font-medium text-gray-700 capitalize">{{ detailOrder.discount_type === 'none' || !detailOrder.discount_type ? 'None' : detailOrder.discount_type }}</p>
              </div>
              <div v-if="detailOrder.received_amount">
                <p class="text-xs text-gray-400">Cash Received</p>
                <p class="font-medium text-gray-700">&#x20B1;{{ formatMoney(detailOrder.received_amount) }}</p>
              </div>
            </div>

            <!-- Items -->
            <div>
              <p class="text-xs text-gray-400 mb-2">Items Ordered</p>
              <div class="border border-gray-100 rounded-lg overflow-hidden">
                <div class="overflow-x-auto -mx-px">
                <table class="w-full text-sm">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-4 py-2 text-left text-xs text-gray-500 font-medium">Item</th>
                      <th class="px-4 py-2 text-center text-xs text-gray-500 font-medium">Qty</th>
                      <th class="px-4 py-2 text-right text-xs text-gray-500 font-medium">Price</th>
                      <th class="px-4 py-2 text-right text-xs text-gray-500 font-medium">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in detailOrder.items" :key="item.item_name" class="border-t border-gray-100">
                      <td class="px-4 py-2 text-gray-700">{{ item.item_name || 'Unknown' }}</td>
                      <td class="px-4 py-2 text-center text-gray-600">{{ item.quantity }}</td>
                      <td class="px-4 py-2 text-right text-gray-600">&#x20B1;{{ formatMoney(item.unit_price) }}</td>
                      <td class="px-4 py-2 text-right font-medium text-gray-700">&#x20B1;{{ formatMoney(item.unit_price * item.quantity) }}</td>
                    </tr>
                  </tbody>
                </table></div>
              </div>
            </div>

            <!-- Total -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 pt-2 border-t border-gray-100">
              <span class="text-sm text-gray-500">Total</span>
              <span class="text-lg font-bold text-amber-600">&#x20B1;{{ formatMoney(detailOrder.total_amount) }}</span>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="px-6 pb-5 flex-shrink-0 space-y-2 border-t border-gray-100 pt-4">
            <button @click="printReceiptFromHistory(detailOrder)"
              class="w-full py-2.5 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
              </svg>
              Print Receipt
            </button>
            <button @click="detailModal = false"
              class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
              Close
            </button>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api from '../services/api'

const orders      = ref([])
const loading     = ref(false)
const search      = ref('')
const currentPage = ref(1)
const PAGE_SIZE   = 10

const detailModal   = ref(false)
const detailLoading = ref(false)
const detailOrder   = ref(null)

// ---- Filtered orders ----
const filteredOrders = computed(() => {
  const q = search.value.trim().toLowerCase()
  if (!q) return orders.value
  return orders.value.filter(o =>
    String(o.id).includes(q) ||
    (o.customer_name && o.customer_name.toLowerCase().includes(q))
  )
})

watch(search, () => { currentPage.value = 1 })

const totalPages = computed(() => Math.max(1, Math.ceil(filteredOrders.value.length / PAGE_SIZE)))

const pagedOrders = computed(() => {
  const start = (currentPage.value - 1) * PAGE_SIZE
  return filteredOrders.value.slice(start, start + PAGE_SIZE)
})

// ---- Helpers ----
function formatMoney(v) {
  return Number(v || 0).toFixed(2)
}

function formatDate(dt) {
  if (!dt) return '—'
  const d = new Date(dt)
  return d.toLocaleDateString('en-PH', { year: 'numeric', month: 'numeric', day: 'numeric' })
}

function formatTime(dt) {
  if (!dt) return ''
  const d = new Date(dt)
  return d.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', hour12: true })
}

// ---- Print Receipt from History ----
function printReceiptFromHistory(order) {
  if (!order) return
  const lines = (order.items || []).map(i =>
    '<tr><td>' + (i.item_name || 'Item') + '</td><td style="text-align:center">x' + i.quantity + '</td><td style="text-align:right">P' + Number((i.unit_price || 0) * i.quantity).toFixed(2) + '</td></tr>'
  ).join('')
  const discountLabel = (order.discount_type && order.discount_type !== 'none') ? order.discount_type : ''
  const subtotal = (order.items || []).reduce((s, i) => s + Number(i.unit_price || 0) * i.quantity, 0)
  const total = Number(order.total_amount || 0)
  const discount = Math.max(0, subtotal - total)
  const received = Number(order.received_amount || 0)
  const change = Math.max(0, received - total)
  const paymentMethod = order.payment_method || 'N/A'
  const contactNum = order.contact_number || ''

  const win = window.open('', '_blank', 'width=380,height=720')
  win.document.write('<!DOCTYPE html><html><head><title>Receipt #' + order.id + '</title>'
    + '<style>* { margin:0; padding:0; box-sizing:border-box; } body { font-family:"Courier New",monospace; font-size:12px; padding:16px; width:320px; background:#fff; } .center { text-align:center; } .bold { font-weight:bold; } .sep { border-top:1px dashed #999; margin:8px 0; } table { width:100%; border-collapse:collapse; } td { padding:2px 0; vertical-align:top; } td:last-child { width:70px; } .grand td { font-weight:bold; font-size:14px; padding-top:6px; } @media print { body { width:280px; } }</style>'
    + '</head><body>'
    + '<div class="center bold" style="font-size:16px;margin-bottom:2px">Joanna\'s Nook Bed &amp; Breakfast</div>'
    + '<div class="center" style="font-size:11px;color:#555">Official Receipt</div>'
    + '<div class="sep"></div>'
    + '<div>Cashier: <b>' + (order.cashier_name || '\u2014') + '</b></div>'
    + '<div>Customer: <b>' + (order.customer_name || 'Guest') + '</b></div>'
    + (contactNum ? '<div>Contact #: <b>' + contactNum + '</b></div>' : '')
    + '<div>Invoice: <b>#' + order.id + '</b></div>'
    + '<div style="font-size:10px;color:#777">' + formatDate(order.created_at) + ' ' + formatTime(order.created_at) + '</div>'
    + '<div class="sep"></div>'
    + '<table><tbody>' + lines + '</tbody></table>'
    + '<div class="sep"></div>'
    + '<table>'
    + '<tr><td>Subtotal</td><td style="text-align:right">P' + subtotal.toFixed(2) + '</td></tr>'
    + (discount > 0 ? '<tr><td>Discount' + (discountLabel ? ' (' + discountLabel + ')' : '') + '</td><td style="text-align:right">-P' + discount.toFixed(2) + '</td></tr>' : '')
    + '<tr class="grand"><td>TOTAL</td><td style="text-align:right">P' + total.toFixed(2) + '</td></tr>'
    + '<tr><td>Payment</td><td style="text-align:right">' + paymentMethod.toUpperCase() + '</td></tr>'
    + (paymentMethod === 'cash' && received ? '<tr><td>Received</td><td style="text-align:right">P' + received.toFixed(2) + '</td></tr><tr><td>Change</td><td style="text-align:right">P' + change.toFixed(2) + '</td></tr>' : '')
    + (order.reference_number ? '<tr><td>Ref #</td><td style="text-align:right;font-size:11px">' + order.reference_number + '</td></tr>' : '')
    + '</table>'
    + '<div class="sep"></div>'
    + '<div class="center" style="font-size:11px">Thank you for dining with us!</div>'
    + '<div class="center" style="font-size:10px;color:#777;margin-top:4px">Joanna\'s Nook Bed &amp; Breakfast - Balingasag, Misamis Oriental</div>'
    + '<div class="sep"></div>'
    + '<div style="font-size:11px;font-weight:bold">Received by:</div>'
    + '<div style="margin-top:28px;border-top:1px solid #333;width:80%"></div>'
    + '<div style="font-size:10px;color:#555">Signature over Printed Name</div>'
    + '<div style="margin-top:10px;font-size:10px;color:#777">Valid for senior citizen &amp; PWD discount claims.</div>'
    + '</body></html>')
  win.document.close()
  setTimeout(() => win.print(), 300)
}

// ---- View Details ----
async function viewDetails(id) {
  detailModal.value   = true
  detailLoading.value = true
  detailOrder.value   = null
  try {
    const res = await api.get(`/restaurant/orders/${id}`)
    detailOrder.value = res.data.data || res.data
  } catch (e) {
    console.error(e)
  } finally {
    detailLoading.value = false
  }
}

// ---- Load orders ----
onMounted(async () => {
  loading.value = true
  try {
    const res = await api.get('/restaurant/orders')
    orders.value = res.data.data || res.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
