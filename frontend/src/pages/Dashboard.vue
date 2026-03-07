<template>
  <div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
      <div class="p-4 lg:p-8 max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
          <div>
            <h1 class="text-2xl lg:text-4xl font-light text-gray-900 tracking-tight">Dashboard</h1>
            <p class="text-gray-600 font-light mt-1">Welcome back, {{ user?.name || 'Staff Member' }}</p>
          </div>
          <div class="sm:text-right">
            <p class="text-sm text-gray-600 font-light">Role: <span class="font-medium text-gray-900 capitalize">{{ user?.role?.replace('_',' ') || 'User' }}</span></p>
            <p class="text-xs text-gray-400 font-light mt-1">{{ new Date().toLocaleDateString('en-US', { weekday:'long', year:'numeric', month:'long', day:'numeric' }) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex items-center justify-center py-32">
      <div class="w-8 h-8 border-2 border-blue-700 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <div v-else class="p-4 lg:p-8 max-w-7xl mx-auto space-y-8">

      <!-- Summary Cards Row 1: Operations -->
      <div>
        <h2 class="text-xs font-medium uppercase tracking-widest text-gray-400 mb-4">Operations Overview</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-medium uppercase tracking-wide text-gray-400">Upcoming Events</span>
              <span class="w-7 h-7 rounded-full bg-purple-50 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </span>
            </div>
            <div class="text-2xl font-light text-gray-900">{{ summary.upcoming_events }}</div>
            <div class="text-xs text-gray-400 mt-1">Active / Pending</div>
          </div>

          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-medium uppercase tracking-wide text-gray-400">Total Staff</span>
              <span class="w-7 h-7 rounded-full bg-green-50 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
              </span>
            </div>
            <div class="text-2xl font-light text-gray-900">{{ summary.total_staff }}</div>
            <div class="text-xs text-gray-400 mt-1">Active employees</div>
          </div>

          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-medium uppercase tracking-wide text-gray-400">Available Rooms</span>
              <span class="w-7 h-7 rounded-full bg-green-50 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
              </span>
            </div>
            <div class="text-2xl font-light text-gray-900">{{ summary.available_rooms }}</div>
            <div class="text-xs text-gray-400 mt-1">Ready for check-in</div>
          </div>
        </div>
      </div>

      <!-- Summary Cards Row 2: Revenue -->
      <div>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xs font-medium uppercase tracking-widest text-gray-400">Revenue Summary</h2>
          <div v-if="lastUpdated" class="flex items-center gap-1.5 text-xs text-gray-400">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse inline-block"></span>
            Live · Updated {{ lastUpdated }}
          </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">

          <!-- Grand Total -->
          <div class="col-span-2 md:col-span-3 xl:col-span-5 bg-gradient-to-r from-blue-700 to-blue-500 rounded-xl shadow-sm p-4 flex items-center justify-between">
            <div>
              <div class="text-xs font-medium uppercase tracking-wide text-blue-100">Total Revenue (All Sources)</div>
              <div class="text-3xl font-light text-white mt-1">₱{{ formatCurrency(summary.grand_total_revenue) }}</div>
              <div class="text-xs text-blue-200 mt-1">Rooms · Café · Events · POS</div>
            </div>
            <span class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </span>
          </div>

          <!-- Room Revenue -->
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-medium uppercase tracking-wide text-gray-400">Room Revenue</span>
              <span class="w-7 h-7 rounded-full bg-green-50 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
              </span>
            </div>
            <div class="text-2xl font-light text-green-800">₱{{ formatCurrency(summary.total_room_revenue) }}</div>
            <div class="text-xs text-gray-400 mt-1">Completed reservations</div>
          </div>

          <!-- Café Revenue -->
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-medium uppercase tracking-wide text-gray-400">Café Revenue</span>
              <span class="w-7 h-7 rounded-full bg-orange-50 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
              </span>
            </div>
            <div class="text-2xl font-light text-orange-600">₱{{ formatCurrency(summary.total_cafe_revenue) }}</div>
            <div class="text-xs text-gray-400 mt-1">Restaurant & room service</div>
          </div>

          <!-- Event Revenue -->
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-medium uppercase tracking-wide text-gray-400">Event Revenue</span>
              <span class="w-7 h-7 rounded-full bg-purple-50 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
              </span>
            </div>
            <div class="text-2xl font-light text-purple-600">₱{{ formatCurrency(summary.total_event_revenue) }}</div>
            <div class="text-xs text-gray-400 mt-1">Confirmed &amp; completed</div>
          </div>

          <!-- POS Revenue -->
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-medium uppercase tracking-wide text-gray-400">POS Revenue</span>
              <span class="w-7 h-7 rounded-full bg-green-50 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              </span>
            </div>
            <div class="text-2xl font-light text-green-700">₱{{ formatCurrency(summary.total_pos_revenue) }}</div>
            <div class="text-xs text-gray-400 mt-1">Finalized transactions</div>
          </div>

        </div>
      </div>

      <!-- Charts Row 1: Revenue Trends (Daily / Weekly / Monthly) -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
          <div>
            <h2 class="text-xl font-light text-gray-900">Revenue Trends by Category</h2>
            <p class="text-sm text-gray-400 font-light mt-1">Breakdown — Rooms, Café, Events, POS</p>
          </div>
          <!-- Period tabs -->
          <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
            <button
              v-for="p in periods" :key="p.value"
              @click="activePeriod = p.value"
              :class="activePeriod === p.value
                ? 'bg-white text-gray-900 shadow-sm'
                : 'text-gray-500 hover:text-gray-700'"
              class="px-3 py-1 text-xs font-medium rounded-md transition"
            >{{ p.label }}</button>
          </div>
        </div>
        <!-- Legend -->
        <div class="flex flex-wrap gap-4 text-xs mb-4">
          <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-700 inline-block"></span>Rooms</span>
          <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-orange-500 inline-block"></span>Café &amp; POS</span>
          <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span>Events</span>
        </div>
        <div v-if="activeChartData" class="h-72">
          <Line :data="activeChartData" :options="trendLineOptions" />
        </div>
        <div v-else class="h-72 flex items-center justify-center text-gray-400 font-light">
          No revenue data available for this period yet.
        </div>
      </div>

      <!-- Charts Row 2: Popularity + Predictive -->
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">

        <!-- Room Popularity Analysis -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 lg:p-8">
          <div class="mb-6">
            <h2 class="text-xl font-light text-gray-900">Popularity Analysis</h2>
            <p class="text-sm text-gray-400 font-light mt-1">Room type demand from reservations</p>
          </div>
          <div v-if="popularityChartData" class="h-64">
            <Doughnut :data="popularityChartData" :options="doughnutOptions" />
          </div>
          <div v-else class="h-64 flex items-center justify-center text-gray-400 font-light">
            No reservation data yet.
          </div>
        </div>

        <!-- Event Popularity Analysis -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 lg:p-8">
          <div class="mb-6">
            <h2 class="text-xl font-light text-gray-900">Event Popularity</h2>
            <p class="text-sm text-gray-400 font-light mt-1">Distribution of events by type</p>
          </div>
          <div v-if="eventTypeChartData" class="h-64">
            <Doughnut :data="eventTypeChartData" :options="doughnutOptions" />
          </div>
          <div v-else class="h-64 flex items-center justify-center text-gray-400 font-light">
            No event data yet.
          </div>
        </div>

        <!-- Predictive Analysis -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 lg:p-8">
          <div class="mb-6">
            <h2 class="text-xl font-light text-gray-900">Predictive Analysis</h2>
            <p class="text-sm text-gray-400 font-light mt-1">Next month revenue forecast (linear trend)</p>
          </div>

          <!-- Trend indicator -->
          <div class="flex items-center gap-3 mb-6 p-4 rounded-lg" :class="trendBg">
            <span class="text-2xl">{{ trendIcon }}</span>
            <div>
              <div class="text-sm font-medium" :class="trendColor">Revenue trend is {{ prediction.trend }}</div>
              <div class="text-xs text-gray-500">Based on last 6 months — rooms, café, events &amp; POS</div>
            </div>
          </div>

          <div v-if="predictionChartData" class="h-48">
            <Bar :data="predictionChartData" :options="barOptions" />
          </div>
          <div v-else class="h-48 flex items-center justify-center text-gray-400 font-light">
            Insufficient data for prediction (need 2+ months).
          </div>
        </div>
      </div>

      <!-- Café Menu Popularity -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 lg:p-8">
        <div class="mb-6">
          <h2 class="text-xl font-light text-gray-900">Café — Top Menu Items</h2>
          <p class="text-sm text-gray-400 font-light mt-1">Most ordered items from restaurant & room service</p>
        </div>
        <div v-if="cafeChartData" class="h-64">
          <Bar :data="cafeChartData" :options="cafeBarOptions" />
        </div>
        <div v-else class="h-64 flex items-center justify-center text-gray-400 font-light">
          No café order data yet.
        </div>
      </div>

    </div>
  </div>

  <!-- ── Floating ? User Manual Button ── -->
  <button
    @click="showUserManual = !showUserManual"
    class="fixed bottom-6 right-4 z-50 w-11 h-11 bg-green-700 hover:bg-green-800 text-white rounded-full shadow-xl flex items-center justify-center font-bold text-xl leading-none transition-all duration-200 hover:scale-110 select-none"
    title="Staff User Manual"
  >?</button>

  <!-- Modal Popup -->
  <Transition name="um-modal">
    <div
      v-if="showUserManual"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
      @click.self="showUserManual = false"
    >
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
      <!-- Modal Header -->
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0 bg-white">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332-.477-4.5-1.253"/>
            </svg>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-900">Staff User Manual</p>
            <p class="text-xs text-gray-400 font-light">Guide for: <span class="capitalize font-medium text-amber-600">{{ (user?.role || '').replace('_', ' ') }}</span></p>
          </div>
        </div>
        <button @click="showUserManual = false" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <!-- Scrollable Content -->
      <div class="flex-1 overflow-y-auto">

          <!-- Role Badge + Intro -->
          <div class="px-6 pt-6 pb-2">
            <div class="flex flex-wrap items-center gap-3 mb-4">
              <span
                class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider"
                :class="{
                  'bg-green-700 text-white':   ['admin'].includes(user?.role),
                  'bg-indigo-600 text-white':  ['manager'].includes(user?.role),
                  'bg-orange-500 text-white':  ['chef'].includes(user?.role),
                  'bg-teal-600 text-white':    ['housekeeping'].includes(user?.role),
                  'bg-gray-700 text-white':    ['security'].includes(user?.role),
                  'bg-purple-600 text-white':  ['front_desk'].includes(user?.role),
                  'bg-gray-400 text-white':    !['admin','manager','chef','housekeeping','security','front_desk'].includes(user?.role),
                }"
              >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ (user?.role || 'staff').replace('_',' ') }}
              </span>
              <p class="text-sm text-gray-500 font-light">
                This guide explains how to use the system based on your assigned role.
              </p>
            </div>
          </div>

          <!-- ─────────────────────────────────────────────────── -->
          <!-- ADMIN / MANAGER MANUAL                              -->
          <!-- ─────────────────────────────────────────────────── -->
          <div v-if="['admin','manager'].includes(user?.role)" class="px-6 pb-8 space-y-8">

            <!-- Phase A -->
            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-green-700 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">A</div>
                <h3 class="text-sm font-semibold text-green-800 tracking-wide">Dashboard Overview</h3>
                <div class="flex-1 h-px bg-green-100"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex gap-3 bg-green-50 rounded-xl p-4">
                  <div class="w-8 h-8 rounded-lg bg-green-700 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">1</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">Log In &amp; Land on Dashboard</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">After logging in, you are brought directly to the Dashboard. It displays Revenue summaries, live charts, and an Operations Overview showing upcoming events, staff count, and available rooms.</p>
                  </div>
                </div>
                <div class="flex gap-3 bg-green-50 rounded-xl p-4">
                  <div class="w-8 h-8 rounded-lg bg-green-700 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">2</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">Use the Sidebar to Navigate</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">Click any item in the left sidebar to go to a section such as Reservations, Rooms, Staff, POS, or Reports. On mobile, tap the menu icon at the top to open the sidebar.</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Phase B -->
            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">B</div>
                <h3 class="text-sm font-semibold text-indigo-700 tracking-wide">Approving Guest Reservations</h3>
                <div class="flex-1 h-px bg-indigo-100"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="(step, i) in [
                  { n:1, t:'Open Pending Approvals', d:'Go to Reservations → Pending Approvals in the sidebar. This lists all reservations submitted by guests that are waiting for review.' },
                  { n:2, t:'Review the Request', d:'Click on a reservation to see the guest details, chosen room or event, dates, uploaded ID, and any special requests.' },
                  { n:3, t:'Approve or Reject', d:'Click Approve to confirm the booking. The guest will be notified. Click Reject and provide a reason if the booking cannot be accommodated.' },
                  { n:4, t:'View Reservation History', d:'Go to Reservations → History to see all past and active bookings. You can filter by status: Pending, Confirmed, Checked-In, Checked-Out.' },
                  { n:5, t:'Create a Manual Reservation', d:'For walk-in or phone-in guests, go to Reservations → New Reservation and fill in the guest details, select a room, and set the dates.' },
                ]" :key="i" class="flex gap-3 border border-indigo-100 rounded-xl p-4 bg-indigo-50">
                  <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ step.n }}</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">{{ step.t }}</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">{{ step.d }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Phase C -->
            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-green-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">C</div>
                <h3 class="text-sm font-semibold text-green-700 tracking-wide">Room &amp; Event Management</h3>
                <div class="flex-1 h-px bg-green-100"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="(step, i) in [
                  { n:1, t:'Check Room Availability', d:'Go to Rooms. Each room is color-coded: Green = Available, Orange = Occupied, Blue = Being Cleaned. Use this to manage check-ins and assign rooms.' },
                  { n:2, t:'Add or Edit Rooms', d:'Go to Rooms → Edit Rooms to add new rooms, update pricing, change room types, or upload room photos.' },
                  { n:3, t:'Archive Inactive Rooms', d:'To temporarily remove a room from bookings (e.g., for renovation), go to Rooms → Archived and archive it.' },
                  { n:4, t:'Manage Event Packages', d:'Go to Events → Edit Packages to create or update event packages (inclusions, pricing, capacity). Guests can see these on the homepage.' },
                  { n:5, t:'View Scheduled Events', d:'Go to Events to see all upcoming events. You can update their status (Upcoming, Ongoing, Completed) and view guest details.' },
                ]" :key="i" class="flex gap-3 border border-green-100 rounded-xl p-4 bg-green-50">
                  <div class="w-8 h-8 rounded-lg bg-green-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ step.n }}</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">{{ step.t }}</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">{{ step.d }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Phase D -->
            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-amber-500 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">D</div>
                <h3 class="text-sm font-semibold text-amber-700 tracking-wide">Staff Management</h3>
                <div class="flex-1 h-px bg-amber-100"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="(step, i) in [
                  { n:1, t:'View All Staff', d:'Go to Staff to see a list of all employees, their roles, contact info, and status. You can add, edit, or archive staff from here.' },
                  { n:2, t:'Track Attendance', d:'Go to Staff → Attendance to log or review daily attendance. Attendance data is used when processing payroll.' },
                  { n:3, t:'Process Payroll', d:'Go to Staff → Payroll. Select the cut-off period and click Generate Payroll to compute salaries based on attendance records.' },
                  { n:4, t:'Approve Leave Requests', d:'Go to Staff → Leave Approvals to review submitted leave requests. Click Approve or Reject for each one.' },
                  { n:5, t:'View Staff Reports', d:'Go to Staff → Staff Reports to see individual performance summaries, attendance rates, and leave history per employee.' },
                ]" :key="i" class="flex gap-3 border border-amber-100 rounded-xl p-4 bg-amber-50">
                  <div class="w-8 h-8 rounded-lg bg-amber-500 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ step.n }}</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">{{ step.t }}</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">{{ step.d }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Phase E -->
            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-orange-500 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">E</div>
                <h3 class="text-sm font-semibold text-orange-700 tracking-wide">POS &amp; Restaurant</h3>
                <div class="flex-1 h-px bg-orange-100"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="(step, i) in [
                  { n:1, t:'Process a POS Transaction', d:'Go to POS. Select items from the menu grid, adjust quantities, then click Process Order. Choose the payment method (Cash, GCash) and confirm.' },
                  { n:2, t:'Manage the Café Menu', d:'Go to Restaurant to add, edit, or archive menu items. Set item names, categories, prices, and photos.' },
                  { n:3, t:'View Order History', d:'Go to POS → Order History to see all past transactions, filter by date or status, and view individual order details.' },
                ]" :key="i" class="flex gap-3 border border-orange-100 rounded-xl p-4 bg-orange-50">
                  <div class="w-8 h-8 rounded-lg bg-orange-500 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ step.n }}</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">{{ step.t }}</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">{{ step.d }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Phase F -->
            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-teal-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">F</div>
                <h3 class="text-sm font-semibold text-teal-700 tracking-wide">Housekeeping &amp; Inventory</h3>
                <div class="flex-1 h-px bg-teal-100"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-for="(step, i) in [
                  { n:1, t:'Assign Housekeeping Tasks', d:'Go to Housekeeping to view all pending cleaning tasks. Tasks are created automatically when a guest checks out. You can reassign tasks to specific housekeeping staff.' },
                  { n:2, t:'Monitor Inventory', d:'Go to Inventory to view current stock levels for all supplies. If an item is low, update the quantity or create a purchase note.' },
                  { n:3, t:'View Inventory Logs', d:'Each inventory update is logged. Review the log to track who made changes and when, ensuring accountability.' },
                ]" :key="i" class="flex gap-3 border border-teal-100 rounded-xl p-4 bg-teal-50">
                  <div class="w-8 h-8 rounded-lg bg-teal-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ step.n }}</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">{{ step.t }}</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">{{ step.d }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Phase G -->
            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-violet-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">G</div>
                <h3 class="text-sm font-semibold text-violet-700 tracking-wide">Billing &amp; Reports</h3>
                <div class="flex-1 h-px bg-violet-100"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="(step, i) in [
                  { n:1, t:'Process Guest Bills', d:'Go to Billing to view all active guest bills. Add charges (room, food, extras), apply discounts, and mark the bill as Paid once the guest settles.' },
                  { n:2, t:'Export Data &amp; Reports', d:'Go to Settings → Export Data to download reports for reservations, revenue, staff, or inventory as CSV files for accounting or analysis.' },
                  { n:3, t:'View Full Reports', d:'The Dashboard charts (Revenue Trends, Popularity Analysis, Predictive Analysis) update in real time every 30 seconds. Switch between Daily, Weekly, and Monthly views using the tabs.' },
                ]" :key="i" class="flex gap-3 border border-violet-100 rounded-xl p-4 bg-violet-50">
                  <div class="w-8 h-8 rounded-lg bg-violet-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ step.n }}</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">{{ step.t }}</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed" v-html="step.d"></p>
                  </div>
                </div>
              </div>
            </div>

          </div>
          <!-- END ADMIN/MANAGER -->

          <!-- ─────────────────────────────────────────────────── -->
          <!-- CHEF MANUAL                                          -->
          <!-- ─────────────────────────────────────────────────── -->
          <div v-else-if="user?.role === 'chef'" class="px-6 pb-8 space-y-8">

            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-orange-500 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">A</div>
                <h3 class="text-sm font-semibold text-orange-700 tracking-wide">Your Chef Dashboard</h3>
                <div class="flex-1 h-px bg-orange-100"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-for="(step, i) in [
                  { n:1, t:'Access Your Dashboard', d:'After logging in, you are taken directly to the Chef Dashboard (Chef → Orders in the sidebar). This is your main workspace.' },
                  { n:2, t:'Understand the Order List', d:'All incoming food and beverage orders appear here. Each order shows the order number, items, quantity, any special notes from the guest, and the current status.' },
                ]" :key="i" class="flex gap-3 border border-orange-100 rounded-xl p-4 bg-orange-50">
                  <div class="w-8 h-8 rounded-lg bg-orange-500 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ step.n }}</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">{{ step.t }}</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">{{ step.d }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-red-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">B</div>
                <h3 class="text-sm font-semibold text-red-700 tracking-wide">Processing Orders</h3>
                <div class="flex-1 h-px bg-red-100"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-for="(step, i) in [
                  { n:1, t:'New Order Arrives', d:'A new order will appear at the top of your list with a &quot;New&quot; status badge. Review the items and quantity carefully.' },
                  { n:2, t:'Start Preparing', d:'Click the Start Cooking (or Update Status) button to change the status to Preparing. This lets the front desk know you have acknowledged the order.' },
                  { n:3, t:'Check Special Notes', d:'Always read the order notes. Guests may have dietary restrictions, special requests, or preferred cooking methods indicated there.' },
                  { n:4, t:'Mark as Ready', d:'Once the food is prepared and ready for service, click Mark as Ready. The front desk will be notified and can arrange delivery or pickup.' },
                ]" :key="i" class="flex gap-3 border border-red-100 rounded-xl p-4 bg-red-50">
                  <div class="w-8 h-8 rounded-lg bg-red-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ step.n }}</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">{{ step.t }}</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed" v-html="step.d"></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tip banner -->
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-3">
              <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
              <p class="text-xs text-amber-800 font-light leading-relaxed"><strong class="font-semibold">Pro Tip:</strong> Keep the Chef Dashboard open throughout your shift. Refresh the page if you notice the orders list seems outdated. Respond promptly to new orders to keep service times fast.</p>
            </div>

          </div>
          <!-- END CHEF -->

          <!-- ─────────────────────────────────────────────────── -->
          <!-- HOUSEKEEPING MANUAL                                  -->
          <!-- ─────────────────────────────────────────────────── -->
          <div v-else-if="user?.role === 'housekeeping'" class="px-6 pb-8 space-y-8">

            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-teal-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">A</div>
                <h3 class="text-sm font-semibold text-teal-700 tracking-wide">Accessing Your Tasks</h3>
                <div class="flex-1 h-px bg-teal-100"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-for="(step, i) in [
                  { n:1, t:'Go to Your Housekeeping Page', d:'After logging in, you are taken directly to the Housekeeping page. This is your main workspace. It lists all rooms assigned to you that require attention.' },
                  { n:2, t:'Read the Task Details', d:'Each task card shows: the room number, type of cleaning required (regular, deep clean, turnover), priority level (Normal or Urgent), and any notes from the manager.' },
                ]" :key="i" class="flex gap-3 border border-teal-100 rounded-xl p-4 bg-teal-50">
                  <div class="w-8 h-8 rounded-lg bg-teal-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ step.n }}</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">{{ step.t }}</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">{{ step.d }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-green-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">B</div>
                <h3 class="text-sm font-semibold text-green-700 tracking-wide">Completing Cleaning Tasks</h3>
                <div class="flex-1 h-px bg-green-100"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-for="(step, i) in [
                  { n:1, t:'Pick Your First Task', d:'Start with Urgent tasks first, then Normal. Tackle rooms in order of check-in time so guests are not kept waiting.' },
                  { n:2, t:'Mark as In Progress', d:'Before you start cleaning, click Start Task to update the status to In Progress. This notifies the manager that the room is being attended to.' },
                  { n:3, t:'Report Any Issues', d:'If you find damage, missing items, or anything unusual in the room, use the Notes field on the task to write a brief description before finishing.' },
                  { n:4, t:'Mark as Done', d:'Once the room is clean and ready, click Mark as Done. The system automatically updates the room status to Available so it can be assigned to new guests.' },
                ]" :key="i" class="flex gap-3 border border-green-100 rounded-xl p-4 bg-green-50">
                  <div class="w-8 h-8 rounded-lg bg-green-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ step.n }}</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">{{ step.t }}</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">{{ step.d }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Status legend -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 rounded-xl p-3">
                <span class="w-3 h-3 rounded-full bg-yellow-400 flex-shrink-0"></span>
                <div>
                  <p class="text-xs font-semibold text-yellow-800">Pending</p>
                  <p class="text-xs text-yellow-700 font-light">Task not yet started.</p>
                </div>
              </div>
              <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-3">
                <span class="w-3 h-3 rounded-full bg-green-600 flex-shrink-0"></span>
                <div>
                  <p class="text-xs font-semibold text-amber-700">In Progress</p>
                  <p class="text-xs text-green-800 font-light">Cleaning currently ongoing.</p>
                </div>
              </div>
              <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-3">
                <span class="w-3 h-3 rounded-full bg-green-500 flex-shrink-0"></span>
                <div>
                  <p class="text-xs font-semibold text-green-800">Done</p>
                  <p class="text-xs text-green-700 font-light">Room is clean and available.</p>
                </div>
              </div>
            </div>

          </div>
          <!-- END HOUSEKEEPING -->

          <!-- ─────────────────────────────────────────────────── -->
          <!-- SECURITY MANUAL                                      -->
          <!-- ─────────────────────────────────────────────────── -->
          <div v-else-if="user?.role === 'security'" class="px-6 pb-8 space-y-8">

            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-gray-700 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">A</div>
                <h3 class="text-sm font-semibold text-gray-700 tracking-wide">Monitoring Rooms</h3>
                <div class="flex-1 h-px bg-gray-200"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="(step, i) in [
                  { n:1, t:'Access the Rooms Page', d:'After logging in, you are taken directly to the Rooms page. This displays all rooms in the hotel along with their real-time status.' },
                  { n:2, t:'Understand Room Statuses', d:'Available (green) — room is empty and ready. Occupied (orange) — a guest is currently checked in. Being Cleaned (blue) — housekeeping is working on the room.' },
                  { n:3, t:'Identify Concerns', d:'If you notice a room that shows as Available but appears to have unauthorized occupants, or any other security concern, report it immediately to the manager or front desk.' },
                  { n:4, t:'Check Your Profile', d:'Go to Profile (top-right menu) to view your personal information and update your contact details or password if needed.' },
                ]" :key="i" class="flex gap-3 border border-gray-200 rounded-xl p-4 bg-gray-50">
                  <div class="w-8 h-8 rounded-lg bg-gray-700 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ step.n }}</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">{{ step.t }}</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">{{ step.d }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex gap-3">
              <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <p class="text-xs text-amber-700 font-light leading-relaxed"><strong class="font-semibold">Note:</strong> Your system access is limited to the Rooms page, Dashboard, and your Profile. If you need access to other areas for operational reasons, contact your manager to request a role adjustment.</p>
            </div>

          </div>
          <!-- END SECURITY -->

          <!-- ─────────────────────────────────────────────────── -->
          <!-- FRONT DESK MANUAL                                    -->
          <!-- ─────────────────────────────────────────────────── -->
          <div v-else-if="user?.role === 'front_desk'" class="px-6 pb-8 space-y-8">

            <!-- Phase A -->
            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-purple-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">A</div>
                <h3 class="text-sm font-semibold text-purple-700 tracking-wide">Your Workspace Overview</h3>
                <div class="flex-1 h-px bg-purple-100"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-for="(step, i) in [
                  { n:1, t:'Log In &amp; Navigate', d:'After logging in, you are taken to the Reservations page. Use the sidebar to navigate to: Reservations, Rooms, POS, and Inventory &mdash; the four main areas you can access.' },
                  { n:2, t:'Dashboard at a Glance', d:'The Dashboard shows operational stats: upcoming events, available rooms, and staff count. You can view it anytime but cannot edit system settings from there.' },
                ]" :key="i" class="flex gap-3 border border-purple-100 rounded-xl p-4 bg-purple-50">
                  <div class="w-8 h-8 rounded-lg bg-purple-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ step.n }}</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1" v-html="step.t"></p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed" v-html="step.d"></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Phase B -->
            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">B</div>
                <h3 class="text-sm font-semibold text-indigo-700 tracking-wide">Managing Reservations</h3>
                <div class="flex-1 h-px bg-indigo-100"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="(step, i) in [
                  { n:1, t:'Review Pending Bookings', d:'Go to Reservations → Pending Approvals. This lists all online bookings submitted by guests from the homepage that are waiting for your review.' },
                  { n:2, t:'Approve or Reject', d:'Click Approve to confirm a booking. The guest is notified. Click Reject and select a reason if the booking cannot be honored (e.g., room unavailable, ID issue).' },
                  { n:3, t:'Create a Walk-In Reservation', d:'For guests who arrive in person or call by phone, go to Reservations → New Reservation and manually fill in their details, chosen room, and dates.' },
                  { n:4, t:'View Reservation History', d:'Go to Reservations → History to track all bookings by their status: Pending, Confirmed, Checked-In, Checked-Out, or Cancelled.' },
                ]" :key="i" class="flex gap-3 border border-indigo-100 rounded-xl p-4 bg-indigo-50">
                  <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ step.n }}</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">{{ step.t }}</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">{{ step.d }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Phase C -->
            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-green-700 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">C</div>
                <h3 class="text-sm font-semibold text-green-800 tracking-wide">Room Monitoring</h3>
                <div class="flex-1 h-px bg-green-100"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-for="(step, i) in [
                  { n:1, t:'Check Room Availability', d:'Go to Rooms to see all rooms and their current status. Use this to guide walk-in guests to available rooms during check-in.' },
                  { n:2, t:'Room Status Reference', d:'Green = Available (can receive guests). Orange = Occupied (guest checked in). Blue = Being Cleaned (not yet ready). Only green rooms can be assigned for new check-ins.' },
                ]" :key="i" class="flex gap-3 border border-green-100 rounded-xl p-4 bg-green-50">
                  <div class="w-8 h-8 rounded-lg bg-green-700 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ step.n }}</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">{{ step.t }}</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">{{ step.d }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Phase D -->
            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-orange-500 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">D</div>
                <h3 class="text-sm font-semibold text-orange-700 tracking-wide">POS &mdash; Processing Orders</h3>
                <div class="flex-1 h-px bg-orange-100"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-for="(step, i) in [
                  { n:1, t:'Open POS', d:'Go to POS in the sidebar. The screen shows the full menu grid. Click on any item to add it to the order list on the right.' },
                  { n:2, t:'Adjust Quantities', d:'In the order list, use the + and &minus; buttons to change the quantity of each item. Items can also be removed from the order before processing.' },
                  { n:3, t:'Process the Order', d:'Click Process Order. A confirmation window will show the order total. Select the payment method (Cash or GCash) and confirm the transaction.' },
                  { n:4, t:'Order Forwarded to Chef', d:'Once processed, the order is automatically sent to the Chef Dashboard. You do not need to manually notify the kitchen.' },
                ]" :key="i" class="flex gap-3 border border-orange-100 rounded-xl p-4 bg-orange-50">
                  <div class="w-8 h-8 rounded-lg bg-orange-500 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ step.n }}</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">{{ step.t }}</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed" v-html="step.d"></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Phase E -->
            <div>
              <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-full bg-teal-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">E</div>
                <h3 class="text-sm font-semibold text-teal-700 tracking-wide">Inventory Monitoring</h3>
                <div class="flex-1 h-px bg-teal-100"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-for="(step, i) in [
                  { n:1, t:'View Stock Levels', d:'Go to Inventory to see all supplies and their current quantities. Items highlighted in red are below the minimum stock level and need restocking.' },
                  { n:2, t:'Report Low Stock', d:'If you notice an item running critically low, inform the manager immediately so a purchase order can be placed. You can update stock quantities if you have received new supplies.' },
                ]" :key="i" class="flex gap-3 border border-teal-100 rounded-xl p-4 bg-teal-50">
                  <div class="w-8 h-8 rounded-lg bg-teal-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">{{ step.n }}</div>
                  <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">{{ step.t }}</p>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">{{ step.d }}</p>
                  </div>
                </div>
              </div>
            </div>

          </div>
          <!-- END FRONT DESK -->

          <!-- Fallback for unknown roles -->
          <div v-else class="px-6 pb-8">
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 flex gap-4">
              <svg class="w-8 h-8 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <div>
                <p class="text-sm font-semibold text-gray-700 mb-1">No specific guide available for your role.</p>
                <p class="text-xs text-gray-500 font-light leading-relaxed">Please contact your manager or system administrator for guidance on how to use the system.</p>
              </div>
            </div>
          </div>

          <!-- Universal: Profile & Password -->
          <div class="mx-6 mb-8 bg-gray-900 rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div class="flex-1">
              <p class="text-sm font-semibold text-white mb-1">All Roles &mdash; Managing Your Profile</p>
              <p class="text-xs text-gray-300 font-light leading-relaxed">
                Every staff member can access their own <strong class="text-white">Profile</strong> page from the user menu at the top right of the screen. There you can update your name, contact number, upload a profile photo, and change your password. Keep your information up-to-date so the system reflects accurate staff records.
              </p>
            </div>
          </div>

      </div><!-- end scrollable content -->
    </div><!-- end modal inner -->
    </div><!-- end modal wrapper -->
  </Transition><!-- end um-modal -->

</template>

<style scoped>
/* Modal popup */
.um-modal-enter-active, .um-modal-leave-active { transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.4,0,0.2,1); }
.um-modal-enter-from, .um-modal-leave-to       { opacity: 0; transform: scale(0.95); }
</style>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'
import {
  Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement,
  BarElement, ArcElement, Title, Tooltip, Legend, Filler
} from 'chart.js'
import { Line, Bar, Doughnut } from 'vue-chartjs'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, ArcElement, Title, Tooltip, Legend, Filler)

const authStore = useAuthStore()
const loading = ref(true)
const showUserManual = ref(false)
const lastUpdated = ref(null)
const summary = ref({
  upcoming_events: 0, total_staff: 0, available_rooms: 0,
  total_room_revenue: 0, total_cafe_revenue: 0, total_event_revenue: 0,
  total_pos_revenue: 0, grand_total_revenue: 0
})
const prediction = ref({ next_month: 0, trend: 'stable', projected_room: 0, projected_cafe: 0, projected_event: 0, projected_pos: 0 })

const user = computed(() => authStore.user)
const canAccess = (roles) => user.value && roles.includes(user.value.role)

const formatCurrency = (val) => {
  if (!val) return '0.00'
  return Number(val).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

// Period tab state
const periods = [
  { label: 'Daily',   value: 'daily' },
  { label: 'Weekly',  value: 'weekly' },
  { label: 'Monthly', value: 'monthly' },
]
const activePeriod = ref('monthly')

// Chart data refs
const dailyChartData   = ref(null)
const weeklyChartData  = ref(null)
const revenueChartData = ref(null)   // monthly
const popularityChartData = ref(null)
const predictionChartData = ref(null)
const cafeChartData = ref(null)
const eventTypeChartData = ref(null)

const activeChartData = computed(() => {
  if (activePeriod.value === 'daily')   return dailyChartData.value
  if (activePeriod.value === 'weekly')  return weeklyChartData.value
  return revenueChartData.value
})

// Trend computed
const trendIcon = computed(() => prediction.value.trend === 'up' ? '📈' : prediction.value.trend === 'down' ? '📉' : '➡️')
const trendColor = computed(() => prediction.value.trend === 'up' ? 'text-green-700' : prediction.value.trend === 'down' ? 'text-red-700' : 'text-gray-700')
const trendBg = computed(() => prediction.value.trend === 'up' ? 'bg-green-50' : prediction.value.trend === 'down' ? 'bg-red-50' : 'bg-gray-50')

const doughnutOptions = {
  responsive: true, maintainAspectRatio: false,
  plugins: {
    legend: { position: 'bottom', labels: { font: { size: 12 }, padding: 16 } },
    tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} reservations` } }
  },
  cutout: '60%'
}

const barOptions = {
  responsive: true, maintainAspectRatio: false,
  plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ' ₱' + Number(ctx.raw).toLocaleString() } } },
  scales: {
    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
    y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 11 }, callback: v => '₱' + Number(v).toLocaleString() } }
  }
}

const cafeBarOptions = {
  responsive: true, maintainAspectRatio: false, indexAxis: 'y',
  plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.raw} orders` } } },
  scales: {
    x: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 11 } } },
    y: { grid: { display: false }, ticks: { font: { size: 11 } } }
  }
}

const COLORS = {
  room:    { bg: 'rgba(29,78,216,0.12)',  border: 'rgba(29,78,216,1)' },
  cafe:    { bg: 'rgba(249,115,22,0.12)', border: 'rgba(249,115,22,1)' },
  event:   { bg: 'rgba(139,92,246,0.12)', border: 'rgba(139,92,246,1)' },
  pos:     { bg: 'rgba(16,185,129,0.12)', border: 'rgba(16,185,129,1)' },
}

// Revenue trends line chart options (all periods)
const trendLineOptions = {
  responsive: true, maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      mode: 'index', intersect: false,
      callbacks: { label: ctx => ` ${ctx.dataset.label}: ₱${Number(ctx.raw).toLocaleString()}` }
    }
  },
  scales: {
    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
    y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 11 }, callback: v => '₱' + Number(v).toLocaleString() } }
  },
  elements: { line: { tension: 0.4 }, point: { radius: 3, hoverRadius: 6 } }
}

// Helper: build 3-dataset line chart — POS and Café are merged into one series
const buildRevenueDatasets = (rows, labelKey) => ({
  labels: rows.map(r => r[labelKey]),
  datasets: [
    {
      label: 'Rooms',
      data: rows.map(r => parseFloat(r.room) || 0),
      backgroundColor: COLORS.room.bg, borderColor: COLORS.room.border,
      borderWidth: 2, fill: true, tension: 0.4, pointRadius: 3, pointHoverRadius: 6,
    },
    {
      label: 'Café & POS',
      data: rows.map(r => (parseFloat(r.cafe) || 0) + (parseFloat(r.pos) || 0)),
      backgroundColor: COLORS.cafe.bg, borderColor: COLORS.cafe.border,
      borderWidth: 2, fill: true, tension: 0.4, pointRadius: 3, pointHoverRadius: 6,
    },
    {
      label: 'Events',
      data: rows.map(r => parseFloat(r.event) || 0),
      backgroundColor: COLORS.event.bg, borderColor: COLORS.event.border,
      borderWidth: 2, fill: true, tension: 0.4, pointRadius: 3, pointHoverRadius: 6,
    },
  ]
})

let pollTimer = null

const loadAnalytics = async (silent = false) => {
  if (!silent) loading.value = true
  try {
    const res = await api.get('/admin/analytics')
    const data = res.data.data

    summary.value = {
      ...summary.value,
      ...data.summary,
    }
    prediction.value = data.prediction

    // Monthly Revenue Trends (line chart)
    if (data.revenue_trends?.length) {
      revenueChartData.value = buildRevenueDatasets(data.revenue_trends, 'month')
    }

    // Daily Revenue Trends (stacked bar, last 7 days)
    if (data.daily_trends?.length) {
      const fmt = day => new Date(day + 'T00:00:00').toLocaleDateString('en-PH', { weekday: 'short', month: 'short', day: 'numeric' })
      dailyChartData.value = buildRevenueDatasets(
        data.daily_trends.map(r => ({ ...r, label: fmt(r.day) })),
        'label'
      )
    }

    // Weekly Revenue Trends (stacked bar, last 8 weeks)
    if (data.weekly_trends?.length) {
      const fmtWeek = ws => {
        const d = new Date(ws + 'T00:00:00')
        return 'Wk of ' + d.toLocaleDateString('en-PH', { month: 'short', day: 'numeric' })
      }
      weeklyChartData.value = buildRevenueDatasets(
        data.weekly_trends.map(r => ({ ...r, label: fmtWeek(r.week_start) })),
        'label'
      )
    }

    // Room Popularity Doughnut
    if (data.popularity?.rooms?.length) {
      const palette = ['#1d4ed8','#166534','#8b5cf6','#06b6d4','#10b981','#ef4444']
      popularityChartData.value = {
        labels: data.popularity.rooms.map(r => r.type),
        datasets: [{
          data: data.popularity.rooms.map(r => parseInt(r.reservations) || 0),
          backgroundColor: palette,
          borderWidth: 0,
          hoverOffset: 8
        }]
      }
    }

    // Predictive Bar Chart — 4 sources
    if (data.prediction?.next_month > 0) {
      predictionChartData.value = {
        labels: ['Rooms', 'Café', 'Events', 'POS'],
        datasets: [{
          data: [
            data.prediction.projected_room,
            data.prediction.projected_cafe,
            data.prediction.projected_event,
            data.prediction.projected_pos ?? 0,
          ],
          backgroundColor: [COLORS.room.bg, COLORS.cafe.bg, COLORS.event.bg, COLORS.pos.bg],
          borderColor: [COLORS.room.border, COLORS.cafe.border, COLORS.event.border, COLORS.pos.border],
          borderWidth: 2,
          borderRadius: 6
        }]
      }
    }

    // Café Menu Popularity
    if (data.popularity?.cafe?.length) {
      cafeChartData.value = {
        labels: data.popularity.cafe.map(c => c.name),
        datasets: [{
          data: data.popularity.cafe.map(c => parseInt(c.total_ordered) || 0),
          backgroundColor: 'rgba(59,130,246,0.8)',
          borderColor: 'rgba(59,130,246,1)',
          borderWidth: 1,
          borderRadius: 4
        }]
      }
    }

    // Event Type Popularity
    if (data.popularity?.event_types?.length) {
      const eventPalette = ['#8b5cf6','#a78bfa','#c4b5fd','#7c3aed','#6d28d9','#5b21b6','#4c1d95','#ddd6fe']
      eventTypeChartData.value = {
        labels: data.popularity.event_types.map(e => e.event_type),
        datasets: [{
          data: data.popularity.event_types.map(e => parseInt(e.count) || 0),
          backgroundColor: eventPalette,
          borderWidth: 0,
          hoverOffset: 8
        }]
      }
    }

    // Update live timestamp
    lastUpdated.value = new Date().toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', second: '2-digit' })

  } catch (err) {
    console.error('Analytics load failed:', err)
  } finally {
    if (!silent) loading.value = false
  }
}

onMounted(() => {
  loadAnalytics()
  // Poll every 30 seconds for real-time updates
  pollTimer = setInterval(() => loadAnalytics(true), 30_000)
})

onUnmounted(() => {
  if (pollTimer) clearInterval(pollTimer)
})
</script>
