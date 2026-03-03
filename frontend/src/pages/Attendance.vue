<template>
  <div class="bg-gray-50 min-h-full">

    <!-- Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
      <div class="p-4 lg:p-8 max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl lg:text-4xl font-light text-gray-900 tracking-tight">Attendance</h1>
          <p class="text-gray-500 font-light mt-1">Daily staff presence record</p>
        </div>
        <!-- Date picker -->
        <div class="flex items-center gap-3">
          <button @click="changeDate(-1)" class="p-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-500 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
          <div class="relative">
            <input
              v-model="selectedDate"
              type="date"
              @change="loadAttendance"
              class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition cursor-pointer"
            />
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
          </div>
          <button @click="changeDate(1)" :disabled="isToday" class="p-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-500 disabled:opacity-40 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
          <button @click="goToday" v-if="!isToday" class="px-3 py-2 text-xs font-medium bg-green-50 text-green-800 border border-green-200 rounded-lg hover:bg-green-100 transition">
            Today
          </button>
          <!-- Scan QR button -->
          <button
            @click="openQrScanner"
            class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-light px-4 py-2.5 rounded-lg transition"
            title="Scan staff QR code"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
            Scan QR
          </button>
        </div>
      </div>
    </div>

    <div class="p-4 lg:p-8 max-w-7xl mx-auto space-y-6">

      <!-- Summary cards -->
      <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Total Staff</p>
              <p class="text-xl lg:text-3xl font-light text-gray-900 mt-1">{{ summary.total ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Present</p>
              <p class="text-xl lg:text-3xl font-light text-green-600 mt-1">{{ summary.present ?? 0 }}</p>
              <p class="text-xs text-gray-400 font-light mt-0.5">{{ presentRate }}% attendance rate</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
              <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Absent</p>
              <p class="text-xl lg:text-3xl font-light text-red-500 mt-1">{{ summary.absent ?? 0 }}</p>
              <p class="text-xs text-gray-400 font-light mt-0.5">{{ absentRate }}% absent rate</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center">
              <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- My Attendance Card (own clock-in/out) -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
          <div>
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 mb-1">My Attendance &mdash; Today</p>
            <div class="flex items-center gap-4">
              <div>
                <span class="text-xs text-gray-400 font-light">Clock In</span>
                <p class="text-sm font-medium text-gray-800">{{ myRecord?.clock_in_time ? formatTime(myRecord.clock_in_time) : '—' }}</p>
              </div>
              <div class="w-px h-8 bg-gray-100"></div>
              <div>
                <span class="text-xs text-gray-400 font-light">Clock Out</span>
                <p class="text-sm font-medium text-gray-800">{{ myRecord?.clock_out_time ? formatTime(myRecord.clock_out_time) : '—' }}</p>
              </div>
              <div class="w-px h-8 bg-gray-100"></div>
              <div>
                <span class="text-xs text-gray-400 font-light">Status</span>
                <p class="text-sm font-medium" :class="myRecord ? 'text-green-600' : 'text-gray-400'">
                  {{ myRecord ? (myRecord.clock_out_time ? 'Checked Out' : 'Clocked In') : 'Not Recorded' }}
                </p>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <button
              v-if="!myRecord"
              @click="openQrScanner('clock_in')"
              :disabled="myActionLoading"
              class="flex items-center gap-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 text-white text-sm font-light px-5 py-2.5 rounded-lg transition"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
              </svg>
              {{ myActionLoading ? 'Processing...' : 'Clock In' }}
            </button>
            <button
              v-else-if="myRecord && !myRecord.clock_out_time"
              @click="openQrScanner('clock_out')"
              :disabled="myActionLoading"
              class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 disabled:bg-gray-300 text-white text-sm font-light px-5 py-2.5 rounded-lg transition"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
              </svg>
              {{ myActionLoading ? 'Processing...' : 'Clock Out' }}
            </button>
            <span v-else class="inline-flex items-center gap-1.5 text-sm text-green-600 font-light">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Shift complete
            </span>
          </div>
        </div>
      </div>

      <!-- Search + Filter Bar -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-wrap items-center gap-4">
        <div class="relative flex-1 min-w-48">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
          </svg>
          <input
            v-model="search"
            type="text"
            placeholder="Search by name or role..."
            class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition"
          />
        </div>
        <select
          v-model="statusFilter"
          class="border border-gray-200 rounded-lg px-3 py-2 text-sm font-light text-gray-700 focus:outline-none focus:border-blue-600 transition"
        >
          <option value="">All Status</option>
          <option value="present">Present</option>
          <option value="absent">Absent</option>
        </select>
        <span class="ml-auto text-xs text-gray-400 font-light">
          {{ filteredRecords.length }} {{ filteredRecords.length === 1 ? 'staff' : 'staff' }} shown
        </span>
      </div>

      <!-- Attendance Table -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div v-if="loading" class="flex items-center justify-center py-20">
          <div class="w-7 h-7 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        </div>

        <div v-else-if="filteredRecords.length === 0" class="flex flex-col items-center justify-center py-20 text-gray-400">
          <svg class="w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          <p class="text-sm font-light">No records found</p>
        </div>

        <div v-else class="overflow-x-auto -mx-px">

        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">#</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Staff</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Role</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Clock In</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Clock Out</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Hours</th>
              <th class="text-left px-6 py-3.5 text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="(rec, i) in filteredRecords"
              :key="rec.user_id"
              class="hover:bg-gray-50 transition-colors duration-100"
              :class="rec.status === 'present' ? '' : 'opacity-75'"
            >
              <td class="px-6 py-4 text-sm text-gray-400 font-light">{{ i + 1 }}</td>
              <!-- Staff -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-medium shrink-0"
                    :style="{ backgroundColor: avatarColor(rec.name) }"
                  >
                    {{ rec.name?.charAt(0)?.toUpperCase() }}
                  </div>
                  <span class="text-sm font-light text-gray-900">{{ rec.name }}</span>
                </div>
              </td>
              <!-- Role -->
              <td class="px-6 py-4">
                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium capitalize" :class="roleBadge(rec.role)">
                  {{ rec.role?.replace(/_/g, ' ') }}
                </span>
              </td>
              <!-- Status -->
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                  :class="rec.status === 'present'
                    ? 'bg-green-50 text-green-700'
                    : 'bg-red-50 text-red-600'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="rec.status === 'present' ? 'bg-green-500' : 'bg-red-400'"></span>
                  {{ rec.status === 'present' ? 'Present' : 'Absent' }}
                </span>
              </td>
              <!-- Clock In -->
              <td class="px-6 py-4">
                <span v-if="rec.clock_in_time" class="text-sm font-light text-gray-700">{{ formatTime(rec.clock_in_time) }}</span>
                <span v-else class="text-sm text-gray-300">&mdash;</span>
              </td>
              <!-- Clock Out -->
              <td class="px-6 py-4">
                <span v-if="rec.clock_out_time" class="text-sm font-light text-gray-700">{{ formatTime(rec.clock_out_time) }}</span>
                <span v-else-if="rec.clock_in_time" class="text-xs text-orange-400 font-light">Still in</span>
                <span v-else class="text-sm text-gray-300">&mdash;</span>
              </td>
              <!-- Hours -->
              <td class="px-6 py-4">
                <span v-if="rec.hours_worked !== null" class="text-sm font-light text-gray-700">{{ rec.hours_worked }}h</span>
                <span v-else class="text-sm text-gray-300">&mdash;</span>
              </td>
              <!-- Actions -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <!-- Mark Present -->
                  <button
                    v-if="rec.status === 'absent'"
                    @click="markPresent(rec)"
                    :disabled="actionLoading === rec.user_id"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 text-xs font-medium rounded-lg transition disabled:opacity-50"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ actionLoading === rec.user_id ? '...' : 'Mark In' }}
                  </button>
                  <!-- Mark Out -->
                  <button
                    v-if="rec.status === 'present' && !rec.clock_out_time"
                    @click="markOut(rec)"
                    :disabled="actionLoading === rec.user_id"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-orange-700 text-xs font-medium rounded-lg transition disabled:opacity-50"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                    </svg>
                    {{ actionLoading === rec.user_id ? '...' : 'Mark Out' }}
                  </button>
                  <!-- Complete -->
                  <span
                    v-if="rec.status === 'present' && rec.clock_out_time"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs text-green-600 font-light"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Complete
                  </span>
                </div>
              </td>
            </tr>
          </tbody>
        </table></div>
      </div>
    </div>

    <!-- Toast notification -->
    <transition name="slide-down">
      <div v-if="toast.show" class="fixed top-20 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg text-sm font-light"
        :class="toast.type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'">
        <svg v-if="toast.type === 'success'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <svg v-else class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{ toast.message }}
      </div>
    </transition>

    <!-- QR Scanner Modal -->
    <transition name="fade">
      <div v-if="showQrScanner" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" @click.self="closeQrScanner">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center">
          <!-- Title changes based on mode -->
          <div class="flex items-center justify-center gap-2 mb-1">
            <span v-if="qrScanMode === 'clock_in'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700">Clock In</span>
            <span v-else-if="qrScanMode === 'clock_out'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-orange-50 text-orange-700">Clock Out</span>
            <span v-else class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">Admin Scan</span>
          </div>
          <h2 class="text-xl font-light text-gray-900 mb-1">Scan Your QR Code</h2>
          <p class="text-sm text-gray-500 font-light mb-4">
            <template v-if="qrScanMode === 'clock_in'">Point the camera at your QR code to clock in</template>
            <template v-else-if="qrScanMode === 'clock_out'">Point the camera at your QR code to clock out</template>
            <template v-else>Point the camera at a staff member's QR code</template>
          </p>

          <!-- Camera feed -->
          <div class="relative rounded-xl overflow-hidden bg-gray-900 mb-4" style="height:260px">
            <video ref="qrVideo" class="w-full h-full object-cover" playsinline muted></video>
            <!-- Scan guide box (colour changes per mode) -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
              <div
                class="w-44 h-44 border-2 rounded-xl opacity-80"
                :class="qrScanMode === 'clock_in' ? 'border-green-400' : qrScanMode === 'clock_out' ? 'border-orange-400' : 'border-indigo-400'"
              ></div>
            </div>
            <!-- Corner markers -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
              <div class="relative w-44 h-44">
                <span class="absolute top-0 left-0 w-5 h-5 border-t-2 border-l-2 rounded-tl" :class="qrScanMode === 'clock_in' ? 'border-green-400' : qrScanMode === 'clock_out' ? 'border-orange-400' : 'border-indigo-400'"></span>
                <span class="absolute top-0 right-0 w-5 h-5 border-t-2 border-r-2 rounded-tr" :class="qrScanMode === 'clock_in' ? 'border-green-400' : qrScanMode === 'clock_out' ? 'border-orange-400' : 'border-indigo-400'"></span>
                <span class="absolute bottom-0 left-0 w-5 h-5 border-b-2 border-l-2 rounded-bl" :class="qrScanMode === 'clock_in' ? 'border-green-400' : qrScanMode === 'clock_out' ? 'border-orange-400' : 'border-indigo-400'"></span>
                <span class="absolute bottom-0 right-0 w-5 h-5 border-b-2 border-r-2 rounded-br" :class="qrScanMode === 'clock_in' ? 'border-green-400' : qrScanMode === 'clock_out' ? 'border-orange-400' : 'border-indigo-400'"></span>
              </div>
            </div>
            <!-- Scanning indicator -->
            <div v-if="qrScanning" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-2 bg-black/50 text-white text-xs px-3 py-1.5 rounded-full">
              <div class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              Scanning...
            </div>
          </div>

          <div v-if="qrScanError" class="mb-3 p-2.5 bg-red-50 border border-red-200 text-sm text-red-600 rounded-lg font-light">{{ qrScanError }}</div>
          <p class="text-xs text-gray-400 font-light mb-4">
            <template v-if="qrScanMode === 'clock_in' || qrScanMode === 'clock_out'">
              Scan your assigned QR code. The system will verify your identity before recording.
            </template>
            <template v-else>
              The system will automatically clock staff in or out upon successful scan.
            </template>
          </p>

          <button @click="closeQrScanner" class="w-full border border-gray-200 hover:bg-gray-50 text-gray-700 font-light py-2.5 rounded-lg transition text-sm">Cancel</button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, reactive, nextTick } from 'vue'
import jsQR from 'jsqr'
import api from '../services/api'

// -- State ----------------------------------------------
const today = new Date().toISOString().split('T')[0]
const selectedDate = ref(today)
const loading = ref(true)
const records = ref([])
const summary = ref({})
const search = ref('')
const statusFilter = ref('')
const actionLoading = ref(null)   // user_id being actioned
const myActionLoading = ref(false)
const myRecord = ref(null)

const toast = reactive({ show: false, message: '', type: 'success' })

// -- Computed -------------------------------------------
const isToday = computed(() => selectedDate.value === today)

const filteredRecords = computed(() => {
  const q = search.value.toLowerCase()
  return records.value.filter(r => {
    const matchSearch = !q || r.name?.toLowerCase().includes(q) || r.role?.includes(q)
    const matchStatus = !statusFilter.value || r.status === statusFilter.value
    return matchSearch && matchStatus
  })
})

const presentRate = computed(() => {
  if (!summary.value.total) return 0
  return Math.round((summary.value.present / summary.value.total) * 100)
})

const absentRate = computed(() => {
  if (!summary.value.total) return 0
  return Math.round((summary.value.absent / summary.value.total) * 100)
})

// -- Helpers --------------------------------------------
const formatTime = (dt) => {
  if (!dt) return '\u2014'
  return new Date(dt).toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', hour12: true })
}

const avatarColors = ['#b45309','#0369a1','#4f46e5','#059669','#dc2626','#9333ea','#0891b2']
const avatarColor = (name) => avatarColors[(name?.charCodeAt(0) ?? 0) % avatarColors.length]

const roleBadge = (role) => ({
  admin:        'bg-red-50 text-red-700',
  manager:      'bg-green-50 text-green-800',
  front_desk:   'bg-cyan-50 text-cyan-700',
  housekeeping: 'bg-green-50 text-green-700',
  chef:         'bg-orange-50 text-orange-700',
  accountant:   'bg-purple-50 text-purple-700',
  guest:        'bg-gray-100 text-gray-600',
}[role] ?? 'bg-gray-100 text-gray-600')

const showToast = (message, type = 'success') => {
  toast.message = message
  toast.type = type
  toast.show = true
  setTimeout(() => { toast.show = false }, 2800)
}

// -- Data loading ---------------------------------------
const loadAttendance = async () => {
  loading.value = true
  try {
    const res = await api.get(`/admin/attendance?date=${selectedDate.value}`)
    records.value = res.data.data.records ?? []
    summary.value = res.data.data.summary ?? {}

    // Update own record from table (match logged-in user by stored user data)
    const storedUser = JSON.parse(localStorage.getItem('user') || '{}')
    const mine = records.value.find(r => r.user_id == storedUser.id)
    myRecord.value = mine && mine.status === 'present'
      ? { clock_in_time: mine.clock_in_time, clock_out_time: mine.clock_out_time }
      : null
  } catch (e) {
    console.error('Failed to load attendance', e)
  } finally {
    loading.value = false
  }
}

// -- Date navigation ------------------------------------
const changeDate = (delta) => {
  const d = new Date(selectedDate.value)
  d.setDate(d.getDate() + delta)
  const iso = d.toISOString().split('T')[0]
  if (iso <= today) {
    selectedDate.value = iso
    loadAttendance()
  }
}

const goToday = () => {
  selectedDate.value = today
  loadAttendance()
}

// -- Own clock-in / clock-out ---------------------------
const selfClockIn = async () => {
  myActionLoading.value = true
  try {
    await api.post('/staff/attendance/clock-in')
    showToast('Clocked in successfully!')
    await loadAttendance()
  } catch (e) {
    showToast(e.response?.data?.message || 'Failed to clock in', 'error')
  } finally {
    myActionLoading.value = false
  }
}

const selfClockOut = async () => {
  myActionLoading.value = true
  try {
    await api.post('/staff/attendance/clock-out')
    showToast('Clocked out successfully!')
    await loadAttendance()
  } catch (e) {
    showToast(e.response?.data?.message || 'Failed to clock out', 'error')
  } finally {
    myActionLoading.value = false
  }
}

// -- Admin mark in / out --------------------------------
const markPresent = async (rec) => {
  actionLoading.value = rec.user_id
  try {
    await api.post('/admin/attendance/clock-in', {
      staff_id: rec.user_id,
      date: selectedDate.value,
    })
    showToast(`${rec.name} marked as present`)
    await loadAttendance()
  } catch (e) {
    showToast(e.response?.data?.message || 'Failed to mark present', 'error')
  } finally {
    actionLoading.value = null
  }
}

const markOut = async (rec) => {
  actionLoading.value = rec.user_id
  try {
    await api.put(`/admin/attendance/${rec.attendance_id}/clock-out`, {
      date: selectedDate.value,
    })
    showToast(`${rec.name} marked as clocked out`)
    await loadAttendance()
  } catch (e) {
    showToast(e.response?.data?.message || 'Failed to mark out', 'error')
  } finally {
    actionLoading.value = null
  }
}

// -- QR Scanner ----------------------------------------
// qrScanMode: 'clock_in' | 'clock_out' | 'admin'
const showQrScanner = ref(false)
const qrScanMode    = ref('admin')
const qrVideo       = ref(null)
const qrScanning    = ref(false)
const qrScanError   = ref('')
let   qrStream      = null
let   qrAnimFrame   = null
let   qrProcessing  = false

// mode: 'clock_in' | 'clock_out' | 'admin' (default)
const openQrScanner = async (mode = 'admin') => {
  qrScanMode.value  = mode
  qrScanError.value = ''
  showQrScanner.value = true
  await nextTick()
  try {
    qrStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
    if (qrVideo.value) {
      qrVideo.value.srcObject = qrStream
      await qrVideo.value.play()
      qrScanning.value = true
      scanFrame()
    }
  } catch (e) {
    qrScanError.value = 'Camera access denied. Please allow camera permissions.'
  }
}

const scanFrame = () => {
  const video = qrVideo.value
  if (!video || !showQrScanner.value) return
  if (video.readyState === video.HAVE_ENOUGH_DATA) {
    const canvas  = document.createElement('canvas')
    canvas.width  = video.videoWidth
    canvas.height = video.videoHeight
    const ctx = canvas.getContext('2d')
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height)
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height)
    const code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'dontInvert' })
    if (code && !qrProcessing) {
      qrProcessing = true
      handleQrResult(code.data)
      return
    }
  }
  qrAnimFrame = requestAnimationFrame(scanFrame)
}

const handleQrResult = async (token) => {
  qrScanning.value = false
  try {
    let res, msg

    if (qrScanMode.value === 'clock_in' || qrScanMode.value === 'clock_out') {
      // Staff self-scan: verify the token belongs to the logged-in user
      res = await api.post('/staff/attendance/qr-clock', {
        token,
        action: qrScanMode.value,
      })
      const d = res.data.data
      msg = d.action === 'clock_in'
        ? `Clocked in at ${d.clock_in}`
        : `Clocked out at ${d.clock_out}`
    } else {
      // Admin scan: auto clock-in or clock-out any staff
      res = await api.post('/attendance/qr-scan', { token })
      const d = res.data.data
      msg = d.action === 'clock_in'
        ? `${d.user_name} clocked in at ${d.clock_in}`
        : `${d.user_name} clocked out at ${d.clock_out}`
    }

    closeQrScanner()
    showToast(msg)
    await loadAttendance()
  } catch (e) {
    qrScanError.value = e.response?.data?.message || 'Invalid QR code'
    qrProcessing = false
    qrScanning.value = true
    qrAnimFrame = requestAnimationFrame(scanFrame)
  }
}

const closeQrScanner = () => {
  showQrScanner.value = false
  qrScanning.value    = false
  qrProcessing        = false
  if (qrAnimFrame) { cancelAnimationFrame(qrAnimFrame); qrAnimFrame = null }
  if (qrStream)    { qrStream.getTracks().forEach(t => t.stop()); qrStream = null }
}

onUnmounted(closeQrScanner)

onMounted(loadAttendance)
</script>

<style scoped>
.slide-down-enter-active, .slide-down-leave-active {
  transition: all 0.3s ease;
}
.slide-down-enter-from {
  opacity: 0;
  transform: translateX(-50%) translateY(-12px);
}
.slide-down-leave-to {
  opacity: 0;
  transform: translateX(-50%) translateY(-8px);
}
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
