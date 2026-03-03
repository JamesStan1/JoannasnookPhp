<template>
  <div class="min-h-screen bg-gray-50 p-6">

    <!-- ── Header ── -->
    <div class="flex items-center gap-3 mb-1">
      <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <h1 class="text-2xl font-bold text-green-800">Bills &amp; Invoices</h1>
    </div>
    <p class="text-sm text-gray-500 mb-6">View and manage all room, event and POS bills</p>

    <!-- ── Tabs ── -->
    <div class="flex items-center gap-6 border-b border-gray-200 mb-5">
      <button @click="activeTab = 'room'"
        :class="[
          'pb-3 text-sm font-medium flex items-center gap-2 border-b-2 transition-colors',
          activeTab === 'room'
            ? 'border-blue-600 text-amber-600'
            : 'border-transparent text-gray-500 hover:text-gray-700'
        ]">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Room Bills <span class="px-1.5 py-0.5 bg-green-100 text-amber-600 rounded text-xs font-semibold">{{ roomBills.length }}</span>
      </button>
      <button @click="activeTab = 'event'"
        :class="[
          'pb-3 text-sm font-medium flex items-center gap-2 border-b-2 transition-colors',
          activeTab === 'event'
            ? 'border-blue-600 text-amber-600'
            : 'border-transparent text-gray-500 hover:text-gray-700'
        ]">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
        </svg>
        Event Bills <span class="px-1.5 py-0.5 bg-green-100 text-amber-600 rounded text-xs font-semibold">{{ eventBills.length }}</span>
      </button>
      <button @click="activeTab = 'pos'"
        :class="[
          'pb-3 text-sm font-medium flex items-center gap-2 border-b-2 transition-colors',
          activeTab === 'pos'
            ? 'border-orange-500 text-orange-500'
            : 'border-transparent text-gray-500 hover:text-gray-700'
        ]">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7H6a2 2 0 00-2 2v9a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-3m-4-4h4m-4 0a1 1 0 00-1 1v1m-1-1a1 1 0 011-1h4a1 1 0 011 1v1m-5 0h4"/>
        </svg>
        POS Receipts <span class="px-1.5 py-0.5 bg-orange-100 text-orange-600 rounded text-xs font-semibold">{{ posOrders.length }}</span>
      </button>
    </div>

    <!-- ── Search + Filter ── -->
    <div class="flex items-center gap-3 mb-4">
      <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input v-model="search" type="text" placeholder="Search by customer, invoice #, room, event, or cashier..."
          class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 shadow-sm"/>
      </div>
      <select v-model="periodFilter"
        class="border border-gray-200 rounded-lg text-sm px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 shadow-sm text-gray-600 min-w-[120px]">
        <option value="all">All Time</option>
        <option value="today">Today</option>
        <option value="week">This Week</option>
        <option value="month">This Month</option>
        <option value="year">This Year</option>
      </select>
    </div>

    <!-- ── Table ── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-5">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">

          <!-- Room Bills -->
          <template v-if="activeTab === 'room'">
            <thead>
              <tr class="border-b border-gray-100">
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Bill #</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Customer</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Room</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Package</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Check-In</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Check-Out</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Amount</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                  <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-green-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                  </svg>
                  Loading...
                </td>
              </tr>
              <tr v-else-if="filteredRoomBills.length === 0">
                <td colspan="8" class="px-5 py-14 text-center text-gray-400 text-sm">No room bills found.</td>
              </tr>
              <tr v-for="bill in filteredRoomBills" :key="bill.id"
                class="border-t border-gray-50 hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3.5 font-medium text-gray-700">#{{ bill.id }}</td>
                <td class="px-5 py-3.5 font-semibold text-gray-800">{{ bill.guest_name || '—' }}</td>
                <td class="px-5 py-3.5 text-gray-600">{{ bill.room_number || '—' }}</td>
                <td class="px-5 py-3.5 text-gray-500">{{ bill.room_type || '—' }}</td>
                <td class="px-5 py-3.5 text-gray-500">{{ formatDate(bill.check_in_date) }}</td>
                <td class="px-5 py-3.5 text-gray-500">{{ formatDate(bill.check_out_date) }}</td>
                <td class="px-5 py-3.5 font-semibold text-amber-600">&#x20B1;{{ formatMoney(bill.total_amount) }}</td>
                <td class="px-5 py-3.5">
                  <div class="flex items-center gap-3">
                    <button @click="openDetail(bill)" title="View" class="text-green-600 hover:text-green-800 transition-colors">
                      <svg class="w-4.5 h-4.5 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>
                    <button @click="printBill(bill)" title="Print" class="text-gray-400 hover:text-gray-600 transition-colors">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </template>

          <!-- Event Bills -->
          <template v-else-if="activeTab === 'event'">
            <thead>
              <tr class="border-b border-gray-100">
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Bill #</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Customer</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Event Type</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Venue</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Event Date</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Amount</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                  <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-green-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                  </svg>
                  Loading...
                </td>
              </tr>
              <tr v-else-if="filteredEventBills.length === 0">
                <td colspan="8" class="px-5 py-14 text-center text-gray-400 text-sm">No event bills found.</td>
              </tr>
              <tr v-for="bill in filteredEventBills" :key="bill.id"
                class="border-t border-gray-50 hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3.5 font-medium text-gray-700">#{{ bill.id }}</td>
                <td class="px-5 py-3.5 font-semibold text-gray-800">{{ bill.guest_name || bill.event_client || '—' }}</td>
                <td class="px-5 py-3.5 text-gray-600">{{ bill.event_type || '—' }}</td>
                <td class="px-5 py-3.5 text-gray-500">{{ bill.venue || '—' }}</td>
                <td class="px-5 py-3.5 text-gray-500">{{ formatDate(bill.event_date) }}</td>
                <td class="px-5 py-3.5 font-semibold text-amber-600">&#x20B1;{{ formatMoney(bill.total_amount) }}</td>
                <td class="px-5 py-3.5">
                  <span :class="statusBadge(bill.payment_status)"
                    class="px-2.5 py-1 rounded-full text-xs font-medium capitalize">
                    {{ bill.payment_status }}
                  </span>
                </td>
                <td class="px-5 py-3.5">
                  <div class="flex items-center gap-3">
                    <button @click="openDetail(bill)" title="View" class="text-green-600 hover:text-green-800 transition-colors">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>
                    <button @click="printBill(bill)" title="Print" class="text-gray-400 hover:text-gray-600 transition-colors">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </template>

          <!-- POS Receipts -->
          <template v-else-if="activeTab === 'pos'">
            <thead>
              <tr class="border-b border-gray-100">
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Invoice #</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Cashier</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Customer</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Items</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Total</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Payment</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Charged to Room</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                  <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-green-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                  </svg>
                  Loading...
                </td>
              </tr>
              <tr v-else-if="filteredPosOrders.length === 0">
                <td colspan="8" class="px-5 py-14 text-center text-gray-400 text-sm">No POS receipts found.</td>
              </tr>
              <tr v-for="order in filteredPosOrders" :key="order.id"
                class="border-t border-gray-50 hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3.5 font-medium text-gray-700 font-mono text-xs">{{ order.invoice_id }}</td>
                <td class="px-5 py-3.5 text-gray-600">{{ order.cashier_name || '—' }}</td>
                <td class="px-5 py-3.5 font-semibold text-gray-800">{{ order.customer_name || 'Guest' }}</td>
                <td class="px-5 py-3.5 text-gray-500 max-w-[200px]">
                  <div class="flex flex-wrap gap-1">
                    <span v-for="item in (order.items || [])"
                      :key="item.item_name"
                      class="inline-block bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded"
                    >{{ item.quantity }}× {{ item.item_name }}</span>
                    <span v-if="!order.items || order.items.length === 0" class="text-gray-400">—</span>
                  </div>
                </td>
                <td class="px-5 py-3.5 font-semibold text-orange-600">&#x20B1;{{ formatMoney(order.total_amount) }}</td>
                <td class="px-5 py-3.5">
                  <span class="capitalize text-sm text-gray-600">{{ (order.payment_method || '').replace(/_/g,' ') }}</span>
                </td>
                <td class="px-5 py-3.5">
                  <span v-if="order.charge_to_room == 1 || order.charge_to_room === true"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-amber-600">
                    Room {{ order.room_number }}
                  </span>
                  <span v-else class="text-gray-400 text-xs">—</span>
                </td>
                <td class="px-5 py-3.5 text-gray-500">{{ formatDate(order.created_at) }}</td>
              </tr>
            </tbody>
          </template>

        </table>
      </div>
    </div>

    <!-- ── Summary cards ── -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <!-- Room Bills -->
      <div class="bg-green-50 rounded-xl p-5 flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-amber-600 mb-1">Room Bills</p>
          <p class="text-xl lg:text-3xl font-bold text-gray-800">{{ roomBills.length }}</p>
        </div>
        <svg class="w-10 h-10 text-green-500 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
      </div>
      <!-- Event Bills -->
      <div class="bg-purple-50 rounded-xl p-5 flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-purple-600 mb-1">Event Bills</p>
          <p class="text-xl lg:text-3xl font-bold text-gray-800">{{ eventBills.length }}</p>
        </div>
        <svg class="w-10 h-10 text-purple-400 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
        </svg>
      </div>
      <!-- POS Receipts -->
      <div class="bg-orange-50 rounded-xl p-5 flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-orange-600 mb-1">POS Receipts</p>
          <p class="text-xl lg:text-3xl font-bold text-gray-800">{{ posOrders.length }}</p>
        </div>
        <svg class="w-10 h-10 text-orange-400 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7H6a2 2 0 00-2 2v9a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-3m-4-4h4m-4 0a1 1 0 00-1 1v1m-1-1a1 1 0 011-1h4a1 1 0 011 1v1m-5 0h4"/>
        </svg>
      </div>
      <!-- Total -->
      <div class="bg-gray-50 rounded-xl p-5 flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-600 mb-1">Total Records</p>
          <p class="text-xl lg:text-3xl font-bold text-gray-800">{{ allBills.length + posOrders.length }}</p>
        </div>
        <svg class="w-10 h-10 text-gray-400 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
      </div>
    </div>


    <!-- ══ VIEW DETAIL MODAL ══ -->
    <transition name="fade">
      <div v-if="detailBill" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg">
          <!-- Modal header -->
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Bill #{{ detailBill.id }} Details</h2>
            <button @click="detailBill = null" class="text-gray-400 hover:text-gray-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          <!-- Modal body -->
          <div class="px-6 py-5 space-y-3 text-sm">
            <div class="grid grid-cols-2 gap-3">
              <div><p class="text-xs text-gray-400 mb-0.5">Bill Number</p><p class="font-medium text-gray-700">{{ detailBill.bill_number || '#' + detailBill.id }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Type</p><p class="font-medium capitalize text-gray-700">{{ detailBill.bill_type }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Customer</p><p class="font-medium text-gray-700">{{ detailBill.guest_name || detailBill.event_client || '—' }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Payment Status</p>
                <span :class="statusBadge(detailBill.payment_status)" class="px-2 py-0.5 rounded-full text-xs font-medium capitalize">{{ detailBill.payment_status }}</span>
              </div>
              <template v-if="detailBill.bill_type === 'room'">
                <div><p class="text-xs text-gray-400 mb-0.5">Room</p><p class="font-medium text-gray-700">{{ detailBill.room_number || '—' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Package</p><p class="font-medium text-gray-700">{{ detailBill.room_type || '—' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Check-In</p><p class="font-medium text-gray-700">{{ formatDate(detailBill.check_in_date) }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Check-Out</p><p class="font-medium text-gray-700">{{ formatDate(detailBill.check_out_date) }}</p></div>
              </template>
              <template v-else>
                <div><p class="text-xs text-gray-400 mb-0.5">Event Type</p><p class="font-medium text-gray-700">{{ detailBill.event_type || '—' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Venue</p><p class="font-medium text-gray-700">{{ detailBill.venue || '—' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Event Date</p><p class="font-medium text-gray-700">{{ formatDate(detailBill.event_date) }}</p></div>
              </template>
            </div>
            <div class="border-t border-gray-100 pt-3 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
              <span class="text-gray-500">Total Amount</span>
              <span class="text-lg font-bold text-amber-600">&#x20B1;{{ formatMoney(detailBill.total_amount) }}</span>
            </div>
          </div>
          <div class="px-6 pb-5 flex justify-end gap-3">
            <button @click="printBill(detailBill)"
              class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
              </svg>
              Print
            </button>
            <button @click="detailBill = null"
              class="px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-medium transition-colors">
              Close
            </button>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'

// ── State ──────────────────────────────────────────────────
const allBills    = ref([])
const posOrders   = ref([])
const loading     = ref(true)
const activeTab   = ref('room')
const search      = ref('')
const periodFilter = ref('all')
const detailBill  = ref(null)

// ── Derived lists ──────────────────────────────────────────
const roomBills  = computed(() => allBills.value.filter(b => b.bill_type === 'room' || b.bill_type === 'room_service'))
const eventBills = computed(() => allBills.value.filter(b => b.bill_type === 'event'))

function applyPeriod(bills) {
  if (periodFilter.value === 'all') return bills
  const now = new Date()
  return bills.filter(b => {
    const d = new Date(b.created_at)
    if (periodFilter.value === 'today') return d.toDateString() === now.toDateString()
    if (periodFilter.value === 'week') {
      const start = new Date(now); start.setDate(now.getDate() - now.getDay())
      const end   = new Date(start); end.setDate(start.getDate() + 6)
      return d >= start && d <= end
    }
    if (periodFilter.value === 'month') return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear()
    if (periodFilter.value === 'year')  return d.getFullYear() === now.getFullYear()
    return true
  })
}

function applySearch(bills) {
  if (!search.value.trim()) return bills
  const q = search.value.toLowerCase()
  return bills.filter(b =>
    (b.guest_name   && b.guest_name.toLowerCase().includes(q))   ||
    (b.event_client && b.event_client.toLowerCase().includes(q)) ||
    (b.bill_number  && b.bill_number.toLowerCase().includes(q))  ||
    (b.room_number  && String(b.room_number).toLowerCase().includes(q)) ||
    (b.event_type   && b.event_type.toLowerCase().includes(q))   ||
    String(b.id).includes(q)
  )
}

const filteredRoomBills  = computed(() => applySearch(applyPeriod(roomBills.value)))
const filteredEventBills = computed(() => applySearch(applyPeriod(eventBills.value)))
const filteredPosOrders  = computed(() => {
  let list = posOrders.value
  // Period filter
  if (periodFilter.value !== 'all') {
    const now = new Date()
    list = list.filter(o => {
      const d = new Date(o.created_at)
      if (periodFilter.value === 'today') return d.toDateString() === now.toDateString()
      if (periodFilter.value === 'week') {
        const start = new Date(now); start.setDate(now.getDate() - now.getDay())
        const end = new Date(start); end.setDate(start.getDate() + 6)
        return d >= start && d <= end
      }
      if (periodFilter.value === 'month') return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear()
      if (periodFilter.value === 'year') return d.getFullYear() === now.getFullYear()
      return true
    })
  }
  // Search
  if (search.value.trim()) {
    const q = search.value.toLowerCase()
    list = list.filter(o =>
      (o.invoice_id     && o.invoice_id.toLowerCase().includes(q))     ||
      (o.customer_name  && o.customer_name.toLowerCase().includes(q))  ||
      (o.cashier_name   && o.cashier_name.toLowerCase().includes(q))   ||
      (o.room_number    && String(o.room_number).includes(q))           ||
      String(o.id).includes(q)
    )
  }
  return list
})

// ── Helpers ────────────────────────────────────────────────
function formatMoney(v) { return Number(v || 0).toFixed(2) }

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { month: 'numeric', day: 'numeric', year: 'numeric' })
}

function statusBadge(s) {
  if (s === 'paid')    return 'bg-green-100 text-green-700'
  if (s === 'partial') return 'bg-yellow-100 text-yellow-700'
  return 'bg-gray-100 text-gray-600'
}

// ── Actions ────────────────────────────────────────────────
function openDetail(bill) { detailBill.value = bill }

function printBill(bill) {
  const win = window.open('', '_blank', 'width=600,height=700')
  win.document.write(`
    <html><head><title>Bill #${bill.id}</title>
    <style>
      body{font-family:sans-serif;padding:32px;color:#111;}
      h1{font-size:20px;margin-bottom:4px;}
      p{margin:4px 0;font-size:13px;color:#555;}
      hr{margin:16px 0;border:none;border-top:1px solid #ddd;}
      .row{display:flex;justify-content:space-between;font-size:13px;margin:4px 0;}
      .total{font-size:16px;font-weight:700;}
    </style></head><body>
    <h1>Joanna's Nook Bed &amp; Breakfast</h1>
    <p>Bill #${bill.id} &nbsp;|&nbsp; ${bill.bill_type?.toUpperCase()}</p>
    <hr/>
    <div class="row"><span>Customer</span><span>${bill.guest_name || bill.event_client || '—'}</span></div>
    ${bill.bill_type === 'room' ? `
      <div class="row"><span>Room</span><span>${bill.room_number || '—'}</span></div>
      <div class="row"><span>Package</span><span>${bill.room_type || '—'}</span></div>
      <div class="row"><span>Check-In</span><span>${bill.check_in_date || '—'}</span></div>
      <div class="row"><span>Check-Out</span><span>${bill.check_out_date || '—'}</span></div>
    ` : `
      <div class="row"><span>Event</span><span>${bill.event_type || '—'}</span></div>
      <div class="row"><span>Venue</span><span>${bill.venue || '—'}</span></div>
      <div class="row"><span>Date</span><span>${bill.event_date || '—'}</span></div>
    `}
    <div class="row"><span>Payment Status</span><span>${bill.payment_status}</span></div>
    <hr/>
    <div class="row total"><span>TOTAL</span><span>₱${formatMoney(bill.total_amount)}</span></div>
    <hr/>
    <p style="text-align:center;margin-top:20px;">Thank you!</p>
    </body></html>
  `)
  win.document.close()
  win.print()
}

// ── Load ───────────────────────────────────────────────────
onMounted(async () => {
  try {
    const [billsRes, posRes] = await Promise.all([
      api.get('/bills'),
      api.get('/pos/orders'),
    ])
    allBills.value  = billsRes.data.data || []
    posOrders.value = posRes.data.data   || []
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
