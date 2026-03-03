<template>
  <div class="min-h-screen bg-gray-50 p-6">

    <!-- Remove Discount Confirmation Modal -->
    <div v-if="discountToRemove" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl p-6 w-80">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Remove Discount</h3>
        <p class="text-sm text-gray-500 mb-5">Are you sure you want to remove this discount? This cannot be undone.</p>
        <div class="flex gap-3">
          <button @click="discountToRemove = null" class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
          <button @click="doRemoveDiscount" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg text-sm font-medium">Remove</button>
        </div>
      </div>
    </div>

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
          <span class="text-xl">??</span> System Settings
        </h1>
        <p class="text-sm text-gray-500 mt-1">Manage billing, notifications, security and logs for your system.</p>
      </div>
      <div class="flex items-center gap-4">
        <span class="text-xs text-gray-400 flex items-center gap-1">
          <span>?</span> All changes are saved locally until you click Save.
        </span>
        <button @click="saveSettings"
                class="flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
          <span>??</span> Save Changes
        </button>
      </div>
    </div>

    <!-- Two-column layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- LEFT COLUMN (2/3 width) -->
      <div class="lg:col-span-2 space-y-6">

        <!-- General Settings Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h2 class="text-lg font-bold text-gray-800">General Settings</h2>
              <p class="text-sm text-gray-500 mt-0.5">Configure your hotel's basic information, operating hours, and billing defaults.</p>
            </div>
            <span class="text-xs font-semibold bg-green-100 text-green-700 px-2 py-1 rounded-full">General</span>
          </div>

          <!-- Hotel Identity -->
          <div class="mb-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Hotel Identity</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hotel / Business Name</label>
                <input v-model="generalSettings.hotel_name" type="text" placeholder="e.g. Joanna's Nook Bed &amp; Breakfast"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                <input v-model="generalSettings.hotel_email" type="email" placeholder="info@srcbhotel.com"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input v-model="generalSettings.hotel_phone" type="text" placeholder="09XXXXXXXXX"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input v-model="generalSettings.hotel_address" type="text" placeholder="Street, City, Province"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
              </div>
            </div>
          </div>

          <!-- Operation Hours -->
          <div class="mb-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Default Check-in / Check-out</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Default Check-in Time</label>
                <input v-model="generalSettings.default_checkin_time" type="time"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
                <p class="text-xs text-gray-400 mt-1">Used as default when creating walk-in reservations.</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Default Check-out Time</label>
                <input v-model="generalSettings.default_checkout_time" type="time"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
                <p class="text-xs text-gray-400 mt-1">Guests are expected to vacate by this time.</p>
              </div>
            </div>
          </div>

          <!-- Billing Defaults -->
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Billing Defaults</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tax Rate (%)</label>
                <div class="relative">
                  <input v-model="generalSettings.tax_rate" type="number" min="0" max="100" step="0.01" placeholder="12"
                         class="w-full border border-gray-200 rounded-lg px-3 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
                  <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Applied to bills and receipts (e.g. 12 for 12% VAT).</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Currency Symbol</label>
                <select v-model="generalSettings.currency"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                  <option value="?">? — Philippine Peso (PHP)</option>
                  <option value="$">$ — US Dollar (USD)</option>
                  <option value="€">€ — Euro (EUR)</option>
                  <option value="£">£ — British Pound (GBP)</option>
                  <option value="¥">¥ — Japanese Yen (JPY)</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Save status message -->
          <transition name="fade">
            <div v-if="generalMsg" class="mt-4 p-3 rounded-lg text-sm flex items-center gap-2"
              :class="generalMsg.type === 'success'
                ? 'bg-green-50 border border-green-200 text-green-700'
                : 'bg-red-50 border border-red-200 text-red-700'">
              <svg v-if="generalMsg.type === 'success'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              <svg v-else class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              {{ generalMsg.text }}
            </div>
          </transition>

          <div class="mt-5 flex justify-end">
            <button @click="saveGeneralSettings" :disabled="generalSaving"
                    class="flex items-center gap-2 bg-green-700 hover:bg-green-800 disabled:bg-gray-300 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
              <svg v-if="generalSaving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
              {{ generalSaving ? 'Saving...' : 'Save General Settings' }}
            </button>
          </div>
        </div>

        <!-- Discounts Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-start justify-between mb-1">
            <div>
              <h2 class="text-lg font-bold text-gray-800">Discounts</h2>
              <p class="text-sm text-gray-500 mt-0.5">Manage discount types shown in the POS. Add, edit or remove discounts and choose the default.</p>
            </div>
            <span class="text-xs font-semibold bg-green-100 text-green-800 px-2 py-1 rounded-full">Billing</span>
          </div>

          <!-- Default Discount -->
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Default Discount</label>
            <select v-model="defaultDiscountId"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
              <option :value="null">No Discount</option>
              <option v-for="d in discounts" :key="d.id" :value="d.id">
                {{ d.name }} ({{ d.type === 'percentage' ? d.value + '%' : '?' + d.value }})
              </option>
            </select>
            <p class="text-xs text-gray-400 mt-1">The selected default will be applied when the POS loads.</p>
          </div>

          <!-- Available Discounts -->
          <div class="mt-4">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm font-semibold text-gray-700">Available Discounts</p>
              <button @click="showAddDiscount = true"
                      class="text-xs bg-green-700 hover:bg-green-800 text-white px-3 py-1.5 rounded-lg transition">
                + Add Discount
              </button>
            </div>
            <div v-if="discounts.length === 0" class="text-sm text-gray-400 italic py-3 text-center">
              No discounts configured. Add a discount to make it available in the POS.
            </div>
            <ul v-else class="divide-y divide-gray-100">
              <li v-for="d in discounts" :key="d.id"
                  class="flex items-center justify-between py-2 text-sm">
                <span class="font-medium text-gray-700">
                  {{ d.name }}
                  <span class="ml-2 text-xs text-gray-400">
                    {{ d.type === 'percentage' ? d.value + '%' : '?' + parseFloat(d.value).toFixed(2) }}
                  </span>
                  <span v-if="d.is_default == 1"
                        class="ml-2 text-xs bg-green-100 text-green-700 px-2 rounded-full">Default</span>
                </span>
                <div class="flex items-center gap-2">
                  <button v-if="d.is_default != 1" @click="setDefault(d.id)"
                          class="text-xs text-amber-600 hover:underline">Set Default</button>
                  <button @click="removeDiscount(d.id)"
                          class="text-xs text-red-500 hover:underline">Remove</button>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <!-- Audit Logs Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-start justify-between mb-1">
            <div>
              <h2 class="text-lg font-bold text-gray-800">Audit Logs</h2>
              <p class="text-sm text-gray-500 mt-0.5">All completed sales transactions — POS, room, and event billing. Filter by date, employee, or transaction number.</p>
            </div>
            <button @click="fetchAuditLogs" class="text-xs bg-green-700 hover:bg-green-800 text-white px-3 py-1.5 rounded-lg transition">
              Refresh
            </button>
          </div>

          <!-- Filters row 1: search + employee -->
          <div class="flex flex-wrap items-center gap-2 mt-4">
            <input v-model="auditSearch" @input="debouncedFetchAuditLogs"
                   type="text" placeholder="Search transaction ID or customer..."
                   class="flex-1 min-w-[160px] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            <input v-model="auditEmployee" @input="debouncedFetchAuditLogs"
                   type="text" placeholder="Filter by employee..."
                   class="flex-1 min-w-[140px] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
          </div>

          <!-- Filters row 2: date range + clear -->
          <div class="flex flex-wrap items-center gap-2 mt-2 mb-3">
            <div class="flex items-center gap-1 flex-1 min-w-[240px]">
              <span class="text-xs text-gray-500 whitespace-nowrap">From</span>
              <input v-model="auditDateFrom" @change="fetchAuditLogs"
                     type="date"
                     class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
              <span class="text-xs text-gray-500">To</span>
              <input v-model="auditDateTo" @change="fetchAuditLogs"
                     type="date"
                     class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
            <button @click="clearAuditFilters"
                    class="px-3 py-2 text-sm border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 whitespace-nowrap">
              Clear Filters
            </button>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-sm">
              <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <tr>
                  <th class="px-4 py-3 text-left">Transaction ID</th>
                  <th class="px-4 py-3 text-left">Date &amp; Time</th>
                  <th class="px-4 py-3 text-left">Customer</th>
                  <th class="px-4 py-3 text-left">Employee</th>
                  <th class="px-4 py-3 text-left">Payment Method</th>
                  <th class="px-4 py-3 text-right">Amount</th>
                  <th class="px-4 py-3 text-center">Type</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-if="auditLoading">
                  <td colspan="7" class="text-center py-8 text-gray-400">Loading...</td>
                </tr>
                <tr v-else-if="pagedAuditLogs.length === 0">
                  <td colspan="7" class="text-center py-8 text-gray-400">No audit records found.</td>
                </tr>
                <tr v-for="row in pagedAuditLogs" :key="row.id + '-' + row.source"
                    class="hover:bg-gray-50 transition">
                  <td class="px-4 py-3 font-mono text-xs text-green-800 whitespace-nowrap">{{ row.transaction_id || ('#' + row.id) }}</td>
                  <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ formatDate(row.created_at) }}</td>
                  <td class="px-4 py-3 font-medium text-gray-800">{{ row.name }}</td>
                  <td class="px-4 py-3 text-gray-700">
                    <span class="inline-flex items-center gap-1">
                      <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                      {{ row.employee || 'N/A' }}
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-gray-100 text-gray-600 capitalize">{{ row.payment_method || 'N/A' }}</span>
                  </td>
                  <td class="px-4 py-3 text-right font-semibold text-gray-800">?{{ parseFloat(row.sales || 0).toFixed(2) }}</td>
                  <td class="px-4 py-3 text-center">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                          :class="{
                            'bg-purple-100 text-purple-700': row.source === 'POS',
                            'bg-green-100 text-green-800': row.source === 'Room',
                            'bg-amber-100 text-amber-700': row.source === 'Event'
                          }">
                      {{ row.source }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="flex items-center justify-between mt-3 text-xs text-gray-500">
            <span>Showing {{ auditFrom }}–{{ auditTo }} of {{ auditLogs.length }}</span>
            <div class="flex items-center gap-2">
              <button @click="auditPage--" :disabled="auditPage <= 1"
                      class="px-2 py-1 border rounded disabled:opacity-40">Prev</button>
              <span>{{ auditPage }}/{{ auditTotalPages }}</span>
              <button @click="auditPage++" :disabled="auditPage >= auditTotalPages"
                      class="px-2 py-1 border rounded disabled:opacity-40">Next</button>
            </div>
          </div>
        </div>

      </div>

      <!-- RIGHT COLUMN (1/3 width) -->
      <div class="space-y-6">

        <!-- Holidays Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="text-lg font-bold text-gray-800 mb-1">Holidays</h2>
          <p class="text-sm text-gray-500 mb-4">Set dates that should be treated as holidays.</p>

          <div class="flex items-center gap-2 mb-3">
            <input v-model="newHolidayDate" type="date"
                   class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            <button @click="addHoliday"
                    class="bg-green-700 hover:bg-green-800 text-white text-xs font-medium px-3 py-2 rounded-lg whitespace-nowrap transition">
              Add Holiday
            </button>
          </div>

          <div v-if="holidays.length === 0" class="text-sm text-gray-400 italic text-center py-3">
            No holidays set.
          </div>
          <ul v-else class="divide-y divide-gray-100">
            <li v-for="h in holidays" :key="h.id"
                class="flex items-center justify-between py-2 text-sm">
              <span class="font-medium text-gray-700">{{ formatDateOnly(h.date) }}</span>
              <span v-if="h.description" class="text-xs text-gray-400 truncate max-w-[80px]">{{ h.description }}</span>
              <button @click="removeHoliday(h.id)"
                      class="text-xs text-red-500 hover:underline ml-2">Remove</button>
            </li>
          </ul>
        </div>

        <!-- System Logs Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-center justify-between mb-1">
            <h2 class="text-lg font-bold text-gray-800">System Logs</h2>
            <span class="text-xs text-amber-600 cursor-pointer hover:underline" @click="fetchSystemLogs">Overview</span>
          </div>
          <p class="text-sm text-gray-500 mb-3">Overview of recent system activity.</p>
          <p class="text-sm font-medium text-gray-700 mb-3">
            Active users (24h): <span class="text-amber-600 font-bold">{{ activeUsers }}</span>
          </p>

          <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-xs">
              <thead class="bg-gray-50 text-gray-500 uppercase tracking-wide">
                <tr>
                  <th class="px-3 py-2 text-left">Time</th>
                  <th class="px-3 py-2 text-left">User</th>
                  <th class="px-3 py-2 text-left">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-if="systemLogs.length === 0">
                  <td colspan="3" class="text-center py-4 text-gray-400">No recent activity.</td>
                </tr>
                <tr v-for="(log, i) in systemLogs" :key="i" class="hover:bg-gray-50">
                  <td class="px-3 py-2 text-gray-500 whitespace-nowrap">{{ formatTime(log.time) }}</td>
                  <td class="px-3 py-2 font-medium text-gray-700">{{ log.user }}</td>
                  <td class="px-3 py-2 text-gray-600">{{ log.action }}<span v-if="log.module" class="text-gray-400"> / {{ log.module }}</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Notifications Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="text-lg font-bold text-gray-800 mb-1">Notifications</h2>
          <p class="text-sm text-gray-500 mb-4">Control how you receive alerts.</p>

          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-700">Email Notifications</p>
                <p class="text-xs text-gray-400">Order updates, invoices, and alerts</p>
              </div>
              <button @click="notifications.email = !notifications.email"
                      :class="notifications.email ? 'bg-green-700' : 'bg-gray-200'"
                      class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                <span :class="notifications.email ? 'translate-x-6' : 'translate-x-1'"
                      class="inline-block h-4 w-4 rounded-full bg-white transition-transform shadow"></span>
              </button>
            </div>

            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-700">Push Notifications</p>
                <p class="text-xs text-gray-400">Live app alerts</p>
              </div>
              <button @click="notifications.push = !notifications.push"
                      :class="notifications.push ? 'bg-green-700' : 'bg-gray-200'"
                      class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                <span :class="notifications.push ? 'translate-x-6' : 'translate-x-1'"
                      class="inline-block h-4 w-4 rounded-full bg-white transition-transform shadow"></span>
              </button>
            </div>
          </div>
        </div>

        <!-- Data Print Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span class="text-green-600">??</span> Data Print
          </h2>

          <div class="mb-3">
            <label class="block text-xs text-gray-500 mb-1">Select Month (for Events &amp; Rooms)</label>
            <input v-model="printMonth" type="month"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
          </div>

          <div class="space-y-2 mt-4">
            <button @click="printEventsData"
                    :disabled="!printMonth"
                    :class="printMonth ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-green-100 text-green-400 cursor-not-allowed'"
                    class="w-full py-2.5 rounded-lg text-sm font-medium transition">
              Print Events Data
            </button>
            <button @click="printRoomsData"
                    :disabled="!printMonth"
                    :class="printMonth ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-green-100 text-green-400 cursor-not-allowed'"
                    class="w-full py-2.5 rounded-lg text-sm font-medium transition">
              Print Rooms Data
            </button>
            <button @click="printReceiptsData"
                    class="w-full py-2.5 rounded-lg text-sm font-medium bg-green-700 hover:bg-green-800 text-white transition">
              Print Receipts Data
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- Add Discount Modal -->
    <div v-if="showAddDiscount"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Add Discount</h3>
        <div class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input v-model="newDiscount.name" type="text" placeholder="e.g. Senior Citizen"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
              <select v-model="newDiscount.type"
                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                <option value="percentage">Percentage (%)</option>
                <option value="fixed">Fixed (?)</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
              <input v-model="newDiscount.value" type="number" min="0" placeholder="0"
                     class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
          </div>
        </div>
        <div class="flex gap-3 mt-5">
          <button @click="showAddDiscount = false"
                  class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
          <button @click="saveDiscount"
                  class="flex-1 bg-green-700 hover:bg-green-800 text-white py-2 rounded-lg text-sm font-medium transition">Add</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api.js'
import { useToastStore } from '../stores/toast'

const toast = useToastStore()

// -- Discounts confirm state ---------------------------------------------------
const discountToRemove = ref(null)

// -- Discounts ----------------------------------------------------------------
const discounts = ref([])
const defaultDiscountId = ref(null)
const showAddDiscount = ref(false)
const newDiscount = ref({ name: '', type: 'percentage', value: 0 })

async function fetchDiscounts() {
  try {
    const res = await api.get('/admin/discounts')
    discounts.value = res.data.data || []
    const def = discounts.value.find(d => d.is_default == 1)
    defaultDiscountId.value = def ? def.id : null
  } catch {}
}

async function saveDiscount() {
  if (!newDiscount.value.name.trim()) return toast.error('Name is required')
  try {
    await api.post('/admin/discounts', newDiscount.value)
    showAddDiscount.value = false
    newDiscount.value = { name: '', type: 'percentage', value: 0 }
    await fetchDiscounts()
    toast.success('Discount added')
  } catch { toast.error('Failed to add discount') }
}

async function removeDiscount(id) {
  discountToRemove.value = id
}

async function doRemoveDiscount() {
  const id = discountToRemove.value
  discountToRemove.value = null
  try {
    await api.delete(`/admin/discounts/${id}`)
    await fetchDiscounts()
    toast.success('Discount removed')
  } catch { toast.error('Failed to remove') }
}

async function setDefault(id) {
  try {
    await api.put(`/admin/discounts/${id}/default`, {})
    await fetchDiscounts()
    toast.success('Default discount updated')
  } catch { toast.error('Failed to set default') }
}

// -- Audit Logs ---------------------------------------------------------------
const auditLogs     = ref([])
const auditSearch   = ref('')
const auditEmployee = ref('')
const auditDateFrom = ref('')
const auditDateTo   = ref('')
const auditLoading  = ref(false)
const auditPage     = ref(1)
const AUDIT_PER_PAGE = 9

async function fetchAuditLogs() {
  auditLoading.value = true
  auditPage.value = 1
  try {
    const res = await api.get('/admin/audit-logs', {
      params: {
        search:    auditSearch.value,
        employee:  auditEmployee.value,
        date_from: auditDateFrom.value,
        date_to:   auditDateTo.value,
      }
    })
    auditLogs.value = res.data.data || []
  } catch { auditLogs.value = [] }
  auditLoading.value = false
}

let _auditDebounce = null
function debouncedFetchAuditLogs() {
  clearTimeout(_auditDebounce)
  _auditDebounce = setTimeout(fetchAuditLogs, 350)
}

function clearAuditFilters() {
  auditSearch.value   = ''
  auditEmployee.value = ''
  auditDateFrom.value = ''
  auditDateTo.value   = ''
  fetchAuditLogs()
}

const auditTotalPages = computed(() => Math.max(1, Math.ceil(auditLogs.value.length / AUDIT_PER_PAGE)))
const pagedAuditLogs  = computed(() => {
  const start = (auditPage.value - 1) * AUDIT_PER_PAGE
  return auditLogs.value.slice(start, start + AUDIT_PER_PAGE)
})
const auditFrom = computed(() => auditLogs.value.length === 0 ? 0 : (auditPage.value - 1) * AUDIT_PER_PAGE + 1)
const auditTo   = computed(() => Math.min(auditPage.value * AUDIT_PER_PAGE, auditLogs.value.length))

// -- Holidays -----------------------------------------------------------------
const holidays = ref([])
const newHolidayDate = ref('')

async function fetchHolidays() {
  try {
    const res = await api.get('/admin/holidays')
    holidays.value = res.data.data || []
  } catch {}
}

async function addHoliday() {
  if (!newHolidayDate.value) return toast.error('Please select a date')
  try {
    await api.post('/admin/holidays', { date: newHolidayDate.value })
    newHolidayDate.value = ''
    await fetchHolidays()
    toast.success('Holiday added')
  } catch { toast.error('Failed to add holiday') }
}

async function removeHoliday(id) {
  try {
    await api.delete(`/admin/holidays/${id}`)
    await fetchHolidays()
    toast.success('Holiday removed')
  } catch { toast.error('Failed to remove') }
}

// -- System Logs ---------------------------------------------------------------
const systemLogs = ref([])
const activeUsers = ref(0)

async function fetchSystemLogs() {
  try {
    const res = await api.get('/admin/system-logs')
    systemLogs.value = (res.data.data?.logs) || []
    activeUsers.value = res.data.data?.active_users ?? 0
  } catch {}
}

// -- General Settings ----------------------------------------------------------
const generalSettings = ref({
  hotel_name: '',
  hotel_email: '',
  hotel_phone: '',
  hotel_address: '',
  default_checkin_time: '14:00',
  default_checkout_time: '12:00',
  tax_rate: '12',
  currency: '?',
})
const generalSaving = ref(false)
const generalMsg = ref(null)

async function saveGeneralSettings() {
  generalSaving.value = true
  generalMsg.value = null
  try {
    await api.put('/admin/settings', {
      hotel_name:             generalSettings.value.hotel_name,
      hotel_email:            generalSettings.value.hotel_email,
      hotel_phone:            generalSettings.value.hotel_phone,
      hotel_address:          generalSettings.value.hotel_address,
      default_checkin_time:   generalSettings.value.default_checkin_time,
      default_checkout_time:  generalSettings.value.default_checkout_time,
      tax_rate:               String(generalSettings.value.tax_rate),
      currency:               generalSettings.value.currency,
    })
    generalMsg.value = { type: 'success', text: 'General settings saved successfully!' }
    toast.success('General settings saved')
  } catch (e) {
    generalMsg.value = { type: 'error', text: e.response?.data?.message || 'Failed to save settings' }
    toast.error('Failed to save general settings')
  } finally {
    generalSaving.value = false
    setTimeout(() => { generalMsg.value = null }, 4000)
  }
}

// -- Notifications -------------------------------------------------------------
const notifications = ref({ email: true, push: true })

// -- Data Print ----------------------------------------------------------------
const printMonth = ref('')

async function printEventsData() {
  if (!printMonth.value) return
  try {
    const res = await api.get('/admin/export', { params: { type: 'events', month: printMonth.value } })
    const rows = res.data.data || []
    printTable('Events Data — ' + printMonth.value, ['ID', 'Type', 'Venue', 'Date', 'Client', 'Amount'], rows.map(r => [
      r.id, r.event_type || r.order_type, r.venue || '—', r.event_date || r.created_at, r.event_client || r.customer_name || '—', '?' + parseFloat(r.total_amount || r.total_price || 0).toFixed(2)
    ]))
  } catch { toast.error('Failed to fetch events data') }
}

async function printRoomsData() {
  if (!printMonth.value) return
  try {
    const res = await api.get('/admin/export', { params: { type: 'reservations', month: printMonth.value } })
    const rows = res.data.data || []
    printTable('Rooms Data — ' + printMonth.value, ['ID', 'Guest', 'Room', 'Check-In', 'Check-Out', 'Status'], rows.map(r => [
      r.id, r.guest_name || r.user_id, r.room_number || r.room_id, r.check_in_date, r.check_out_date, r.status
    ]))
  } catch { toast.error('Failed to fetch rooms data') }
}

async function printReceiptsData() {
  try {
    const res = await api.get('/admin/audit-logs', {
      params: {
        search:    auditSearch.value,
        employee:  auditEmployee.value,
        date_from: auditDateFrom.value,
        date_to:   auditDateTo.value,
      }
    })
    const rows = res.data.data || []
    printTable('Sales Audit Log', ['Transaction ID', 'Date & Time', 'Customer', 'Employee', 'Payment Method', 'Amount', 'Type'], rows.map(r => [
      r.transaction_id || ('#' + r.id),
      r.created_at,
      r.name,
      r.employee || 'N/A',
      r.payment_method || 'N/A',
      '\u20b1' + parseFloat(r.sales || 0).toFixed(2),
      r.source
    ]))
  } catch { toast.error('Failed to fetch receipts data') }
}

function printTable(title, headers, rows) {
  const html = `
    <html><head><title>${title}</title>
    <style>
      body { font-family: Arial, sans-serif; padding: 24px; }
      h2 { margin-bottom: 16px; color: #1e3a5f; }
      table { width: 100%; border-collapse: collapse; font-size: 13px; }
      th { background: #1d4ed8; color: white; padding: 8px 12px; text-align: left; }
      td { padding: 7px 12px; border-bottom: 1px solid #e5e7eb; }
      tr:nth-child(even) td { background: #f8fafc; }
    </style></head>
    <body>
      <h2>${title}</h2>
      <table>
        <thead><tr>${headers.map(h => `<th>${h}</th>`).join('')}</tr></thead>
        <tbody>${rows.map(r => `<tr>${r.map(c => `<td>${c ?? '—'}</td>`).join('')}</tr>`).join('')}</tbody>
      </table>
    </body></html>`
  const w = window.open('', '_blank')
  w.document.write(html)
  w.document.close()
  w.print()
}

// -- Save Settings -------------------------------------------------------------
async function saveSettings() {
  try {
    if (defaultDiscountId.value !== null) {
      await api.put(`/admin/discounts/${defaultDiscountId.value}/default`, {})
    }
    await api.put('/admin/settings', {
      email_notifications: notifications.value.email ? '1' : '0',
      push_notifications: notifications.value.push ? '1' : '0',
    })
    toast.success('Settings saved successfully')
  } catch { toast.error('Failed to save settings') }
}

// -- Helpers -------------------------------------------------------------------
function formatDate(dt) {
  if (!dt) return '—'
  const d = new Date(dt)
  return d.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' }) +
    ' ' + d.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit' })
}

function formatDateOnly(dt) {
  if (!dt) return '—'
  const d = new Date(dt)
  return d.toLocaleDateString('en-PH', { month: 'long', day: 'numeric', year: 'numeric' })
}

function formatTime(dt) {
  if (!dt) return '—'
  const d = new Date(dt)
  return d.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
}

// -- Init ----------------------------------------------------------------------
onMounted(async () => {
  await Promise.all([fetchDiscounts(), fetchAuditLogs(), fetchHolidays(), fetchSystemLogs()])

  // Load notification settings
  try {
    const res = await api.get('/admin/settings')
    const settings = res.data.data || []
    for (const s of settings) {
      if (s.key === 'email_notifications') notifications.value.email = s.value === '1'
      if (s.key === 'push_notifications')  notifications.value.push  = s.value === '1'
      // General settings
      if (s.key in generalSettings.value) {
        generalSettings.value[s.key] = s.value ?? generalSettings.value[s.key]
      }
    }
  } catch {}
})
</script>
<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>