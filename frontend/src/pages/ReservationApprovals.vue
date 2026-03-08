<template>
  <div class="p-6 bg-gray-50 min-h-screen font-light">

    <!-- Header -->
    <div class="mb-5">
      <h1 class="text-2xl font-semibold text-green-800">Reservation Approvals</h1>
      <p class="text-gray-500 text-sm mt-1">Review online requests, approve or reject, and manage confirmed reservations</p>
    </div>

    <!-- Tab Bar -->
    <div class="bg-white border-b border-gray-200 mb-6 -mx-6 px-6">
      <div class="flex gap-0 overflow-x-auto">
        <button v-for="tab in tabs" :key="tab.key" @click="setTab(tab.key)"
          :class="['px-5 py-3 text-sm whitespace-nowrap border-b-2 transition-colors duration-150 flex items-center gap-2',
            activeTab === tab.key ? 'border-blue-600 text-amber-600 font-medium' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300']">
          {{ tab.label }}
          <span v-if="tabCounts[tab.key]"
            :class="['text-xs px-1.5 py-0.5 rounded-full font-medium', activeTab === tab.key ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500']">
            {{ tabCounts[tab.key] }}
          </span>
        </button>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- ONLINE REQUESTS TAB                                            -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <template v-if="activeTab === 'online'">
      <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <div class="relative flex-1 max-w-sm">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input v-model="onlineSearch" type="text" placeholder="Search name, reference, email..."
            class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-600 bg-white" />
        </div>
        <select v-model="onlineTypeFilter"
          class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-600 bg-white">
          <option value="">All Types</option>
          <option value="room">Room</option>
          <option value="event">Event</option>
        </select>
      </div>

      <div v-if="onlineLoading" class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex flex-col items-center justify-center py-20 text-gray-400">
          <svg class="w-8 h-8 mb-3 animate-spin text-gray-300" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
          </svg>
          Loading requests...
        </div>
      </div>
      <div v-else-if="filteredOnline.length === 0" class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex flex-col items-center justify-center py-20">
          <svg class="w-14 h-14 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          <p class="text-base font-medium text-gray-500">No requests found</p>
        </div>
      </div>
      <div v-else class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                <th class="px-4 py-3 text-left font-medium">Ref #</th>
                <th class="px-4 py-3 text-left font-medium">Type</th>
                <th class="px-4 py-3 text-left font-medium">Guest</th>
                <th class="px-4 py-3 text-left font-medium">Details</th>
                <th class="px-4 py-3 text-right font-medium">Est. Total</th>
                <th class="px-4 py-3 text-center font-medium">Status</th>
                <th class="px-4 py-3 text-center font-medium">Submitted</th>
                <th class="px-4 py-3 text-center font-medium">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="req in filteredOnline" :key="req.id" class="hover:bg-green-50/20 transition-colors">
                <td class="px-4 py-3 font-mono text-xs text-amber-600">{{ req.reference_number }}</td>
                <td class="px-4 py-3">
                  <span :class="req.reservation_type === 'room' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-700'"
                    class="px-2 py-0.5 rounded-full text-xs font-medium capitalize">{{ req.reservation_type }}</span>
                </td>
                <td class="px-4 py-3">
                  <div class="font-medium text-gray-800">{{ req.guest_name }}</div>
                  <div class="text-xs text-gray-400">{{ req.guest_email }}</div>
                  <div class="text-xs text-gray-400">{{ req.guest_contact }}</div>
                </td>
                <td class="px-4 py-3 text-xs text-gray-600">
                  <template v-if="req.reservation_type === 'room'">
                    <div>Room: {{ req.room_number || req.room_id }}</div>
                    <div>{{ formatDate(req.check_in_date) }} → {{ formatDate(req.check_out_date) }}</div>
                    <div>{{ req.number_of_guests }} guest(s)</div>
                  </template>
                  <template v-else>
                    <div>{{ req.event_name || 'Event Reservation' }}</div>
                    <div>Date: {{ formatDate(req.event_date) }}</div>
                    <div v-if="req.event_package_name">Pkg: {{ req.event_package_name }}</div>
                  </template>
                </td>
                <td class="px-4 py-3 text-right font-medium text-gray-800">
                  &#x20B1;{{ formatMoney(req.estimated_total) }}
                </td>
                <td class="px-4 py-3 text-center">
                  <span :class="pendingStatusBadge(req.status)" class="px-2.5 py-1 rounded-full text-xs font-medium capitalize">{{ req.status }}</span>
                </td>
                <td class="px-4 py-3 text-center text-xs text-gray-400">{{ formatDateTime(req.created_at) }}</td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-center gap-1.5">
                    <button @click="viewPending(req)" title="View details"
                      class="p-1.5 text-amber-600 hover:bg-green-50 rounded-lg transition">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>
                    <button v-if="req.status === 'pending'" @click="openApproveModal(req)"
                      class="px-2.5 py-1 text-xs font-medium bg-green-50 text-green-700 hover:bg-green-100 rounded-lg transition flex items-center gap-1">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      Approve
                    </button>
                    <button v-if="req.status === 'pending'" @click="openRejectModal(req)"
                      class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition" title="Reject">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-50 text-xs text-gray-400">
          {{ filteredOnline.length }} request{{ filteredOnline.length !== 1 ? 's' : '' }}
        </div>
      </div>
    </template>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- CONFIRMED RESERVATIONS TABS                                    -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <template v-else>
      <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <div class="relative flex-1 max-w-sm">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input v-model="search" type="text" placeholder="Search guest, room, reference..."
            class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-600 bg-white" />
        </div>
        <input v-model="dateFilter" type="date"
          class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-600 bg-white" />
      </div>

      <div v-if="loading" class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex flex-col items-center justify-center py-20 text-gray-400">
          <svg class="w-8 h-8 mb-3 animate-spin text-gray-300" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
          </svg>
          Loading reservations...
        </div>
      </div>

      <div v-else-if="filtered.length === 0" class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex flex-col items-center justify-center py-20">
          <svg class="w-14 h-14 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke-width="1.5"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6l4 2"/>
          </svg>
          <p class="text-base font-medium text-gray-500">No reservations found</p>
          <p class="text-sm text-gray-400 mt-1">Reservations with this status will appear here</p>
        </div>
      </div>

      <div v-else class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                <th class="px-4 py-3 text-left font-medium">Reference</th>
                <th class="px-4 py-3 text-left font-medium">Guest</th>
                <th class="px-4 py-3 text-left font-medium">Room</th>
                <th class="px-4 py-3 text-left font-medium">Check-in</th>
                <th class="px-4 py-3 text-left font-medium">Check-out</th>
                <th class="px-4 py-3 text-center font-medium">Nights</th>
                <th class="px-4 py-3 text-right font-medium">Total</th>
                <th class="px-4 py-3 text-right font-medium">Down Payment</th>
                <th class="px-4 py-3 text-center font-medium">Status</th>
                <th class="px-4 py-3 text-center font-medium">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="res in filtered" :key="res.id" class="hover:bg-green-50/20 transition-colors">
                <td class="px-4 py-3 font-mono text-xs text-amber-600">{{ res.reference_number }}</td>
                <td class="px-4 py-3">
                  <div class="font-medium text-gray-800">{{ res.guest_name }}</div>
                  <div class="text-xs text-gray-400">{{ res.guest_email }}</div>
                </td>
                <td class="px-4 py-3">
                  <div class="font-medium text-gray-700">Room {{ res.room_number }}</div>
                  <div class="text-xs text-gray-400 capitalize">{{ res.room_type }}</div>
                </td>
                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ formatDate(res.check_in_date) }}</td>
                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ formatDate(res.check_out_date) }}</td>
                <td class="px-4 py-3 text-center text-gray-600">{{ res.nights }}</td>
                <td class="px-4 py-3 text-right font-medium text-gray-800 whitespace-nowrap">&#x20B1;{{ formatMoney(res.total_amount) }}</td>
                <td class="px-4 py-3 text-right whitespace-nowrap"
                  :class="Number(res.down_payment) > 0 ? 'text-green-600 font-medium' : 'text-gray-400'">
                  &#x20B1;{{ formatMoney(res.down_payment) }}
                </td>
                <td class="px-4 py-3 text-center">
                  <span :class="statusBadge(res.status)" class="px-2.5 py-1 rounded-full text-xs font-medium capitalize">
                    {{ statusLabel(res.status) }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-center gap-1.5">
                    <button @click="viewReservation(res)" title="View details"
                      class="p-1.5 text-amber-600 hover:bg-green-50 rounded-lg transition">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>
                    <button v-if="res.status === 'pending'" @click="doAction(res.id, 'approve')" title="Approve"
                      class="px-2.5 py-1 text-xs font-medium bg-green-50 text-green-700 hover:bg-green-100 rounded-lg transition flex items-center gap-1">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      Approve
                    </button>
                    <button v-if="res.status === 'approved'" @click="doAction(res.id, 'check-in')" title="Check In"
                      class="px-2.5 py-1 text-xs font-medium bg-green-50 text-green-800 hover:bg-green-100 rounded-lg transition">Check In</button>
                    <button v-if="res.status === 'checked_in'" @click="handleCheckOut(res)" title="Check Out"
                      class="px-2.5 py-1 text-xs font-medium bg-purple-50 text-purple-700 hover:bg-purple-100 rounded-lg transition">Check Out</button>
                    <button v-if="['approved','checked_in','checked_out'].includes(res.status)"
                      @click="printContract(res)" title="Print Contract"
                      class="p-1.5 text-gray-500 hover:bg-gray-100 rounded-lg transition">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                      </svg>
                    </button>
                    <button v-if="res.status !== 'cancelled' && res.status !== 'checked_out'" @click="confirmReject(res)" title="Reject / Cancel"
                      class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-50 text-xs text-gray-400">
          {{ filtered.length }} reservation{{ filtered.length !== 1 ? 's' : '' }}
        </div>
      </div>
    </template>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- VIEW PENDING REQUEST MODAL                                     -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div v-if="pendingViewTarget" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 overflow-y-auto">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <div>
            <h3 class="text-base font-semibold text-gray-800">Online Reservation Request</h3>
            <p class="text-xs text-amber-600 font-mono">{{ pendingViewTarget.reference_number }}</p>
          </div>
          <div class="flex items-center gap-2">
            <span :class="pendingStatusBadge(pendingViewTarget.status)" class="px-2.5 py-1 rounded-full text-xs font-medium capitalize">{{ pendingViewTarget.status }}</span>
            <button @click="pendingViewTarget = null" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
        </div>
        <div class="p-6 space-y-4 max-h-[65vh] overflow-y-auto">
          <div class="flex items-center gap-2">
            <span :class="pendingViewTarget.reservation_type === 'room' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-700'"
              class="px-3 py-1 rounded-full text-xs font-semibold capitalize">{{ pendingViewTarget.reservation_type }} Reservation</span>
            <span class="text-xs text-gray-400">Submitted {{ formatDateTime(pendingViewTarget.created_at) }}</span>
          </div>
          <div>
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Guest Information</h4>
            <div class="bg-gray-50 rounded-xl p-4 grid grid-cols-2 gap-3">
              <div><p class="text-xs text-gray-400 mb-0.5">Full Name</p><p class="text-sm font-medium text-gray-800">{{ pendingViewTarget.guest_name }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Email</p><p class="text-sm text-gray-700">{{ pendingViewTarget.guest_email || '—' }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Contact</p><p class="text-sm text-gray-700">{{ pendingViewTarget.guest_contact || '—' }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Country</p><p class="text-sm text-gray-700">{{ pendingViewTarget.guest_country || '—' }}</p></div>
              <div class="col-span-2"><p class="text-xs text-gray-400 mb-0.5">Address</p><p class="text-sm text-gray-700">{{ pendingViewTarget.guest_address || '—' }}</p></div>
            </div>
          </div>
          <div v-if="pendingViewTarget.reservation_type === 'room'">
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Room Details</h4>
            <div class="bg-gray-50 rounded-xl p-4 grid grid-cols-2 gap-3">
              <div><p class="text-xs text-gray-400 mb-0.5">Room</p><p class="text-sm font-semibold text-green-800">{{ pendingViewTarget.room_number ? 'Room ' + pendingViewTarget.room_number : 'ID: ' + pendingViewTarget.room_id }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Guests</p><p class="text-sm text-gray-700">{{ pendingViewTarget.number_of_guests }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Check-in</p><p class="text-sm font-medium text-green-600">{{ formatDate(pendingViewTarget.check_in_date) }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Check-out</p><p class="text-sm font-medium text-red-500">{{ formatDate(pendingViewTarget.check_out_date) }}</p></div>
            </div>
          </div>
          <div v-else>
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Event Details</h4>
            <div class="bg-gray-50 rounded-xl p-4 grid grid-cols-2 gap-3">
              <div><p class="text-xs text-gray-400 mb-0.5">Event Name</p><p class="text-sm font-medium text-gray-800">{{ pendingViewTarget.event_name || '—' }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Event Type</p><p class="text-sm text-gray-700 capitalize">{{ pendingViewTarget.event_type || '—' }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Event Date</p><p class="text-sm text-gray-700">{{ formatDate(pendingViewTarget.event_date) }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Time</p><p class="text-sm text-gray-700">{{ pendingViewTarget.event_time || '—' }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Package</p><p class="text-sm text-gray-700">{{ pendingViewTarget.event_package_name || '—' }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Buffet Set</p><p class="text-sm text-gray-700">{{ pendingViewTarget.buffet_set || '—' }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Supervisor</p><p class="text-sm text-gray-700">{{ pendingViewTarget.supervisor || '—' }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Guests</p><p class="text-sm text-gray-700">{{ pendingViewTarget.number_of_guests }}</p></div>
              <div v-if="pendingViewTarget.selected_foods" class="col-span-2">
                <p class="text-xs text-gray-400 mb-0.5">Selected Foods</p>
                <p class="text-sm text-gray-700">{{ pendingViewTarget.selected_foods }}</p>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-green-50 rounded-xl p-4">
              <p class="text-xs text-gray-500 mb-1">Estimated Total</p>
              <p class="text-xl font-bold text-green-800">&#x20B1;{{ formatMoney(pendingViewTarget.estimated_total) }}</p>
            </div>
            <div v-if="pendingViewTarget.special_requests || pendingViewTarget.remarks" class="bg-amber-50 rounded-xl p-4">
              <p class="text-xs text-gray-500 mb-1">Special Requests / Remarks</p>
              <p class="text-xs text-amber-800">{{ pendingViewTarget.special_requests || pendingViewTarget.remarks }}</p>
            </div>
          </div>
          <div v-if="pendingViewTarget.status === 'rejected' && pendingViewTarget.rejection_reason" class="bg-red-50 border border-red-100 rounded-xl p-4">
            <p class="text-xs font-semibold text-red-600 mb-1">Rejection Reason</p>
            <p class="text-sm text-red-700">{{ pendingViewTarget.rejection_reason }}</p>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
          <div class="flex gap-2" v-if="pendingViewTarget.status === 'pending'">
            <button @click="openApproveModal(pendingViewTarget); pendingViewTarget = null"
              class="px-4 py-2 text-sm bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-medium">Approve</button>
            <button @click="openRejectModal(pendingViewTarget); pendingViewTarget = null"
              class="px-4 py-2 text-sm border border-red-200 text-red-600 rounded-xl hover:bg-red-50 transition font-medium">Reject</button>
          </div>
          <div v-else></div>
          <button @click="pendingViewTarget = null" class="px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Close</button>
        </div>
      </div>
    </div>

    <!-- APPROVE PENDING MODAL -->
    <div v-if="approveTarget" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <div>
            <h3 class="text-base font-semibold text-gray-800">Approve Reservation Request</h3>
            <p class="text-xs text-amber-600 font-mono">{{ approveTarget.reference_number }}</p>
          </div>
          <button @click="approveTarget = null" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="p-6 space-y-4">
          <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-sm font-medium text-gray-800">{{ approveTarget.guest_name }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ approveTarget.guest_email }}</p>
            <template v-if="approveTarget.reservation_type === 'room'">
              <p class="text-xs text-gray-600 mt-1">Room {{ approveTarget.room_number }} &middot; {{ formatDate(approveTarget.check_in_date) }} &rarr; {{ formatDate(approveTarget.check_out_date) }}</p>
            </template>
            <template v-else>
              <p class="text-xs text-gray-600 mt-1">{{ approveTarget.event_name }} &middot; {{ formatDate(approveTarget.event_date) }}</p>
            </template>
            <p class="text-sm font-bold text-green-800 mt-1">Est. Total: &#x20B1;{{ formatMoney(approveTarget.estimated_total) }}</p>
          </div>
          <div v-if="approveTarget.reservation_type === 'room'">
            <button @click="runDuplicateCheck" :disabled="dupChecking"
              class="w-full py-2 text-sm border border-green-200 text-amber-600 rounded-xl hover:bg-green-50 transition flex items-center justify-center gap-2">
              <svg v-if="dupChecking" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
              {{ dupChecking ? 'Checking...' : 'Check for Duplicate Reservations' }}
            </button>
            <div v-if="dupResult" class="mt-2">
              <div v-if="dupResult.has_conflicts" class="bg-red-50 border border-red-200 rounded-xl p-3">
                <p class="text-xs font-semibold text-red-600 mb-1">&#x26A0; Conflicts Found</p>
                <div v-for="c in dupResult.room_conflicts" :key="c.id" class="text-xs text-red-700">
                  Room conflict: {{ c.guest_name }} ({{ c.status }}) &mdash; {{ c.check_in_date }} &rarr; {{ c.check_out_date }}
                </div>
                <div v-for="c in dupResult.pending_by_guest" :key="c.id" class="text-xs text-orange-700 mt-1">
                  Existing pending: {{ c.reference_number }} ({{ c.reservation_type }})
                </div>
              </div>
              <div v-else class="bg-green-50 border border-green-200 rounded-xl p-3 text-xs text-green-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                No conflicts found. Safe to approve.
              </div>
            </div>
          </div>
          <div v-if="approveTarget.reservation_type === 'room'">
            <p class="text-xs font-medium text-gray-600 mb-2">Payment Option</p>
            <div class="grid grid-cols-2 gap-2">
              <div @click="approveForm.payment_option = 'full_payment'"
                :class="approveForm.payment_option === 'full_payment' ? 'border-blue-500 bg-green-50' : 'border-gray-200'"
                class="border-2 rounded-xl p-3 cursor-pointer transition">
                <div class="flex items-center gap-2 mb-0.5">
                  <div :class="approveForm.payment_option === 'full_payment' ? 'border-blue-500 bg-green-600' : 'border-gray-300'" class="w-4 h-4 rounded-full border-2 flex items-center justify-center">
                    <svg v-if="approveForm.payment_option === 'full_payment'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                  </div>
                  <span class="text-xs font-semibold text-gray-800">Full at Checkout</span>
                </div>
                <p class="text-xs text-gray-400 ml-6">Pay total upon checkout</p>
              </div>
              <div @click="approveForm.payment_option = 'downpayment'"
                :class="approveForm.payment_option === 'downpayment' ? 'border-blue-500 bg-green-50' : 'border-gray-200'"
                class="border-2 rounded-xl p-3 cursor-pointer transition">
                <div class="flex items-center gap-2 mb-0.5">
                  <div :class="approveForm.payment_option === 'downpayment' ? 'border-blue-500 bg-green-600' : 'border-gray-300'" class="w-4 h-4 rounded-full border-2 flex items-center justify-center">
                    <svg v-if="approveForm.payment_option === 'downpayment'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                  </div>
                  <span class="text-xs font-semibold text-gray-800">Downpayment</span>
                </div>
                <p class="text-xs text-gray-400 ml-6">Partial now, rest later</p>
              </div>
            </div>
            <div v-if="approveForm.payment_option === 'downpayment'" class="mt-3 space-y-3">
              <!-- Amount -->
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Downpayment Amount (&#x20B1;)</label>
                <input v-model="approveForm.down_payment" type="number" min="0" step="0.01" placeholder="0.00"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600" />
              </div>
              <!-- Payment Method -->
              <div>
                <p class="text-xs font-medium text-gray-600 mb-1.5">Payment Method</p>
                <div class="grid grid-cols-2 gap-2">
                  <div @click="approveForm.payment_method = 'cash'; approveForm.online_payment_type = ''; approveForm.payment_ref = ''"
                    :class="approveForm.payment_method === 'cash' ? 'border-green-500 bg-green-50' : 'border-gray-200'"
                    class="border-2 rounded-xl p-3 cursor-pointer transition">
                    <div class="flex items-center gap-2">
                      <div :class="approveForm.payment_method === 'cash' ? 'border-green-500 bg-green-500' : 'border-gray-300'" class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0">
                        <svg v-if="approveForm.payment_method === 'cash'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                      </div>
                      <div>
                        <p class="text-xs font-semibold text-gray-800">Cash</p>
                        <p class="text-xs text-gray-400">Physical payment</p>
                      </div>
                    </div>
                  </div>
                  <div @click="approveForm.payment_method = 'online'"
                    :class="approveForm.payment_method === 'online' ? 'border-blue-500 bg-green-50' : 'border-gray-200'"
                    class="border-2 rounded-xl p-3 cursor-pointer transition">
                    <div class="flex items-center gap-2">
                      <div :class="approveForm.payment_method === 'online' ? 'border-blue-500 bg-green-600' : 'border-gray-300'" class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0">
                        <svg v-if="approveForm.payment_method === 'online'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                      </div>
                      <div>
                        <p class="text-xs font-semibold text-gray-800">Online</p>
                        <p class="text-xs text-gray-400">GCash / Bank</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Online sub-options -->
              <div v-if="approveForm.payment_method === 'online'" class="space-y-2">
                <p class="text-xs font-medium text-gray-600 mb-1.5">Online Payment Type</p>
                <div class="grid grid-cols-2 gap-2">
                  <div @click="approveForm.online_payment_type = 'gcash'"
                    :class="approveForm.online_payment_type === 'gcash' ? 'border-blue-500 bg-green-50' : 'border-gray-200'"
                    class="border-2 rounded-xl p-3 cursor-pointer transition">
                    <div class="flex items-center gap-2">
                      <div :class="approveForm.online_payment_type === 'gcash' ? 'border-blue-500 bg-green-600' : 'border-gray-300'" class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0">
                        <svg v-if="approveForm.online_payment_type === 'gcash'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                      </div>
                      <div>
                        <p class="text-xs font-semibold text-green-800">GCash</p>
                        <p class="text-xs text-gray-400">Mobile wallet</p>
                      </div>
                    </div>
                  </div>
                  <div @click="approveForm.online_payment_type = 'bank_transfer'"
                    :class="approveForm.online_payment_type === 'bank_transfer' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'"
                    class="border-2 rounded-xl p-3 cursor-pointer transition">
                    <div class="flex items-center gap-2">
                      <div :class="approveForm.online_payment_type === 'bank_transfer' ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'" class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0">
                        <svg v-if="approveForm.online_payment_type === 'bank_transfer'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                      </div>
                      <div>
                        <p class="text-xs font-semibold text-indigo-700">Bank Transfer</p>
                        <p class="text-xs text-gray-400">Direct deposit</p>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Reference Number -->
                <div v-if="approveForm.online_payment_type">
                  <label class="block text-xs font-medium text-gray-600 mb-1">
                    {{ approveForm.online_payment_type === 'gcash' ? 'GCash' : 'Bank Transfer' }} Reference Number
                    <span class="text-red-400">*</span>
                  </label>
                  <input v-model="approveForm.payment_ref" type="text"
                    :placeholder="approveForm.online_payment_type === 'gcash' ? 'e.g. 123456789012' : 'e.g. TRF-20260227-001'"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 font-mono" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end">
          <button @click="approveTarget = null" class="px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
          <button @click="doApprove" :disabled="approving"
            class="px-5 py-2 text-sm bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-medium flex items-center gap-2 disabled:opacity-60">
            <svg v-if="approving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            {{ approving ? 'Approving...' : 'Confirm Approval' }}
          </button>
        </div>
      </div>
    </div>

    <!-- CHECKOUT BLOCKED (unpaid balance) -->
    <div v-if="checkoutBlockedRes" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        </div>
        <p class="text-sm font-semibold text-gray-800 mb-1">Cannot Check Out</p>
        <p class="text-xs text-gray-500 mb-3">
          <strong>{{ checkoutBlockedRes.guestName }}</strong> (Room {{ checkoutBlockedRes.roomNumber }}) still has an unpaid balance.
        </p>
        <div class="bg-orange-50 border border-orange-200 rounded-xl px-4 py-3 mb-5">
          <p class="text-xs text-orange-600 font-medium">Outstanding Balance</p>
          <p class="text-xl font-bold text-orange-700">&#x20b1;{{ Number(checkoutBlockedRes.balance).toLocaleString('en-PH', { minimumFractionDigits: 2 }) }}</p>
        </div>
        <p class="text-xs text-gray-400 mb-5">Please settle the full remaining balance before completing the checkout.</p>
        <button @click="checkoutBlockedRes = null" class="w-full py-2 text-sm bg-green-800 text-white rounded-xl hover:bg-green-900 transition">Understood</button>
      </div>
    </div>

    <!-- REJECT PENDING MODAL -->
    <div v-if="rejectPendingTarget" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <p class="text-sm font-semibold text-gray-800 text-center mb-1">Reject Request {{ rejectPendingTarget.reference_number }}?</p>
        <p class="text-xs text-gray-500 text-center mb-4">From <strong>{{ rejectPendingTarget.guest_name }}</strong></p>
        <div class="mb-4">
          <label class="block text-xs font-medium text-gray-600 mb-1">Reason for Rejection <span class="text-gray-400">(optional)</span></label>
          <textarea v-model="rejectReason" rows="3" placeholder="e.g. Room not available on requested dates..."
            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 resize-none"></textarea>
        </div>
        <div class="flex gap-3">
          <button @click="rejectPendingTarget = null; rejectReason = ''" class="flex-1 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Keep</button>
          <button @click="doReject" :disabled="rejecting"
            class="flex-1 py-2 text-sm bg-red-600 text-white rounded-xl hover:bg-red-700 transition font-medium flex items-center justify-center gap-2 disabled:opacity-60">
            <svg v-if="rejecting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            {{ rejecting ? 'Rejecting...' : 'Reject' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ====== VIEW RESERVATION MODAL ====== -->
    <div v-if="viewTarget" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 overflow-y-auto">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-4">
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
            <span :class="statusBadge(viewTarget.status)" class="px-2.5 py-1 rounded-full text-xs font-medium capitalize">
              {{ statusLabel(viewTarget.status) }}
            </span>
            <button @click="viewTarget = null" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400 ml-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
          <!-- Guest -->
          <div>
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Guest Information</h4>
            <div class="bg-gray-50 rounded-xl p-4 grid grid-cols-2 gap-3">
              <div><p class="text-xs text-gray-400 mb-0.5">Full Name</p><p class="text-sm font-medium text-gray-800">{{ viewTarget.guest_name }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Email</p><p class="text-sm text-gray-700">{{ viewTarget.guest_email || '&mdash;' }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">No. of Guests</p><p class="text-sm text-gray-700">{{ viewTarget.number_of_guests }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Booked On</p><p class="text-sm text-gray-700">{{ formatDateTime(viewTarget.created_at) }}</p></div>
            </div>
          </div>
          <!-- Room -->
          <div>
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Room Details</h4>
            <div class="bg-gray-50 rounded-xl p-4 grid grid-cols-2 gap-3">
              <div><p class="text-xs text-gray-400 mb-0.5">Room Number</p><p class="text-sm font-semibold text-green-800">Room {{ viewTarget.room_number }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Room Type</p><p class="text-sm text-gray-700 capitalize">{{ viewTarget.room_type }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Price / Night</p><p class="text-sm font-medium text-gray-800">&#x20B1;{{ formatMoney(viewTarget.room_price) }}</p></div>
              <div><p class="text-xs text-gray-400 mb-0.5">Duration</p><p class="text-sm text-gray-700">{{ viewTarget.nights }} night{{ viewTarget.nights != 1 ? 's' : '' }}</p></div>
            </div>
          </div>
          <!-- Dates -->
          <div>
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Stay Dates</h4>
            <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-4">
              <div class="flex-1 text-center bg-white rounded-xl border border-gray-100 p-3">
                <p class="text-xs text-gray-400 mb-1">Check-in</p>
                <p class="text-sm font-semibold text-green-600">{{ formatDate(viewTarget.check_in_date) }}</p>
              </div>
              <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
              <div class="flex-1 text-center bg-white rounded-xl border border-gray-100 p-3">
                <p class="text-xs text-gray-400 mb-1">Check-out</p>
                <p class="text-sm font-semibold text-red-500">{{ formatDate(viewTarget.check_out_date) }}</p>
              </div>
            </div>
          </div>
          <!-- Payment -->
          <div>
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Payment Details</h4>
            <div class="bg-gray-50 rounded-xl p-4 space-y-2">
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Room charges ({{ viewTarget.nights }} &times; &#x20B1;{{ formatMoney(viewTarget.room_price) }})</span>
                <span class="font-medium text-gray-800">&#x20B1;{{ formatMoney(viewTarget.total_amount) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Down payment received</span>
                <span class="text-green-600 font-medium">&#x2212; &#x20B1;{{ formatMoney(viewTarget.down_payment) }}</span>
              </div>
              <div class="h-px bg-gray-200"></div>
              <div class="flex justify-between">
                <span class="text-sm font-semibold text-gray-700">Remaining Balance</span>
                <span class="text-base font-bold" :class="Number(viewTarget.remaining_balance) > 0 ? 'text-orange-600' : 'text-green-600'">
                  &#x20B1;{{ formatMoney(viewTarget.remaining_balance) }}
                </span>
              </div>
            </div>
          </div>
          <!-- Special Requests -->
          <div v-if="viewTarget.special_requests">
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Special Requests</h4>
            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4">
              <p class="text-sm text-amber-800">{{ viewTarget.special_requests }}</p>
            </div>
          </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
          <div class="flex gap-2">
            <button v-if="viewTarget.status === 'pending'" @click="doAction(viewTarget.id, 'approve'); viewTarget = null"
              class="px-4 py-2 text-sm bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-medium">
              Approve
            </button>
            <button v-if="viewTarget.status === 'approved'" @click="doAction(viewTarget.id, 'check-in'); viewTarget = null"
              class="px-4 py-2 text-sm bg-green-800 text-white rounded-xl hover:bg-green-900 transition font-medium">
              Check In
            </button>
            <button v-if="viewTarget.status === 'checked_in'" @click="handleCheckOut(viewTarget); viewTarget = null"
              class="px-4 py-2 text-sm bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition font-medium">
              Check Out
            </button>
            <button v-if="['approved','checked_in','checked_out'].includes(viewTarget.status)"
              @click="printContract(viewTarget)"
              class="px-4 py-2 text-sm bg-gray-700 text-white rounded-xl hover:bg-gray-800 transition font-medium flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
              Print Contract
            </button>
            <button v-if="viewTarget.status !== 'cancelled' && viewTarget.status !== 'checked_out'"
              @click="confirmReject(viewTarget); viewTarget = null"
              class="px-4 py-2 text-sm border border-red-200 text-red-600 rounded-xl hover:bg-red-50 transition font-medium">
              Reject
            </button>
          </div>
          <button @click="viewTarget = null" class="px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- ====== REJECT CONFIRM ====== -->
    <div v-if="rejectTarget" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <p class="text-sm font-semibold text-gray-800 mb-1">Reject Reservation {{ rejectTarget.reference_number }}?</p>
        <p class="text-xs text-gray-500 mb-4">This will cancel the reservation for <strong>{{ rejectTarget.guest_name }}</strong>.</p>
        <div class="flex gap-3">
          <button @click="rejectTarget = null" class="flex-1 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Keep</button>
          <button @click="doAction(rejectTarget.id, 'cancel'); rejectTarget = null"
            class="flex-1 py-2 text-sm bg-red-600 text-white rounded-xl hover:bg-red-700 transition font-medium">Reject</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'
import { useToastStore } from '../stores/toast'
import { usePendingReservationsStore } from '../stores/pendingReservations'

const toast   = useToastStore()
const prStore = usePendingReservationsStore()
const tabs = [
  { key: 'online',     label: '🌐 Online Requests' },
  { key: 'pending',    label: 'Pending' },
  { key: 'approved',   label: 'Approved' },
  { key: 'checked_in', label: 'Checked In' },
  { key: 'rejected',   label: 'Rejected' },
  { key: 'all',        label: 'All' },
]

const activeTab = ref('online')

// ── Online requests state ─────────────────────────────────────────────────────
const onlineRequests      = ref([])
const onlineLoading       = ref(false)
const onlineSearch        = ref('')
const onlineTypeFilter    = ref('')
const pendingViewTarget   = ref(null)
const approveTarget       = ref(null)
const approveForm         = ref({ payment_option: 'full_payment', down_payment: 0 })
const approving           = ref(false)
const rejectPendingTarget = ref(null)
const rejectReason        = ref('')
const rejecting           = ref(false)
const dupChecking         = ref(false)
const dupResult           = ref(null)

// ── Reservations state ────────────────────────────────────────────────────────
const reservations = ref([])
const loading      = ref(false)
const search       = ref('')
const dateFilter   = ref('')
const viewTarget   = ref(null)
const rejectTarget = ref(null)
const checkoutBlockedRes = ref(null)

// ── Tab counts ────────────────────────────────────────────────────────────────
const tabCounts = computed(() => ({
  online:     onlineRequests.value.filter(r => r.status === 'pending').length || null,
  pending:    reservations.value.filter(r => r.status === 'pending').length || null,
  approved:   reservations.value.filter(r => r.status === 'approved').length || null,
  checked_in: reservations.value.filter(r => r.status === 'checked_in').length || null,
  rejected:   reservations.value.filter(r => r.status === 'cancelled').length || null,
  all:        reservations.value.length || null,
}))

// ── Online filtered ───────────────────────────────────────────────────────────
const filteredOnline = computed(() => {
  let list = onlineRequests.value
  if (onlineTypeFilter.value) list = list.filter(r => r.reservation_type === onlineTypeFilter.value)
  if (onlineSearch.value.trim()) {
    const s = onlineSearch.value.toLowerCase()
    list = list.filter(r =>
      r.guest_name?.toLowerCase().includes(s) ||
      r.reference_number?.toLowerCase().includes(s) ||
      r.guest_email?.toLowerCase().includes(s)
    )
  }
  return list
})

// ── Reservations filtered ────────────────────────────────────────────────────
const filtered = computed(() => {
  let list = reservations.value
  if (activeTab.value === 'pending')    list = list.filter(r => r.status === 'pending')
  else if (activeTab.value === 'approved')   list = list.filter(r => r.status === 'approved')
  else if (activeTab.value === 'checked_in') list = list.filter(r => r.status === 'checked_in')
  else if (activeTab.value === 'rejected')   list = list.filter(r => r.status === 'cancelled')
  if (search.value.trim()) {
    const s = search.value.toLowerCase()
    list = list.filter(r =>
      r.guest_name?.toLowerCase().includes(s) ||
      r.room_number?.toLowerCase().includes(s) ||
      r.reference_number?.toLowerCase().includes(s) ||
      r.guest_email?.toLowerCase().includes(s)
    )
  }
  if (dateFilter.value) {
    list = list.filter(r => r.check_in_date === dateFilter.value || r.check_out_date === dateFilter.value)
  }
  return list
})

function setTab(key) {
  activeTab.value = key
  if (key === 'online') fetchOnlineRequests()
}

// ── Fetchers ──────────────────────────────────────────────────────────────────
async function fetchOnlineRequests() {
  onlineLoading.value = true
  try {
    const res = await api.get('/pending-reservations')
    onlineRequests.value = res.data?.data ?? res.data ?? []
  } catch (e) { console.error(e) } finally { onlineLoading.value = false }
}

async function fetchAll() {
  loading.value = true
  try {
    const res = await api.get('/admin/reservations')
    reservations.value = res.data?.data ?? res.data ?? []
  } catch (e) { console.error(e) } finally { loading.value = false }
}

// ── Approve pending request ───────────────────────────────────────────────────
function openApproveModal(req) {
  approveTarget.value = req
  approveForm.value   = { payment_option: 'full_payment', down_payment: 0, payment_method: '', online_payment_type: '', payment_ref: '' }
  dupResult.value     = null
}

async function runDuplicateCheck() {
  if (!approveTarget.value) return
  dupChecking.value = true
  try {
    const params = new URLSearchParams({
      email:         approveTarget.value.guest_email || '',
      name:          approveTarget.value.guest_name  || '',
      check_in_date: approveTarget.value.check_in_date || '',
      room_id:       approveTarget.value.room_id || '',
    })
    const res = await api.get(`/pending-reservations/check-duplicate?${params}`)
    dupResult.value = res.data?.data ?? res.data
  } catch (e) { console.error(e) } finally { dupChecking.value = false }
}

async function doApprove() {
  if (!approveTarget.value) return
  approving.value = true
  try {
    if (approveForm.value.payment_option === 'downpayment') {
      if (!approveForm.value.payment_method) {
        toast.warning('Please select a payment method for the downpayment.')
        approving.value = false
        return
      }
      if (approveForm.value.payment_method === 'online' && !approveForm.value.online_payment_type) {
        toast.warning('Please select GCash or Bank Transfer.')
        approving.value = false
        return
      }
      if (approveForm.value.payment_method === 'online' && approveForm.value.online_payment_type && !approveForm.value.payment_ref.trim()) {
        toast.warning('Please enter the reference number for the online payment.')
        approving.value = false
        return
      }
    }
    const res = await api.put(`/pending-reservations/${approveTarget.value.id}/approve`, {
      payment_option:      approveForm.value.payment_option,
      down_payment:        Number(approveForm.value.down_payment) || 0,
      payment_method:      approveForm.value.payment_method || null,
      online_payment_type: approveForm.value.online_payment_type || null,
      payment_ref:         approveForm.value.payment_ref || null,
    })
    const approved = res.data?.data
    approveTarget.value = null
    await fetchOnlineRequests()
    await fetchAll()
    prStore.fetchPending()
    if (approved && approved.room_number) {
      // Room reservation — print contract
      toast.success(`Reservation approved! Guest: ${approved.guest_name} · Room ${approved.room_number}`)
      printContract(approved)
    } else if (approved && (approved.event_type || approved.event_date)) {
      // Event reservation — print contract
      toast.success(`Event reservation approved! Client: ${approved.client_name}`)
      printEventContract(approved)
    } else {
      toast.success('Reservation approved successfully!')
    }
  } catch (e) {
    toast.error(e.response?.data?.message || 'Failed to approve reservation')
  } finally { approving.value = false }
}

// ── Reject pending request ────────────────────────────────────────────────────
function openRejectModal(req) {
  rejectPendingTarget.value = req
  rejectReason.value = ''
}

async function doReject() {
  if (!rejectPendingTarget.value) return
  rejecting.value = true
  try {
    await api.put(`/pending-reservations/${rejectPendingTarget.value.id}/reject`, {
      reason: rejectReason.value,
    })
    rejectPendingTarget.value = null
    rejectReason.value = ''
    await fetchOnlineRequests()
    prStore.fetchPending()
  } catch (e) {
    toast.error(e.response?.data?.message || 'Failed to reject')
  } finally { rejecting.value = false }
}

// ── Confirmed reservation actions ─────────────────────────────────────────────
function handleCheckOut(res) {
  const balance = Number(res.remaining_balance || 0) + Number(res.cafe_payment || 0)
  if (balance > 0.01) {
    checkoutBlockedRes.value = { guestName: res.guest_name, roomNumber: res.room_number, balance }
    return
  }
  doAction(res.id, 'check-out')
}

async function doAction(id, action) {
  try {
    await api.put(`/reservations/${id}/${action}`)
    await fetchAll()
  } catch (e) {
    toast.error(e.response?.data?.message ?? `Error: ${action}`)
  }
}

function viewPending(req)     { pendingViewTarget.value = req }
function viewReservation(res) { viewTarget.value = res }
function confirmReject(res)   { rejectTarget.value = res }

// ── Print Event Contract ───────────────────────────────────────────────────────
function printEventContract(ev) {
  const total   = Number(ev.total_amount || 0)
  const dp      = Number(ev.down_payment || 0)
  const balance = total - dp
  const today   = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
  const ref     = ev.reference_number || ('EVT-' + String(ev.id).padStart(5, '0'))
  const payMethod = ev.payment_method ? ev.payment_method.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase()) : '—'
  const payRef    = ev.payment_ref ? `<div class='field'><label>Reference #</label><p style='font-family:monospace'>${ev.payment_ref}</p></div>` : ''
  const win = window.open('', '_blank', 'width=850,height=1100')
  win.document.write(`<!DOCTYPE html><html><head>
  <title>Event Contract — ${ref}</title>
  <style>
    @page { size: A4; margin: 20mm 18mm; }
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; color: #111; }
    body { padding: 32px 36px; }
    .header { display: flex; align-items: center; gap: 16px; border-bottom: 3px solid #1d4ed8; padding-bottom: 16px; margin-bottom: 20px; }
    .header h1 { font-size: 22px; color: #1d4ed8; font-weight: 700; }
    .header p  { font-size: 12px; color: #555; margin-top: 2px; }
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
    .tc-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; font-size: 11px; color: #374151; line-height: 1.7; }
    .tc-box li { margin-bottom: 4px; }
    .sig-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 32px; }
    .sig-block { border-top: 1.5px solid #111; padding-top: 8px; }
    .sig-block p { font-size: 12px; color: #374151; }
    .footer-note { text-align: right; font-size: 11px; color: #9ca3af; margin-top: 24px; }
  </style>
  </head><body>
  <div class="header"><div><h1>JOANNA'S NOOK BED &amp; BREAKFAST</h1><p>Official Event Contract &nbsp;|&nbsp; Generated ${today}</p></div></div>
  <p class="contract-title">Event Service Contract</p>
  <div style="text-align:center"><span class="ref-badge">${ref}</span></div>
  <div class="section">
    <div class="section-title">Client Information</div>
    <div class="grid-2">
      <div class="field"><label>Full Name</label><p>${ev.client_name || '—'}</p></div>
      <div class="field"><label>Phone</label><p>${ev.client_phone || ev.guest_contact || '—'}</p></div>
      <div class="field"><label>Email</label><p>${ev.client_email || ev.guest_email || '—'}</p></div>
      <div class="field"><label>Guests</label><p>${ev.number_of_guests || '—'}</p></div>
    </div>
  </div>
  <div class="section">
    <div class="section-title">Event Details</div>
    <div class="grid-2">
      <div class="field"><label>Event Type</label><p>${ev.event_type || '—'}</p></div>
      <div class="field"><label>Event Name</label><p>${ev.event_name || ev.event_type || '—'}</p></div>
      <div class="field"><label>Date</label><p>${new Date((ev.event_date||'')+'T00:00:00').toLocaleDateString('en-US',{year:'numeric',month:'long',day:'numeric'})}</p></div>
      <div class="field"><label>Time</label><p>${ev.event_time ? (()=>{ const [h,m]=(ev.event_time||'').split(':'); const hr=parseInt(h||0); return (hr%12||12)+':'+m+' '+(hr>=12?'PM':'AM'); })() : '—'}</p></div>
      <div class="field"><label>Package / Set</label><p>${ev.buffet_set || ev.package_set || '—'}</p></div>
      <div class="field"><label>Venue</label><p>Joanna's Nook Bed &amp; Breakfast</p></div>
    </div>
    ${ev.special_requests ? `<div style="margin-top:10px" class="field"><label>Special Requests</label><p>${ev.special_requests}</p></div>` : ''}
    ${ev.selected_foods  ? `<div style="margin-top:6px" class="field"><label>Selected Foods</label><p>${ev.selected_foods}</p></div>` : ''}
  </div>
  <div class="section">
    <div class="section-title">Payment Information</div>
    <div class="amount-box">
      <div class="amount-row"><span>Event Total</span><span>&#x20B1;${total.toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>
      <div class="amount-row"><span>Downpayment Received</span><span style="color:#16a34a">− &#x20B1;${dp.toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>
      <div class="amount-row total"><span>Balance Due</span><span>&#x20B1;${balance.toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>
    </div>
    <div class="grid-2" style="margin-top:10px">
      <div class="field"><label>Payment Method</label><p>${payMethod}</p></div>
      ${payRef}
    </div>
  </div>
  <div class="section">
    <div class="section-title">Terms &amp; Conditions</div>
    <div class="tc-box"><ul>
      <li>A downpayment of at least 30% is required to confirm the reservation.</li>
      <li>Full payment must be settled on or before the event date.</li>
      <li>Cancellation must be requested at least 7 days prior to the event date.</li>
      <li>Joanna's Nook Bed &amp; Breakfast reserves the right to cancel for non-compliance with terms.</li>
      <li>Additional services requested on the event day are subject to separate billing.</li>
      <li>The client is responsible for the conduct of their guests within the venue premises.</li>
    </ul></div>
  </div>
  <div class="sig-grid">
    <div class="sig-block"><p>Client Signature</p><p style="margin-top:32px">${ev.client_name || ev.guest_name || ''}</p></div>
    <div class="sig-block"><p>Authorized Representative</p><p style="margin-top:32px">Joanna's Nook Bed &amp; Breakfast Management</p></div>
  </div>
  <p class="footer-note">Printed on ${today} &nbsp;|&nbsp; Joanna's Nook Bed &amp; Breakfast, Balingasag, Misamis Oriental</p>
  <script>window.onload=function(){window.print()}<\/script>
  </body></html>`)
  win.document.close()
}

// ── Print Room Contract ─────────────────────────────────────────────────────────
function printContract(res) {
  const nights  = res.nights || Math.max(Math.floor((new Date(res.check_out_date) - new Date(res.check_in_date)) / 86400000), 1)
  const total   = Number(res.total_amount || (Number(res.room_price || 0) * nights))
  const dp      = Number(res.down_payment || 0)
  const balance = total - dp
  const today   = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
  const win = window.open('', '_blank', 'width=850,height=1100')
  win.document.write(`<!DOCTYPE html><html><head>
  <title>Contract — ${res.reference_number}</title>
  <style>
    @page { size: A4; margin: 20mm 18mm; }
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; color: #111; }
    body { padding: 32px 36px; }
    .header { display: flex; align-items: center; gap: 16px; border-bottom: 3px solid #1d4ed8; padding-bottom: 16px; margin-bottom: 20px; }
    .header h1 { font-size: 22px; color: #1d4ed8; font-weight: 700; }
    .header p  { font-size: 12px; color: #555; margin-top: 2px; }
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
    .tc-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; font-size: 11px; color: #374151; line-height: 1.7; }
    .tc-box li { margin-bottom: 4px; }
    .sig-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 32px; }
    .sig-block { border-top: 1.5px solid #111; padding-top: 8px; }
    .sig-block p { font-size: 12px; color: #374151; }
    .footer-note { text-align: right; font-size: 11px; color: #9ca3af; margin-top: 24px; }
    @media print { .print-btn { display: none !important; } }
  </style>
  </head><body>
  <div class="header">
    <div><h1>JOANNA'S NOOK BED &amp; BREAKFAST</h1><p>Official Reservation Contract &nbsp;|&nbsp; Generated ${today}</p></div>
  </div>
  <p class="contract-title">Reservation Contract</p>
  <div style="text-align:center"><span class="ref-badge">${res.reference_number}</span></div>
  <div class="section">
    <div class="section-title">Guest Information</div>
    <div class="grid-2">
      <div class="field"><label>Full Name</label><p>${res.guest_name}</p></div>
      <div class="field"><label>Email</label><p>${res.guest_email || '—'}</p></div>
      <div class="field"><label>Guests</label><p>${res.number_of_guests}</p></div>
      <div class="field"><label>Status</label><p style="text-transform:capitalize">${(res.status||'').replace('_',' ')}</p></div>
    </div>
  </div>
  <div class="section">
    <div class="section-title">Room &amp; Stay Details</div>
    <div class="grid-2">
      <div class="field"><label>Room Number</label><p>Room ${res.room_number}</p></div>
      <div class="field"><label>Type</label><p style="text-transform:capitalize">${res.room_type || ''}</p></div>
      <div class="field"><label>Check-in</label><p>${new Date(res.check_in_date).toLocaleDateString('en-US',{year:'numeric',month:'long',day:'numeric'})}</p></div>
      <div class="field"><label>Check-out</label><p>${new Date(res.check_out_date).toLocaleDateString('en-US',{year:'numeric',month:'long',day:'numeric'})}</p></div>
      <div class="field"><label>Duration</label><p>${nights} night${nights!==1?'s':''}</p></div>
      <div class="field"><label>Rate/Night</label><p>₱${Number(res.room_price||0).toLocaleString('en-PH',{minimumFractionDigits:2})}</p></div>
    </div>
    ${res.special_requests ? `<div style="margin-top:10px" class="field"><label>Special Requests</label><p>${res.special_requests}</p></div>` : ''}
  </div>
  <div class="section">
    <div class="section-title">Payment Information</div>
    <div class="amount-box">
      <div class="amount-row"><span>Room Charges (${nights} × ₱${Number(res.room_price||0).toLocaleString('en-PH',{minimumFractionDigits:2})})</span><span>₱${total.toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>
      <div class="amount-row"><span>Downpayment Received</span><span style="color:#16a34a">− ₱${dp.toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>
      <div class="amount-row total"><span>Balance Due</span><span>₱${balance.toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>
    </div>
  </div>
  <div class="section">
    <div class="section-title">Terms &amp; Conditions</div>
    <div class="tc-box"><ul>
      <li>A valid government-issued ID must be presented upon check-in.</li>
      <li>Check-in: 2:00 PM &nbsp;|&nbsp; Check-out: 12:00 noon. Late check-out subject to additional charges.</li>
      <li>All guests must register at the Front Desk. Additional guests must be declared.</li>
      <li>Valuables and important documents must be secured in the in-room safety deposit box. The hotel is not liable for any loss.</li>
      <li>Gambling, illegal drugs, and firearms are strictly prohibited on hotel premises.</li>
      <li>Smoking inside rooms is prohibited. A fumigation fee of ₱5,000.00 applies for violations.</li>
      <li>Cancellations must be made at least 48 hours before check-in to avoid a one-night cancellation fee.</li>
      <li>Joanna's Nook Bed &amp; Breakfast reserves the right to deny service to guests who violate hotel policies.</li>
    </ul></div>
  </div>
  <div class="sig-grid">
    <div><div class="sig-block"><p><strong>${res.guest_name}</strong></p><p>Guest Signature &amp; Date</p></div></div>
    <div><div class="sig-block"><p><strong>Joanna's Nook Bed &amp; Breakfast Representative</strong></p><p>Authorized Signature &amp; Date</p></div></div>
  </div>
  <p class="footer-note">Generated ${today} &nbsp;|&nbsp; Joanna's Nook Bed &amp; Breakfast &nbsp;|&nbsp; ${res.reference_number}</p>
  <br><button class="print-btn" onclick="window.print()" style="background:#1d4ed8;color:#fff;border:none;padding:10px 28px;border-radius:10px;cursor:pointer;font-size:14px">🖨 Print Contract</button>
  <script>window.onload=()=>window.print()<\/script>
  </body></html>`)
  win.document.close()
}

// ── Formatters ────────────────────────────────────────────────────────────────
function formatMoney(v) {
  return Number(v || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}
function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
function formatDateTime(d) {
  if (!d) return '—'
  return new Date(d).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' })
}
function statusLabel(status) {
  const map = { pending:'Pending', approved:'Approved', checked_in:'Checked In', checked_out:'Checked Out', cancelled:'Rejected' }
  return map[status] ?? status
}
function statusBadge(status) {
  const map = {
    pending:     'bg-yellow-100 text-yellow-700',
    approved:    'bg-green-100 text-green-800',
    checked_in:  'bg-green-100 text-green-700',
    checked_out: 'bg-gray-100 text-gray-500',
    cancelled:   'bg-red-100 text-red-600',
  }
  return map[status] ?? 'bg-gray-100 text-gray-500'
}
function pendingStatusBadge(status) {
  const map = {
    pending:  'bg-yellow-100 text-yellow-700',
    approved: 'bg-green-100 text-green-700',
    rejected: 'bg-red-100 text-red-600',
  }
  return map[status] ?? 'bg-gray-100 text-gray-500'
}

onMounted(() => {
  fetchOnlineRequests()
  fetchAll()
})
</script>
