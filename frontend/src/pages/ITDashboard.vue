<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">

    <!-- Success Toast -->
    <transition name="slide-down">
      <div v-if="successMessage" class="fixed top-4 right-4 z-50 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 max-w-md">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span class="text-sm font-medium">{{ successMessage }}</span>
        <button @click="successMessage = ''" class="ml-auto text-green-600 hover:text-green-800">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </transition>

    <!-- Error Toast -->
    <transition name="slide-down">
      <div v-if="errorMessage" class="fixed top-4 right-4 z-50 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 max-w-md">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm font-medium">{{ errorMessage }}</span>
        <button @click="errorMessage = ''" class="ml-auto text-red-600 hover:text-red-800">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </transition>

    <!-- Password Reset Modal -->
    <transition name="fade">
      <div v-if="showPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="closePasswordModal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
          <!-- Modal Header -->
          <div class="px-6 py-5 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900">Reset Password</h3>
                <p class="text-xs text-gray-600">IT Staff action — will be logged</p>
              </div>
            </div>
            <button @click="closePasswordModal" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          <!-- Modal Body -->
          <div class="px-6 py-5">
            <div class="flex items-center gap-3 mb-5 p-3 bg-gray-50 rounded-xl border border-gray-200">
              <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-sm font-semibold text-gray-600">
                {{ passwordResetStaff?.name?.charAt(0)?.toUpperCase() }}
              </div>
              <div>
                <p class="font-semibold text-gray-900">{{ passwordResetStaff?.name }}</p>
                <p class="text-xs text-gray-500">{{ passwordResetStaff?.email }}</p>
              </div>
            </div>
            <form @submit.prevent="submitPasswordReset" class="space-y-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">New Password</label>
                <input v-model="newPassword" type="password" required autocomplete="new-password"
                  class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                  placeholder="Min. 8 characters" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm Password</label>
                <input v-model="confirmPassword" type="password" required autocomplete="new-password"
                  class="w-full px-4 py-2.5 border-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                  :class="confirmPassword && newPassword !== confirmPassword ? 'border-red-300 bg-red-50' : 'border-gray-200'"
                  placeholder="Re-enter new password" />
                <p v-if="confirmPassword && newPassword !== confirmPassword" class="text-xs text-red-600 mt-1.5 flex items-center gap-1">
                  <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                  Passwords do not match
                </p>
                <p v-else-if="newPassword && newPassword.length < 8" class="text-xs text-red-600 mt-1.5">Password must be at least 8 characters</p>
              </div>
              <div class="flex gap-3 pt-2">
                <button type="submit"
                  :disabled="!newPassword || newPassword.length < 8 || newPassword !== confirmPassword || passwordResetting"
                  class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                  {{ passwordResetting ? 'Resetting...' : '🔑 Reset Password' }}
                </button>
                <button type="button" @click="closePasswordModal"
                  class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </transition>

    <!-- Notification Backdrop -->
    <div v-if="showNotifications" class="fixed inset-0 z-30" @click="showNotifications = false"></div>

    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
      <div class="px-4 lg:px-8 py-5 max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">IT Dashboard</h1>
              <p class="text-sm text-gray-500 mt-0.5">System monitoring • Staff management • Support requests</p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <!-- Notification Bell -->
            <div class="relative" style="z-index: 40">
              <button @click="toggleNotifications"
                :class="['relative p-2.5 rounded-xl border-2 transition-all shadow-sm', showNotifications ? 'bg-blue-50 border-blue-300 text-blue-700' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300']">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span v-if="unreadCount > 0"
                  class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow">
                  {{ unreadCount > 9 ? '9+' : unreadCount }}
                </span>
              </button>

              <!-- Notification Dropdown -->
              <transition name="dropdown">
                <div v-if="showNotifications"
                  class="absolute right-0 top-full mt-2 w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden"
                  style="z-index: 50">
                  <!-- Panel Header -->
                  <div class="px-5 py-3.5 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                      </svg>
                      <span class="font-bold text-gray-900">Notifications</span>
                      <span v-if="notifications.length > 0" class="text-[10px] px-2 py-0.5 bg-blue-600 text-white rounded-full font-bold">
                        {{ notifications.length }}
                      </span>
                    </div>
                    <button v-if="hasUnread" @click.stop="markAllRead"
                      class="text-xs text-blue-600 hover:text-blue-800 font-semibold hover:underline">
                      Mark all read
                    </button>
                  </div>

                  <!-- Notifications List -->
                  <div class="max-h-96 overflow-y-auto divide-y divide-gray-100">
                    <!-- Empty state -->
                    <div v-if="notifications.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-400">
                      <svg class="w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                      </svg>
                      <p class="text-sm font-semibold text-gray-500">All caught up!</p>
                      <p class="text-xs text-gray-400 mt-0.5">No notifications right now.</p>
                    </div>

                    <!-- Notification items -->
                    <button v-for="notif in notifications" :key="notif.id"
                      @click.stop="clickNotification(notif)"
                      :class="['w-full text-left px-5 py-4 hover:bg-gray-50 transition-colors flex items-start gap-3', !readNotifIds.includes(notif.id) ? 'bg-blue-50/50' : 'bg-white']">
                      <!-- Icon -->
                      <div :class="['w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 shadow-sm', notifIconBg(notif.type)]">
                        <svg v-if="notif.type === 'password_request'" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        <svg v-else-if="notif.type === 'system_alert'" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <svg v-else class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      </div>
                      <!-- Content -->
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-0.5">
                          <span class="text-xs font-bold text-gray-900 truncate">{{ notif.title }}</span>
                          <span v-if="!readNotifIds.includes(notif.id)" class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></span>
                        </div>
                        <p class="text-xs text-gray-600 leading-relaxed line-clamp-2">{{ notif.description }}</p>
                        <p class="text-[10px] text-gray-400 mt-1.5 flex items-center gap-1">
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>
                          {{ formatDateTime(notif.time) }}
                        </p>
                      </div>
                    </button>
                  </div>

                  <!-- Panel Footer -->
                  <div class="px-5 py-3 bg-gray-50 border-t border-gray-200 flex gap-3 justify-center flex-wrap">
                    <button @click.stop="scrollToSection('section-forgot-password'); showNotifications = false"
                      class="text-xs text-blue-600 hover:text-blue-800 font-semibold hover:underline">
                      Password Requests
                    </button>
                    <span class="text-gray-300">|</span>
                    <button @click.stop="scrollToSection('section-system-logs'); showNotifications = false"
                      class="text-xs text-blue-600 hover:text-blue-800 font-semibold hover:underline">
                      System Logs
                    </button>
                    <span class="text-gray-300">|</span>
                    <button @click.stop="scrollToSection('section-audit-logs'); showNotifications = false"
                      class="text-xs text-blue-600 hover:text-blue-800 font-semibold hover:underline">
                      Audit Logs
                    </button>
                    <span class="text-gray-300">|</span>
                    <button @click.stop="scrollToSection('section-reports'); showNotifications = false"
                      class="text-xs text-blue-600 hover:text-blue-800 font-semibold hover:underline">
                      Reports
                    </button>
                  </div>
                </div>
              </transition>
            </div>

            <div class="text-right mr-2 hidden sm:block">
              <p class="text-xs text-gray-500">Last updated</p>
              <p class="text-sm font-medium text-gray-700">{{ lastUpdated }}</p>
            </div>
            <button @click="loadAll" :disabled="loading"
              class="flex items-center gap-2 text-sm font-medium border-2 border-blue-200 text-blue-700 hover:bg-blue-50 px-4 py-2 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow">
              <svg :class="['w-4 h-4', loading && 'animate-spin']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              <span class="hidden sm:inline">{{ loading ? 'Refreshing...' : 'Refresh Data' }}</span>
              <span class="sm:hidden">Refresh</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading && !systemLogs.length" class="flex flex-col items-center justify-center py-32">
      <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mb-4"></div>
      <p class="text-gray-500 font-medium">Loading dashboard data...</p>
    </div>

    <div v-else class="p-4 lg:p-8 max-w-7xl mx-auto space-y-6">

      <!-- ══════════════════════════════════════════════════════════════════ -->
      <!-- STAT CARDS ROW                                                      -->
      <!-- ══════════════════════════════════════════════════════════════════ -->
      <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">

        <!-- Card 1: System Logs -->
        <button @click="scrollToSection('section-system-logs')"
          class="group bg-white rounded-2xl border border-blue-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-200 p-5 text-left overflow-hidden relative">
          <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-blue-100 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-2xl"></div>
          <div class="relative">
            <div class="flex items-center justify-between mb-3">
              <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <span class="text-xs font-bold px-2 py-1 bg-blue-100 text-blue-700 rounded-full">Logs</span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mb-1">{{ totalLogs.toLocaleString() }}</p>
            <p class="text-sm font-semibold text-gray-700">System Logs</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ activeUsers }} active users (24h)</p>
          </div>
        </button>

        <!-- Card 2: Audit Logs -->
        <button @click="scrollToSection('section-audit-logs')"
          class="group bg-white rounded-2xl border border-violet-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-200 p-5 text-left overflow-hidden relative">
          <div class="absolute inset-0 bg-gradient-to-br from-violet-50 to-purple-100 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-2xl"></div>
          <div class="relative">
            <div class="flex items-center justify-between mb-3">
              <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
              </div>
              <span class="text-xs font-bold px-2 py-1 bg-violet-100 text-violet-700 rounded-full">Audit</span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mb-1">{{ auditTotal.toLocaleString() }}</p>
            <p class="text-sm font-semibold text-gray-700">Audit Logs</p>
            <p class="text-xs text-gray-500 mt-0.5">Full activity trail</p>
          </div>
        </button>

        <!-- Card 3: Staff Hourly Rates -->
        <button @click="scrollToSection('section-staff-rates')"
          class="group bg-white rounded-2xl border border-amber-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-200 p-5 text-left overflow-hidden relative">
          <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-orange-100 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-2xl"></div>
          <div class="relative">
            <div class="flex items-center justify-between mb-3">
              <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <span class="text-xs font-bold px-2 py-1 bg-amber-100 text-amber-700 rounded-full">Rates</span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mb-1">{{ staffList.length }}</p>
            <p class="text-sm font-semibold text-gray-700">Staff Hourly Rates</p>
            <p class="text-xs text-gray-500 mt-0.5">Active staff members</p>
          </div>
        </button>

        <!-- Card 4: Forgot Password Requests -->
        <button @click="scrollToSection('section-forgot-password')"
          class="group bg-white rounded-2xl border border-red-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-200 p-5 text-left overflow-hidden relative">
          <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-pink-100 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-2xl"></div>
          <div class="relative">
            <div class="flex items-center justify-between mb-3">
              <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-red-500 to-pink-600 flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
              </div>
              <span v-if="pendingRequests.length > 0"
                class="text-xs font-bold px-2 py-1 bg-red-500 text-white rounded-full animate-pulse">
                {{ pendingRequests.length }}
              </span>
              <span v-else class="text-xs font-bold px-2 py-1 bg-green-100 text-green-700 rounded-full">All Clear</span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mb-1">{{ forgotPasswordRequests.length }}</p>
            <p class="text-sm font-semibold text-gray-700">Password Requests</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ pendingRequests.length }} pending</p>
          </div>
        </button>

        <!-- Card 5: Reports -->
        <button @click="scrollToSection('section-reports')"
          class="group bg-white rounded-2xl border border-emerald-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-200 p-5 text-left overflow-hidden relative">
          <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-teal-100 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-2xl"></div>
          <div class="relative">
            <div class="flex items-center justify-between mb-3">
              <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                </svg>
              </div>
              <span v-if="reportsSummary.pending > 0"
                class="text-xs font-bold px-2 py-1 bg-emerald-500 text-white rounded-full animate-pulse">
                {{ reportsSummary.pending }}
              </span>
              <span v-else class="text-xs font-bold px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full">Reports</span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mb-1">{{ reportsSummary.total }}</p>
            <p class="text-sm font-semibold text-gray-700">Staff Reports</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ reportsSummary.pending }} pending review</p>
          </div>
        </button>

      </div>

      <!-- ══════════════════════════════════════════════════════════════════ -->
      <!-- SECTION 1: SYSTEM LOGS                                             -->
      <!-- ══════════════════════════════════════════════════════════════════ -->
      <div id="section-system-logs" class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
        <!-- Card Header -->
        <div class="px-6 py-5 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900">System Activity Logs</h2>
                <p class="text-sm text-gray-600 mt-0.5">{{ totalLogs > 0 ? `${totalLogs} total events` : 'Real-time monitoring' }} • Page {{ logPage }} of {{ totalLogPages }}</p>
              </div>
            </div>
            <div v-if="activeUsers" class="text-right bg-white px-4 py-2 rounded-lg shadow-sm border border-blue-200">
              <p class="text-xs text-gray-600 font-medium">Active Users (24h)</p>
              <p class="text-2xl font-bold text-blue-600">{{ activeUsers }}</p>
            </div>
          </div>
        </div>

        <!-- System Logs Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-100 border-b-2 border-gray-200">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Timestamp</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Action</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Module</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Details</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-for="(log, index) in systemLogs" :key="`${log.time}-${log.user}-${log.action}-${index}`"
                class="hover:bg-blue-50 transition-colors cursor-default">
                <td class="px-6 py-4 text-gray-900 font-mono text-xs whitespace-nowrap">
                  {{ formatDateTime(log.time) }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-semibold text-gray-600">
                      {{ log.user.charAt(0).toUpperCase() }}
                    </div>
                    <span class="font-medium text-gray-900">{{ log.user }}</span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span :class="['text-xs px-3 py-1.5 rounded-full font-semibold shadow-sm', actionBadgeClass(log.action)]">
                    {{ log.action.toUpperCase() }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="text-gray-700 font-medium">{{ log.module }}</span>
                </td>
                <td class="px-6 py-4 text-gray-600 text-xs max-w-md truncate" :title="log.details">
                  {{ log.details || 'No additional details' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Controls -->
        <div v-if="totalLogs > 0" class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
          <p class="text-sm text-gray-600">
            Showing <span class="font-semibold text-gray-900">{{ (logPage - 1) * logsPerPage + 1 }}–{{ Math.min(logPage * logsPerPage, totalLogs) }}</span>
            of <span class="font-semibold text-gray-900">{{ totalLogs }}</span> events
          </p>
          <div class="flex items-center gap-2">
            <button @click="prevLogPage" :disabled="logPage === 1"
              class="flex items-center gap-1 px-4 py-2 text-sm font-semibold rounded-lg border-2 border-blue-200 text-blue-700 hover:bg-blue-50 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Prev
            </button>
            <span class="px-3 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg">
              {{ logPage }} / {{ totalLogPages }}
            </span>
            <button @click="nextLogPage" :disabled="logPage >= totalLogPages"
              class="flex items-center gap-1 px-4 py-2 text-sm font-semibold rounded-lg border-2 border-blue-200 text-blue-700 hover:bg-blue-50 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
              Next
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="systemLogs.length === 0" class="flex flex-col items-center justify-center py-20 text-gray-400 bg-gray-50">
          <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <p class="text-lg font-medium text-gray-600 mb-1">No System Logs Available</p>
          <p class="text-sm text-gray-500">Activity logs will appear here once users start interacting with the system.</p>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════════════ -->
      <!-- SECTION 2: AUDIT LOGS                                              -->
      <!-- ══════════════════════════════════════════════════════════════════ -->
      <div id="section-audit-logs" class="bg-white rounded-2xl border border-violet-100 shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
        <!-- Card Header -->
        <div class="px-6 py-5 bg-gradient-to-r from-violet-50 to-purple-100 border-b border-violet-200">
          <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900">Audit Logs</h2>
                <p class="text-sm text-gray-600 mt-0.5">{{ auditTotal.toLocaleString() }} total records • Page {{ auditPage }} of {{ auditTotalPages }}</p>
              </div>
            </div>
            <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-violet-200">
              <p class="text-xs text-gray-600 font-medium">Total Records</p>
              <p class="text-2xl font-bold text-violet-600">{{ auditTotal.toLocaleString() }}</p>
            </div>
          </div>

          <!-- Filters Row -->
          <div class="mt-4 flex flex-wrap gap-3 items-end">
            <!-- Action filter -->
            <div class="flex-1 min-w-[130px]">
              <label class="block text-xs font-semibold text-gray-600 mb-1">Action Type</label>
              <select v-model="auditFilterAction"
                class="w-full px-3 py-2 bg-white border border-violet-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 text-gray-700">
                <option value="">All Actions</option>
                <option value="login">Login</option>
                <option value="create">Create</option>
                <option value="update">Update</option>
                <option value="delete">Delete</option>
              </select>
            </div>
            <!-- Module filter -->
            <div class="flex-1 min-w-[130px]">
              <label class="block text-xs font-semibold text-gray-600 mb-1">Module</label>
              <select v-model="auditFilterModule"
                class="w-full px-3 py-2 bg-white border border-violet-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 text-gray-700">
                <option value="">All Modules</option>
                <option v-for="mod in auditModules" :key="mod" :value="mod">{{ mod }}</option>
              </select>
            </div>
            <!-- Date From -->
            <div class="flex-1 min-w-[130px]">
              <label class="block text-xs font-semibold text-gray-600 mb-1">From Date</label>
              <input v-model="auditFilterDateFrom" type="date"
                class="w-full px-3 py-2 bg-white border border-violet-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 text-gray-700"/>
            </div>
            <!-- Date To -->
            <div class="flex-1 min-w-[130px]">
              <label class="block text-xs font-semibold text-gray-600 mb-1">To Date</label>
              <input v-model="auditFilterDateTo" type="date"
                class="w-full px-3 py-2 bg-white border border-violet-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 text-gray-700"/>
            </div>
            <!-- Search -->
            <div class="flex-1 min-w-[150px]">
              <label class="block text-xs font-semibold text-gray-600 mb-1">Search</label>
              <input v-model="auditSearch" type="text" placeholder="User, module, details..."
                class="w-full px-3 py-2 bg-white border border-violet-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 text-gray-700"/>
            </div>
            <!-- Apply / Reset -->
            <div class="flex gap-2">
              <button @click="loadAuditLogs(1)"
                class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-lg shadow transition-all">
                Apply
              </button>
              <button @click="resetAuditFilters"
                class="px-4 py-2 bg-white border border-violet-200 text-violet-700 hover:bg-violet-50 text-sm font-semibold rounded-lg shadow transition-all">
                Reset
              </button>
            </div>
          </div>
        </div>

        <!-- Audit Logs Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b-2 border-gray-200">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Timestamp</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Action</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Module</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Details</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr v-for="(log, idx) in auditLogs" :key="`al-${log.id || idx}`"
                class="hover:bg-violet-50 transition-colors cursor-default">
                <td class="px-6 py-4 text-gray-900 font-mono text-xs whitespace-nowrap">
                  {{ formatDateTime(log.time) }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center text-xs font-semibold text-violet-700">
                      {{ log.user.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                      <p class="font-medium text-gray-900 text-xs">{{ log.user }}</p>
                      <p v-if="log.role" class="text-[10px] text-gray-500 uppercase">{{ log.role }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span :class="['text-xs px-3 py-1.5 rounded-full font-semibold shadow-sm', actionBadgeClass(log.action)]">
                    {{ log.action.toUpperCase() }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="text-xs px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 font-medium">{{ log.module }}</span>
                </td>
                <td class="px-6 py-4 text-gray-600 text-xs max-w-xs truncate" :title="log.details">
                  {{ log.details || '—' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="auditTotal > 0" class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between flex-wrap gap-3">
          <p class="text-sm text-gray-600">
            Showing <span class="font-semibold text-gray-900">{{ (auditPage - 1) * auditLogsPerPage + 1 }}–{{ Math.min(auditPage * auditLogsPerPage, auditTotal) }}</span>
            of <span class="font-semibold text-gray-900">{{ auditTotal }}</span> records
          </p>
          <div class="flex items-center gap-2">
            <button @click="prevAuditPage" :disabled="auditPage === 1"
              class="flex items-center gap-1 px-4 py-2 text-sm font-semibold rounded-lg border-2 border-violet-200 text-violet-700 hover:bg-violet-50 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Prev
            </button>
            <span class="px-3 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg">
              {{ auditPage }} / {{ auditTotalPages }}
            </span>
            <button @click="nextAuditPage" :disabled="auditPage >= auditTotalPages"
              class="flex items-center gap-1 px-4 py-2 text-sm font-semibold rounded-lg border-2 border-violet-200 text-violet-700 hover:bg-violet-50 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
              Next
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="auditLogs.length === 0" class="flex flex-col items-center justify-center py-20 text-gray-400 bg-gray-50">
          <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
          </svg>
          <p class="text-lg font-medium text-gray-600 mb-1">No Audit Logs Found</p>
          <p class="text-sm text-gray-500">Try adjusting your filters or check back later.</p>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════════════ -->
      <!-- SECTION 3: STAFF HOURLY RATES                                      -->
      <!-- ══════════════════════════════════════════════════════════════════ -->
      <div id="section-staff-rates" class="bg-white rounded-2xl border border-gray-100 shadow-lg hover:shadow-xl overflow-hidden transition-shadow">
        <!-- Card Header with Gradient -->
        <div class="px-6 py-6 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-amber-100 flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-xl font-bold text-gray-900">Staff Hourly Rates</h3>
              <p class="text-sm text-gray-600 mt-0.5">View and manage staff compensation</p>
            </div>
          </div>
          <div class="text-right bg-white px-4 py-2 rounded-xl border border-amber-200 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Staff</p>
            <p class="text-2xl font-bold text-amber-600">{{ staffList.length }}</p>
          </div>
        </div>

        <!-- Staff Rates Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gradient-to-r from-amber-50 to-orange-50 border-b border-amber-100">
              <tr>
                <th class="px-6 py-4 text-left font-bold text-gray-700 uppercase text-xs tracking-wider">Staff Name</th>
                <th class="px-6 py-4 text-left font-bold text-gray-700 uppercase text-xs tracking-wider">Email</th>
                <th class="px-6 py-4 text-left font-bold text-gray-700 uppercase text-xs tracking-wider">Role</th>
                <th class="px-6 py-4 text-left font-bold text-gray-700 uppercase text-xs tracking-wider">Hourly Rate (PHP)</th>
                <th class="px-6 py-4 text-left font-bold text-gray-700 uppercase text-xs tracking-wider">Status</th>
                <th class="px-6 py-4 text-center font-bold text-gray-700 uppercase text-xs tracking-wider">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-for="staff in staffList" :key="staff.id"
                :class="[
                  'transition-all',
                  editingStaffId === staff.id 
                    ? 'bg-amber-50 border-l-4 border-amber-500 shadow-inner' 
                    : 'hover:bg-gray-50'
                ]">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-sm font-bold text-white shadow-md">
                      {{ staff.name.charAt(0).toUpperCase() }}
                    </div>
                    <span class="font-semibold text-gray-900">{{ staff.name }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-gray-600 text-xs font-medium">{{ staff.email }}</td>
                <td class="px-6 py-4">
                  <span class="text-xs px-3 py-1.5 rounded-full font-semibold bg-gray-100 text-gray-700 shadow-sm uppercase">
                    {{ staff.role }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span v-if="editingStaffId === staff.id" class="flex items-center gap-2">
                    <span class="text-lg font-bold text-amber-600">₱</span>
                    <input v-model.number="editingRate" type="number" step="0.01" min="0"
                      class="w-32 px-3 py-2 border-2 border-amber-300 rounded-lg text-sm font-mono font-bold focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent bg-white shadow-sm" 
                      placeholder="0.00" />
                  </span>
                  <span v-else class="text-gray-900 font-mono text-base font-semibold">
                    {{ staff.hourly_rate ? `₱${parseFloat(staff.hourly_rate).toFixed(2)}` : '—' }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span :class="[
                    'text-xs px-3 py-1.5 rounded-full font-semibold shadow-sm uppercase',
                    staff.active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200'
                  ]">
                    {{ staff.active ? '✓ Active' : '✗ Inactive' }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex gap-2 justify-center">
                    <div v-if="editingStaffId === staff.id" class="flex gap-2">
                      <button @click="saveRate(staff.id)"
                        class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                        💾 Save
                      </button>
                      <button @click="editingStaffId = null"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                        Cancel
                      </button>
                    </div>
                    <button v-else @click="startEditingRate(staff)"
                      class="px-4 py-2 bg-white border-2 border-amber-300 text-amber-700 hover:bg-amber-50 hover:border-amber-400 text-sm font-semibold rounded-lg shadow-sm hover:shadow-md transition-all">
                      ✏️ Edit Rate
                    </button>
                    <button v-if="editingStaffId !== staff.id" @click="openPasswordModal(staff)"
                      class="px-4 py-2 bg-white border-2 border-blue-300 text-blue-700 hover:bg-blue-50 hover:border-blue-400 text-sm font-semibold rounded-lg shadow-sm hover:shadow-md transition-all">
                      🔑 Reset Password
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="staffList.length === 0" class="flex flex-col items-center justify-center py-20 text-gray-400 bg-gray-50">
          <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 00-9.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <p class="text-lg font-medium text-gray-600 mb-1">No Staff Members Found</p>
          <p class="text-sm text-gray-500">Staff hourly rates will be displayed here once available.</p>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════════════ -->
      <!-- SECTION 4: FORGOT PASSWORD REQUESTS                                -->
      <!-- ══════════════════════════════════════════════════════════════════ -->
      <div id="section-forgot-password" class="bg-white rounded-2xl border border-gray-100 shadow-lg hover:shadow-xl overflow-hidden transition-shadow">
        <!-- Card Header with Gradient -->
        <div class="px-6 py-6 bg-gradient-to-r from-red-50 to-pink-50 border-b border-red-100 flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-pink-600 flex items-center justify-center shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-xl font-bold text-gray-900">Forgot Password Requests</h3>
              <p class="text-sm text-gray-600 mt-0.5">User password reset requests from login page</p>
            </div>
          </div>
          <div class="text-right bg-white px-4 py-2 rounded-xl border border-red-200 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Pending</p>
            <p class="text-2xl font-bold text-red-600">{{ pendingRequests.length }}</p>
          </div>
        </div>

        <!-- Filter Tabs -->
        <div class="px-6 py-4 border-b border-gray-100 flex gap-3 bg-gradient-to-r from-gray-50 to-gray-50">
          <button @click="statusFilter = 'all'"
            :class="[
              'text-sm px-5 py-2.5 rounded-xl font-semibold transition-all shadow-sm',
              statusFilter === 'all' 
                ? 'bg-gradient-to-r from-gray-800 to-gray-700 text-white shadow-md scale-105' 
                : 'bg-white text-gray-600 hover:text-gray-800 hover:shadow-md hover:scale-105'
            ]">
            All ({{ forgotPasswordRequests.length }})
          </button>
          <button @click="statusFilter = 'pending'"
            :class="[
              'text-sm px-5 py-2.5 rounded-xl font-semibold transition-all shadow-sm',
              statusFilter === 'pending' 
                ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-md scale-105' 
                : 'bg-white text-gray-600 hover:text-gray-800 hover:shadow-md hover:scale-105'
            ]">
            Pending ({{ pendingRequests.length }})
          </button>
          <button @click="statusFilter = 'resolved'"
            :class="[
              'text-sm px-5 py-2.5 rounded-xl font-semibold transition-all shadow-sm',
              statusFilter === 'resolved' 
                ? 'bg-gradient-to-r from-green-600 to-green-500 text-white shadow-md scale-105' 
                : 'bg-white text-gray-600 hover:text-gray-800 hover:shadow-md hover:scale-105'
            ]">
            Resolved ({{ forgotPasswordRequests.filter(r => r.status === 'resolved').length }})
          </button>
        </div>

        <!-- Requests List -->
        <div class="divide-y divide-gray-200 bg-gray-50">
          <div v-for="req in filteredRequests" :key="req.id"
            :class="[
              'px-6 py-5 transition-all',
              req.status === 'pending' 
                ? 'bg-white hover:bg-blue-50 border-l-4 border-blue-500' 
                : 'bg-gray-50 hover:bg-gray-100 border-l-4 border-gray-300'
            ]">
            <div class="flex items-start justify-between gap-4 mb-3">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-400 to-pink-500 flex items-center justify-center text-sm font-bold text-white shadow-md">
                    {{ req.name.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <div class="flex items-center gap-2">
                      <span class="font-bold text-gray-900 text-base">{{ req.name }}</span>
                      <span :class="[
                        'text-[10px] px-2.5 py-1 rounded-full font-bold uppercase shadow-sm',
                        statusBadgeClass(req.status)
                      ]">
                        {{ req.status === 'pending' ? '⏳ ' : req.status === 'resolved' ? '✓ ' : '✗ ' }}{{ req.status }}
                      </span>
                    </div>
                    <p class="text-sm text-gray-600 font-mono mt-1">{{ req.email }}</p>
                  </div>
                </div>
              </div>
              <div class="text-right text-xs text-gray-500">
                <div class="flex items-center gap-1 bg-white px-2 py-1 rounded-lg border border-gray-200 shadow-sm">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  {{ formatDateTime(req.created_at) }}
                </div>
              </div>
            </div>
            <div class="bg-gradient-to-r from-gray-100 to-gray-50 p-4 rounded-xl mb-3 border border-gray-200 shadow-inner">
              <p class="text-sm text-gray-800 leading-relaxed">{{ req.message }}</p>
            </div>
            <div v-if="req.resolved_at" class="flex items-center gap-2 text-xs text-gray-600 mb-3 bg-green-50 px-3 py-2 rounded-lg border border-green-200">
              <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span><strong>Resolved by {{ req.resolved_by_name }}</strong> on {{ formatDateTime(req.resolved_at) }}</span>
            </div>
            <div v-if="req.status === 'pending'" class="flex gap-3">
              <button @click="resolveRequest(req.id, 'resolved')"
                class="flex-1 px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                ✓ Mark Resolved
              </button>
              <button @click="resolveRequest(req.id, 'dismissed')"
                class="flex-1 px-5 py-2.5 bg-white border-2 border-gray-300 text-gray-700 hover:bg-gray-100 hover:border-gray-400 text-sm font-bold rounded-xl shadow-sm hover:shadow-md transition-all">
                ✗ Dismiss
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredRequests.length === 0" class="flex flex-col items-center justify-center py-20 text-gray-400 bg-gray-50">
          <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <p class="text-lg font-medium text-gray-600 mb-1">No Requests Found</p>
          <p class="text-sm text-gray-500">Password reset requests matching your filter will appear here.</p>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════════════ -->
      <!-- SECTION 5: REPORTS                                                  -->
      <!-- ══════════════════════════════════════════════════════════════════ -->
      <div id="section-reports" class="bg-white rounded-2xl border border-emerald-100 shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
        <!-- Card Header -->
        <div class="px-6 py-5 bg-gradient-to-r from-emerald-50 to-teal-100 border-b border-emerald-200">
          <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900">Staff Reports</h2>
                <p class="text-sm text-gray-600 mt-0.5">Submitted by staff members for review</p>
              </div>
            </div>
            <!-- Summary Badges -->
            <div class="flex gap-2 flex-wrap">
              <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">
                Total: {{ reportsSummary.total }}
              </span>
              <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                Pending: {{ reportsSummary.pending }}
              </span>
              <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">
                In Review: {{ reportsSummary.in_review }}
              </span>
              <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                Resolved: {{ reportsSummary.resolved }}
              </span>
              <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">
                Dismissed: {{ reportsSummary.dismissed }}
              </span>
            </div>
          </div>
        </div>

        <!-- Status Filter Tabs -->
        <div class="px-6 py-4 border-b border-gray-100 flex gap-2 flex-wrap bg-gray-50">
          <button v-for="tab in reportsTabs" :key="tab.value" @click="reportsFilter = tab.value"
            :class="[
              'text-sm px-4 py-2 rounded-xl font-semibold transition-all shadow-sm',
              reportsFilter === tab.value ? tab.activeClass : 'bg-white text-gray-600 hover:text-gray-800 hover:shadow-md border border-gray-200'
            ]">
            {{ tab.label }}
            <span v-if="tab.count > 0" class="ml-1 text-[10px] font-bold">
              ({{ tab.count }})
            </span>
          </button>
        </div>

        <!-- Reports List -->
        <div class="divide-y divide-gray-100">
          <div v-for="report in filteredReports" :key="report.id"
            :class="[
              'px-6 py-5 hover:bg-emerald-50/30 transition-all',
              report.status === 'pending' ? 'border-l-4 border-blue-400' :
              report.status === 'in_review' ? 'border-l-4 border-amber-400' :
              report.status === 'resolved' ? 'border-l-4 border-emerald-400' :
              'border-l-4 border-gray-200'
            ]">
            <div class="flex items-start justify-between gap-4 mb-3">
              <!-- Reporter -->
              <div class="flex items-center gap-3 flex-1 min-w-0">
                <div :class="[
                  'w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white shadow-md flex-shrink-0',
                  report.is_anonymous == 1 ? 'bg-gray-400' : 'bg-gradient-to-br from-emerald-400 to-teal-500'
                ]">
                  {{ report.is_anonymous == 1 ? '?' : report.reporter_name?.charAt(0)?.toUpperCase() || '?' }}
                </div>
                <div class="min-w-0">
                  <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-bold text-gray-900">{{ report.is_anonymous == 1 ? 'Anonymous' : (report.reporter_name || 'Unknown') }}</span>
                    <span v-if="report.reporter_role && report.is_anonymous != 1"
                      class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 uppercase font-bold">
                      {{ report.reporter_role }}
                    </span>
                    <span :class="['text-[10px] px-2.5 py-1 rounded-full font-bold uppercase', reportStatusBadgeClass(report.status)]">
                      {{ report.status === 'in_review' ? 'In Review' : report.status }}
                    </span>
                  </div>
                  <p v-if="report.reporter_email && report.is_anonymous != 1" class="text-xs text-gray-500 font-mono mt-0.5">{{ report.reporter_email }}</p>
                </div>
              </div>
              <!-- Category + Date -->
              <div class="flex flex-col items-end gap-1 flex-shrink-0">
                <span :class="['text-xs px-3 py-1 rounded-full font-bold uppercase', reportCategoryBadgeClass(report.category)]">
                  {{ report.category }}
                </span>
                <span class="text-[10px] text-gray-500 flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  {{ formatDateTime(report.created_at) }}
                </span>
              </div>
            </div>

            <!-- Subject & Description -->
            <div class="mb-3">
              <p class="font-semibold text-gray-900 mb-1">{{ report.subject }}</p>
              <div class="text-sm text-gray-700 bg-gray-50 rounded-xl p-4 border border-gray-200 leading-relaxed">
                {{ report.description }}
              </div>
            </div>

            <!-- Admin Response (if any) -->
            <div v-if="report.admin_response" class="mb-3 flex items-start gap-2 text-sm bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3">
              <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"/>
              </svg>
              <div>
                <p class="text-xs font-bold text-emerald-700 mb-0.5">Response by {{ report.responder_name || 'Admin' }}</p>
                <p class="text-gray-700">{{ report.admin_response }}</p>
              </div>
            </div>

            <!-- Action Buttons -->
            <div v-if="report.status === 'pending' || report.status === 'in_review'" class="flex gap-2 flex-wrap">
              <button v-if="report.status === 'pending'" @click="updateReportStatus(report.id, 'in_review')"
                class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg shadow transition-all">
                🔍 Mark In Review
              </button>
              <button @click="updateReportStatus(report.id, 'resolved')"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow transition-all">
                ✓ Mark Resolved
              </button>
              <button @click="updateReportStatus(report.id, 'dismissed')"
                class="px-4 py-2 bg-white border-2 border-gray-300 text-gray-700 hover:bg-gray-100 text-xs font-bold rounded-lg shadow transition-all">
                ✗ Dismiss
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredReports.length === 0" class="flex flex-col items-center justify-center py-20 text-gray-400 bg-gray-50">
          <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
          </svg>
          <p class="text-lg font-medium text-gray-600 mb-1">No Reports Found</p>
          <p class="text-sm text-gray-500">Staff reports matching your filter will appear here.</p>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const router = useRouter()
const authStore = useAuthStore()

// Verify IT role
if (authStore.userRole !== 'it' && authStore.userRole !== 'admin') {
  router.push('/unauthorized')
}

const loading = ref(false)
const systemLogs = ref([])
const activeUsers = ref(0)
const staffList = ref([])
const forgotPasswordRequests = ref([])
const statusFilter = ref('all')
const lastUpdated = ref('Never')

const editingStaffId = ref(null)
const editingRate = ref(0)

// System Log pagination
const logPage = ref(1)
const logsPerPage = 10
const totalLogPages = ref(1)
const totalLogs = ref(0)

// Audit Logs
const auditLogs = ref([])
const auditPage = ref(1)
const auditLogsPerPage = 20
const auditTotalPages = ref(1)
const auditTotal = ref(0)
const auditModules = ref([])
const auditFilterAction = ref('')
const auditFilterModule = ref('')
const auditFilterDateFrom = ref('')
const auditFilterDateTo = ref('')
const auditSearch = ref('')

// Reports
const reports = ref([])
const reportsSummary = ref({ total: 0, pending: 0, in_review: 0, resolved: 0, dismissed: 0 })
const reportsFilter = ref('all')

// Password reset modal
const showPasswordModal = ref(false)
const passwordResetStaff = ref(null)
const newPassword = ref('')
const confirmPassword = ref('')
const passwordResetting = ref(false)

// Notifications
const showNotifications = ref(false)
const readNotifIds = ref(JSON.parse(localStorage.getItem('it_read_notifications') || '[]'))

// Toast messages
const successMessage = ref('')
const errorMessage = ref('')

const showSuccess = (message) => {
  successMessage.value = message
  setTimeout(() => { successMessage.value = '' }, 5000)
}

const showError = (message) => {
  errorMessage.value = message
  setTimeout(() => { errorMessage.value = '' }, 5000)
}

const pendingRequests = computed(() => forgotPasswordRequests.value.filter(r => r.status === 'pending'))

const filteredRequests = computed(() => {
  if (statusFilter.value === 'all') return forgotPasswordRequests.value
  return forgotPasswordRequests.value.filter(r => r.status === statusFilter.value)
})

const notifications = computed(() => {
  const items = []

  // Pending forgot-password requests → actionable notifications
  forgotPasswordRequests.value
    .filter(r => r.status === 'pending')
    .forEach(r => {
      items.push({
        id: `fpw_${r.id}`,
        type: 'password_request',
        title: 'Password Reset Request',
        description: `${r.name} (${r.email}) is requesting a password reset.`,
        time: r.created_at,
        target: 'section-forgot-password',
      })
    })

  // Recent delete actions from system logs → system alerts
  systemLogs.value
    .filter(l => l.action === 'delete')
    .slice(0, 5)
    .forEach((l, i) => {
      items.push({
        id: `log_del_${i}_${l.time}`,
        type: 'system_alert',
        title: 'System Alert — Deletion',
        description: `${l.user} deleted a record on ${l.module}${l.details ? ': ' + l.details : ''}.`,
        time: l.time,
        target: 'section-system-logs',
      })
    })

  // Sort newest first
  return items.sort((a, b) => new Date(b.time) - new Date(a.time))
})

const unreadCount = computed(() =>
  notifications.value.filter(n => !readNotifIds.value.includes(n.id)).length
)

const hasUnread = computed(() => unreadCount.value > 0)

// Reports computed
const filteredReports = computed(() => {
  if (reportsFilter.value === 'all') return reports.value
  return reports.value.filter(r => r.status === reportsFilter.value)
})

const reportsTabs = computed(() => [
  { value: 'all',       label: 'All',        count: reportsSummary.value.total,     activeClass: 'bg-gray-800 text-white shadow-md' },
  { value: 'pending',   label: 'Pending',    count: reportsSummary.value.pending,   activeClass: 'bg-blue-600 text-white shadow-md' },
  { value: 'in_review', label: 'In Review',  count: reportsSummary.value.in_review, activeClass: 'bg-amber-500 text-white shadow-md' },
  { value: 'resolved',  label: 'Resolved',   count: reportsSummary.value.resolved,  activeClass: 'bg-emerald-600 text-white shadow-md' },
  { value: 'dismissed', label: 'Dismissed',  count: reportsSummary.value.dismissed, activeClass: 'bg-gray-500 text-white shadow-md' },
])

const notifIconBg = (type) => {
  if (type === 'password_request') return 'bg-gradient-to-br from-red-400 to-pink-500'
  if (type === 'system_alert')     return 'bg-gradient-to-br from-orange-400 to-amber-500'
  return 'bg-gradient-to-br from-gray-400 to-gray-500'
}

const formatDateTime = (dateTimeStr) => {
  if (!dateTimeStr) return '—'
  const date = new Date(dateTimeStr)
  return date.toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
}

const actionBadgeClass = (action) => {
  const classes = {
    login:  'bg-blue-100 text-blue-700',
    create: 'bg-green-100 text-green-700',
    update: 'bg-amber-100 text-amber-700',
    delete: 'bg-red-100 text-red-700',
  }
  return classes[action] || 'bg-gray-100 text-gray-700'
}

const statusBadgeClass = (status) => {
  const classes = {
    pending:   'bg-blue-100 text-blue-700',
    resolved:  'bg-green-100 text-green-700',
    dismissed: 'bg-gray-100 text-gray-700',
  }
  return classes[status] || 'bg-gray-100 text-gray-700'
}

const reportStatusBadgeClass = (status) => {
  const classes = {
    pending:   'bg-blue-100 text-blue-700',
    in_review: 'bg-amber-100 text-amber-700',
    resolved:  'bg-emerald-100 text-emerald-700',
    dismissed: 'bg-gray-100 text-gray-500',
  }
  return classes[status] || 'bg-gray-100 text-gray-700'
}

const reportCategoryBadgeClass = (category) => {
  const classes = {
    complaint:    'bg-red-100 text-red-700',
    suggestion:   'bg-blue-100 text-blue-700',
    maintenance:  'bg-amber-100 text-amber-700',
    safety:       'bg-orange-100 text-orange-700',
    hr:           'bg-purple-100 text-purple-700',
  }
  return classes[(category || '').toLowerCase()] || 'bg-gray-100 text-gray-700'
}

const toggleNotifications = () => {
  if (!showNotifications.value) {
    showNotifications.value = true
    // Mark all as read when panel is opened
    markAllRead()
  } else {
    showNotifications.value = false
  }
}

const markAllRead = () => {
  const allIds = notifications.value.map(n => n.id)
  const merged = [...new Set([...readNotifIds.value, ...allIds])]
  readNotifIds.value = merged
  localStorage.setItem('it_read_notifications', JSON.stringify(merged))
}

const clickNotification = (notif) => {
  if (!readNotifIds.value.includes(notif.id)) {
    const updated = [...readNotifIds.value, notif.id]
    readNotifIds.value = updated
    localStorage.setItem('it_read_notifications', JSON.stringify(updated))
  }
  showNotifications.value = false
  scrollToSection(notif.target)
}

const scrollToSection = (sectionId) => {
  const el = document.getElementById(sectionId)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

const loadSystemLogs = async (page = 1) => {
  try {
    const response = await api.get('/it/system-logs', { params: { page, limit: logsPerPage } })
    systemLogs.value = response.data.data.logs || []
    activeUsers.value = response.data.data.active_users || 0
    totalLogs.value = response.data.data.total || 0
    totalLogPages.value = response.data.data.total_pages || 1
    logPage.value = page
  } catch (err) {
    console.error('Failed to load system logs:', err)
    showError('Failed to load system logs')
  }
}

const prevLogPage = () => {
  if (logPage.value > 1) loadSystemLogs(logPage.value - 1)
}

const nextLogPage = () => {
  if (logPage.value < totalLogPages.value) loadSystemLogs(logPage.value + 1)
}

const loadStaffRates = async () => {
  try {
    const response = await api.get('/it/staff-hourly-rates')
    staffList.value = response.data.data || []
  } catch (err) {
    console.error('Failed to load staff rates:', err)
    showError('Failed to load staff hourly rates')
  }
}

const loadForgotPasswordRequests = async () => {
  try {
    const response = await api.get('/it/forgot-password-requests')
    forgotPasswordRequests.value = response.data.data || []
  } catch (err) {
    console.error('Failed to load forgot password requests:', err)
    showError('Failed to load password reset requests')
  }
}

const loadAuditLogs = async (page = 1) => {
  try {
    const params = { page, limit: auditLogsPerPage }
    if (auditFilterAction.value)   params.action    = auditFilterAction.value
    if (auditFilterModule.value)   params.module    = auditFilterModule.value
    if (auditFilterDateFrom.value) params.date_from = auditFilterDateFrom.value
    if (auditFilterDateTo.value)   params.date_to   = auditFilterDateTo.value
    if (auditSearch.value)         params.search    = auditSearch.value
    const response = await api.get('/it/audit-logs', { params })
    auditLogs.value       = response.data.data.logs        || []
    auditTotal.value      = response.data.data.total       || 0
    auditTotalPages.value = response.data.data.total_pages || 1
    auditPage.value       = page
    if (response.data.data.modules?.length) auditModules.value = response.data.data.modules
  } catch (err) {
    console.error('Failed to load audit logs:', err)
    showError('Failed to load audit logs')
  }
}

const prevAuditPage = () => { if (auditPage.value > 1) loadAuditLogs(auditPage.value - 1) }
const nextAuditPage = () => { if (auditPage.value < auditTotalPages.value) loadAuditLogs(auditPage.value + 1) }

const resetAuditFilters = () => {
  auditFilterAction.value   = ''
  auditFilterModule.value   = ''
  auditFilterDateFrom.value = ''
  auditFilterDateTo.value   = ''
  auditSearch.value         = ''
  loadAuditLogs(1)
}

const loadReports = async () => {
  try {
    const response = await api.get('/it/reports')
    reports.value        = response.data.data.reports || []
    reportsSummary.value = response.data.data.summary || { total: 0, pending: 0, in_review: 0, resolved: 0, dismissed: 0 }
  } catch (err) {
    console.error('Failed to load reports:', err)
    showError('Failed to load staff reports')
  }
}

const updateReportStatus = async (reportId, status) => {
  try {
    await api.put(`/it/reports/${reportId}/status`, { status })
    await loadReports()
    showSuccess(`Report marked as ${status.replace('_', ' ')}`)
  } catch (err) {
    console.error('Failed to update report status:', err)
    showError('Failed to update report status')
  }
}

const loadAll = async () => {
  loading.value = true
  try {
    await Promise.all([
      loadSystemLogs(),
      loadAuditLogs(),
      loadStaffRates(),
      loadForgotPasswordRequests(),
      loadReports(),
    ])
    lastUpdated.value = new Date().toLocaleString('en-US', { hour: '2-digit', minute: '2-digit' })
    showSuccess('Dashboard data refreshed successfully')
  } catch (err) {
    showError('Failed to refresh dashboard data')
  } finally {
    loading.value = false
  }
}

const startEditingRate = (staff) => {
  editingStaffId.value = staff.id
  editingRate.value = parseFloat(staff.hourly_rate || 0)
}

const saveRate = async (staffId) => {
  try {
    await api.put(`/it/staff-hourly-rates/${staffId}`, { hourly_rate: editingRate.value })
    editingStaffId.value = null
    await loadStaffRates()
    showSuccess('Hourly rate updated successfully')
  } catch (err) {
    console.error('Failed to save hourly rate:', err)
    showError('Failed to update hourly rate')
  }
}

const resolveRequest = async (id, status) => {
  try {
    await api.put(`/it/forgot-password-requests/${id}/resolve`, { status })
    await loadForgotPasswordRequests()
    showSuccess(`Request ${status} successfully`)
  } catch (err) {
    console.error('Failed to resolve request:', err)
    showError('Failed to update request status')
  }
}

const openPasswordModal = (staff) => {
  passwordResetStaff.value = staff
  newPassword.value = ''
  confirmPassword.value = ''
  showPasswordModal.value = true
}

const closePasswordModal = () => {
  showPasswordModal.value = false
  passwordResetStaff.value = null
  newPassword.value = ''
  confirmPassword.value = ''
}

const submitPasswordReset = async () => {
  if (!newPassword.value || newPassword.value.length < 8 || newPassword.value !== confirmPassword.value) return
  passwordResetting.value = true
  try {
    await api.put(`/it/staff-password/${passwordResetStaff.value.id}`, { password: newPassword.value })
    const name = passwordResetStaff.value.name
    closePasswordModal()
    showSuccess(`Password reset successfully for ${name}`)
    await loadSystemLogs(logPage.value)
  } catch (err) {
    console.error('Failed to reset password:', err)
    showError(err.response?.data?.message || 'Failed to reset password')
  } finally {
    passwordResetting.value = false
  }
}

onMounted(async () => {
  await loadAll()
})
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from {
  transform: translateY(-20px);
  opacity: 0;
}

.slide-down-leave-to {
  transform: translateY(-20px);
  opacity: 0;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.dropdown-enter-active {
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.dropdown-leave-active {
  transition: all 0.15s ease-in;
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px) scale(0.97);
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>

