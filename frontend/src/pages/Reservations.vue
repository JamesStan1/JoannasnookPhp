<template>
  <div class="p-6 bg-gray-50 min-h-screen font-light">

    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-semibold text-gray-800">Room Management</h1>
      <p class="text-gray-500 text-sm mt-1">Manage hotel rooms, reservations, and availability</p>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 mb-6 bg-white border border-gray-200 rounded-xl p-1 w-fit shadow-sm">
      <button v-for="tab in tabs" :key="tab.key"
        @click="activeTab = tab.key"
        :class="[
          'px-5 py-2 rounded-lg text-sm font-medium transition-all duration-200',
          activeTab === tab.key
            ? 'bg-green-800 text-white shadow'
            : 'text-gray-600 hover:bg-gray-100'
        ]">
        {{ tab.label }}
      </button>
    </div>

    <!-- ====== RESERVATIONS TAB ====== -->
    <div v-if="activeTab === 'reservations'">

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
          <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Reservations for Today</p>
          <p class="text-xl lg:text-3xl font-semibold text-green-800">{{ stats.today_reservations ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
          <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Reservations</p>
          <p class="text-xl lg:text-3xl font-semibold text-green-800">{{ stats.total_reservations ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
          <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Check-outs for Today</p>
          <p class="text-xl lg:text-3xl font-semibold text-green-800">{{ stats.today_checkouts ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
          <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Balance to Collect</p>
          <p class="text-xl lg:text-3xl font-semibold text-green-600">₱{{ formatMoney(stats.balance_to_collect ?? 0) }}</p>
        </div>
      </div>

      <!-- Search + Walk-in -->
      <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <div class="relative flex-1">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input v-model="search" @input="fetchReservations" type="text" placeholder="Search by guest, room number, or reference..."
            class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-600 bg-white" />
        </div>
        <select v-model="statusFilter" @change="fetchReservations"
          class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-600 bg-white">
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="checked_in">Checked In</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <button @click="openWalkInModal"
          class="bg-green-800 hover:bg-green-900 text-white text-sm font-medium px-5 py-2.5 rounded-xl flex items-center gap-2 transition whitespace-nowrap">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Walk-in Reservation
        </button>
      </div>

      <!-- Reservations Table -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-green-800 text-white">
                <th class="px-3 py-3 text-left font-medium text-xs whitespace-nowrap">Room #</th>
                <th class="px-3 py-3 text-left font-medium text-xs whitespace-nowrap">Type</th>
                <th class="px-3 py-3 text-left font-medium text-xs whitespace-nowrap">Customer</th>
                <th class="px-3 py-3 text-left font-medium text-xs whitespace-nowrap">Status</th>
                <th class="px-3 py-3 text-right font-medium text-xs whitespace-nowrap">Price/Night</th>
                <th class="px-3 py-3 text-left font-medium text-xs whitespace-nowrap">Check-in</th>
                <th class="px-3 py-3 text-left font-medium text-xs whitespace-nowrap">Check-out</th>
                <th class="px-3 py-3 text-right font-medium text-xs whitespace-nowrap">Total Payment</th>
                <th class="px-3 py-3 text-right font-medium text-xs whitespace-nowrap">Downpayment</th>
                <th class="px-3 py-3 text-right font-medium text-xs whitespace-nowrap">Remaining</th>
                <th class="px-3 py-3 text-right font-medium text-xs whitespace-nowrap">Café Payment</th>
                <th class="px-3 py-3 text-right font-medium text-xs whitespace-nowrap">Total Due</th>
                <th class="px-3 py-3 text-left font-medium text-xs whitespace-nowrap">Reference</th>
                <th class="px-3 py-3 text-center font-medium text-xs whitespace-nowrap">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="14" class="px-4 py-10 text-center text-gray-400">Loading...</td>
              </tr>
              <tr v-else-if="displayedReservations.length === 0">
                <td colspan="14" class="px-4 py-10 text-center text-gray-400">No reservations found</td>
              </tr>
              <tr v-for="res in displayedReservations" :key="res.id"
                class="border-t border-gray-50 hover:bg-green-50/40 transition-colors">
                <td class="px-3 py-2.5 font-medium text-green-800 whitespace-nowrap">{{ res.room_number }}</td>
                <td class="px-3 py-2.5 text-gray-600 whitespace-nowrap capitalize">{{ res.room_type }}</td>
                <td class="px-3 py-2.5 text-gray-800 whitespace-nowrap">{{ res.guest_name }}</td>
                <td class="px-3 py-2.5 whitespace-nowrap">
                  <span :class="statusBadge(res.status)" class="px-2 py-0.5 rounded-full text-xs font-medium capitalize">
                    {{ res.status?.replace('_', ' ') }}
                  </span>
                </td>
                <td class="px-3 py-2.5 text-right whitespace-nowrap">₱{{ formatMoney(res.room_price) }}</td>
                <td class="px-3 py-2.5 whitespace-nowrap text-gray-600">{{ formatDate(res.check_in_date) }}</td>
                <td class="px-3 py-2.5 whitespace-nowrap text-gray-600">{{ formatDate(res.check_out_date) }}</td>
                <td class="px-3 py-2.5 text-right font-medium whitespace-nowrap">₱{{ formatMoney(res.total_amount) }}</td>
                <td class="px-3 py-2.5 text-right whitespace-nowrap text-gray-600">₱{{ formatMoney(res.down_payment) }}</td>
                <td class="px-3 py-2.5 text-right whitespace-nowrap text-orange-600 font-medium">₱{{ formatMoney(res.remaining_balance) }}</td>
                <td class="px-3 py-2.5 text-right whitespace-nowrap text-gray-600">₱{{ formatMoney(res.cafe_payment) }}</td>
                <td class="px-3 py-2.5 text-right whitespace-nowrap font-semibold text-green-800">₱{{ formatMoney(Number(res.remaining_balance) + Number(res.cafe_payment)) }}</td>
                <td class="px-3 py-2.5 text-gray-500 text-xs whitespace-nowrap">{{ res.reference_number }}</td>
                <td class="px-3 py-2.5 whitespace-nowrap">
                  <div class="flex items-center justify-center gap-1">
                    <button @click="openViewModal(res)" title="View details"
                      class="p-1.5 text-amber-600 hover:bg-green-100 rounded-lg transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                    <button @click="openEditModal(res)" title="Edit reservation"
                      class="p-1.5 text-gray-500 hover:bg-gray-100 rounded-lg transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <template v-if="res.status === 'pending'">
                      <button @click="updateStatus(res.id, 'approve')" title="Approve"
                        class="p-1.5 text-green-600 hover:bg-green-100 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      </button>
                    </template>
                    <template v-else-if="res.status === 'approved'">
                      <button @click="updateStatus(res.id, 'check-in')" title="Check In"
                        class="p-1.5 text-amber-600 hover:bg-green-100 rounded-lg transition text-xs font-medium px-2">
                        CI
                      </button>
                    </template>
                    <template v-else-if="res.status === 'checked_in'">
                      <button @click="updateStatus(res.id, 'check-out')" title="Check Out"
                        class="p-1.5 text-purple-600 hover:bg-purple-100 rounded-lg transition text-xs font-medium px-2">
                        CO
                      </button>
                    </template>
                    <button @click="printReceipt(res)" title="Print receipt"
                      class="p-1.5 text-gray-400 hover:bg-gray-100 rounded-lg transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                    <button v-if="res.status !== 'cancelled' && res.status !== 'checked_out'"
                      @click="confirmCancel(res)" title="Cancel reservation"
                      class="p-1.5 text-red-500 hover:bg-red-100 rounded-lg transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Calendar + Info Panel -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Booking Calendar -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
          <h3 class="text-sm font-semibold text-gray-700 mb-4">Booking Calendar</h3>
          <div class="flex items-center justify-between mb-4">
            <button @click="prevMonth" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <span class="text-sm font-medium text-gray-800">{{ calendarMonthLabel }}</span>
            <button @click="nextMonth" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
          </div>
          <div class="grid grid-cols-7 text-center mb-2">
            <div v-for="d in ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']" :key="d"
              class="text-xs font-medium text-gray-400 py-1">{{ d }}</div>
          </div>
          <div class="grid grid-cols-7 gap-0.5">
            <div v-for="(day, idx) in calendarDays" :key="idx"
              @click="day.date && selectCalendarDate(day.date)"
              :class="[
                'text-xs text-center py-1.5 rounded-lg transition cursor-pointer',
                !day.date ? 'invisible' : '',
                day.isToday ? 'bg-green-700 text-white font-semibold' : '',
                day.isBooked && !day.isToday ? 'bg-green-100 text-green-800 font-medium' : '',
                day.isSelected && !day.isToday ? 'ring-2 ring-blue-500' : '',
                !day.isToday && !day.isBooked ? 'hover:bg-gray-100 text-gray-700' : '',
              ]">
              {{ day.day }}
            </div>
          </div>
          <div class="mt-3 flex gap-4 text-xs text-gray-500">
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-green-700 inline-block"></span> Today</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-green-100 inline-block"></span> Booked</span>
          </div>
        </div>

        <!-- Information of Reserved Days -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
          <h3 class="text-sm font-semibold text-gray-700 mb-1">Information of Reserved Days</h3>
          <p class="text-xs text-gray-400 mb-4">
            {{ selectedDate ? formatDate(selectedDate) : 'Select a date on the calendar' }}
          </p>
          <div v-if="!selectedDate" class="flex flex-col items-center justify-center py-10 text-gray-300">
            <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <p class="text-sm">Click a date to see bookings</p>
          </div>
          <div v-else-if="selectedDateReservations.length === 0" class="flex flex-col items-center justify-center py-10 text-gray-300">
            <p class="text-sm">No reservations on this date</p>
          </div>
          <div v-else class="space-y-3 max-h-64 overflow-y-auto pr-1">
            <div v-for="r in selectedDateReservations" :key="r.id"
              class="border border-gray-100 rounded-xl p-3 hover:border-green-200 transition">
              <div class="flex items-start justify-between">
                <div>
                  <p class="font-medium text-gray-800 text-sm">{{ r.guest_name }}</p>
                  <p class="text-xs text-gray-500 mt-0.5">Room {{ r.room_number }} · {{ r.room_type }}</p>
                </div>
                <span :class="statusBadge(r.status)" class="px-2 py-0.5 rounded-full text-xs font-medium capitalize">
                  {{ r.status?.replace('_', ' ') }}
                </span>
              </div>
              <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-gray-500">
                <div><span class="text-gray-400">Check-in:</span> {{ formatDate(r.check_in_date) }}</div>
                <div><span class="text-gray-400">Check-out:</span> {{ formatDate(r.check_out_date) }}</div>
                <div><span class="text-gray-400">Ref:</span> {{ r.reference_number }}</div>
                <div><span class="text-gray-400">Guests:</span> {{ r.number_of_guests }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ====== ROOM STATUS TAB ====== -->
    <div v-if="activeTab === 'roomstatus'">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-5">
        <div class="flex gap-2 flex-wrap">
          <button v-for="f in ['all','available','occupied','maintenance','dirty']" :key="f"
            @click="roomStatusFilter = f"
            :class="[
              'px-4 py-1.5 rounded-full text-xs font-medium border transition capitalize',
              roomStatusFilter === f ? 'bg-green-800 text-white border-blue-700' : 'bg-white text-gray-600 border-gray-200 hover:border-green-400'
            ]">
            {{ f === 'all' ? 'All Rooms' : f }}
          </button>
        </div>
        <button @click="openWalkInModal"
          class="bg-green-800 hover:bg-green-900 text-white text-sm font-medium px-4 py-2 rounded-xl flex items-center gap-2 transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Add New Reservation
        </button>
      </div>

      <!-- Room Stats -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
        <div v-for="s in roomCountsByStatus" :key="s.status"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 text-center">
          <p class="text-2xl font-semibold" :class="s.color">{{ s.count }}</p>
          <p class="text-xs text-gray-500 mt-0.5 capitalize">{{ s.status }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <div v-for="room in filteredRooms" :key="room.id"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition">
          <div class="flex items-start justify-between mb-3">
            <div>
              <p class="text-lg font-semibold text-gray-800">Room {{ room.room_number }}</p>
              <p class="text-xs text-gray-500 capitalize">{{ room.type }}</p>
            </div>
            <span :class="roomStatusBadge(room.status)" class="text-xs font-medium px-2 py-0.5 rounded-full capitalize">
              {{ room.status }}
            </span>
          </div>
          <div class="space-y-1 text-xs text-gray-600 mb-3">
            <div class="flex justify-between">
              <span class="text-gray-400">Capacity</span>
              <span>{{ room.capacity }} guests</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-400">Price/Night</span>
              <span class="font-medium text-green-800">₱{{ formatMoney(room.price) }}</span>
            </div>
            <div v-if="room.description" class="text-gray-400 pt-1 truncate">{{ room.description }}</div>
          </div>
          <div class="flex gap-2 pt-2 border-t border-gray-50">
            <button @click="openEditRoomModal(room)"
              class="flex-1 text-xs py-1.5 rounded-lg bg-gray-50 hover:bg-green-50 text-gray-600 hover:text-green-800 transition text-center font-medium">
              Edit
            </button>
            <div class="relative flex-1 group">
              <button class="w-full text-xs py-1.5 rounded-lg bg-gray-50 hover:bg-green-50 text-gray-600 hover:text-green-700 transition text-center font-medium">
                Status ▾
              </button>
              <div class="absolute bottom-full mb-1 left-0 bg-white border border-gray-200 rounded-xl shadow-lg z-10 hidden group-hover:block w-36 py-1">
                <button v-for="st in ['available','occupied','maintenance','dirty']" :key="st"
                  @click="changeRoomStatus(room.id, st)"
                  class="w-full text-left text-xs px-3 py-1.5 hover:bg-gray-50 capitalize">{{ st }}</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ====== HOUSEKEEPING TAB ====== -->
    <div v-if="activeTab === 'housekeeping'">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h3 class="text-base font-medium text-gray-700">Housekeeping Tasks</h3>
          <p class="text-xs text-gray-400">Room cleaning and maintenance status</p>
        </div>
        <button @click="$router.push('/housekeeping')"
          class="bg-green-800 hover:bg-green-900 text-white text-sm font-medium px-4 py-2 rounded-xl flex items-center gap-2 transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
          Open Housekeeping
        </button>
      </div>

      <div v-if="hkLoading" class="text-center py-10 text-gray-400">Loading tasks...</div>
      <div v-else-if="hkTasks.length === 0" class="text-center py-10 text-gray-400">No pending housekeeping tasks</div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="task in hkTasks" :key="task.id"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
          <div class="flex items-start justify-between mb-2">
            <p class="font-medium text-sm text-gray-800">Room {{ task.room_number ?? task.room_id }}</p>
            <span :class="hkStatusBadge(task.status)" class="text-xs px-2 py-0.5 rounded-full capitalize font-medium">{{ task.status }}</span>
          </div>
          <p class="text-xs text-gray-600 mb-1">{{ task.task_type ?? task.description }}</p>
          <p class="text-xs text-gray-400">Assigned: {{ task.assigned_to_name ?? 'Unassigned' }}</p>
          <p class="text-xs text-gray-400">Due: {{ formatDate(task.due_date ?? task.created_at) }}</p>
        </div>
      </div>
    </div>

    <!-- ====== VIEW MODAL ====== -->
    <div v-if="viewModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
          <h3 class="text-base font-semibold text-gray-800">Reservation Details</h3>
          <button @click="viewModal = false" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div v-if="selectedRes" class="p-5 space-y-4 text-sm">
          <div class="flex items-center justify-between">
            <span class="text-gray-400">Reference</span>
            <span class="font-medium text-green-800">{{ selectedRes.reference_number }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-gray-400">Status</span>
            <span :class="statusBadge(selectedRes.status)" class="px-2 py-0.5 rounded-full text-xs font-medium capitalize">{{ selectedRes.status?.replace('_',' ') }}</span>
          </div>
          <div class="h-px bg-gray-100"></div>
          <div class="grid grid-cols-2 gap-3">
            <div><p class="text-gray-400 text-xs mb-0.5">Guest</p><p class="font-medium">{{ selectedRes.guest_name }}</p></div>
            <div><p class="text-gray-400 text-xs mb-0.5">Email</p><p class="text-gray-600 truncate">{{ selectedRes.guest_email }}</p></div>
            <div><p class="text-gray-400 text-xs mb-0.5">Room</p><p class="font-medium">{{ selectedRes.room_number }} ({{ selectedRes.room_type }})</p></div>
            <div><p class="text-gray-400 text-xs mb-0.5">Guests</p><p>{{ selectedRes.number_of_guests }}</p></div>
            <div><p class="text-gray-400 text-xs mb-0.5">Check-in</p><p>{{ formatDate(selectedRes.check_in_date) }}</p></div>
            <div><p class="text-gray-400 text-xs mb-0.5">Check-out</p><p>{{ formatDate(selectedRes.check_out_date) }}</p></div>
            <div><p class="text-gray-400 text-xs mb-0.5">Nights</p><p>{{ selectedRes.nights }}</p></div>
            <div><p class="text-gray-400 text-xs mb-0.5">Price/Night</p><p class="text-green-800 font-medium">₱{{ formatMoney(selectedRes.room_price) }}</p></div>
          </div>
          <div class="h-px bg-gray-100"></div>
          <div class="grid grid-cols-2 gap-3">
            <div><p class="text-gray-400 text-xs mb-0.5">Total Amount</p><p class="font-semibold">₱{{ formatMoney(selectedRes.total_amount) }}</p></div>
            <div><p class="text-gray-400 text-xs mb-0.5">Downpayment</p><p>₱{{ formatMoney(selectedRes.down_payment) }}</p></div>
            <div><p class="text-gray-400 text-xs mb-0.5">Remaining</p><p class="text-orange-600 font-semibold">₱{{ formatMoney(selectedRes.remaining_balance) }}</p></div>
            <div><p class="text-gray-400 text-xs mb-0.5">Café Payment</p><p>₱{{ formatMoney(selectedRes.cafe_payment) }}</p></div>
          </div>
          <div v-if="selectedRes.special_requests" class="bg-gray-50 rounded-xl p-3">
            <p class="text-xs text-gray-400 mb-1">Special Requests</p>
            <p class="text-sm text-gray-700">{{ selectedRes.special_requests }}</p>
          </div>
        </div>
        <div class="p-5 border-t border-gray-100 flex gap-3 justify-end">
          <button @click="printReceipt(selectedRes)"
            class="px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Print Receipt</button>
          <button @click="viewModal = false"
            class="px-5 py-2 text-sm bg-green-800 text-white rounded-xl hover:bg-green-900 transition">Close</button>
        </div>
      </div>
    </div>

    <!-- ====== EDIT MODAL ====== -->
    <div v-if="editModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
          <h3 class="text-base font-semibold text-gray-800">Edit Reservation</h3>
          <button @click="editModal = false" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <form @submit.prevent="saveEdit" class="p-5 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-500 mb-1">Check-in Date</label>
              <input v-model="editForm.check_in_date" type="date" required
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">Check-out Date</label>
              <input v-model="editForm.check_out_date" type="date" required
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600" />
            </div>
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">Number of Guests</label>
            <input v-model="editForm.number_of_guests" type="number" min="1"
              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600" />
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">Downpayment (₱)</label>
            <input v-model="editForm.down_payment" type="number" min="0" step="0.01"
              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600" />
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">Special Requests</label>
            <textarea v-model="editForm.special_requests" rows="2"
              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 resize-none"></textarea>
          </div>
          <div class="flex gap-3 justify-end pt-2">
            <button type="button" @click="editModal = false"
              class="px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
            <button type="submit" :disabled="editSaving"
              class="px-5 py-2 text-sm bg-green-800 text-white rounded-xl hover:bg-green-900 transition disabled:opacity-60">
              {{ editSaving ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ====== WALK-IN MODAL ====== -->
    <div v-if="walkInModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col">

        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
          <h3 class="text-base font-bold text-gray-800">Add Reservation</h3>
          <button @click="walkInModal = false" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <form @submit.prevent="submitWalkIn" class="p-5 space-y-4 overflow-y-auto flex-1">

          <!-- Select Package (Room) -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Select Rooms</label>
            <select v-model="walkInForm.room_id" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
              <option value="">Select Rooms</option>
              <option v-for="r in availableRooms" :key="r.id" :value="r.id">
                Room {{ r.room_number }} — {{ r.type }} (₱{{ formatMoney(r.price) }}/night)
              </option>
            </select>
          </div>

          <!-- Returning Customer -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="text-xs font-medium text-gray-600">Returning Customer?</label>
              <button type="button" @click="walkInManual = !walkInManual"
                class="text-xs text-green-600 hover:underline">Enter manually</button>
            </div>
            <div class="flex gap-2">
              <input v-model="walkInGuestSearch" type="text"
                placeholder="Search by name, email, or phone number"
                class="flex-1 border border-green-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
              <button type="button" @click="searchGuest"
                class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg transition">Search</button>
            </div>
            <!-- Guest search results -->
            <div v-if="guestResults.length > 0" class="mt-1 border border-gray-200 rounded-lg overflow-hidden shadow-sm">
              <div v-for="g in guestResults" :key="g.id"
                @click="fillGuest(g)"
                class="px-3 py-2 text-sm hover:bg-green-50 cursor-pointer border-b border-gray-100 last:border-0">
                <span class="font-medium text-gray-800">{{ g.name }}</span>
                <span class="text-gray-400 ml-2 text-xs">{{ g.email }}</span>
              </div>
            </div>
          </div>

          <!-- Name + Email -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Customer Name *</label>
              <input v-model="walkInForm.guest_name" type="text" required placeholder="Customer Name"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Email *</label>
              <input v-model="walkInForm.guest_email" type="email" required placeholder="Email"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
            </div>
          </div>

          <!-- Contact + Address -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Contact Number *</label>
              <input v-model="walkInForm.contact_number" type="text" required placeholder="Contact Number"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Address *</label>
              <input v-model="walkInForm.address" type="text" required placeholder="Address"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
            </div>
          </div>

          <!-- Nationality + Additional Guests -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Nationality</label>
              <select v-model="walkInForm.nationality"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Select Nationality</option>
                <option>Filipino</option>
                <option>American</option>
                <option>British</option>
                <option>Australian</option>
                <option>Japanese</option>
                <option>Korean</option>
                <option>Chinese</option>
                <option>Singaporean</option>
                <option>Other</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Additional Guests</label>
              <input v-model="walkInForm.number_of_guests" type="number" min="1"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
              <p class="text-xs text-gray-400 mt-0.5">Please choose a room package first</p>
            </div>
          </div>

          <!-- Check-in + Check-out -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Check-in Date *</label>
              <input v-model="walkInForm.check_in_date" type="date" required :min="today"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Check-out Date *</label>
              <input v-model="walkInForm.check_out_date" type="date" required :min="walkInForm.check_in_date || today"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
            </div>
          </div>

          <!-- ID Upload -->
          <div>
            <div class="flex items-center gap-2">
              <input type="file" ref="idFileInput" @change="handleIdFile" accept="image/*" class="hidden" />
              <div class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-500 cursor-pointer"
                   @click="idFileInput.click()">
                {{ walkInIdFile ? walkInIdFile.name : 'Choose File   No file chosen' }}
              </div>
              <button type="button" @click="idFileInput.click()"
                class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg transition whitespace-nowrap">
                Capture ID
              </button>
            </div>
          </div>

          <!-- Additional Requests -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="text-xs font-medium text-gray-600">Additional Requests</label>
              <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </div>
            <textarea v-model="walkInForm.special_requests" rows="2" placeholder="Additional Requests"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none"></textarea>
          </div>

          <!-- Remarks -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="text-xs font-medium text-gray-600">Remarks</label>
              <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </div>
            <textarea v-model="walkInForm.remarks" rows="2"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none"></textarea>
          </div>

          <!-- Payment Options -->
          <div>
            <div class="border border-green-200 rounded-xl p-4 bg-green-50/30">
              <div class="flex items-center gap-2 mb-3">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span class="text-sm font-semibold text-gray-700">Payment Options</span>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div @click="walkInForm.payment_option = 'full_payment'"
                     :class="walkInForm.payment_option === 'full_payment' ? 'border-blue-500 bg-green-50' : 'border-gray-200 bg-white'"
                     class="border-2 rounded-xl p-3 cursor-pointer transition">
                  <div class="flex items-center gap-2 mb-1">
                    <div :class="walkInForm.payment_option === 'full_payment' ? 'border-blue-500' : 'border-gray-300'"
                         class="w-4 h-4 rounded-full border-2 flex items-center justify-center">
                      <div v-if="walkInForm.payment_option === 'full_payment'" class="w-2 h-2 rounded-full bg-green-600"></div>
                    </div>
                    <span class="text-sm font-semibold text-gray-800">Full Payment at Checkout</span>
                  </div>
                  <p class="text-xs text-gray-400 ml-6">Pay the total amount when checking out.</p>
                </div>
                <div @click="walkInForm.payment_option = 'downpayment'"
                     :class="walkInForm.payment_option === 'downpayment' ? 'border-blue-500 bg-green-50' : 'border-gray-200 bg-white'"
                     class="border-2 rounded-xl p-3 cursor-pointer transition">
                  <div class="flex items-center gap-2 mb-1">
                    <div :class="walkInForm.payment_option === 'downpayment' ? 'border-blue-500 bg-green-600' : 'border-gray-300'"
                         class="w-4 h-4 rounded-full border-2 flex items-center justify-center">
                      <svg v-if="walkInForm.payment_option === 'downpayment'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-800">Downpayment Now</span>
                  </div>
                  <p class="text-xs text-gray-400 ml-6">Pay partial amount now, rest at checkout.</p>
                </div>
              </div>
              <!-- Downpayment amount input -->
              <div v-if="walkInForm.payment_option === 'downpayment'" class="mt-3">
                <label class="block text-xs font-medium text-gray-600 mb-1">Downpayment Amount (₱)</label>
                <input v-model="walkInForm.down_payment" type="number" min="0" step="0.01" placeholder="0.00"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
              </div>
            </div>
          </div>

          <!-- Terms & Conditions -->
          <div class="border border-yellow-300 bg-yellow-50 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              <span class="text-xs font-bold text-yellow-800 uppercase tracking-wide">Terms & Conditions of Stay</span>
            </div>
            <ul class="text-xs text-yellow-900 space-y-1 list-disc list-inside leading-relaxed">
              <li>A valid identification card must be presented upon check-in.</li>
              <li>All guests arriving must register with the Hotels Front Desk. Check-in time is 2:00 p.m. and check-out time is 12:00 noon.</li>
              <li>Should you wish to stay beyond the designated check-out time, please inform the Front Desk. Early check-in and check-out is subject to additional charge and room availability.</li>
              <li>Proper courtesy must be observed at all times. The privacy of other guests must be respected.</li>
              <li>Money, valuables, and important documents must be kept in the safety deposit box located inside your room. The hotel shall not be liable for items lost in any form.</li>
              <li>Gambling and possession of illegal drugs are not allowed within the hotel premises.</li>
              <li>Pets, firearms, and explosives should not be brought out or transferred to another room to avoid unnecessary damages.</li>
              <li>Amenities are provided for your comfort during your stay. Should you wish to request additional items, please call the Front Desk.</li>
              <li>Smoking inside the room and bringing food with a strong odor are not allowed. A fine of ₱5,000.00 for fumigation shall be charged for non-compliance.</li>
              <li>By affixing your signature (whether personally, through an agent, or a representative), you hereby agree to the terms and conditions set forth herein and consent to the collection and processing of your data by the hotel in accordance with the Data Privacy Act and the regulations of the National Privacy Commission (NPC).</li>
            </ul>
          </div>

          <!-- Buttons -->
          <div class="flex gap-3 justify-end pt-1">
            <button type="button" @click="walkInModal = false"
              class="px-5 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
            <button type="submit" :disabled="walkInSaving"
              class="px-6 py-2 text-sm bg-green-700 text-white rounded-xl hover:bg-green-800 transition disabled:opacity-60 font-medium">
              {{ walkInSaving ? 'Creating...' : 'Create Reservation' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ====== ADD/EDIT ROOM MODAL ====== -->
    <div v-if="roomModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
          <h3 class="text-base font-semibold text-gray-800">{{ editingRoom ? 'Edit Room' : 'Add Room' }}</h3>
          <button @click="roomModal = false" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <form @submit.prevent="saveRoom" class="p-5 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-500 mb-1">Room Number *</label>
              <input v-model="roomForm.room_number" required
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">Type *</label>
              <select v-model="roomForm.type" required
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                <option value="">Select...</option>
                <option value="standard">Standard</option>
                <option value="deluxe">Deluxe</option>
                <option value="suite">Suite</option>
                <option value="family">Family</option>
                <option value="executive">Executive</option>
              </select>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-500 mb-1">Price/Night (₱) *</label>
              <input v-model="roomForm.price" type="number" min="0" step="0.01" required
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">Capacity</label>
              <input v-model="roomForm.capacity" type="number" min="1"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600" />
            </div>
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">Description</label>
            <textarea v-model="roomForm.description" rows="2"
              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 resize-none"></textarea>
          </div>
          <div class="flex gap-3 justify-end pt-2">
            <button type="button" @click="roomModal = false"
              class="px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
            <button type="submit" :disabled="roomSaving"
              class="px-5 py-2 text-sm bg-green-800 text-white rounded-xl hover:bg-green-900 transition disabled:opacity-60">
              {{ roomSaving ? 'Saving...' : (editingRoom ? 'Save Changes' : 'Add Room') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ====== CANCEL CONFIRM ====== -->
    <div v-if="cancelConfirmRes" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        </div>
        <p class="text-sm text-gray-700 font-medium mb-1">Cancel Reservation</p>
        <p class="text-xs text-gray-500 mb-5">Are you sure you want to cancel <strong>{{ cancelConfirmRes.reference_number }}</strong> for <strong>{{ cancelConfirmRes.guest_name }}</strong>?</p>
        <div class="flex gap-3">
          <button @click="cancelConfirmRes = null" class="flex-1 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">No, Keep</button>
          <button @click="doCancel" class="flex-1 py-2 text-sm bg-red-600 text-white rounded-xl hover:bg-red-700 transition">Yes, Cancel</button>
        </div>
      </div>
    </div>

    <!-- ====== WALK-IN PRINT CONFIRMATION ====== -->
    <div v-if="walkInPrintModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <p class="text-base font-semibold text-gray-800 mb-1">Reservation Created!</p>
        <p class="text-xs text-gray-500 mb-1">Guest: <strong>{{ walkInResult?.guest_name }}</strong></p>
        <p class="font-mono text-xs text-amber-600 mb-4">{{ walkInResult?.reference_number }}</p>
        <p class="text-xs text-gray-500 mb-5">Would you like to print the contract and billing summary for this walk-in reservation?</p>
        <div class="flex flex-col gap-2">
          <button @click="printContractAndBill(walkInResult); walkInPrintModal = false"
            class="w-full py-2.5 text-sm bg-green-800 text-white rounded-xl hover:bg-green-900 transition font-medium flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Contract &amp; Bill
          </button>
          <button @click="walkInPrintModal = false"
            class="w-full py-2.5 text-sm border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition">
            Skip for Now
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import { useToastStore } from '../stores/toast'

const toast = useToastStore()

const router = useRouter()

const tabs = [
  { key: 'reservations', label: 'Reservations' },
  { key: 'roomstatus', label: 'Room Status' },
  { key: 'housekeeping', label: 'Housekeeping' },
]
const activeTab = ref('reservations')

const reservations = ref([])
const loading = ref(false)
const stats = ref({})
const search = ref('')
const statusFilter = ref('')
const bookedDates = ref([])

const rooms = ref([])
const roomStatusFilter = ref('all')

const hkTasks = ref([])
const hkLoading = ref(false)

const today = new Date().toISOString().split('T')[0]
const calendarDate = ref(new Date())
const selectedDate = ref(null)

const viewModal = ref(false)
const editModal = ref(false)
const walkInModal = ref(false)
const walkInManual = ref(false)
const walkInGuestSearch = ref('')
const guestResults = ref([])
const idFileInput = ref(null)
const walkInIdFile = ref(null)
const roomModal = ref(false)
const cancelConfirmRes = ref(null)
const selectedRes = ref(null)
const editSaving = ref(false)
const walkInSaving    = ref(false)
const walkInResult    = ref(null)
const walkInPrintModal = ref(false)
const roomSaving = ref(false)
const editingRoom = ref(null)

const editForm = ref({})
const walkInForm = ref({ number_of_guests: 1, down_payment: 0, payment_option: 'full_payment' })
const roomForm = ref({ capacity: 2 })

// Exclude checked_out from the Room Management table; those belong in Billing
const displayedReservations = computed(() =>
  reservations.value.filter(r => r.status !== 'checked_out')
)

const availableRooms = computed(() => rooms.value.filter(r => r.status === 'available'))
const walkInRoom = computed(() => rooms.value.find(r => r.id == walkInForm.value.room_id))
const walkInNights = computed(() => {
  if (!walkInForm.value.check_in_date || !walkInForm.value.check_out_date) return 0
  const diff = new Date(walkInForm.value.check_out_date) - new Date(walkInForm.value.check_in_date)
  return Math.max(Math.floor(diff / 86400000), 0)
})

async function fetchReservations() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (search.value) params.append('search', search.value)
    if (statusFilter.value) params.append('status', statusFilter.value)
    const res = await api.get(`/admin/reservations?${params}`)
    reservations.value = res.data?.data ?? res.data ?? []
  } catch (e) { console.error(e) } finally { loading.value = false }
}

async function fetchStats() {
  try {
    const res = await api.get('/admin/reservations/stats')
    stats.value = res.data?.data ?? res.data ?? {}
  } catch (e) { console.error(e) }
}

async function fetchBookedDates() {
  try {
    const res = await api.get('/admin/reservations/booked-dates')
    bookedDates.value = res.data?.data ?? res.data ?? []
  } catch (e) { console.error(e) }
}

async function fetchRooms() {
  try {
    const res = await api.get('/rooms')
    rooms.value = res.data?.data ?? res.data ?? []
  } catch (e) { console.error(e) }
}

async function fetchHkTasks() {
  hkLoading.value = true
  try {
    const res = await api.get('/housekeeping/pending-tasks')
    hkTasks.value = res.data?.data ?? res.data ?? []
  } catch (e) { console.error(e) } finally { hkLoading.value = false }
}

async function updateStatus(id, action) {
  try {
    await api.put(`/reservations/${id}/${action}`)
    await fetchReservations()
    await fetchStats()
    await fetchBookedDates()
    await fetchRooms()
  } catch (e) { toast.error(e.response?.data?.message ?? 'Error updating status') }
}

function confirmCancel(res) { cancelConfirmRes.value = res }
async function doCancel() {
  if (!cancelConfirmRes.value) return
  await updateStatus(cancelConfirmRes.value.id, 'cancel')
  cancelConfirmRes.value = null
}

function openViewModal(res) { selectedRes.value = res; viewModal.value = true }
function openEditModal(res) {
  editForm.value = {
    check_in_date: res.check_in_date,
    check_out_date: res.check_out_date,
    number_of_guests: res.number_of_guests,
    down_payment: res.down_payment,
    special_requests: res.special_requests,
  }
  selectedRes.value = res
  editModal.value = true
}
async function saveEdit() {
  editSaving.value = true
  try {
    await api.put(`/reservations/${selectedRes.value.id}`, editForm.value)
    editModal.value = false
    await fetchReservations()
    await fetchStats()
    toast.success('Reservation updated successfully')
  } catch (e) { toast.error(e.response?.data?.message ?? 'Error saving') } finally { editSaving.value = false }
}

function openWalkInModal() {
  const now = new Date()
  const timeStr = now.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', hour12: true })
  walkInForm.value = {
    number_of_guests: 1,
    down_payment: 0,
    payment_option: 'full_payment',
    check_in_date: today,
    remarks: `Walk-in customer - Check-in time: ${timeStr}`,
  }
  walkInGuestSearch.value = ''
  guestResults.value = []
  walkInIdFile.value = null
  walkInModal.value = true
}

async function searchGuest() {
  if (!walkInGuestSearch.value.trim()) return
  try {
    const res = await api.get('/admin/reservations/search-guests', { params: { q: walkInGuestSearch.value } })
    guestResults.value = res.data.data || []
  } catch { guestResults.value = [] }
}

function fillGuest(g) {
  walkInForm.value.guest_name = g.name
  walkInForm.value.guest_email = g.email
  walkInForm.value.contact_number = g.contact_number || ''
  walkInForm.value.address = g.address || ''
  walkInForm.value.nationality = g.nationality || ''
  walkInForm.value.guest_id = g.id
  guestResults.value = []
  walkInGuestSearch.value = g.name
}

function handleIdFile(e) {
  walkInIdFile.value = e.target.files[0] || null
}

async function submitWalkIn() {
  walkInSaving.value = true
  try {
    const res = await api.post('/admin/reservations/walk-in', walkInForm.value)
    const newRes = res.data?.data ?? res.data
    walkInModal.value = false
    await fetchReservations()
    await fetchStats()
    await fetchBookedDates()
    await fetchRooms()
    walkInResult.value = newRes
    walkInPrintModal.value = true
  } catch (e) { toast.error(e.response?.data?.message ?? 'Error creating reservation') } finally { walkInSaving.value = false }
}

function openAddRoomModal() { editingRoom.value = null; roomForm.value = { capacity: 2 }; roomModal.value = true }
function openEditRoomModal(room) {
  editingRoom.value = room
  roomForm.value = { room_number: room.room_number, type: room.type, price: room.price, capacity: room.capacity, description: room.description }
  roomModal.value = true
}
async function saveRoom() {
  roomSaving.value = true
  try {
    if (editingRoom.value) {
      await api.put(`/rooms/${editingRoom.value.id}`, roomForm.value)
    } else {
      await api.post('/rooms', roomForm.value)
    }
    roomModal.value = false
    await fetchRooms()
    toast.success(editingRoom.value ? 'Room updated' : 'Room added')
  } catch (e) { toast.error(e.response?.data?.message ?? 'Error saving room') } finally { roomSaving.value = false }
}
async function changeRoomStatus(id, status) {
  try {
    await api.put(`/rooms/${id}/status`, { status })
    await fetchRooms()
  } catch (e) { console.error(e) }
}

function printContractAndBill(res) {
  const nights  = res.nights || Math.max(Math.floor((new Date(res.check_out_date) - new Date(res.check_in_date)) / 86400000), 1)
  const total   = Number(res.total_amount || (Number(res.room_price || 0) * nights))
  const dp      = Number(res.down_payment || 0)
  const cafe    = Number(res.cafe_payment || 0)
  const balance = total + cafe - dp
  const today   = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
  const win = window.open('', '_blank', 'width=850,height=1100')
  win.document.write(`<!DOCTYPE html><html><head>
  <title>Contract & Bill — ${res.reference_number}</title>
  <style>
    @page { size: A4; margin: 20mm 18mm; }
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; color: #111; }
    body { padding: 32px 36px; }
    .header { display: flex; align-items: center; gap: 16px; border-bottom: 3px solid #1d4ed8; padding-bottom: 16px; margin-bottom: 20px; }
    .header h1 { font-size: 22px; color: #1d4ed8; font-weight: 700; }
    .header p  { font-size: 12px; color: #555; margin-top: 2px; }
    .contract-title { text-align: center; font-size: 16px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #1d4ed8; margin-bottom: 16px; }
    .ref-badge { text-align: center; font-size: 13px; background: #eff6ff; color: #1d4ed8; padding: 6px 16px; border-radius: 20px; display: inline-block; font-family: monospace; font-weight: 700; }
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
    .page2 { page-break-before: always; padding-top: 8px; }
    .bill-row { display: flex; justify-content: space-between; font-size: 13px; padding: 6px 0; border-bottom: 1px solid #e5e7eb; }
    .bill-row.subtotal { font-weight: 600; }
    .bill-row.grand { font-weight: 700; font-size: 16px; color: #1d4ed8; border-bottom: none; margin-top: 8px; }
    .footer-note { text-align: right; font-size: 11px; color: #9ca3af; margin-top: 20px; }
    @media print { .print-btn { display: none !important; } }
  </style>
  </head><body>
  <!-- PAGE 1: CONTRACT -->
  <div class="header">
    <div><h1>JOANNA'S NOOK BED &amp; BREAKFAST</h1><p>Reservation Contract &nbsp;|&nbsp; Generated ${today}</p></div>
  </div>
  <p class="contract-title">Reservation Contract</p>
  <div style="text-align:center;margin-bottom:20px"><span class="ref-badge">${res.reference_number}</span></div>
  <div class="section">
    <div class="section-title">Guest Information</div>
    <div class="grid-2">
      <div class="field"><label>Full Name</label><p>${res.guest_name}</p></div>
      <div class="field"><label>Email</label><p>${res.guest_email || '—'}</p></div>
      <div class="field"><label>Guests</label><p>${res.number_of_guests}</p></div>
      <div class="field"><label>Type</label><p>Walk-in</p></div>
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
    <div class="section-title">Terms &amp; Conditions</div>
    <div class="tc-box"><ul>
      <li>A valid government-issued ID must be presented upon check-in.</li>
      <li>Check-in: 2:00 PM &nbsp;|&nbsp; Check-out: 12:00 noon. Late check-out subject to additional charges.</li>
      <li>All guests must register at the Front Desk. Additional guests must be declared.</li>
      <li>Money, valuables, and important documents must be kept in the in-room safety deposit box. The hotel is not liable for any loss.</li>
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
  <!-- PAGE 2: BILLING SUMMARY -->
  <div class="page2">
    <div class="header">
      <div><h1>JOANNA'S NOOK BED &amp; BREAKFAST</h1><p>Billing Summary &nbsp;|&nbsp; ${today}</p></div>
    </div>
    <p class="contract-title">Billing Summary</p>
    <div style="text-align:center;margin-bottom:20px"><span class="ref-badge">${res.reference_number}</span></div>
    <div class="section">
      <div class="section-title">Guest</div>
      <div class="grid-2">
        <div class="field"><label>Name</label><p>${res.guest_name}</p></div>
        <div class="field"><label>Room</label><p>Room ${res.room_number} &nbsp;/&nbsp; ${nights} night${nights!==1?'s':''}</p></div>
        <div class="field"><label>Check-in</label><p>${res.check_in_date}</p></div>
        <div class="field"><label>Check-out</label><p>${res.check_out_date}</p></div>
      </div>
    </div>
    <div class="section">
      <div class="section-title">Charges</div>
      <div class="amount-box">
        <div class="bill-row"><span>Room Rate (${nights} × ₱${Number(res.room_price||0).toLocaleString('en-PH',{minimumFractionDigits:2})})</span><span>₱${total.toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>
        ${cafe > 0 ? `<div class="bill-row"><span>Café Charges</span><span>₱${cafe.toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>` : ''}
        <div class="bill-row subtotal"><span>Gross Total</span><span>₱${(total+cafe).toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>
        <div class="bill-row" style="color:#16a34a"><span>Downpayment Received</span><span>− ₱${dp.toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>
        <div class="bill-row grand"><span>BALANCE DUE</span><span>₱${balance.toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>
      </div>
    </div>
    <div class="sig-grid" style="margin-top:40px">
      <div><div class="sig-block"><p>Cashier Signature</p><p>Date: ___________________</p></div></div>
      <div><div class="sig-block"><p>Guest Acknowledgment</p><p>Date: ___________________</p></div></div>
    </div>
    <p class="footer-note">Joanna's Nook Bed &amp; Breakfast &nbsp;|&nbsp; ${res.reference_number} &nbsp;|&nbsp; ${today}</p>
  </div>
  <br><button class="print-btn" onclick="window.print()" style="background:#1d4ed8;color:#fff;border:none;padding:10px 28px;border-radius:10px;cursor:pointer;font-size:14px">🖨 Print</button>
  <script>window.onload=()=>window.print()<\/script>
  </body></html>`)
  win.document.close()
}

function printReceipt(res) {
  const win = window.open('', '_blank', 'width=600,height=700')
  win.document.write(`<!DOCTYPE html><html><head><title>Receipt - ${res.reference_number}</title>
  <style>body{font-family:Arial,sans-serif;padding:24px;color:#111;} h1{color:#1d4ed8;margin-bottom:4px;} .row{display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px solid #eee;font-size:14px;} .total{font-weight:bold;color:#1d4ed8;font-size:16px;} @media print{button{display:none}}</style>
  </head><body>
  <h1>JOANNA'S NOOK BED &amp; BREAKFAST</h1><p style="color:#666;font-size:13px;margin-top:0">Official Guest Receipt</p><hr>
  <div class="row"><span>Reference</span><span>${res.reference_number}</span></div>
  <div class="row"><span>Guest</span><span>${res.guest_name}</span></div>
  <div class="row"><span>Room</span><span>${res.room_number} (${res.room_type})</span></div>
  <div class="row"><span>Check-in</span><span>${res.check_in_date}</span></div>
  <div class="row"><span>Check-out</span><span>${res.check_out_date}</span></div>
  <div class="row"><span>Nights</span><span>${res.nights}</span></div>
  <div class="row"><span>Price/Night</span><span>₱${Number(res.room_price).toLocaleString()}</span></div>
  <div class="row"><span>Total Amount</span><span>₱${Number(res.total_amount).toLocaleString()}</span></div>
  <div class="row"><span>Downpayment</span><span>₱${Number(res.down_payment).toLocaleString()}</span></div>
  <div class="row"><span>Café Payment</span><span>₱${Number(res.cafe_payment ?? 0).toLocaleString()}</span></div>
  <div class="row total"><span>Balance Due</span><span>₱${(Number(res.remaining_balance) + Number(res.cafe_payment ?? 0)).toLocaleString()}</span></div>
  <div class="row"><span>Status</span><span style="text-transform:capitalize">${res.status?.replace('_',' ')}</span></div>
  <br><p style="font-size:12px;color:#888;text-align:center">Thank you for staying at Joanna's Nook Bed &amp; Breakfast!</p>
  <br><button onclick="window.print()" style="background:#1d4ed8;color:#fff;border:none;padding:8px 20px;border-radius:8px;cursor:pointer">Print</button>
  </body></html>`)
  win.document.close()
}

const calendarMonthLabel = computed(() =>
  calendarDate.value.toLocaleString('default', { month: 'long', year: 'numeric' })
)
function prevMonth() { calendarDate.value = new Date(calendarDate.value.getFullYear(), calendarDate.value.getMonth() - 1, 1) }
function nextMonth() { calendarDate.value = new Date(calendarDate.value.getFullYear(), calendarDate.value.getMonth() + 1, 1) }

const bookedDateSet = computed(() => {
  const set = new Set()
  bookedDates.value.forEach(b => {
    let d = new Date(b.check_in_date)
    const end = new Date(b.check_out_date)
    while (d <= end) {
      set.add(d.toISOString().split('T')[0])
      d = new Date(d.getTime() + 86400000)
    }
  })
  return set
})

const calendarDays = computed(() => {
  const year = calendarDate.value.getFullYear()
  const month = calendarDate.value.getMonth()
  const firstDay = new Date(year, month, 1).getDay()
  const daysInMonth = new Date(year, month + 1, 0).getDate()
  const offset = (firstDay + 6) % 7
  const days = []
  for (let i = 0; i < offset; i++) days.push({ date: null, day: '' })
  for (let d = 1; d <= daysInMonth; d++) {
    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`
    days.push({
      date: dateStr, day: d,
      isToday: dateStr === today,
      isBooked: bookedDateSet.value.has(dateStr),
      isSelected: selectedDate.value === dateStr,
    })
  }
  return days
})

function selectCalendarDate(date) {
  selectedDate.value = selectedDate.value === date ? null : date
}

const selectedDateReservations = computed(() => {
  if (!selectedDate.value) return []
  return reservations.value.filter(r => r.check_in_date <= selectedDate.value && r.check_out_date >= selectedDate.value)
})

const filteredRooms = computed(() =>
  roomStatusFilter.value === 'all' ? rooms.value : rooms.value.filter(r => r.status === roomStatusFilter.value)
)

const roomCountsByStatus = computed(() => {
  const statuses = [
    { status: 'available', color: 'text-green-600' },
    { status: 'occupied', color: 'text-amber-600' },
    { status: 'maintenance', color: 'text-yellow-600' },
    { status: 'dirty', color: 'text-red-500' },
  ]
  return statuses.map(s => ({ ...s, count: rooms.value.filter(r => r.status === s.status).length }))
})

function formatMoney(v) { return Number(v || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }
function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
function statusBadge(status) {
  const map = { pending:'bg-yellow-100 text-yellow-700', approved:'bg-green-100 text-green-800', checked_in:'bg-green-100 text-green-700', checked_out:'bg-gray-100 text-gray-600', cancelled:'bg-red-100 text-red-600' }
  return map[status] ?? 'bg-gray-100 text-gray-500'
}
function roomStatusBadge(status) {
  const map = { available:'bg-green-100 text-green-700', occupied:'bg-green-100 text-green-800', maintenance:'bg-yellow-100 text-yellow-700', dirty:'bg-red-100 text-red-600' }
  return map[status] ?? 'bg-gray-100 text-gray-500'
}
function hkStatusBadge(status) {
  const map = { pending:'bg-yellow-100 text-yellow-700', in_progress:'bg-green-100 text-green-800', completed:'bg-green-100 text-green-700' }
  return map[status] ?? 'bg-gray-100 text-gray-500'
}

watch(activeTab, (tab) => {
  if (tab === 'roomstatus') fetchRooms()
  if (tab === 'housekeeping') fetchHkTasks()
})

onMounted(() => {
  fetchReservations()
  fetchStats()
  fetchBookedDates()
  fetchRooms()
})
</script>
