<template>
  <div class="min-h-screen bg-gray-50">

    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200 px-4 lg:px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div>
        <h1 class="text-xl font-bold text-gray-800">Chef Dashboard</h1>
        <p class="text-sm text-gray-500">Kitchen Order Tickets — live updates every 15s</p>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <!-- Live indicator -->
        <div class="flex items-center gap-1.5 text-xs text-green-600">
          <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse inline-block"></span>
          Live
        </div>
        <!-- Filter -->
        <div class="flex gap-1 bg-gray-100 rounded-lg p-1">
          <button v-for="f in filters" :key="f.value" @click="statusFilter = f.value"
            :class="['px-3 py-1 rounded text-xs font-medium transition-colors',
              statusFilter === f.value ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700']">
            {{ f.label }}
          </button>
        </div>
        <button @click="loadOrders" :class="['text-xs flex items-center gap-1 px-3 py-1.5 rounded-lg border transition-colors',
          loading ? 'border-gray-200 text-gray-400' : 'border-gray-200 text-gray-600 hover:bg-gray-50']">
          <svg :class="['w-3.5 h-3.5', loading && 'animate-spin']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Refresh
        </button>
      </div>
    </div>

    <!-- Stats bar -->
    <div class="px-4 lg:px-6 py-3 border-b border-gray-100 bg-white flex flex-wrap gap-3">
      <div v-for="s in stats" :key="s.label" class="flex items-center gap-2">
        <span :class="['w-2.5 h-2.5 rounded-full', s.color]"></span>
        <span class="text-xs text-gray-500">{{ s.label }}</span>
        <span :class="['text-sm font-bold', s.text]">{{ s.count }}</span>
      </div>
    </div>

    <!-- Main content -->
    <div class="p-4 lg:p-6">
      <!-- Empty state -->
      <div v-if="!loading && filteredInvoices.length === 0"
        class="flex flex-col items-center justify-center py-24 text-gray-400">
        <div class="text-6xl mb-4">🍳</div>
        <p class="text-lg font-medium text-gray-500">No pending orders — kitchen is clear!</p>
        <p class="text-sm mt-1">New tickets will appear here automatically.</p>
      </div>

      <!-- Loading skeleton -->
      <div v-else-if="loading && orders.length === 0" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="i in 3" :key="i" class="bg-white rounded-2xl shadow-sm overflow-hidden">
          <div class="h-12 bg-gray-100 animate-pulse"></div>
          <div class="p-4 space-y-2">
            <div class="h-3 bg-gray-100 rounded animate-pulse w-3/4"></div>
            <div class="h-3 bg-gray-100 rounded animate-pulse w-1/2"></div>
          </div>
        </div>
      </div>

      <!-- Invoice groups grid -->
      <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="invoice in filteredInvoices" :key="invoice.invoice_id"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">

          <!-- Invoice header -->
          <div :class="['px-4 py-3 flex items-start justify-between', headerClass(invoice.overall_status)]">
            <div>
              <div class="font-mono text-sm font-bold text-gray-800">{{ invoice.invoice_id }}</div>
              <div class="text-xs text-gray-500 mt-0.5">
                {{ invoice.customer_name || 'Guest' }}
                <span v-if="invoice.room_number" class="text-amber-600"> · Room {{ invoice.room_number }}</span>
              </div>
            </div>
            <div class="text-right">
              <span :class="['text-[10px] px-2 py-0.5 rounded-full font-semibold uppercase', statusBadgeClass(invoice.overall_status)]">
                {{ invoice.overall_status }}
              </span>
              <div class="text-[10px] text-gray-400 mt-1">{{ timeAgo(invoice.created_at) }}</div>
            </div>
          </div>

          <!-- Items list -->
          <div class="flex-1 px-4 py-3 space-y-2">
            <div v-for="item in invoice.items" :key="item.id"
              class="flex items-center justify-between gap-2">
              <div class="flex items-center gap-2 min-w-0">
                <span :class="['w-2 h-2 rounded-full flex-shrink-0', itemStatusDot(item.status)]"></span>
                <span class="text-sm text-gray-700 truncate">{{ item.item_name }}</span>
              </div>
              <div class="flex items-center gap-2 flex-shrink-0">
                <span class="text-xs font-bold text-gray-800 bg-gray-100 px-2 py-0.5 rounded-full">×{{ item.quantity }}</span>
                <select v-model="item.status" @change="updateItemStatus(item)"
                  :class="['text-[10px] border rounded-lg px-1.5 py-0.5 font-medium focus:outline-none transition-colors', itemSelectClass(item.status)]">
                  <option value="pending">Pending</option>
                  <option value="preparing">Preparing</option>
                  <option value="ready">Ready</option>
                  <option value="served">Served</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Invoice actions -->
          <div class="px-4 pb-4 flex gap-2 border-t border-gray-50 pt-3">
            <button @click="markAllStatus(invoice, 'preparing')"
              v-if="invoice.overall_status === 'pending'"
              class="flex-1 py-1.5 bg-orange-50 hover:bg-orange-100 text-orange-700 border border-orange-200 rounded-lg text-xs font-medium transition">
              All Preparing
            </button>
            <button @click="markAllStatus(invoice, 'ready')"
              v-if="invoice.overall_status === 'preparing'"
              class="flex-1 py-1.5 bg-green-50 hover:bg-green-100 text-green-800 border border-green-200 rounded-lg text-xs font-medium transition">
              All Ready
            </button>
            <button @click="markAllStatus(invoice, 'served')"
              v-if="['ready', 'preparing', 'pending'].includes(invoice.overall_status)"
              class="flex-1 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 border border-green-200 rounded-lg text-xs font-medium transition">
              All Served
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import api from '../services/api'

const orders        = ref([])
const loading       = ref(false)
const statusFilter  = ref('all')
let   pollTimer     = null

const filters = [
  { value: 'all',       label: 'All' },
  { value: 'pending',   label: 'Pending' },
  { value: 'preparing', label: 'Preparing' },
  { value: 'ready',     label: 'Ready' },
]

// ── Data loading ───────────────────────────────────────────────────────────────
async function loadOrders() {
  loading.value = true
  try {
    const res = await api.get('/chef/orders')
    // Backend returns grouped invoice objects; flatten into item rows
    // so the invoices computed can re-group them with full metadata.
    const groups = res.data?.data || []
    const flat = []
    for (const inv of groups) {
      for (const item of (inv.items || [])) {
        flat.push({
          ...item,
          invoice_id:    inv.invoice_id,
          customer_name: inv.customer_name,
          room_number:   inv.room_number ?? item.room_number ?? null,
          created_at:    item.created_at || inv.created_at,
        })
      }
    }
    orders.value = flat
  } catch (e) { console.error(e) }
  finally { loading.value = false }
}

// ── Invoice groups ─────────────────────────────────────────────────────────────
const invoices = computed(() => {
  const map = {}
  for (const item of orders.value) {
    if (!map[item.invoice_id]) {
      map[item.invoice_id] = {
        invoice_id:    item.invoice_id,
        customer_name: item.customer_name,
        room_number:   item.room_number,
        created_at:    item.created_at,
        items:         [],
        overall_status: 'pending',
      }
    }
    map[item.invoice_id].items.push(item)
  }
  // Compute overall_status = lowest in the pipeline
  const order = ['pending', 'preparing', 'ready', 'served']
  Object.values(map).forEach(inv => {
    const statuses = inv.items.map(i => i.status)
    inv.overall_status = order.find(s => statuses.includes(s)) || 'served'
  })
  return Object.values(map).sort((a, b) => new Date(a.created_at) - new Date(b.created_at))
})

const filteredInvoices = computed(() => {
  if (statusFilter.value === 'all') return invoices.value
  return invoices.value.filter(inv => inv.overall_status === statusFilter.value)
})

// ── Stats ──────────────────────────────────────────────────────────────────────
const stats = computed(() => [
  { label: 'Pending',   count: invoices.value.filter(i => i.overall_status === 'pending').length,   color: 'bg-yellow-400', text: 'text-yellow-600' },
  { label: 'Preparing', count: invoices.value.filter(i => i.overall_status === 'preparing').length, color: 'bg-orange-400', text: 'text-orange-600' },
  { label: 'Ready',     count: invoices.value.filter(i => i.overall_status === 'ready').length,     color: 'bg-green-400',  text: 'text-green-600' },
])

// ── Update single item status ──────────────────────────────────────────────────
async function updateItemStatus(item) {
  try {
    await api.put('/chef/orders/' + item.id + '/status', { status: item.status })
    if (item.status === 'served') {
      // Remove from local list (backend deleted the row)
      orders.value = orders.value.filter(o => o.id !== item.id)
    }
  } catch (e) {
    console.error(e)
    await loadOrders() // re-sync on error
  }
}

// ── Mark all items in an invoice ───────────────────────────────────────────────
async function markAllStatus(invoice, status) {
  try {
    await api.put('/chef/orders/invoice/' + invoice.invoice_id + '/status', { status })
    if (status === 'served') {
      orders.value = orders.value.filter(o => o.invoice_id !== invoice.invoice_id)
    } else {
      orders.value = orders.value.map(o =>
        o.invoice_id === invoice.invoice_id ? { ...o, status } : o
      )
    }
  } catch (e) {
    console.error(e)
    await loadOrders()
  }
}

// ── Styling helpers ────────────────────────────────────────────────────────────
function headerClass(status) {
  const map = {
    pending:   'bg-yellow-50 border-b border-yellow-100',
    preparing: 'bg-orange-50 border-b border-orange-100',
    ready:     'bg-green-50 border-b border-green-100',
    served:    'bg-gray-50 border-b border-gray-100',
  }
  return map[status] || 'bg-gray-50 border-b border-gray-100'
}
function statusBadgeClass(status) {
  const map = {
    pending:   'bg-yellow-100 text-yellow-700',
    preparing: 'bg-orange-100 text-orange-700',
    ready:     'bg-green-100 text-green-700',
    served:    'bg-gray-100 text-gray-600',
  }
  return map[status] || 'bg-gray-100 text-gray-600'
}
function itemStatusDot(status) {
  const map = { pending: 'bg-yellow-400', preparing: 'bg-orange-400', ready: 'bg-green-500', served: 'bg-gray-300' }
  return map[status] || 'bg-gray-300'
}
function itemSelectClass(status) {
  const map = {
    pending:   'border-yellow-200 bg-yellow-50 text-yellow-700',
    preparing: 'border-orange-200 bg-orange-50 text-orange-700',
    ready:     'border-green-200 bg-green-50 text-green-700',
    served:    'border-gray-200 bg-gray-50 text-gray-500',
  }
  return map[status] || 'border-gray-200 bg-gray-50 text-gray-600'
}

// ── Time ago ───────────────────────────────────────────────────────────────────
function timeAgo(ts) {
  if (!ts) return ''
  const diff = (Date.now() - new Date(ts).getTime()) / 1000
  if (diff < 60)   return Math.round(diff) + 's ago'
  if (diff < 3600) return Math.round(diff / 60) + 'm ago'
  return Math.round(diff / 3600) + 'h ago'
}

// ── Lifecycle ──────────────────────────────────────────────────────────────────
onMounted(() => {
  loadOrders()
  pollTimer = setInterval(loadOrders, 15000)
})
onUnmounted(() => clearInterval(pollTimer))
</script>
