<template>
  <div class="p-6 bg-gray-50 min-h-screen font-light">

    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-semibold text-gray-800">Reservation History</h1>
      <p class="text-gray-500 text-sm mt-1">Complete record of all room and event reservations</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Total</p>
        <p class="text-xl lg:text-3xl font-semibold text-green-800">{{ stats.total_reservations ?? 0 }}</p>
      </div>
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Today's Arrivals</p>
        <p class="text-xl lg:text-3xl font-semibold text-green-600">{{ stats.today_reservations ?? 0 }}</p>
      </div>
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Today's Check-outs</p>
        <p class="text-xl lg:text-3xl font-semibold text-orange-500">{{ stats.today_checkouts ?? 0 }}</p>
      </div>
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Balance to Collect</p>
        <p class="text-2xl font-semibold text-emerald-600">&#x20B1;{{ formatMoney(stats.balance_to_collect ?? 0) }}</p>
      </div>
    </div>

    <!-- Filters Row -->
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
      <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input v-model="search" @input="onFilterChange" type="text"
          placeholder="Search by guest name, room number, or reference..."
          class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-600 bg-white" />
      </div>
      <select v-model="statusFilter" @change="onFilterChange"
        class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-600 bg-white">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="checked_in">Checked In</option>
        <option value="checked_out">Checked Out</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <select v-model="typeFilter" @change="onFilterChange"
        class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-600 bg-white">
        <option value="">All Types</option>
        <option value="room">Room</option>
        <option value="event">Event</option>
      </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-green-800 text-white">
              <th class="px-3 py-3 text-left font-medium text-xs whitespace-nowrap">Reference</th>
              <th class="px-3 py-3 text-left font-medium text-xs whitespace-nowrap">Type</th>
              <th class="px-3 py-3 text-left font-medium text-xs whitespace-nowrap">Guest</th>
              <th class="px-3 py-3 text-left font-medium text-xs whitespace-nowrap">Room / Event</th>
              <th class="px-3 py-3 text-left font-medium text-xs whitespace-nowrap">Check-in / Date</th>
              <th class="px-3 py-3 text-left font-medium text-xs whitespace-nowrap">Check-out</th>
              <th class="px-3 py-3 text-center font-medium text-xs whitespace-nowrap">Nights / Guests</th>
              <th class="px-3 py-3 text-right font-medium text-xs whitespace-nowrap">Total</th>
              <th class="px-3 py-3 text-right font-medium text-xs whitespace-nowrap">Balance</th>
              <th class="px-3 py-3 text-center font-medium text-xs whitespace-nowrap">Status</th>
              <th class="px-3 py-3 text-center font-medium text-xs whitespace-nowrap">Booked On</th>
              <th class="px-3 py-3 text-center font-medium text-xs whitespace-nowrap">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="12" class="px-4 py-12 text-center text-gray-400">
                <svg class="w-6 h-6 mx-auto mb-2 animate-spin text-gray-300" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                Loading reservations...
              </td>
            </tr>
            <tr v-else-if="filtered.length === 0">
              <td colspan="12" class="px-4 py-12 text-center text-gray-400">No reservations found</td>
            </tr>
            <tr v-for="res in filtered" :key="(res._type || 'room') + '-' + res.id"
              class="border-t border-gray-50 hover:bg-green-50/30 transition-colors">
              <td class="px-3 py-2.5 font-mono text-xs text-green-800 whitespace-nowrap">{{ res.reference_number }}</td>
              <td class="px-3 py-2.5 whitespace-nowrap">
                <span v-if="res._type === 'event'" class="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700">Event</span>
                <span v-else class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-800">Room</span>
              </td>
              <td class="px-3 py-2.5 whitespace-nowrap">
                <div class="font-medium text-gray-800">{{ res._type === 'event' ? res.client_name : res.guest_name }}</div>
                <div class="text-xs text-gray-400">{{ res._type === 'event' ? (res.client_phone || res.client_email) : res.guest_email }}</div>
              </td>
              <td class="px-3 py-2.5 whitespace-nowrap">
                <template v-if="res._type === 'event'">
                  <div class="font-medium text-gray-700">{{ res.event_type }}</div>
                  <div class="text-xs text-gray-400">{{ res.venue || res.package_set || '&mdash;' }}</div>
                </template>
                <template v-else>
                  <div class="font-medium text-gray-700">Room {{ res.room_number }}</div>
                  <div class="text-xs text-gray-400 capitalize">{{ res.room_type }}</div>
                </template>
              </td>
              <td class="px-3 py-2.5 text-gray-600 whitespace-nowrap">
                {{ res._type === 'event' ? formatDate(res.event_date) : formatDate(res.check_in_date) }}
              </td>
              <td class="px-3 py-2.5 text-gray-600 whitespace-nowrap">
                {{ res._type === 'event' ? '&mdash;' : formatDate(res.check_out_date) }}
              </td>
              <td class="px-3 py-2.5 text-center text-gray-600">{{ res._type === 'event' ? res.number_of_guests : res.nights }}</td>
              <td class="px-3 py-2.5 text-right font-medium whitespace-nowrap">&#x20B1;{{ formatMoney(res.total_amount) }}</td>
              <td class="px-3 py-2.5 text-right whitespace-nowrap"
                :class="Number(res.remaining_balance) > 0 ? 'text-orange-600 font-medium' : 'text-green-600'">
                &#x20B1;{{ formatMoney(res.remaining_balance) }}
              </td>
              <td class="px-3 py-2.5 text-center whitespace-nowrap">
                <span v-if="res._type === 'event'" class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                <span v-else :class="statusBadge(res.status)" class="px-2 py-0.5 rounded-full text-xs font-medium capitalize">
                  {{ res.status?.replace('_', ' ') }}
                </span>
              </td>
              <td class="px-3 py-2.5 text-center text-xs text-gray-400 whitespace-nowrap">{{ formatDate(res.created_at) }}</td>
              <td class="px-3 py-2.5 text-center">
                <button @click="viewReservation(res)"
                  class="px-3 py-1.5 bg-green-800 hover:bg-green-900 text-white text-xs font-medium rounded-lg transition flex items-center gap-1.5 mx-auto">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  View
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="px-4 py-3 border-t border-gray-50 text-xs text-gray-400 flex items-center justify-between">
        <span>Showing {{ filtered.length }} of {{ reservations.length + events.length }} records</span>
        <span v-if="search || statusFilter" class="text-green-600 cursor-pointer hover:underline"
          @click="search = ''; statusFilter = ''; onFilterChange()">Clear filters</span>
      </div>
    </div>

    <!-- ====== VIEW MODAL ====== -->
    <div v-if="viewTarget" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 overflow-y-auto">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-4">

        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-green-100 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-semibold text-gray-800">Reservation Details</h3>
              <p class="text-xs text-amber-600 font-mono">{{ viewTarget.reference_number }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span v-if="viewTarget._type === 'event'" class="px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Event &mdash; Completed</span>
            <span v-else :class="statusBadge(viewTarget.status)" class="px-2.5 py-1 rounded-full text-xs font-medium capitalize">
              {{ viewTarget.status?.replace('_', ' ') }}
            </span>
            <button @click="viewTarget = null" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400 ml-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">

          <!-- ── EVENT RECORD ── -->
          <template v-if="viewTarget._type === 'event'">
            <div>
              <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Client Information</h4>
              <div class="bg-gray-50 rounded-xl p-4 grid grid-cols-2 gap-3">
                <div><p class="text-xs text-gray-400 mb-0.5">Full Name</p><p class="text-sm font-medium text-gray-800">{{ viewTarget.client_name }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Phone</p><p class="text-sm text-gray-700">{{ viewTarget.client_phone || '&mdash;' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Email</p><p class="text-sm text-gray-700">{{ viewTarget.client_email || '&mdash;' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Address</p><p class="text-sm text-gray-700">{{ viewTarget.client_address || '&mdash;' }}</p></div>
              </div>
            </div>
            <div>
              <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Event Details</h4>
              <div class="bg-gray-50 rounded-xl p-4 grid grid-cols-2 gap-3">
                <div><p class="text-xs text-gray-400 mb-0.5">Event Type</p><p class="text-sm font-medium text-gray-800">{{ viewTarget.event_type }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Package / Set</p><p class="text-sm text-gray-700">{{ viewTarget.package_set || '&mdash;' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Date</p><p class="text-sm text-gray-700">{{ formatDate(viewTarget.event_date) }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Venue</p><p class="text-sm text-gray-700">{{ viewTarget.venue || '&mdash;' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Guests</p><p class="text-sm text-gray-700">{{ viewTarget.number_of_guests }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Payment Method</p><p class="text-sm text-gray-700 capitalize">{{ viewTarget.payment_method?.replace('_',' ') || '&mdash;' }}</p></div>
              </div>
            </div>
            <div>
              <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Payment</h4>
              <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                <div class="flex justify-between text-sm"><span class="text-gray-500">Total Amount</span><span class="font-medium text-gray-800">&#x20B1;{{ formatMoney(viewTarget.total_amount) }}</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Amount Paid</span><span class="text-green-600 font-medium">&#x2212; &#x20B1;{{ formatMoney(viewTarget.down_payment) }}</span></div>
                <div class="h-px bg-gray-200 my-1"></div>
                <div class="flex justify-between">
                  <span class="text-sm font-semibold text-gray-700">Remaining Balance</span>
                  <span class="text-base font-bold" :class="Number(viewTarget.remaining_balance) > 0 ? 'text-orange-600' : 'text-green-600'">&#x20B1;{{ formatMoney(viewTarget.remaining_balance) }}</span>
                </div>
              </div>
            </div>
            <div v-if="viewTarget.notes">
              <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Notes</h4>
              <div class="bg-amber-50 border border-amber-100 rounded-xl p-4"><p class="text-sm text-amber-800">{{ viewTarget.notes }}</p></div>
            </div>
          </template>

          <!-- ── ROOM RECORD ── -->
          <template v-else>
          <div>
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              Guest Information
            </h4>
            <div class="bg-gray-50 rounded-xl p-4 grid grid-cols-2 gap-3">
              <div>
                <p class="text-xs text-gray-400 mb-0.5">Full Name</p>
                <p class="text-sm font-medium text-gray-800">{{ viewTarget.guest_name }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400 mb-0.5">Email Address</p>
                <p class="text-sm text-gray-700">{{ viewTarget.guest_email || '\u2014' }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400 mb-0.5">Number of Guests</p>
                <p class="text-sm text-gray-700">{{ viewTarget.number_of_guests }} guest{{ viewTarget.number_of_guests !== 1 ? 's' : '' }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400 mb-0.5">Booked On</p>
                <p class="text-sm text-gray-700">{{ formatDateTime(viewTarget.created_at) }}</p>
              </div>
            </div>
          </div>

          <!-- Room Details -->
          <div>
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
              Room Details
            </h4>
            <div class="bg-gray-50 rounded-xl p-4 grid grid-cols-2 gap-3">
              <div>
                <p class="text-xs text-gray-400 mb-0.5">Room Number</p>
                <p class="text-sm font-semibold text-green-800">Room {{ viewTarget.room_number }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400 mb-0.5">Room Type</p>
                <p class="text-sm text-gray-700 capitalize">{{ viewTarget.room_type }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400 mb-0.5">Price per Night</p>
                <p class="text-sm font-medium text-gray-800">&#x20B1;{{ formatMoney(viewTarget.room_price) }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400 mb-0.5">Duration</p>
                <p class="text-sm text-gray-700">{{ viewTarget.nights }} night{{ viewTarget.nights !== 1 ? 's' : '' }}</p>
              </div>
            </div>
          </div>

          <!-- Stay Dates -->
          <div>
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              Stay Dates
            </h4>
            <div class="bg-gray-50 rounded-xl p-4">
              <div class="flex items-center gap-4">
                <div class="flex-1 text-center bg-white rounded-xl border border-gray-100 p-3">
                  <p class="text-xs text-gray-400 mb-1">Check-in</p>
                  <p class="text-sm font-semibold text-green-600">{{ formatDate(viewTarget.check_in_date) }}</p>
                </div>
                <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
                <div class="flex-1 text-center bg-white rounded-xl border border-gray-100 p-3">
                  <p class="text-xs text-gray-400 mb-1">Check-out</p>
                  <p class="text-sm font-semibold text-red-500">{{ formatDate(viewTarget.check_out_date) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Details -->
          <div>
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              Payment Details
            </h4>
            <div class="bg-gray-50 rounded-xl p-4 space-y-2">
              <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 text-sm">
                <span class="text-gray-500">Room charges ({{ viewTarget.nights }} night{{ viewTarget.nights !== 1 ? 's' : '' }} × &#x20B1;{{ formatMoney(viewTarget.room_price) }})</span>
                <span class="font-medium text-gray-800">&#x20B1;{{ formatMoney(viewTarget.total_amount) }}</span>
              </div>
              <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 text-sm">
                <span class="text-gray-500">Down payment</span>
                <span class="text-green-600 font-medium">&#x2212; &#x20B1;{{ formatMoney(viewTarget.down_payment) }}</span>
              </div>
              <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 text-sm">
                <span class="text-gray-500">Café / restaurant charges</span>
                <span class="text-gray-600">&#x20B1;{{ formatMoney(viewTarget.cafe_payment ?? 0) }}</span>
              </div>
              <div class="h-px bg-gray-200 my-1"></div>
              <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                <span class="text-sm font-semibold text-gray-700">Total Balance Due</span>
                <span class="text-base font-bold"
                  :class="Number(viewTarget.remaining_balance) > 0 ? 'text-orange-600' : 'text-green-600'">
                  &#x20B1;{{ formatMoney(Number(viewTarget.remaining_balance) + Number(viewTarget.cafe_payment ?? 0)) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Special Requests -->
          <div v-if="viewTarget.special_requests">
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
              Special Requests
            </h4>
            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4">
              <p class="text-sm text-amber-800">{{ viewTarget.special_requests }}</p>
            </div>
          </div>

          </template><!-- end room -->
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
          <button @click="viewTarget = null"
            class="px-5 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">
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

const reservations = ref([])
const events = ref([])
const stats = ref({})
const loading = ref(false)
const search = ref('')
const statusFilter = ref('')
const typeFilter = ref('')
const viewTarget = ref(null)

let debounceTimer = null
function onFilterChange() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    fetchReservations()
    fetchEvents()
  }, 300)
}

const filtered = computed(() => {
  const sq = search.value.toLowerCase()
  const roomRows = reservations.value.map(r => ({ ...r, _type: 'room' }))
  const eventRows = events.value.map(e => ({ ...e, _type: 'event' }))

  let rows
  if (typeFilter.value === 'event')      rows = eventRows
  else if (typeFilter.value === 'room')  rows = roomRows
  else                                   rows = [...roomRows, ...eventRows]

  if (sq) {
    rows = rows.filter(r => {
      const name  = (r._type === 'event' ? r.client_name : r.guest_name) || ''
      const ref   = r.reference_number || ''
      const extra = r._type === 'event'
        ? (r.event_type + ' ' + (r.venue || ''))
        : ('Room ' + (r.room_number || '') + ' ' + (r.room_type || ''))
      return (name + ' ' + ref + ' ' + extra).toLowerCase().includes(sq)
    })
  }

  // Sort combined list by date descending
  rows.sort((a, b) => {
    const da = new Date(a._type === 'event' ? a.event_date : a.check_in_date)
    const db = new Date(b._type === 'event' ? b.event_date : b.check_in_date)
    return db - da
  })

  return rows
})

async function fetchReservations() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (search.value)       params.set('search', search.value)
    if (statusFilter.value) params.set('status', statusFilter.value)
    const qs = params.toString()
    const res = await api.get('/admin/reservations' + (qs ? '?' + qs : ''))
    reservations.value = res.data?.data ?? res.data ?? []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

async function fetchEvents() {
  try {
    const params = new URLSearchParams({ status: 'completed' })
    if (search.value) params.set('search', search.value)
    const res = await api.get('/events?' + params.toString())
    events.value = res.data?.data ?? []
  } catch (e) {
    console.error(e)
  }
}

async function fetchStats() {
  try {
    const res = await api.get('/admin/reservations/stats')
    stats.value = res.data?.data ?? res.data ?? {}
  } catch (e) { console.error(e) }
}

function viewReservation(res) {
  viewTarget.value = res
}

function formatMoney(v) {
  return Number(v || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}
function formatDate(d) {
  if (!d) return '\u2014'
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
function formatDateTime(d) {
  if (!d) return '\u2014'
  return new Date(d).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' })
}
function statusBadge(status) {
  const map = {
    pending:     'bg-yellow-100 text-yellow-700',
    approved:    'bg-green-100 text-green-800',
    checked_in:  'bg-green-100 text-green-700',
    checked_out: 'bg-gray-100 text-gray-600',
    cancelled:   'bg-red-100 text-red-600',
  }
  return map[status] ?? 'bg-gray-100 text-gray-500'
}

onMounted(() => {
  fetchReservations()
  fetchEvents()
  fetchStats()
})
</script>
