<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <!-- Page Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-semibold text-green-800">Event Management</h1>
      <p class="text-sm text-gray-500 font-light mt-1">Manage event reservations and bookings</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 font-light">Total Bookings</p>
          <p class="text-2xl font-semibold text-green-800">{{ stats.total_bookings }}</p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 font-light">Pending Reservations</p>
          <p class="text-2xl font-semibold text-orange-500">{{ stats.pending_reservations }}</p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 font-light">Total Revenue</p>
          <p class="text-2xl font-semibold text-green-800">&#x20B1;{{ formatMoney(stats.total_revenue) }}</p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
        </div>
        <div>
          <p class="text-xs text-gray-500 font-light">Upcoming Event</p>
          <template v-if="stats.upcoming_event">
            <p class="text-base font-semibold text-green-700 leading-tight">{{ stats.upcoming_event.event_type }}</p>
            <p class="text-xs text-gray-500 font-light">{{ formatDate(stats.upcoming_event.event_date) }}</p>
          </template>
          <p v-else class="text-sm text-gray-400 font-light">None upcoming</p>
        </div>
      </div>
    </div>

    <!-- Search + Actions -->
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
      <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input v-model="search" @input="fetchEvents" type="text" placeholder="Search events..."
          class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white"/>
      </div>
      <select v-model="statusFilter" @change="fetchEvents"
        class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm font-light bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="confirmed">Confirmed</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <button @click="showAddModal = true"
        class="flex items-center gap-2 bg-green-800 hover:bg-green-900 text-white px-4 py-2.5 rounded-lg text-sm font-light transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Reservation
      </button>
      <button @click="refresh"
        class="flex items-center gap-2 border border-blue-700 text-green-800 hover:bg-green-50 px-4 py-2.5 rounded-lg text-sm font-light transition-colors">
        <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Refresh
      </button>

    </div>

    <!-- Completed events notice -->
    <div v-if="completedCount > 0 && !statusFilter" class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 text-sm font-light rounded-xl px-4 py-3">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <span>{{ completedCount }} completed event{{ completedCount !== 1 ? 's' : '' }} moved to history.</span>

    </div>

    <!-- Event Reservations Table -->
    <div class="bg-white rounded-xl shadow-sm mb-6">
      <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-base font-semibold text-green-800">Event Reservations</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50">
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Type</th>
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Date</th>
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Time</th>
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Guests</th>
              <th class="px-4 py-3 text-left font-semibold text-green-800 text-xs uppercase tracking-wide">Set</th>
              <th class="px-4 py-3 text-right font-semibold text-green-800 text-xs uppercase tracking-wide">Total Amount</th>
              <th class="px-4 py-3 text-right font-semibold text-green-800 text-xs uppercase tracking-wide">Downpayment</th>
              <th class="px-4 py-3 text-right font-semibold text-green-800 text-xs uppercase tracking-wide">Remaining</th>
              <th class="px-4 py-3 text-center font-semibold text-green-800 text-xs uppercase tracking-wide">Status</th>
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
                Loading events...
              </td>
            </tr>
            <tr v-else-if="displayedEvents.length === 0">
              <td colspan="10" class="px-4 py-12 text-center text-gray-400 font-light">No events found.</td>
            </tr>
            <tr v-for="ev in displayedEvents" :key="ev.id" class="border-t border-gray-50 hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 font-light text-gray-800">{{ ev.event_type }}</td>
              <td class="px-4 py-3 font-light text-gray-600 whitespace-nowrap">{{ formatDate(ev.event_date) }}</td>
              <td class="px-4 py-3 font-light text-gray-600 whitespace-nowrap">{{ formatTime(ev.event_time) }}</td>
              <td class="px-4 py-3 font-light text-gray-600">{{ ev.number_of_guests }}</td>
              <td class="px-4 py-3 font-light text-gray-600">{{ ev.package_set || '&mdash;' }}</td>
              <td class="px-4 py-3 font-light text-gray-800 text-right whitespace-nowrap">&#x20B1;{{ formatMoney(ev.total_amount) }}</td>
              <td class="px-4 py-3 font-light text-gray-600 text-right whitespace-nowrap">&#x20B1;{{ formatMoney(ev.down_payment) }}</td>
              <td class="px-4 py-3 font-light text-right whitespace-nowrap"
                :class="ev.remaining_balance > 0 ? 'text-red-600' : 'text-green-600'">
                &#x20B1;{{ formatMoney(ev.remaining_balance) }}
              </td>
              <td class="px-4 py-3 text-center">
                <span :class="statusBadge(ev.status)" class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap">
                  {{ statusLabel(ev.status) }}
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
                  <!-- Edit -->
                  <button @click="editEvent(ev)" title="Edit"
                    class="p-1.5 rounded-lg text-yellow-600 hover:bg-yellow-50 border border-yellow-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </button>
                  <!-- Delete -->
                  <button @click="deletePrompt(ev)" title="Delete"
                    class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 border border-red-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                  <!-- Print Contract -->
                  <button @click="printEvent(ev)" title="Print Contract"
                    class="p-1.5 rounded-lg text-amber-600 hover:bg-green-50 border border-green-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                  </button>
                  <!-- Record Payment -->
                  <button @click="openPaymentModal(ev)" title="Record Payment"
                    :disabled="ev.status === 'cancelled' || ev.status === 'completed'"
                    class="p-1.5 rounded-lg text-green-600 hover:bg-green-50 border border-green-200 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </button>
                  <!-- Approve -->
                  <button
                    @click="ev.status === 'pending' ? openEventApproveModal(ev) : ev.status === 'confirmed' ? confirmStatusChange(ev, 'completed') : null"
                    :disabled="ev.status === 'completed'"
                    :title="ev.status === 'pending' ? 'Approve' : ev.status === 'confirmed' ? 'Mark Completed' : ''"
                    :class="ev.status === 'completed' ? 'text-gray-300 border-gray-200 cursor-default' : 'text-green-600 hover:bg-green-50 border-green-200'"
                    class="p-1.5 rounded-lg border transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Calendar + Event Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Calendar -->
      <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-base font-semibold text-green-800">Event Calendar</h3>
          <div class="flex items-center gap-2">
            <button @click="prevMonth" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </button>
            <span class="text-sm font-medium text-gray-700 w-32 text-center">{{ calendarTitle }}</span>
            <button @click="nextMonth" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
        </div>
        <!-- Day headers -->
        <div class="grid grid-cols-7 mb-1">
          <div v-for="d in ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']" :key="d"
            class="text-center text-xs font-semibold text-gray-400 py-1">{{ d }}</div>
        </div>
        <!-- Calendar days -->
        <div class="grid grid-cols-7 gap-1">
          <div v-for="(day, idx) in calendarDays" :key="idx"
            @click="day.date ? selectCalendarDate(day.date) : null"
            :class="[
              'relative flex flex-col items-center justify-start pt-1 h-10 rounded-lg text-sm transition-colors',
              day.date ? 'cursor-pointer hover:bg-green-50' : '',
              day.date === selectedCalDate ? 'bg-green-100 text-green-800 font-semibold' : 'text-gray-700',
              day.isToday ? 'border border-green-300' : '',
              !day.date ? 'text-transparent' : ''
            ]">
            <span>{{ day.label }}</span>
            <span v-if="day.hasEvent" class="w-1.5 h-1.5 rounded-full bg-green-600 mt-0.5 absolute bottom-1"></span>
          </div>
        </div>
      </div>

      <!-- Event Information Panel -->
      <div class="bg-white rounded-xl shadow-sm p-5">
        <h3 class="text-base font-semibold text-green-800 mb-3">
          Events on {{ selectedCalDate ? formatDate(selectedCalDate) : 'Selected Date' }}
        </h3>
        <div v-if="dateEvents.length === 0" class="flex flex-col items-center justify-center h-48 text-gray-400">
          <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <p class="text-sm font-light">No events scheduled for this date</p>
        </div>
        <div v-else class="space-y-3">
          <div v-for="ev in dateEvents" :key="ev.id"
            class="border border-gray-100 rounded-lg p-4 hover:shadow-sm transition-shadow cursor-pointer"
            @click="viewEvent(ev)">
            <div class="flex items-start justify-between gap-3">
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 truncate">{{ ev.event_type }}</p>
                <p class="text-xs text-gray-500 font-light mt-0.5">{{ ev.client_name }}</p>
                <div class="flex items-center gap-3 mt-2 text-xs text-gray-500 font-light">
                  <span>{{ formatTime(ev.event_time) }}</span>
                  <span>{{ ev.number_of_guests }} guests</span>
                  <span v-if="ev.venue">{{ ev.venue }}</span>
                </div>
              </div>
              <span :class="statusBadge(ev.status)" class="px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap flex-shrink-0">
                {{ statusLabel(ev.status) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ===================== ADD / EDIT MODAL ===================== -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-2 bg-black/50">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[95vh] flex flex-col">

        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 shrink-0">
          <h3 class="text-base font-bold text-gray-800">{{ showEditModal ? 'Edit Event' : 'Add New Event' }}</h3>
          <button @click="closeModals" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <!-- Two-panel body -->
        <div class="flex flex-1 min-h-0">

          <!-- LEFT: Package list -->
          <div class="w-44 shrink-0 border-r border-gray-100 overflow-y-auto p-3 space-y-2">
            <p class="text-xs font-semibold text-amber-600 mb-2">Select Package</p>
            <div v-if="eventPackages.length === 0" class="text-xs text-gray-400">No packages</div>
            <div v-for="pkg in eventPackages" :key="pkg.id"
              @click="selectPackage(pkg)"
              :class="form.package_id == pkg.id ? 'border-blue-500 bg-green-50' : 'border-gray-200 hover:bg-gray-50'"
              class="border rounded-xl p-2.5 cursor-pointer transition">
              <p class="text-xs font-semibold text-gray-800 leading-tight">{{ pkg.name }}</p>
              <p class="text-xs text-amber-600 font-medium mt-0.5">₱{{ formatMoney(pkg.price) }}</p>
              <p class="text-xs text-gray-400">{{ pkg.max_guests }} Guests</p>
            </div>
          </div>

          <!-- RIGHT: Form -->
          <div class="flex-1 overflow-y-auto p-4 space-y-3">

            <!-- Returning Customer -->
            <div>
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-medium text-gray-600">Returning Customer?</span>
                <button type="button" @click="eventGuestManual = !eventGuestManual" class="text-xs text-green-600 hover:underline">Enter manually</button>
              </div>
              <div class="flex gap-2">
                <input v-model="eventGuestSearch" type="text" placeholder="Search by email or phone number"
                  class="flex-1 border border-green-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
                <button type="button" @click="searchEventGuest"
                  class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1.5 rounded-lg transition">Search</button>
              </div>
              <div v-if="eventGuestResults.length > 0" class="mt-1 border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                <div v-for="g in eventGuestResults" :key="g.id"
                  @click="fillEventGuest(g)"
                  class="px-3 py-2 text-sm hover:bg-green-50 cursor-pointer border-b border-gray-100 last:border-0">
                  <span class="font-medium text-gray-800">{{ g.name }}</span>
                  <span class="text-gray-400 ml-2 text-xs">{{ g.email }}</span>
                </div>
              </div>
            </div>

            <!-- Event Name + Event Type -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Event Name:</label>
                <input v-model="form.event_name" type="text" placeholder=""
                  class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Event Type:</label>
                <select v-model="form.event_type"
                  class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-400">
                  <option value="">Select Type</option>
                  <option>Wedding</option>
                  <option>Birthday</option>
                  <option>Corporate</option>
                  <option>Debut</option>
                  <option>Anniversary</option>
                  <option>Christening</option>
                  <option>Other</option>
                </select>
              </div>
            </div>

            <!-- Date + Time Slot -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Date:</label>
                <input v-model="form.event_date" type="date"
                  class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Time Slot:</label>
                <select v-model="form.event_time"
                  class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-400">
                  <option value="">Select Time</option>
                  <option value="08:00">8:00 AM</option>
                  <option value="09:00">9:00 AM</option>
                  <option value="10:00">10:00 AM</option>
                  <option value="11:00">11:00 AM</option>
                  <option value="12:00">12:00 PM</option>
                  <option value="13:00">1:00 PM</option>
                  <option value="14:00">2:00 PM</option>
                  <option value="15:00">3:00 PM</option>
                  <option value="16:00">4:00 PM</option>
                  <option value="17:00">5:00 PM</option>
                  <option value="18:00">6:00 PM</option>
                </select>
              </div>
            </div>

            <!-- Guests + Booked By -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Guests:</label>
                <input v-model.number="form.number_of_guests" type="number" min="0" placeholder="0" @input="computeTotal"
                  class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Booked By:</label>
                <input v-model="form.booked_by" type="text" placeholder=""
                  class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
              </div>
            </div>

            <!-- Contact Number + Address -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Contact Number:</label>
                <input v-model="form.client_phone" type="text" placeholder="09XXXXXXXXXX"
                  class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Address:</label>
                <input v-model="form.client_address" type="text" placeholder="Customer Address"
                  class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
              </div>
            </div>

            <!-- Supervisor + Additional Guests -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Supervisor:</label>
                <select v-model="form.supervisor_id"
                  class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-400">
                  <option value="">Select Supervisor</option>
                  <option v-for="s in supervisors" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Additional Guests:</label>
                <input v-model.number="form.additional_guests" type="number" min="0" placeholder="0"
                  class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
              </div>
            </div>

            <!-- Status + Total Cost -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status:</label>
                <select v-model="form.status"
                  class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-400">
                  <option value="pending">Pending</option>
                  <option value="confirmed">Confirmed</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Total Cost (₱):</label>
                <input v-model.number="form.total_amount" type="number" min="0" step="0.01" placeholder="0.00"
                  class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
              </div>
            </div>

            <!-- Customer ID -->
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Customer ID:</label>
              <div class="flex items-center gap-2">
                <input type="file" ref="eventIdInput" @change="handleEventIdFile" accept="image/*" class="hidden" />
                <div class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-500 cursor-pointer"
                  @click="eventIdInput.click()">
                  {{ eventIdFile ? eventIdFile.name : 'Choose File  No file chosen' }}
                </div>
                <button type="button" @click="eventIdInput.click()"
                  class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1.5 rounded-lg transition whitespace-nowrap">Take Photo</button>
              </div>
            </div>

            <!-- E-Signature -->
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">E-Signature:</label>
              <button type="button"
                class="bg-green-700 hover:bg-green-800 text-white text-xs font-medium px-4 py-1.5 rounded-lg transition">
                E-Signature
              </button>
            </div>

            <!-- Additional Requests -->
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Additional Requests:</label>
              <textarea v-model="form.notes" rows="2" placeholder=""
                class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 resize-none"></textarea>
            </div>

            <!-- Remarks -->
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Remarks:</label>
              <textarea v-model="form.remarks" rows="2" placeholder=""
                class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 resize-none"></textarea>
            </div>

            <p v-if="formError" class="text-sm text-red-500">{{ formError }}</p>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 px-5 py-4 border-t border-gray-100 shrink-0">
          <button @click="closeModals" class="px-5 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">Cancel</button>
          <button @click="submitForm" :disabled="saving"
            class="px-5 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-medium transition disabled:opacity-50">
            {{ saving ? 'Saving...' : (showEditModal ? 'Save Changes' : 'Create Reservation') }}
          </button>
        </div>
      </div>
    </div>

    <!-- ===================== VIEW MODAL ===================== -->
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
            <span :class="statusBadge(viewingEvent.status)" class="px-3 py-1 rounded-full text-xs font-medium">{{ statusLabel(viewingEvent.status) }}</span>
            <span class="text-xs text-gray-400 font-light">Created {{ formatDate(viewingEvent.created_at) }}</span>
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
              <p class="text-xs text-gray-400 font-light mb-0.5">Date</p>
              <p class="font-light text-gray-700">{{ formatDate(viewingEvent.event_date) }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 font-light mb-0.5">Time</p>
              <p class="font-light text-gray-700">{{ formatTime(viewingEvent.event_time) }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 font-light mb-0.5">Number of Guests</p>
              <p class="font-light text-gray-700">{{ viewingEvent.number_of_guests }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 font-light mb-0.5">Price Per Head</p>
              <p class="font-light text-gray-700">&#x20B1;{{ formatMoney(viewingEvent.price_per_head) }}</p>
            </div>
          </div>

          <div class="bg-gray-50 rounded-xl p-4 space-y-2">
            <div class="flex justify-between text-sm">
              <span class="font-light text-gray-500">Total Amount</span>
              <span class="font-semibold text-gray-800">&#x20B1;{{ formatMoney(viewingEvent.total_amount) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="font-light text-gray-500">Down Payment</span>
              <span class="font-light text-green-600">&#x20B1;{{ formatMoney(viewingEvent.down_payment) }}</span>
            </div>
            <div class="border-t border-gray-200 pt-2 flex justify-between text-sm">
              <span class="font-medium text-gray-700">Remaining Balance</span>
              <span class="font-semibold" :class="viewingEvent.remaining_balance > 0 ? 'text-red-600' : 'text-green-600'">
                &#x20B1;{{ formatMoney(viewingEvent.remaining_balance) }}
              </span>
            </div>
          </div>

          <div v-if="viewingEvent.notes">
            <p class="text-xs text-gray-400 font-light mb-1">Notes</p>
            <p class="font-light text-gray-700 text-sm leading-relaxed">{{ viewingEvent.notes }}</p>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-between gap-3 flex-wrap">
          <div class="flex gap-2 flex-wrap">
            <button v-if="viewingEvent.status === 'pending'" @click="openEventApproveModal(viewingEvent); showViewModal = false"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-light transition-colors">
              Approve
            </button>
            <button v-if="viewingEvent.status === 'pending' || viewingEvent.status === 'confirmed'"
              @click="confirmStatusChange(viewingEvent, 'cancelled'); showViewModal = false"
              class="px-4 py-2 border border-red-300 text-red-500 hover:bg-red-50 rounded-lg text-sm font-light transition-colors">
              Cancel
            </button>
            <button v-if="viewingEvent.status === 'confirmed' || viewingEvent.status === 'pending'"
              @click="openPaymentModal(viewingEvent)"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-light transition-colors flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Record Payment
            </button>
          </div>
          <div class="flex gap-2">
            <button @click="printEvent(viewingEvent)"
              class="px-4 py-2 border border-green-300 text-green-800 hover:bg-green-50 rounded-lg text-sm font-light transition-colors flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
              Print Contract
            </button>
            <button @click="editEvent(viewingEvent); showViewModal = false"
              class="px-4 py-2 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-lg text-sm font-light transition-colors">
              Edit
            </button>
            <button @click="showViewModal = false"
              class="px-4 py-2 bg-green-800 hover:bg-green-900 text-white rounded-lg text-sm font-light transition-colors">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ===================== APPROVE EVENT MODAL ===================== -->
    <div v-if="showApproveEventModal && approveEventTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <div>
            <h3 class="text-base font-semibold text-gray-800">Approve Event Reservation</h3>
            <p class="text-xs text-amber-600 font-mono">{{ approveEventTarget.reference_number }}</p>
          </div>
          <button @click="showApproveEventModal = false" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="p-6 space-y-4">
          <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-sm font-medium text-gray-800">{{ approveEventTarget.client_name }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ approveEventTarget.client_email }}</p>
            <p class="text-xs text-gray-600 mt-1">{{ approveEventTarget.event_type }} &middot; {{ formatDate(approveEventTarget.event_date) }}</p>
            <p class="text-sm font-bold text-green-800 mt-1">Total: &#x20B1;{{ formatMoney(approveEventTarget.total_amount) }}</p>
          </div>
          <!-- Payment Option -->
          <div>
            <p class="text-xs font-medium text-gray-600 mb-2">Payment Option</p>
            <div class="grid grid-cols-2 gap-2">
              <div @click="approveEventForm.payment_option = 'full_payment'"
                :class="approveEventForm.payment_option === 'full_payment' ? 'border-blue-500 bg-green-50' : 'border-gray-200'"
                class="border-2 rounded-xl p-3 cursor-pointer transition">
                <div class="flex items-center gap-2 mb-0.5">
                  <div :class="approveEventForm.payment_option === 'full_payment' ? 'border-blue-500 bg-green-600' : 'border-gray-300'" class="w-4 h-4 rounded-full border-2 flex items-center justify-center">
                    <svg v-if="approveEventForm.payment_option === 'full_payment'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                  </div>
                  <span class="text-xs font-semibold text-gray-800">Full at Event</span>
                </div>
                <p class="text-xs text-gray-400 ml-6">Pay total on event day</p>
              </div>
              <div @click="approveEventForm.payment_option = 'downpayment'"
                :class="approveEventForm.payment_option === 'downpayment' ? 'border-blue-500 bg-green-50' : 'border-gray-200'"
                class="border-2 rounded-xl p-3 cursor-pointer transition">
                <div class="flex items-center gap-2 mb-0.5">
                  <div :class="approveEventForm.payment_option === 'downpayment' ? 'border-blue-500 bg-green-600' : 'border-gray-300'" class="w-4 h-4 rounded-full border-2 flex items-center justify-center">
                    <svg v-if="approveEventForm.payment_option === 'downpayment'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                  </div>
                  <span class="text-xs font-semibold text-gray-800">Downpayment</span>
                </div>
                <p class="text-xs text-gray-400 ml-6">Partial now, rest later</p>
              </div>
            </div>
            <div v-if="approveEventForm.payment_option === 'downpayment'" class="mt-3 space-y-3">
              <!-- Amount -->
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Downpayment Amount (&#x20B1;)</label>
                <input v-model="approveEventForm.down_payment" type="number" min="0" step="0.01" placeholder="0.00"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600" />
              </div>
              <!-- Payment Method -->
              <div>
                <p class="text-xs font-medium text-gray-600 mb-1.5">Payment Method</p>
                <div class="grid grid-cols-2 gap-2">
                  <div @click="approveEventForm.payment_method = 'cash'; approveEventForm.online_payment_type = ''; approveEventForm.payment_ref = ''"
                    :class="approveEventForm.payment_method === 'cash' ? 'border-green-500 bg-green-50' : 'border-gray-200'"
                    class="border-2 rounded-xl p-3 cursor-pointer transition">
                    <div class="flex items-center gap-2">
                      <div :class="approveEventForm.payment_method === 'cash' ? 'border-green-500 bg-green-500' : 'border-gray-300'" class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0">
                        <svg v-if="approveEventForm.payment_method === 'cash'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                      </div>
                      <div>
                        <p class="text-xs font-semibold text-gray-800">Cash</p>
                        <p class="text-xs text-gray-400">Physical payment</p>
                      </div>
                    </div>
                  </div>
                  <div @click="approveEventForm.payment_method = 'online'"
                    :class="approveEventForm.payment_method === 'online' ? 'border-blue-500 bg-green-50' : 'border-gray-200'"
                    class="border-2 rounded-xl p-3 cursor-pointer transition">
                    <div class="flex items-center gap-2">
                      <div :class="approveEventForm.payment_method === 'online' ? 'border-blue-500 bg-green-600' : 'border-gray-300'" class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0">
                        <svg v-if="approveEventForm.payment_method === 'online'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
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
              <div v-if="approveEventForm.payment_method === 'online'" class="space-y-2">
                <p class="text-xs font-medium text-gray-600 mb-1.5">Online Payment Type</p>
                <div class="grid grid-cols-2 gap-2">
                  <div @click="approveEventForm.online_payment_type = 'gcash'"
                    :class="approveEventForm.online_payment_type === 'gcash' ? 'border-blue-500 bg-green-50' : 'border-gray-200'"
                    class="border-2 rounded-xl p-3 cursor-pointer transition">
                    <div class="flex items-center gap-2">
                      <div :class="approveEventForm.online_payment_type === 'gcash' ? 'border-blue-500 bg-green-600' : 'border-gray-300'" class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0">
                        <svg v-if="approveEventForm.online_payment_type === 'gcash'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                      </div>
                      <div>
                        <p class="text-xs font-semibold text-green-800">GCash</p>
                        <p class="text-xs text-gray-400">Mobile wallet</p>
                      </div>
                    </div>
                  </div>
                  <div @click="approveEventForm.online_payment_type = 'bank_transfer'"
                    :class="approveEventForm.online_payment_type === 'bank_transfer' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'"
                    class="border-2 rounded-xl p-3 cursor-pointer transition">
                    <div class="flex items-center gap-2">
                      <div :class="approveEventForm.online_payment_type === 'bank_transfer' ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'" class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0">
                        <svg v-if="approveEventForm.online_payment_type === 'bank_transfer'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                      </div>
                      <div>
                        <p class="text-xs font-semibold text-indigo-700">Bank Transfer</p>
                        <p class="text-xs text-gray-400">Direct deposit</p>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Reference Number -->
                <div v-if="approveEventForm.online_payment_type">
                  <label class="block text-xs font-medium text-gray-600 mb-1">
                    {{ approveEventForm.online_payment_type === 'gcash' ? 'GCash' : 'Bank Transfer' }} Reference Number
                    <span class="text-red-400">*</span>
                  </label>
                  <input v-model="approveEventForm.payment_ref" type="text"
                    :placeholder="approveEventForm.online_payment_type === 'gcash' ? 'e.g. 123456789012' : 'e.g. TRF-20260227-001'"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 font-mono" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end">
          <button @click="showApproveEventModal = false" class="px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
          <button @click="doEventApprove" :disabled="approvingEvent"
            class="px-5 py-2 text-sm bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-medium flex items-center gap-2 disabled:opacity-60">
            <svg v-if="approvingEvent" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            {{ approvingEvent ? 'Approving...' : 'Confirm Approval' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ===================== COMPLETION BLOCKED (unpaid balance) ===================== -->
    <div v-if="eventCompletionBlockModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        </div>
        <p class="text-sm font-semibold text-gray-800 mb-1">Cannot Complete Event</p>
        <p class="text-xs text-gray-500 mb-3">
          <strong>{{ eventCompletionBlockModal.clientName }}</strong> still has an unpaid balance.
        </p>
        <div class="bg-orange-50 border border-orange-200 rounded-xl px-4 py-3 mb-5">
          <p class="text-xs text-orange-600 font-medium">Outstanding Balance</p>
          <p class="text-xl font-bold text-orange-700">&#x20b1;{{ Number(eventCompletionBlockModal.balance).toLocaleString('en-PH', { minimumFractionDigits: 2 }) }}</p>
        </div>
        <p class="text-xs text-gray-400 mb-5">Please record the full payment before marking this event as completed.</p>
        <button @click="eventCompletionBlockModal = null" class="w-full py-2 text-sm bg-green-800 text-white rounded-xl hover:bg-green-900 transition">Understood</button>
      </div>
    </div>

    <!-- ===================== STATUS CONFIRM MODAL ===================== -->
    <div v-if="showStatusModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center gap-3 mb-4">
          <div :class="statusActionColor" class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-gray-800">Confirm Action</h3>
            <p class="text-sm text-gray-500 font-light">{{ statusConfirmMsg }}</p>
          </div>
        </div>
        <div class="flex justify-end gap-3">
          <button @click="showStatusModal = false" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-light text-gray-600 hover:bg-gray-50">Cancel</button>
          <button @click="applyStatusChange" :class="statusActionBtnClass" class="px-4 py-2 rounded-lg text-sm font-light text-white transition-colors">Confirm</button>
        </div>
      </div>
    </div>

    <!-- ===================== RECORD PAYMENT MODAL ===================== -->
    <div v-if="showPaymentModal && paymentTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <div>
            <h3 class="text-base font-semibold text-green-800">Record Payment</h3>
            <p class="text-xs text-gray-400 font-light">{{ paymentTarget.reference_number }} &mdash; {{ paymentTarget.client_name }}</p>
          </div>
          <button @click="showPaymentModal = false" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="p-6 space-y-4">
          <!-- Summary -->
          <div class="bg-green-50 rounded-xl p-4 space-y-1.5">
            <div class="flex justify-between text-sm">
              <span class="font-light text-gray-500">Total Amount</span>
              <span class="font-semibold text-gray-800">&#x20B1;{{ formatMoney(paymentTarget.total_amount) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="font-light text-gray-500">Existing Down Payment</span>
              <span class="font-light text-green-600">&#x20B1;{{ formatMoney(paymentTarget.down_payment) }}</span>
            </div>
            <div class="border-t border-green-100 pt-1.5 flex justify-between text-sm">
              <span class="font-medium text-gray-700">Remaining Balance</span>
              <span class="font-semibold text-red-600">&#x20B1;{{ formatMoney(paymentTarget.remaining_balance) }}</span>
            </div>
          </div>

          <!-- Payment Option -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-2">Payment Option</label>
            <div class="grid grid-cols-2 gap-2">
              <label :class="paymentForm.payment_option === 'full_payment' ? 'border-blue-500 bg-green-50' : 'border-gray-200'"
                class="flex items-center gap-2 p-3 rounded-lg border cursor-pointer transition">
                <input type="radio" v-model="paymentForm.payment_option" value="full_payment" class="accent-green-700" />
                <span class="text-sm font-light">Full Payment</span>
              </label>
              <label :class="paymentForm.payment_option === 'downpayment' ? 'border-blue-500 bg-green-50' : 'border-gray-200'"
                class="flex items-center gap-2 p-3 rounded-lg border cursor-pointer transition">
                <input type="radio" v-model="paymentForm.payment_option" value="downpayment" class="accent-green-700" />
                <span class="text-sm font-light">Downpayment</span>
              </label>
            </div>
          </div>

          <!-- Amount -->
          <div v-if="paymentForm.payment_option === 'downpayment'">
            <label class="block text-xs font-medium text-gray-600 mb-1">Downpayment Amount (&#x20B1;)</label>
            <input v-model.number="paymentForm.down_payment" type="number" min="0" step="0.01" placeholder="0.00"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
          </div>

          <!-- Payment Method -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-2">Payment Method</label>
            <div class="grid grid-cols-2 gap-2">
              <label :class="paymentForm.payment_method === 'cash' ? 'border-blue-500 bg-green-50' : 'border-gray-200'"
                class="flex items-center gap-2 p-3 rounded-lg border cursor-pointer transition">
                <input type="radio" v-model="paymentForm.payment_method" value="cash" class="accent-green-700" />
                <span class="text-sm font-light">Cash</span>
              </label>
              <label :class="paymentForm.payment_method === 'online' ? 'border-blue-500 bg-green-50' : 'border-gray-200'"
                class="flex items-center gap-2 p-3 rounded-lg border cursor-pointer transition">
                <input type="radio" v-model="paymentForm.payment_method" value="online" class="accent-green-700" />
                <span class="text-sm font-light">Online</span>
              </label>
            </div>
          </div>

          <!-- Online sub-type -->
          <div v-if="paymentForm.payment_method === 'online'">
            <label class="block text-xs font-medium text-gray-600 mb-2">Online Payment Type</label>
            <div class="grid grid-cols-2 gap-2">
              <label :class="paymentForm.online_payment_type === 'gcash' ? 'border-blue-500 bg-green-50' : 'border-gray-200'"
                class="flex items-center gap-2 p-3 rounded-lg border cursor-pointer transition">
                <input type="radio" v-model="paymentForm.online_payment_type" value="gcash" class="accent-green-700" />
                <span class="text-sm font-light">GCash</span>
              </label>
              <label :class="paymentForm.online_payment_type === 'bank_transfer' ? 'border-blue-500 bg-green-50' : 'border-gray-200'"
                class="flex items-center gap-2 p-3 rounded-lg border cursor-pointer transition">
                <input type="radio" v-model="paymentForm.online_payment_type" value="bank_transfer" class="accent-green-700" />
                <span class="text-sm font-light">Bank Transfer</span>
              </label>
            </div>
          </div>

          <!-- Reference Number -->
          <div v-if="paymentForm.payment_method === 'online' && paymentForm.online_payment_type">
            <label class="block text-xs font-medium text-gray-600 mb-1">Reference Number <span class="text-red-500">*</span></label>
            <input v-model="paymentForm.payment_ref" type="text" placeholder="e.g. 1234567890"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-green-400" />
          </div>

          <p v-if="paymentError" class="text-sm text-red-500">{{ paymentError }}</p>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
          <button @click="showPaymentModal = false" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-light text-gray-600 hover:bg-gray-50">Cancel</button>
          <button @click="submitPayment" :disabled="saving"
            class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-light transition disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Record Payment' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ===================== DELETE CONFIRM MODAL ===================== -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-gray-800">Delete Event</h3>
            <p class="text-sm text-gray-500 font-light">Delete <strong>{{ deletingEvent?.event_type }}</strong> for {{ deletingEvent?.client_name }}? This cannot be undone.</p>
          </div>
        </div>
        <div class="flex justify-end gap-3">
          <button @click="showDeleteModal = false" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-light text-gray-600 hover:bg-gray-50">Cancel</button>
          <button @click="confirmDelete" :disabled="saving"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-light transition-colors disabled:opacity-50">
            {{ saving ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'
import { useToastStore } from '../stores/toast'

const toast = useToastStore()

// ---- State ----
const events = ref([])
const loading = ref(false)
const saving = ref(false)
const search = ref('')
const statusFilter = ref('')
const formError = ref('')

// Computed: exclude completed events from the default view (they live in Reservation History)
const completedCount = computed(() => events.value.filter(e => e.status === 'completed').length)
const displayedEvents = computed(() => {
  // When no status filter is active, hide completed events (shown in History page)
  if (!statusFilter.value) {
    return events.value.filter(e => e.status !== 'completed')
  }
  return events.value
})

const stats = ref({
  total_bookings: 0,
  pending_reservations: 0,
  total_revenue: 0,
  upcoming_event: null
})

// Calendar
const calendarDate = ref(new Date())
const selectedCalDate = ref(null)
const bookedDates = ref([])
const dateEvents = ref([])

// Modals
const showAddModal = ref(false)
const showEditModal = ref(false)
const showViewModal = ref(false)
const showDeleteModal = ref(false)
const showStatusModal = ref(false)
const showPaymentModal = ref(false)
const showApproveEventModal = ref(false)
const eventCompletionBlockModal = ref(null)
const approveEventTarget = ref(null)
const approveEventForm = ref({ payment_option: 'full_payment', down_payment: 0, payment_method: '', online_payment_type: '', payment_ref: '' })
const approvingEvent = ref(false)
const viewingEvent = ref(null)
const deletingEvent = ref(null)
const paymentTarget = ref(null)
const paymentError = ref('')
const paymentForm = ref({ payment_option: 'downpayment', down_payment: 0, payment_method: 'cash', online_payment_type: '', payment_ref: '' })
const editingId = ref(null)

// Status change
const pendingStatus = ref({ event: null, newStatus: '' })

const statusConfirmMsg = computed(() => {
  if (!pendingStatus.value.event) return ''
  const s = pendingStatus.value.newStatus
  const map = { confirmed: 'approve and confirm', completed: 'mark as completed', cancelled: 'cancel' }
  return `Are you sure you want to ${map[s] || s} this event?`
})

const statusActionColor = computed(() => {
  const s = pendingStatus.value.newStatus
  if (s === 'confirmed') return 'bg-green-100 text-green-600'
  if (s === 'cancelled') return 'bg-red-100 text-red-500'
  return 'bg-green-100 text-amber-600'
})

const statusActionBtnClass = computed(() => {
  const s = pendingStatus.value.newStatus
  if (s === 'confirmed') return 'bg-green-600 hover:bg-green-700'
  if (s === 'cancelled') return 'bg-red-600 hover:bg-red-700'
  return 'bg-green-800 hover:bg-green-900'
})

// Event packages + supervisors
const eventPackages = ref([])
const supervisors = ref([])

// Guest search for event modal
const eventGuestSearch = ref('')
const eventGuestResults = ref([])
const eventGuestManual = ref(false)

// ID file for event modal
const eventIdInput = ref(null)
const eventIdFile = ref(null)

// Form
const defaultForm = () => ({
  event_name: '', event_type: '', client_name: '', client_email: '', client_phone: '',
  client_address: '', event_date: '', event_time: '', number_of_guests: 0,
  additional_guests: 0, booked_by: '', supervisor_id: '', package_id: null,
  venue: '', package_set: '', price_per_head: 0, total_amount: 0, down_payment: 0,
  status: 'pending', notes: '', remarks: ''
})
const form = ref(defaultForm())

// ---- Calendar logic ----
const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December']

const calendarTitle = computed(() => {
  return `${monthNames[calendarDate.value.getMonth()]} ${calendarDate.value.getFullYear()}`
})

const calendarDays = computed(() => {
  const y = calendarDate.value.getFullYear()
  const m = calendarDate.value.getMonth()
  const firstDay = new Date(y, m, 1)
  const lastDay = new Date(y, m + 1, 0)
  const today = new Date()
  const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`

  // Monday-first offset
  let startDow = firstDay.getDay() // 0=Sun
  let offset = (startDow === 0) ? 6 : startDow - 1

  const days = []
  for (let i = 0; i < offset; i++) days.push({ label: '', date: null })
  for (let d = 1; d <= lastDay.getDate(); d++) {
    const dateStr = `${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`
    days.push({
      label: d,
      date: dateStr,
      isToday: dateStr === todayStr,
      hasEvent: bookedDates.value.includes(dateStr)
    })
  }
  return days
})

function prevMonth() {
  const d = new Date(calendarDate.value)
  d.setMonth(d.getMonth() - 1)
  calendarDate.value = d
}

function nextMonth() {
  const d = new Date(calendarDate.value)
  d.setMonth(d.getMonth() + 1)
  calendarDate.value = d
}

async function selectCalendarDate(dateStr) {
  selectedCalDate.value = dateStr
  try {
    const res = await api.get(`/events/by-date?date=${dateStr}`)
    dateEvents.value = res.data.data || []
  } catch {
    dateEvents.value = []
  }
}

// ---- Formatters ----
function formatMoney(v) {
  return Number(v || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

function formatDate(d) {
  if (!d) return ''
  const dt = new Date(d + (d.includes('T') ? '' : 'T00:00:00'))
  return dt.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

function formatTime(t) {
  if (!t) return ''
  const [h, m] = t.split(':')
  const hr = parseInt(h)
  const ampm = hr >= 12 ? 'PM' : 'AM'
  const hr12 = hr % 12 || 12
  return `${hr12}:${m} ${ampm}`
}

function statusLabel(s) {
  const map = {
    pending: 'Pending',
    confirmed: '\u2713 Confirmed',
    completed: 'Completed',
    cancelled: 'Cancelled'
  }
  return map[s] || s
}

function statusBadge(s) {
  const map = {
    pending: 'bg-yellow-100 text-yellow-700',
    confirmed: 'bg-green-100 text-green-700',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-600'
  }
  return map[s] || 'bg-gray-100 text-gray-600'
}

// ---- Data fetching ----
async function fetchEvents() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (search.value) params.set('search', search.value)
    if (statusFilter.value) params.set('status', statusFilter.value)
    const res = await api.get(`/events?${params}`)
    events.value = res.data.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

async function fetchStats() {
  try {
    const res = await api.get('/events/stats')
    stats.value = res.data.data || stats.value
  } catch (e) {
    console.error(e)
  }
}

async function fetchBookedDates() {
  try {
    const res = await api.get('/events/booked-dates')
    bookedDates.value = res.data.data || []
  } catch (e) {
    console.error(e)
  }
}

async function fetchPackages() {
  try {
    const res = await api.get('/event-packages/active')
    eventPackages.value = res.data.data || []
  } catch (e) { console.error(e) }
}

async function fetchSupervisors() {
  try {
    const res = await api.get('/admin/users')
    const all = res.data.data || []
    supervisors.value = all.filter(u => ['admin','manager','front_desk'].includes(u.role))
  } catch (e) { console.error(e) }
}

function selectPackage(pkg) {
  form.value.package_id = pkg.id
  form.value.package_set = pkg.name
  form.value.price_per_head = pkg.price
  computeTotal()
}

async function searchEventGuest() {
  if (!eventGuestSearch.value.trim()) return
  try {
    const res = await api.get('/admin/reservations/search-guests', { params: { q: eventGuestSearch.value } })
    eventGuestResults.value = res.data.data || []
  } catch (e) { console.error(e) }
}

function fillEventGuest(g) {
  form.value.client_name = g.name || ''
  form.value.client_email = g.email || ''
  form.value.client_phone = g.phone || g.contact_number || ''
  eventGuestResults.value = []
  eventGuestSearch.value = ''
}

function handleEventIdFile(e) {
  eventIdFile.value = e.target.files[0] || null
}

async function refresh() {
  await Promise.all([fetchEvents(), fetchStats(), fetchBookedDates()])
}

// ---- Form helpers ----
function computeTotal() {
  const p = Number(form.value.price_per_head) || 0
  const g = Number(form.value.number_of_guests) || 0
  form.value.total_amount = p * g
  form.value.down_payment = Math.round(form.value.total_amount * 0.30 * 100) / 100
}

function closeModals() {
  showAddModal.value = false
  showEditModal.value = false
  form.value = defaultForm()
  formError.value = ''
  editingId.value = null
  eventGuestSearch.value = ''
  eventGuestResults.value = []
  eventIdFile.value = null
}

function viewEvent(ev) {
  viewingEvent.value = ev
  showViewModal.value = true
}

function editEvent(ev) {
  form.value = {
    event_name: ev.event_name || '',
    event_type: ev.event_type,
    client_name: ev.client_name,
    client_email: ev.client_email || '',
    client_phone: ev.client_phone || '',
    client_address: ev.client_address || '',
    event_date: ev.event_date,
    event_time: ev.event_time,
    number_of_guests: ev.number_of_guests,
    additional_guests: ev.additional_guests || 0,
    booked_by: ev.booked_by || '',
    supervisor_id: ev.supervisor_id || '',
    package_id: ev.package_id || null,
    venue: ev.venue || '',
    package_set: ev.package_set || '',
    price_per_head: ev.price_per_head,
    total_amount: ev.total_amount,
    down_payment: ev.down_payment,
    status: ev.status || 'pending',
    notes: ev.notes || '',
    remarks: ev.remarks || ''
  }
  editingId.value = ev.id
  showEditModal.value = true
}

function deletePrompt(ev) {
  deletingEvent.value = ev
  showDeleteModal.value = true
}

function openEventApproveModal(ev) {
  approveEventTarget.value = ev
  approveEventForm.value = { payment_option: 'full_payment', down_payment: 0, payment_method: '', online_payment_type: '', payment_ref: '' }
  showApproveEventModal.value = true
}

async function doEventApprove() {
  if (!approveEventTarget.value) return
  approvingEvent.value = true
  try {
    const f = approveEventForm.value
    if (f.payment_option === 'downpayment') {
      if (!f.payment_method) {
        toast.warning('Please select a payment method for the downpayment.')
        approvingEvent.value = false
        return
      }
      if (f.payment_method === 'online' && !f.online_payment_type) {
        toast.warning('Please select GCash or Bank Transfer.')
        approvingEvent.value = false
        return
      }
      if (f.payment_method === 'online' && f.online_payment_type && !f.payment_ref.trim()) {
        toast.warning('Please enter the reference number for the online payment.')
        approvingEvent.value = false
        return
      }
    }
    await api.put(`/events/${approveEventTarget.value.id}/status`, {
      status: 'confirmed',
      payment_option: f.payment_option,
      down_payment: Number(f.down_payment) || 0,
      payment_method: f.payment_method || null,
      online_payment_type: f.online_payment_type || null,
      payment_ref: f.payment_ref || null,
    })
    showApproveEventModal.value = false
    approveEventTarget.value = null
    toast.success('Event approved successfully!')
    await refresh()
    if (selectedCalDate.value) await selectCalendarDate(selectedCalDate.value)
  } catch (e) {
    toast.error(e.response?.data?.error || 'Failed to approve event')
  } finally {
    approvingEvent.value = false
  }
}

function confirmStatusChange(ev, newStatus) {
  if (newStatus === 'completed') {
    const balance = Number(ev.total_amount || 0) - Number(ev.down_payment || 0)
    if (balance > 0.01) {
      eventCompletionBlockModal.value = { clientName: ev.client_name, balance }
      return
    }
  }
  pendingStatus.value = { event: ev, newStatus }
  showStatusModal.value = true
}

// ---- Submit ----
async function submitForm() {
  formError.value = ''
  if (!form.value.event_type || !form.value.event_date || !form.value.event_time) {
    formError.value = 'Please fill in event type, date and time.'
    return
  }
  saving.value = true
  try {
    if (showEditModal.value) {
      await api.put(`/events/${editingId.value}`, form.value)
    } else {
      await api.post('/events', form.value)
    }
    toast.success(showEditModal.value ? 'Event updated successfully' : 'Event created successfully')
    closeModals()
    await refresh()
  } catch (e) {
    formError.value = e.response?.data?.error || 'Failed to save. Please try again.'
  } finally {
    saving.value = false
  }
}

async function confirmDelete() {
  if (!deletingEvent.value) return
  saving.value = true
  try {
    await api.delete(`/events/${deletingEvent.value.id}`)
    showDeleteModal.value = false
    deletingEvent.value = null
    await refresh()
    toast.success('Event archived successfully')
  } catch (e) {
    toast.error(e.response?.data?.error || 'Failed to delete event')
    console.error(e)
  } finally {
    saving.value = false
  }
}

async function applyStatusChange() {
  const { event, newStatus } = pendingStatus.value
  if (!event) return
  try {
    await api.put(`/events/${event.id}/status`, { status: newStatus })
    showStatusModal.value = false
    await refresh()
    if (selectedCalDate.value) await selectCalendarDate(selectedCalDate.value)
    if (newStatus === 'completed') {
      toast.success('Event marked as completed and moved to Reservation History')
    } else {
      toast.success('Event status updated')
    }
  } catch (e) {
    toast.error(e.response?.data?.error || 'Failed to update status')
    console.error(e)
  }
}

function openPaymentModal(ev) {
  paymentTarget.value = ev
  paymentForm.value = {
    payment_option: ev.payment_option || 'downpayment',
    down_payment: Number(ev.down_payment) || 0,
    payment_method: ev.payment_method || 'cash',
    online_payment_type: ev.online_payment_type || '',
    payment_ref: ev.payment_ref || ''
  }
  paymentError.value = ''
  showPaymentModal.value = true
  showViewModal.value = false
}

async function submitPayment() {
  paymentError.value = ''
  const f = paymentForm.value
  if (!f.payment_method) { paymentError.value = 'Please select a payment method.'; return }
  if (f.payment_method === 'online' && !f.online_payment_type) { paymentError.value = 'Please select an online payment type.'; return }
  if (f.payment_method === 'online' && f.online_payment_type && !f.payment_ref.trim()) { paymentError.value = 'Please enter the reference number.'; return }
  const dp = f.payment_option === 'full_payment' ? Number(paymentTarget.value.total_amount) : Number(f.down_payment)
  saving.value = true
  try {
    await api.post(`/events/${paymentTarget.value.id}/payment`, {
      down_payment:   dp,
      payment_option: f.payment_option,
      payment_method: f.payment_method === 'online' ? (f.online_payment_type || 'online') : 'cash',
      payment_ref:    f.payment_ref || null,
    })
    showPaymentModal.value = false
    paymentTarget.value = null
    await refresh()
    const payLabel = f.payment_option === 'full_payment' ? 'Full payment' : 'Down payment'
    toast.success(`${payLabel} recorded successfully`)
  } catch (e) {
    paymentError.value = e.response?.data?.error || 'Failed to record payment.'
  } finally {
    saving.value = false
  }
}

function printEvent(ev) {
  const total   = Number(ev.total_amount || 0)
  const dp      = Number(ev.down_payment || 0)
  const balance = total - dp
  const today   = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
  const ref     = ev.reference_number || ('EVT-' + String(ev.id).padStart(5, '0'))
  const payMethod = ev.payment_method ? (ev.payment_method.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase())) : '—'
  const payRef    = ev.payment_ref ? `<div class='field'><label>Reference #</label><p style='font-family:monospace'>${ev.payment_ref}</p></div>` : ''
  const win = window.open('', '_blank', 'width=850,height=1100')
  win.document.write(`<!DOCTYPE html><html><head>
  <title>Contract — ${ref}</title>
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
    <div><h1>JOANNA'S NOOK BED &amp; BREAKFAST</h1><p>Official Event Contract &nbsp;|&nbsp; Generated ${today}</p></div>
  </div>
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
      <div class="field"><label>Time</label><p>${ev.event_time ? (()=>{ const [h,m]=ev.event_time.split(':'); const hr=parseInt(h); return (hr%12||12)+':'+m+' '+(hr>=12?'PM':'AM'); })() : '—'}</p></div>
      <div class="field"><label>Number of Guests</label><p>${ev.number_of_guests || '—'}</p></div>
      <div class="field"><label>Venue</label><p>${ev.venue || 'Joanna\'s Nook Bed &amp; Breakfast'}</p></div>
      <div class="field"><label>Package / Set</label><p>${ev.package_set || '—'}</p></div>
      <div class="field"><label>Status</label><p style="text-transform:capitalize">${(ev.status||'').replace('_',' ')}</p></div>
    </div>
    ${ev.notes ? `<div style="margin-top:10px" class="field"><label>Notes / Special Requests</label><p>${ev.notes}</p></div>` : ''}
  </div>
  <div class="section">
    <div class="section-title">Payment Information</div>
    <div class="amount-box">
      <div class="amount-row"><span>Total Event Amount</span><span>&#x20B1;${total.toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>
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
      <li>Cancellation must be requested at least 7 days prior to the event date to receive a refund.</li>
      <li>Joanna's Nook Bed &amp; Breakfast reserves the right to cancel the event in case of non-compliance with terms.</li>
      <li>Any additional services requested on the event day are subject to separate billing.</li>
      <li>The client is responsible for the conduct of their guests within the venue premises.</li>
    </ul></div>
  </div>
  <div class="sig-grid">
    <div class="sig-block"><p>Client Signature</p><p style="margin-top:32px">${ev.client_name || ''}</p></div>
    <div class="sig-block"><p>Authorized Representative</p><p style="margin-top:32px">Joanna's Nook Bed &amp; Breakfast Management</p></div>
  </div>
  <p class="footer-note">Printed on ${today} &nbsp;|&nbsp; Joanna's Nook Bed &amp; Breakfast, Balingasag, Misamis Oriental</p>
  <script>window.onload=function(){window.print()}<\/script>
  </body></html>`)
  win.document.close()
}

onMounted(() => {
  refresh()
  fetchPackages()
  fetchSupervisors()
  // default selected date = today
  const t = new Date()
  selectedCalDate.value = `${t.getFullYear()}-${String(t.getMonth()+1).padStart(2,'0')}-${String(t.getDate()).padStart(2,'0')}`
  selectCalendarDate(selectedCalDate.value)
})
</script>
