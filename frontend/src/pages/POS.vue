<template>
  <div class="flex flex-col h-[calc(100vh-80px)] overflow-hidden bg-gray-50">

    <!-- Mobile Tab Bar -->
    <div class="flex lg:hidden border-b border-gray-200 bg-white flex-shrink-0">
      <button @click="posTab = 'menu'"
        :class="['flex-1 py-3 text-sm font-medium flex items-center justify-center gap-2 border-b-2 transition',
          posTab === 'menu' ? 'border-blue-600 text-amber-600' : 'border-transparent text-gray-500']">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
        Menu
      </button>
      <button @click="posTab = 'cart'"
        :class="['flex-1 py-3 text-sm font-medium flex items-center justify-center gap-2 border-b-2 transition relative',
          posTab === 'cart' ? 'border-blue-600 text-amber-600' : 'border-transparent text-gray-500']">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        Cart
        <span v-if="cart.length" class="absolute top-1.5 right-6 bg-green-700 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">{{ cart.length }}</span>
      </button>
    </div>

    <div class="flex flex-1 overflow-hidden">

    <!-- LEFT PANEL — Menu -->
    <div class="flex-1 flex flex-col overflow-hidden border-r border-gray-200 bg-white"
      :class="posTab === 'cart' ? 'hidden lg:flex' : 'flex'">

      <!-- Top bar -->
      <div class="px-5 pt-4 pb-3 border-b border-gray-100 flex-shrink-0 space-y-3">
        <!-- Category chips -->
        <div class="flex items-center gap-2 flex-wrap">
          <button v-for="cat in ['All', ...categories]" :key="cat"
            @click="activeCategory = cat; currentPage = 1"
            :class="['px-3 py-1 rounded-full text-xs font-medium border transition-colors',
              activeCategory === cat
                ? 'bg-green-700 text-white border-blue-600'
                : 'bg-white text-gray-600 border-gray-200 hover:border-green-300 hover:text-amber-600']">
            {{ cat }}
          </button>
        </div>
        <!-- Search -->
        <div class="relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input v-model="search" @input="currentPage = 1" type="text" placeholder="Search dishes..."
            class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
        </div>
      </div>

      <!-- Grid -->
      <div class="flex-1 overflow-y-auto p-4">
        <div v-if="menuLoading" class="flex justify-center items-center h-40">
          <svg class="w-7 h-7 animate-spin text-green-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
          </svg>
        </div>
        <div v-else-if="pagedItems.length === 0" class="flex flex-col items-center justify-center h-40 text-gray-400">
          <p class="text-sm">No dishes found.</p>
        </div>
        <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
          <button v-for="item in pagedItems" :key="item.id"
            @click="addToCart(item)"
            class="group relative bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm hover:shadow-md hover:border-green-200 transition-all text-left">
            <div class="h-24 bg-gray-100 overflow-hidden">
              <img v-if="dishImg(item)" :src="dishImg(item)" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"/>
              <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
              </div>
            </div>
            <div class="p-2.5">
              <p class="text-xs font-semibold text-gray-800 leading-tight line-clamp-2">{{ item.name }}</p>
              <p class="text-xs text-amber-600 font-bold mt-0.5">P{{ fmt(item.price) }}</p>
            </div>
            <div v-if="cartQty(item.id)" class="absolute top-1.5 right-1.5 w-5 h-5 bg-green-700 rounded-full text-white text-[10px] font-bold flex items-center justify-center shadow">
              {{ cartQty(item.id) }}
            </div>
          </button>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex items-center justify-center gap-1 py-3 border-t border-gray-100 flex-shrink-0">
        <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1"
          class="px-3 py-1 text-xs border border-gray-200 rounded text-gray-600 hover:bg-gray-50 disabled:opacity-40">Prev</button>
        <button v-for="p in totalPages" :key="p" @click="currentPage = p"
          :class="['w-7 h-7 text-xs rounded border transition-colors',
            currentPage === p ? 'bg-green-700 text-white border-blue-600' : 'border-gray-200 text-gray-600 hover:bg-gray-50']">
          {{ p }}
        </button>
        <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages"
          class="px-3 py-1 text-xs border border-gray-200 rounded text-gray-600 hover:bg-gray-50 disabled:opacity-40">Next</button>
      </div>
    </div>

    <!-- RIGHT PANEL — Order -->
    <div class="w-full lg:w-[380px] flex flex-col overflow-hidden bg-white flex-shrink-0 shadow-lg"
      :class="posTab === 'menu' ? 'hidden lg:flex' : 'flex'">

      <!-- Header -->
      <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
        <h2 class="text-base font-bold text-gray-800">Current Order</h2>
        <button v-if="cart.length" @click="cart = []"
          class="text-xs text-red-400 hover:text-red-600 border border-red-200 px-2 py-1 rounded transition">Clear all</button>
      </div>

      <div class="flex-1 overflow-y-auto px-5 py-4 space-y-5">

        <!-- Cashier -->
        <div>
          <label class="block text-xs text-gray-400 font-medium mb-1 uppercase tracking-wider">Cashier</label>
          <div class="text-sm font-medium text-gray-700 bg-gray-50 rounded-lg px-3 py-2">{{ cashierName }}</div>
        </div>

        <!-- Customer Name -->
        <div>
          <label class="block text-xs text-gray-400 font-medium mb-1 uppercase tracking-wider">Customer Name</label>
          <input v-model="customerName" type="text" placeholder="Guest"
            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
        </div>

        <!-- Contact Number -->
        <div>
          <label class="block text-xs text-gray-400 font-medium mb-1 uppercase tracking-wider">Contact #</label>
          <input v-model="contactNumber" type="text" placeholder="Optional"
            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
        </div>

        <!-- Charge to Room toggle -->
        <div>
          <label class="flex items-center gap-2 cursor-pointer select-none">
            <div @click="chargeToRoom = !chargeToRoom"
              :class="['w-10 h-5 rounded-full transition-colors relative flex-shrink-0 cursor-pointer',
                chargeToRoom ? 'bg-green-700' : 'bg-gray-200']">
              <span :class="['absolute top-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform',
                chargeToRoom ? 'translate-x-5' : 'translate-x-0.5']"/>
            </div>
            <span class="text-sm text-gray-700">Charge to Room</span>
          </label>
          <div v-if="chargeToRoom" class="mt-2">
            <select v-model="selectedRoom"
              class="w-full px-3 py-2 border border-green-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
              <option :value="null">-- Select occupied room --</option>
              <option v-for="room in occupiedRooms" :key="room.id" :value="room">
                Room {{ room.room_number }} -- {{ room.guest_name || 'Guest' }}
              </option>
            </select>
          </div>
        </div>

        <!-- Cart Items -->
        <div>
          <label class="block text-xs text-gray-400 font-medium mb-2 uppercase tracking-wider">Ordered Dishes</label>
          <div v-if="cart.length === 0" class="text-center py-8 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-sm text-gray-400">No dishes selected</p>
          </div>
          <div v-else class="space-y-1.5 max-h-48 overflow-y-auto">
            <div v-for="(item, idx) in cart" :key="item.id"
              class="flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-gray-50">
              <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-700 truncate">{{ item.name }}</p>
                <p class="text-xs text-gray-400">P{{ fmt(item.price) }}</p>
              </div>
              <div class="flex items-center gap-0.5">
                <button @click="decrement(idx)"
                  class="w-6 h-6 rounded border border-gray-200 text-gray-500 hover:bg-red-50 hover:border-red-200 hover:text-red-500 text-xs flex items-center justify-center transition">-</button>
                <span class="text-xs w-6 text-center font-bold text-gray-800">{{ item.quantity }}</span>
                <button @click="item.quantity++"
                  class="w-6 h-6 rounded border border-gray-200 text-gray-500 hover:bg-green-50 hover:border-green-200 hover:text-green-600 text-xs flex items-center justify-center transition">+</button>
              </div>
              <p class="text-xs font-bold text-gray-700 w-14 text-right">P{{ fmt(item.price * item.quantity) }}</p>
              <button @click="cart.splice(idx, 1)" class="text-gray-300 hover:text-red-400 transition ml-1 flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Discount -->
        <div>
          <label class="block text-xs text-gray-400 font-medium mb-1 uppercase tracking-wider">Discount</label>
          <div v-if="discountsLoading" class="text-xs text-gray-400 py-2">Loading discounts...</div>
          <select v-else v-model="discountType"
            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
            <option value="none">No Discount</option>
            <option v-for="d in allDiscounts" :key="d.id" :value="String(d.id)">
              {{ d.name }} &mdash; {{ d.type === 'percentage' ? d.value + '%' : '&#8369;' + parseFloat(d.value).toFixed(2) }}
              <template v-if="d.is_default == 1"> (Default)</template>
            </option>
          </select>
        </div>

        <!-- Payment Method -->
        <div v-if="!chargeToRoom">
          <label class="block text-xs text-gray-400 font-medium mb-2 uppercase tracking-wider">Payment Method</label>
          <div class="grid grid-cols-3 gap-2">
            <button v-for="m in paymentMethods" :key="m.value" @click="paymentMethod = m.value"
              :class="['py-2.5 border rounded-lg text-xs font-medium flex flex-col items-center gap-1 transition',
                paymentMethod === m.value
                  ? 'bg-green-50 border-green-400 text-green-800'
                  : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100']">
              <span class="text-base">{{ m.icon }}</span>
              {{ m.label }}
            </button>
          </div>
        </div>

        <!-- Cash Received -->
        <div v-if="!chargeToRoom && paymentMethod === 'cash'">
          <label class="block text-xs text-gray-400 font-medium mb-1 uppercase tracking-wider">Cash Received</label>
          <input v-model.number="receivedAmount" type="number" min="0" step="1" placeholder="0.00"
            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          <div v-if="receivedAmount >= total && receivedAmount > 0" class="mt-1.5 flex justify-between text-xs bg-green-50 border border-green-200 rounded-lg px-3 py-1.5">
            <span class="text-green-700 font-medium">Change</span>
            <span class="text-green-700 font-bold">P{{ fmt(receivedAmount - total) }}</span>
          </div>
        </div>

        <!-- Reference Number (GCash / Bank Transfer) -->
        <div v-if="!chargeToRoom && (paymentMethod === 'gcash' || paymentMethod === 'bank_transfer')">
          <label class="block text-xs text-gray-400 font-medium mb-1 uppercase tracking-wider">Reference Number <span class="text-red-400">*</span></label>
          <input v-model="referenceNumber" type="text" placeholder="Enter reference number"
            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          <p class="text-xs text-gray-400 mt-1">Required for GCash / Bank Transfer payments.</p>
        </div>

        <!-- Totals -->
        <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl p-4 space-y-2 text-sm border border-gray-100">
          <div class="flex justify-between text-gray-500">
            <span>Subtotal</span><span>P{{ fmt(subtotal) }}</span>
          </div>
          <div v-if="discountAmount > 0" class="flex justify-between text-orange-500">
            <span class="text-xs">{{ discountLabel }}</span><span>- P{{ fmt(discountAmount) }}</span>
          </div>
          <div class="flex justify-between font-bold text-gray-800 text-base pt-2 border-t border-gray-200">
            <span>TOTAL</span><span class="text-green-800">P{{ fmt(total) }}</span>
          </div>
          <div v-if="chargeToRoom && selectedRoom" class="text-xs text-amber-600 bg-green-50 rounded-lg px-2 py-1 text-center">
            Charging to Room {{ selectedRoom.room_number }}
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="px-5 pb-5 pt-3 space-y-2 flex-shrink-0 border-t border-gray-100">
        <button @click="openReceiptPreview" :disabled="cart.length === 0"
          class="w-full py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 text-sm font-medium rounded-xl flex items-center justify-center gap-2 transition disabled:opacity-40">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
          </svg>
          Print Receipt Preview
        </button>
        <button @click="openBillPreview" :disabled="cart.length === 0"
          class="w-full py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 text-sm font-medium rounded-xl flex items-center justify-center gap-2 transition disabled:opacity-40">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Generate Bill (A4)
        </button>
        <button @click="processPayment"
          :disabled="cart.length === 0 || processing || (!chargeToRoom && !paymentMethod) || (!chargeToRoom && (paymentMethod === 'gcash' || paymentMethod === 'bank_transfer') && !referenceNumber.trim())"
          class="w-full py-3 bg-green-700 hover:bg-green-800 disabled:bg-green-400 text-white text-sm font-bold rounded-xl flex items-center justify-center gap-2 transition shadow-md shadow-blue-200">
          <svg v-if="!processing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
          </svg>
          <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
          </svg>
          {{ processing ? 'Processing...' : 'Process Payment' }}
        </button>
      </div>
    </div>

    </div><!-- end flex wrapper -->

    <!-- SUCCESS OVERLAY -->
    <transition name="fade">
      <div v-if="successState" class="absolute inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm mx-4 p-8 text-center">
          <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-gray-800 mb-1">Payment Successful!</h3>
          <p class="text-sm text-gray-500 mb-1">Order sent to kitchen</p>
          <div class="bg-gray-50 rounded-xl px-4 py-3 mb-5 mt-4">
            <div class="text-xs text-gray-400 mb-0.5">Invoice Number</div>
            <div class="font-mono text-lg font-bold text-green-800">{{ lastInvoiceId }}</div>
            <div class="text-2xl font-bold text-gray-800 mt-2">P{{ fmt(lastTotal) }}</div>
            <div class="text-xs text-gray-400 mt-0.5">{{ lastPaymentMethod }}</div>
          </div>
          <div class="space-y-2">
            <button @click="printThermalReceipt(lastReceiptData)"
              class="w-full py-2.5 border border-gray-200 text-gray-700 hover:bg-gray-50 text-sm font-medium rounded-xl transition flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
              </svg>
              Print Receipt
            </button>
            <button @click="resetPOS"
              class="w-full py-2.5 bg-green-700 hover:bg-green-800 text-white text-sm font-bold rounded-xl transition shadow-md">
              New Order
            </button>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import api from '../services/api'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()

const allItems        = ref([])
const menuLoading     = ref(false)
const search          = ref('')
const activeCategory  = ref('All')
const currentPage     = ref(1)
const PAGE_SIZE       = 12

const cart            = ref([])
const customerName    = ref('')
const contactNumber   = ref('')
const chargeToRoom    = ref(false)
const selectedRoom    = ref(null)
const occupiedRooms   = ref([])
const discountType    = ref('none')
const allDiscounts    = ref([])   // all discounts from DB (Settings-managed)
const discountsLoading = ref(false)
const paymentMethod   = ref('cash')
const receivedAmount  = ref('')
const referenceNumber = ref('')
const processing      = ref(false)
const posTab          = ref('menu')  // 'menu' | 'cart' — mobile tab switching

const successState      = ref(false)
const lastInvoiceId     = ref('')
const lastTotal         = ref(0)
const lastPaymentMethod = ref('')
const lastReceiptData   = ref(null)

const cashierName = computed(() => {
  const u = authStore.user || JSON.parse(localStorage.getItem('user') || '{}')
  return u?.name || u?.email || 'Cashier'
})

const IMG_BASE = 'http://localhost:8000/'
function dishImg(item) {
  if (!item.image_url) return ''
  // Absolute URL ? use as-is; frontend public asset (starts with /) ? use as-is;
  // otherwise treat as a backend-hosted upload path
  if (item.image_url.startsWith('http') || item.image_url.startsWith('/')) return item.image_url
  return IMG_BASE + item.image_url
}

const categories = computed(() =>
  [...new Set(allItems.value.map(i => i.category).filter(Boolean))].sort()
)

const filtered = computed(() => {
  let items = allItems.value
  if (activeCategory.value !== 'All') items = items.filter(i => i.category === activeCategory.value)
  if (search.value) {
    const q = search.value.toLowerCase()
    items = items.filter(i => i.name.toLowerCase().includes(q))
  }
  return items
})
const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / PAGE_SIZE)))
const pagedItems = computed(() => {
  const s = (currentPage.value - 1) * PAGE_SIZE
  return filtered.value.slice(s, s + PAGE_SIZE)
})

function cartQty(id) { return cart.value.find(i => i.id === id)?.quantity ?? 0 }

function addToCart(item) {
  const e = cart.value.find(i => i.id === item.id)
  if (e) { e.quantity++ } else { cart.value.push({ ...item, quantity: 1 }) }
}

function decrement(idx) {
  if (cart.value[idx].quantity > 1) cart.value[idx].quantity--
  else cart.value.splice(idx, 1)
}

const subtotal = computed(() => cart.value.reduce((s, i) => s + Number(i.price) * i.quantity, 0))

// Look up the selected discount object
const selectedDiscount = computed(() =>
  discountType.value === 'none'
    ? null
    : allDiscounts.value.find(d => String(d.id) === String(discountType.value)) ?? null
)

const discountAmount = computed(() => {
  const d = selectedDiscount.value
  if (!d) return 0
  if (d.type === 'percentage') return subtotal.value * (parseFloat(d.value) / 100)
  if (d.type === 'fixed')      return Math.min(parseFloat(d.value), subtotal.value)
  return 0
})

const discountLabel = computed(() => {
  const d = selectedDiscount.value
  if (!d) return ''
  return d.type === 'percentage'
    ? `${d.name} (${d.value}%)`
    : `${d.name} (\u20b1${parseFloat(d.value).toFixed(2)} off)`
})

const total = computed(() => subtotal.value - discountAmount.value)

const paymentMethods = [
  { value: 'cash',          label: 'Cash',  icon: 'P' },
  { value: 'gcash',         label: 'GCash', icon: 'G' },
  { value: 'bank_transfer', label: 'Bank',  icon: 'B' },
]

function fmt(v) {
  return Number(v || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')
}

function buildReceiptData(overrides = {}) {
  return {
    cashier:        cashierName.value,
    customer_name:  customerName.value || 'Guest',
    items:          cart.value.map(i => ({ item_name: i.name, quantity: i.quantity, unit_price: Number(i.price) })),
    subtotal:       subtotal.value,
    discount_type:  discountType.value,
    discount_label: discountLabel.value,
    discount:       discountAmount.value,
    total:          total.value,
    payment_method: chargeToRoom.value
      ? ('Charge to Room ' + (selectedRoom.value?.room_number || ''))
      : paymentMethod.value,
    received:       receivedAmount.value || 0,
    change:         Math.max(0, (Number(receivedAmount.value) || 0) - total.value),
    charge_to_room: chargeToRoom.value,
    room_number:    selectedRoom.value?.room_number || null,
    reference_number: referenceNumber.value || null,
    contact_number: contactNumber.value || '',
    generated_at:   new Date().toLocaleString('en-PH'),
    ...overrides,
  }
}

function printThermalReceipt(data) {
  if (!data) data = buildReceiptData()
  const lines = data.items.map(i =>
    '<tr><td>' + i.item_name + '</td><td style="text-align:center">x' + i.quantity + '</td><td style="text-align:right">P' + Number(i.unit_price * i.quantity).toFixed(2) + '</td></tr>'
  ).join('')

  const win = window.open('', '_blank', 'width=380,height=680')
  win.document.write('<!DOCTYPE html><html><head><title>Receipt ' + (data.invoice_id || '') + '</title>'
    + '<style>* { margin:0; padding:0; box-sizing:border-box; } body { font-family:"Courier New",monospace; font-size:12px; padding:16px; width:320px; background:#fff; } .center { text-align:center; } .bold { font-weight:bold; } .sep { border-top:1px dashed #999; margin:8px 0; } table { width:100%; border-collapse:collapse; } td { padding:2px 0; vertical-align:top; } td:last-child { width:70px; } .grand td { font-weight:bold; font-size:14px; padding-top:6px; } @media print { body { width:280px; } }</style>'
    + '</head><body>'
    + '<div class="center bold" style="font-size:16px;margin-bottom:2px">Joanna\'s Nook Bed &amp; Breakfast</div>'
    + '<div class="center" style="font-size:11px;color:#555">Official Receipt</div>'
    + '<div class="sep"></div>'
    + '<div>Cashier: <b>' + data.cashier + '</b></div>'
    + '<div>Customer: <b>' + data.customer_name + '</b></div>'
    + (data.contact_number ? '<div>Contact #: <b>' + data.contact_number + '</b></div>' : '')
    + (data.invoice_id ? '<div>Invoice: <b>' + data.invoice_id + '</b></div>' : '')
    + '<div style="font-size:10px;color:#777">' + data.generated_at + '</div>'
    + '<div class="sep"></div>'
    + '<table><tbody>' + lines + '</tbody></table>'
    + '<div class="sep"></div>'
    + '<table>'
    + '<tr><td>Subtotal</td><td style="text-align:right">P' + Number(data.subtotal).toFixed(2) + '</td></tr>'
    + (data.discount > 0 ? '<tr><td>Discount' + (data.discount_label ? ' (' + data.discount_label + ')' : '') + '</td><td style="text-align:right">-P' + Number(data.discount).toFixed(2) + '</td></tr>' : '')
    + '<tr class="grand"><td>TOTAL</td><td style="text-align:right">P' + Number(data.total).toFixed(2) + '</td></tr>'
    + '<tr><td>Payment</td><td style="text-align:right">' + String(data.payment_method).toUpperCase() + '</td></tr>'
    + (data.payment_method === 'cash' && data.received ? '<tr><td>Received</td><td style="text-align:right">P' + Number(data.received).toFixed(2) + '</td></tr><tr><td>Change</td><td style="text-align:right">P' + Number(data.change).toFixed(2) + '</td></tr>' : '')
    + (data.reference_number ? '<tr><td>Ref #</td><td style="text-align:right;font-size:11px">' + data.reference_number + '</td></tr>' : '')
    + (data.charge_to_room ? '<tr><td colspan="2" style="color:#2563eb;font-size:11px">Charged to Room ' + data.room_number + '</td></tr>' : '')
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

function printA4Bill(data) {
  if (!data) data = buildReceiptData()
  const rows = data.items.map(i =>
    '<tr><td>' + i.item_name + '</td><td style="text-align:center">' + i.quantity + '</td><td style="text-align:right">P' + Number(i.unit_price).toFixed(2) + '</td><td style="text-align:right">P' + Number(i.unit_price * i.quantity).toFixed(2) + '</td></tr>'
  ).join('')

  const win = window.open('', '_blank', 'width=850,height=1100')
  win.document.write('<!DOCTYPE html><html><head><title>Bill ' + (data.invoice_id || '') + '</title>'
    + '<style>body{font-family:Arial,sans-serif;padding:40px;color:#111;font-size:13px}h1{font-size:22px;color:#1e40af;margin:0}.sub{color:#555;font-size:12px;margin-top:2px}.header{display:flex;justify-content:space-between;margin-bottom:28px}.info-block{font-size:12px}table{width:100%;border-collapse:collapse;margin:20px 0}th{background:#f1f5f9;padding:8px 12px;text-align:left;font-size:12px;border-bottom:2px solid #e2e8f0}td{padding:8px 12px;border-bottom:1px solid #f0f0f0}.totals{float:right;width:260px;margin-top:10px}.totals td{padding:5px 12px}.grand td{font-weight:bold;font-size:15px;color:#1e40af;border-top:2px solid #1e40af}.footer{text-align:center;margin-top:60px;font-size:11px;color:#999}@media print{body{padding:20px}}</style>'
    + '</head><body>'
    + '<div class="header"><div><h1>Joanna\'s Nook Bed &amp; Breakfast</h1><div class="sub">Official Bill / Statement of Account</div></div>'
    + '<div class="info-block" style="text-align:right">' + (data.invoice_id ? '<div><b>Invoice #:</b> ' + data.invoice_id + '</div>' : '') + '<div><b>Date:</b> ' + data.generated_at + '</div><div><b>Cashier:</b> ' + data.cashier + '</div></div></div>'
    + '<div class="info-block" style="margin-bottom:16px"><div><b>Customer:</b> ' + data.customer_name + '</div>' + (data.charge_to_room ? '<div><b>Room:</b> ' + data.room_number + ' (Charged to Room)</div>' : '') + '</div>'
    + '<table><thead><tr><th>Description</th><th style="text-align:center">Qty</th><th style="text-align:right">Unit Price</th><th style="text-align:right">Amount</th></tr></thead><tbody>' + rows + '</tbody></table>'
    + '<table class="totals"><tr><td>Subtotal</td><td style="text-align:right">P' + Number(data.subtotal).toFixed(2) + '</td></tr>' + (data.discount > 0 ? '<tr><td>Discount' + (data.discount_label ? ' &mdash; ' + data.discount_label : '') + '</td><td style="text-align:right">-P' + Number(data.discount).toFixed(2) + '</td></tr>' : '') + '<tr class="grand"><td>TOTAL DUE</td><td style="text-align:right">P' + Number(data.total).toFixed(2) + '</td></tr><tr><td style="color:#555;font-size:12px">Payment Method</td><td style="text-align:right;color:#555;font-size:12px">' + String(data.payment_method).toUpperCase() + '</td></tr>' + (data.reference_number ? '<tr><td style="color:#555;font-size:12px">Reference #</td><td style="text-align:right;color:#555;font-size:12px">' + data.reference_number + '</td></tr>' : '') + '</table>'
    + '<br style="clear:both"/>'
    + '<div class="footer">Joanna\'s Nook Bed &amp; Breakfast - Balingasag, Misamis Oriental - Thank you for your business!</div>'
    + '</body></html>')
  win.document.close()
  setTimeout(() => win.print(), 300)
}

function openReceiptPreview() { printThermalReceipt(buildReceiptData()) }
function openBillPreview()    { printA4Bill(buildReceiptData()) }

async function processPayment() {
  if (cart.value.length === 0) return
  if (!chargeToRoom.value && !paymentMethod.value) return
  if (chargeToRoom.value && !selectedRoom.value) {
    alert('Please select the room to charge.')
    return
  }

  processing.value = true
  try {
    const payload = {
      items: cart.value.map(i => ({
        menu_item_id: i.id,
        item_name:    i.name,
        quantity:     i.quantity,
        unit_price:   Number(i.price),
      })),
      customer_name:   customerName.value || 'Guest',
      cashier:         cashierName.value,
      discount_type:   discountType.value,
      payment_method:  chargeToRoom.value ? 'charge_to_room' : paymentMethod.value,
      received_amount: paymentMethod.value === 'cash' && receivedAmount.value ? Number(receivedAmount.value) : null,
      charge_to_room:  chargeToRoom.value ? 1 : 0,
      room_id:         selectedRoom.value?.id ?? null,
      room_number:      selectedRoom.value?.room_number ?? null,
      reference_number: referenceNumber.value || null,
    }

    const res = await api.post('/pos/checkout', payload)
    const data = res.data?.data

    const receiptData = buildReceiptData({
      invoice_id: data.invoice_id,
      total:      data.total,
      discount:   data.discount,
      subtotal:   data.subtotal,
    })

    lastInvoiceId.value     = data.invoice_id
    lastTotal.value         = data.total
    lastPaymentMethod.value = chargeToRoom.value
      ? 'Charged to Room ' + (selectedRoom.value?.room_number || '')
      : paymentMethod.value.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
    lastReceiptData.value   = receiptData

    successState.value = true
    printThermalReceipt(receiptData)

  } catch (e) {
    console.error(e)
    alert(e.response?.data?.message || 'Checkout failed. Please try again.')
  } finally {
    processing.value = false
  }
}

function resetPOS() {
  cart.value            = []
  customerName.value    = ''
  contactNumber.value   = ''
  chargeToRoom.value    = false
  selectedRoom.value    = null
  discountType.value    = applyDefaultDiscount()
  paymentMethod.value   = 'cash'
  receivedAmount.value  = ''
  referenceNumber.value = ''
  successState.value    = false
  lastInvoiceId.value   = ''
  lastTotal.value       = 0
}

// Returns the id (as string) of the default discount, or 'none'
function applyDefaultDiscount() {
  const def = allDiscounts.value.find(d => d.is_default == 1)
  return def ? String(def.id) : 'none'
}

async function fetchDiscounts() {
  discountsLoading.value = true
  try {
    const res = await api.get('/pos/discounts')
    allDiscounts.value = res.data?.data || []
    // Apply default discount if nothing is currently selected
    if (discountType.value === 'none') {
      discountType.value = applyDefaultDiscount()
    }
  } catch (e) { console.error('Failed to load discounts', e) }
  finally { discountsLoading.value = false }
}

onMounted(async () => {
  menuLoading.value = true
  try {
    const [menuRes, roomsRes] = await Promise.allSettled([
      api.get('/pos/menu'),
      api.get('/pos/rooms'),
    ])
    if (menuRes.status === 'fulfilled')  allItems.value      = menuRes.value.data?.data || []
    if (roomsRes.status === 'fulfilled') occupiedRooms.value = roomsRes.value.data?.data || []
  } catch (e) { console.error(e) }
  finally { menuLoading.value = false }

  // Fetch discounts separately so they stay in sync with Settings
  await fetchDiscounts()

  // Re-fetch discounts whenever the tab regains focus (e.g. after admin changes in Settings)
  window.addEventListener('focus', fetchDiscounts)
})

onBeforeUnmount(() => {
  window.removeEventListener('focus', fetchDiscounts)
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
