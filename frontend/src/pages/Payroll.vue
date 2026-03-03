<template>
  <div class="min-h-screen bg-gray-50 font-light">

    <!-- Header -->
    <div class="bg-white border-b border-gray-100 px-8 py-5">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-normal text-gray-800">Payroll</h1>
          <p class="text-sm text-gray-400 mt-0.5">Weekly hours worked and pay summary</p>
        </div>
        <!-- Week navigator -->
        <div class="flex items-center gap-2">
          <button @click="prevWeek" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-500 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
          <div class="text-sm text-gray-700 px-3 py-1.5 bg-white border border-gray-200 rounded-lg min-w-[190px] text-center">
            {{ formatWeekLabel(weekStart) }}
          </div>
          <button @click="nextWeek" :disabled="isCurrentWeek" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-500 transition disabled:opacity-30 disabled:cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
          <button v-if="!isCurrentWeek" @click="goToCurrentWeek" class="text-xs text-green-800 hover:underline px-2">Today</button>
        </div>
      </div>
    </div>

    <div class="px-8 py-6 max-w-7xl mx-auto">

      <!-- Summary cards -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-gray-100 rounded-xl p-4">
          <p class="text-2xl font-normal text-gray-800">{{ summary.staffCount }}</p>
          <p class="text-xs text-gray-400 mt-0.5">Staff Members</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4">
          <p class="text-2xl font-normal text-gray-800">{{ summary.totalHours }}h</p>
          <p class="text-xs text-gray-400 mt-0.5">Total Hours Worked</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4">
          <p class="text-2xl font-normal text-gray-800">{{ summary.staffPresent }}</p>
          <p class="text-xs text-gray-400 mt-0.5">Staff with Hours</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4">
          <p class="text-2xl font-normal text-green-800">₱{{ formatMoney(summary.totalPay) }}</p>
          <p class="text-xs text-gray-400 mt-0.5">Total Weekly Pay</p>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex items-center justify-center py-24">
        <div class="w-8 h-8 border-2 border-blue-700 border-t-transparent rounded-full animate-spin"></div>
      </div>

      <!-- Table -->
      <div v-else class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="overflow-x-auto -mx-px">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
              <th class="text-left text-xs text-gray-400 font-normal px-5 py-3">Employee</th>
              <th class="text-left text-xs text-gray-400 font-normal px-5 py-3">Role</th>
              <th class="text-left text-xs text-gray-400 font-normal px-5 py-3">Department</th>
              <th class="text-right text-xs text-gray-400 font-normal px-5 py-3">Days</th>
              <th class="text-right text-xs text-gray-400 font-normal px-5 py-3">Hours Worked</th>
              <th class="text-right text-xs text-gray-400 font-normal px-5 py-3">Rate / hr</th>
              <th class="text-right text-xs text-gray-400 font-normal px-5 py-3">Total Pay</th>
              <th class="text-center text-xs text-gray-400 font-normal px-5 py-3">Payslip</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="!payrollData.length">
              <td colspan="8" class="text-center py-16 text-gray-400 text-sm">No staff data found.</td>
            </tr>
            <tr
              v-for="row in payrollData"
              :key="row.user_id"
              class="hover:bg-gray-50/60 transition"
            >
              <!-- Employee -->
              <td class="px-5 py-3.5">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                    <span class="text-green-800 text-xs font-normal">{{ initials(row.name) }}</span>
                  </div>
                  <div>
                    <p class="text-gray-800 font-normal text-sm">{{ row.name }}</p>
                    <p class="text-xs text-gray-400">{{ row.email }}</p>
                  </div>
                </div>
              </td>
              <!-- Role -->
              <td class="px-5 py-3.5">
                <span :class="roleBadge(row.role)" class="text-xs px-2 py-0.5 rounded-full capitalize">{{ row.role }}</span>
              </td>
              <!-- Department -->
              <td class="px-5 py-3.5 text-gray-500 text-sm">{{ row.department || '—' }}</td>
              <!-- Days -->
              <td class="px-5 py-3.5 text-right text-gray-700">{{ row.days_present }}</td>
              <!-- Hours -->
              <td class="px-5 py-3.5 text-right">
                <span :class="row.hours_worked > 0 ? 'text-gray-800 font-normal' : 'text-gray-300'">
                  {{ row.hours_worked }}h
                </span>
              </td>
              <!-- Rate (editable) -->
              <td class="px-5 py-3.5 text-right">
                <div v-if="editingRateId === row.user_id" class="flex items-center justify-end gap-1.5">
                  <span class="text-gray-400 text-xs">₱</span>
                  <input
                    v-model.number="editingRateValue"
                    type="number"
                    min="0"
                    step="0.01"
                    class="w-20 border border-green-300 rounded px-2 py-1 text-sm text-right focus:outline-none focus:ring-1 focus:ring-blue-400"
                    @keyup.enter="saveRate(row)"
                    @keyup.esc="cancelEditRate"
                    autofocus
                  />
                  <button @click="saveRate(row)" class="text-green-600 hover:text-green-700 text-xs font-normal">✓</button>
                  <button @click="cancelEditRate" class="text-gray-400 hover:text-gray-600 text-xs">✕</button>
                </div>
                <div v-else class="flex items-center justify-end gap-1 group cursor-pointer" @click="startEditRate(row)">
                  <span :class="row.hourly_rate > 0 ? 'text-gray-700' : 'text-gray-300'">
                    ₱{{ formatMoney(row.hourly_rate) }}
                  </span>
                  <svg class="w-3 h-3 text-gray-300 group-hover:text-amber-600 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a4 4 0 01-1.414.93l-3 1a1 1 0 01-1.273-1.273l1-3a4 4 0 01.93-1.414z"/>
                  </svg>
                </div>
              </td>
              <!-- Total Pay -->
              <td class="px-5 py-3.5 text-right">
                <span :class="row.total_pay > 0 ? 'text-green-800 font-normal' : 'text-gray-300'">
                  ₱{{ formatMoney(row.total_pay) }}
                </span>
                <div v-if="row.paid_leave_days > 0" class="text-xs text-green-600 mt-0.5">
                  incl. {{ row.paid_leave_days }}d paid leave
                </div>
                <div v-if="row.unpaid_leave_days > 0" class="text-xs text-orange-500 mt-0.5">
                  {{ row.unpaid_leave_days }}d unpaid leave
                </div>
              </td>
              <!-- Payslip -->
              <td class="px-5 py-3.5 text-center">
                <button
                  @click="downloadPayslip(row)"
                  :disabled="row.total_pay <= 0"
                  class="text-xs text-green-800 border border-green-200 bg-green-50 px-3 py-1.5 rounded-lg hover:bg-green-100 transition disabled:opacity-30 disabled:cursor-not-allowed"
                >
                  <svg class="w-3.5 h-3.5 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                  </svg>
                  Download
                </button>
              </td>
            </tr>
          </tbody>
          <!-- Footer totals -->
          <tfoot v-if="payrollData.length">
            <tr class="border-t-2 border-gray-100 bg-gray-50/80">
              <td colspan="4" class="px-5 py-3 text-xs text-gray-400">Total</td>
              <td class="px-5 py-3 text-right text-sm font-normal text-gray-700">{{ summary.totalHours }}h</td>
              <td class="px-5 py-3"></td>
              <td class="px-5 py-3 text-right text-sm font-normal text-green-800">₱{{ formatMoney(summary.totalPay) }}</td>
              <td class="px-5 py-3"></td>
            </tr>
          </tfoot>
        </table></div>
      </div>

      <p class="text-xs text-gray-400 mt-3">
        Click the pencil icon next to a rate to edit it. Changes are saved immediately and applied to future payslips.
      </p>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'
import { useToastStore } from '../stores/toast'

const toast = useToastStore()

// --- Week state -----------------------------------------------
function getMondayOf(dateStr) {
  const d = new Date(dateStr)
  const day = d.getDay() // 0=Sun
  const diff = day === 0 ? -6 : 1 - day
  d.setDate(d.getDate() + diff)
  return d.toISOString().slice(0, 10)
}

const today = new Date().toISOString().slice(0, 10)
const weekStart = ref(getMondayOf(today))

const isCurrentWeek = computed(() => weekStart.value === getMondayOf(today))

function prevWeek() {
  const d = new Date(weekStart.value)
  d.setDate(d.getDate() - 7)
  weekStart.value = d.toISOString().slice(0, 10)
  fetchPayroll()
}

function nextWeek() {
  if (isCurrentWeek.value) return
  const d = new Date(weekStart.value)
  d.setDate(d.getDate() + 7)
  weekStart.value = d.toISOString().slice(0, 10)
  fetchPayroll()
}

function goToCurrentWeek() {
  weekStart.value = getMondayOf(today)
  fetchPayroll()
}

function formatWeekLabel(ws) {
  const start = new Date(ws)
  const end = new Date(ws)
  end.setDate(end.getDate() + 6)
  const opts = { month: 'short', day: 'numeric' }
  return `${start.toLocaleDateString('en-US', opts)} – ${end.toLocaleDateString('en-US', { ...opts, year: 'numeric' })}`
}

// --- Data -----------------------------------------------------
const loading = ref(false)
const payrollData = ref([])

const summary = computed(() => ({
  staffCount: payrollData.value.length,
  staffPresent: payrollData.value.filter(r => r.hours_worked > 0).length,
  totalHours: payrollData.value.reduce((s, r) => s + r.hours_worked, 0).toFixed(2),
  totalPay: payrollData.value.reduce((s, r) => s + r.total_pay, 0),
}))

async function fetchPayroll() {
  loading.value = true
  try {
    const res = await api.get('/admin/payroll/weekly', { params: { week_start: weekStart.value } })
    payrollData.value = res.data?.data?.staff ?? []
  } catch {
    payrollData.value = []
  } finally {
    loading.value = false
  }
}

onMounted(fetchPayroll)

// --- Edit Rate ------------------------------------------------
const editingRateId = ref(null)
const editingRateValue = ref(0)

function startEditRate(row) {
  editingRateId.value = row.user_id
  editingRateValue.value = row.hourly_rate
}

function cancelEditRate() {
  editingRateId.value = null
}

async function saveRate(row) {
  try {
    await api.post('/admin/payroll/rate', { user_id: row.user_id, rate: editingRateValue.value })
    row.hourly_rate = editingRateValue.value
    row.total_pay = parseFloat((row.hours_worked * row.hourly_rate).toFixed(2))
    editingRateId.value = null
  } catch (e) {
    toast.error(e?.response?.data?.message || 'Failed to update rate')
  }
}

// --- Payslip --------------------------------------------------
function downloadPayslip(row) {
  const weekEnd = (() => {
    const d = new Date(weekStart.value)
    d.setDate(d.getDate() + 6)
    return d.toISOString().slice(0, 10)
  })()

  const grossPay       = row.total_pay
  const wagesOnly      = Math.round((row.hours_worked * row.hourly_rate) * 100) / 100
  const sssContrib     = calcSSS(grossPay)
  const lateDeduction  = row.late_deduction || 0
  const totalDeductions = Math.round((sssContrib + lateDeduction) * 100) / 100
  const netPay         = Math.max(0, Math.round((grossPay - totalDeductions) * 100) / 100)

  const html = `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <title>Payslip – ${row.name}</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; font-size:13px; color:#333; padding:40px; background:#fff; }
    .header { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:2px solid #1d4ed8; padding-bottom:20px; margin-bottom:28px; }
    .hotel-name { font-size:22px; font-weight:600; color:#1d4ed8; letter-spacing:-0.5px; }
    .hotel-sub  { font-size:11px; color:#888; margin-top:3px; }
    .slip-title { font-size:14px; font-weight:600; color:#555; text-align:right; }
    .slip-ref   { font-size:11px; color:#aaa; margin-top:4px; text-align:right; }
    .section    { margin-bottom:22px; }
    .section h3 { font-size:10px; text-transform:uppercase; letter-spacing:1px; color:#aaa; margin-bottom:10px; }
    .grid2 { display:grid; grid-template-columns:1fr 1fr; gap:10px 40px; }
    .field label { font-size:10px; color:#aaa; display:block; margin-bottom:2px; }
    .field value, .field span { font-size:13px; color:#333; }
    .table { width:100%; border-collapse:collapse; margin-top:4px; }
    .table th { text-align:left; font-size:10px; text-transform:uppercase; letter-spacing:0.8px; color:#aaa; border-bottom:1px solid #eee; padding:6px 0; }
    .table td { padding:8px 0; border-bottom:1px solid #f5f5f5; font-size:13px; }
    .table td:last-child, .table th:last-child { text-align:right; }
    .total-row td { font-weight:700; font-size:15px; border-top:2px solid #1d4ed8; color:#1d4ed8; border-bottom:none; padding-top:14px; }
    .gross-row td { font-weight:600; font-size:13px; border-top:1px solid #e5e7eb; color:#374151; border-bottom:2px solid #e5e7eb; padding-top:8px; padding-bottom:8px; }
    .deduct-row td { color:#92400e; }
    .deduct-total-row td { font-weight:600; font-size:13px; border-top:1px solid #fde68a; color:#b45309; border-bottom:none; padding-top:8px; }
    .footer { margin-top:40px; border-top:1px solid #eee; padding-top:16px; display:flex; justify-content:space-between; }
    .sig-line { width:160px; border-top:1px solid #ccc; padding-top:6px; font-size:10px; color:#aaa; text-align:center; }
    @media print {
      body { padding:20px; }
      @page { margin:15mm; }
    }
  </style>
</head>
<body>
  <div class="header">
    <div>
      <div class="hotel-name">Joanna's Nook Bed & Breakfast</div>
      <div class="hotel-sub">Payroll Department</div>
    </div>
    <div>
      <div class="slip-title">PAYSLIP</div>
      <div class="slip-ref">Week of ${weekStart.value}</div>
    </div>
  </div>

  <div class="section">
    <h3>Employee Details</h3>
    <div class="grid2">
      <div class="field"><label>Full Name</label><span>${row.name}</span></div>
      <div class="field"><label>Email</label><span>${row.email}</span></div>
      <div class="field"><label>Role</label><span style="text-transform:capitalize">${row.role}</span></div>
      <div class="field"><label>Department</label><span>${row.department || '—'}</span></div>
    </div>
  </div>

  <div class="section">
    <h3>Pay Period</h3>
    <div class="grid2">
      <div class="field"><label>Week Start</label><span>${weekStart.value}</span></div>
      <div class="field"><label>Week End</label><span>${weekEnd}</span></div>
    </div>
  </div>

  <div class="section">
    <h3>Earnings</h3>
    <table class="table">
      <thead>
        <tr>
          <th>Description</th>
          <th style="text-align:right">Days</th>
          <th style="text-align:right">Hours</th>
          <th style="text-align:right">Rate / hr</th>
          <th style="text-align:right">Amount</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Weekly Wages</td>
          <td style="text-align:right">${row.days_present}d</td>
          <td style="text-align:right">${row.hours_worked}h</td>
          <td style="text-align:right">₱${formatMoney(row.hourly_rate)}</td>
          <td style="text-align:right">₱${formatMoney(wagesOnly)}</td>
        </tr>
        ${row.paid_leave_days > 0 ? `
        <tr>
          <td>Paid Leave <span style="font-size:11px;color:#6b7280">(${row.paid_leave_days} day${row.paid_leave_days !== 1 ? 's' : ''} × 8h @ ₱${formatMoney(row.hourly_rate)}/hr)</span></td>
          <td colspan="3"></td>
          <td style="text-align:right">₱${formatMoney(row.leave_pay)}</td>
        </tr>` : ''}
        ${row.unpaid_leave_days > 0 ? `
        <tr style="color:#92400e">
          <td>Unpaid Leave <span style="font-size:11px">(${row.unpaid_leave_days} day${row.unpaid_leave_days !== 1 ? 's' : ''} — no pay)</span></td>
          <td colspan="3"></td>
          <td style="text-align:right">—</td>
        </tr>` : ''}
        <tr class="gross-row">
          <td colspan="4">Gross Pay</td>
          <td style="text-align:right">₱${formatMoney(grossPay)}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="section">
    <h3>Deductions</h3>
    <table class="table">
      <thead>
        <tr>
          <th>Description</th>
          <th style="text-align:right">Amount</th>
        </tr>
      </thead>
      <tbody>
        <tr class="deduct-row">
          <td>SSS Contribution (Employee Share, 4.5%)</td>
          <td style="text-align:right">–&#8369;${formatMoney(sssContrib)}</td>
        </tr>
        <tr class="deduct-row">
          <td>Late Deduction${lateDeduction > 0 ? ' (' + row.total_late_minutes + ' min late)' : ''}</td>
          <td style="text-align:right">${lateDeduction > 0 ? '–&#8369;' + formatMoney(lateDeduction) : '—'}</td>
        </tr>
        <tr class="deduct-total-row">
          <td>Total Deductions</td>
          <td style="text-align:right">–&#8369;${formatMoney(totalDeductions)}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="section">
    <table class="table">
      <tbody>
        <tr class="total-row">
          <td>NET PAY</td>
          <td style="text-align:right">&#8369;${formatMoney(netPay)}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="footer">
    <div class="sig-line">Prepared By</div>
    <div class="sig-line">Received By</div>
  </div>
</body>
</html>`

  const win = window.open('', '_blank', 'width=700,height=900')
  win.document.write(html)
  win.document.close()
  win.focus()
  setTimeout(() => win.print(), 400)
}

// --- Helpers --------------------------------------------------
function formatMoney(n) {
  return Number(n || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

/**
 * Philippine SSS 2024 employee contribution (4.5% of Monthly Salary Credit).
 * Input: weeklyGross. Returns the weekly SSS deduction.
 * MSC brackets: ₱4,000 – ₱30,000 in ₱500 increments.
 */
function calcSSS(weeklyGross) {
  if (!weeklyGross || weeklyGross <= 0) return 0
  const monthly = weeklyGross * 52 / 12
  // Clamp and round up to nearest ₱500 bracket for MSC
  const msc = Math.min(Math.max(Math.ceil(monthly / 500) * 500, 4000), 30000)
  // Employee share = 4.5% of MSC, rounded to nearest ₱22.50
  const monthlySSS = Math.round(msc * 0.045 / 22.5) * 22.5
  // Convert back to weekly equivalent
  return Math.round((monthlySSS * 12 / 52) * 100) / 100
}

function initials(name) {
  return name?.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase() ?? '?'
}

function roleBadge(role) {
  const map = {
    admin:       'bg-purple-50 text-purple-700',
    manager:     'bg-green-50 text-green-800',
    front_desk:  'bg-cyan-50 text-cyan-700',
    housekeeping:'bg-green-50 text-green-700',
    chef:        'bg-orange-50 text-orange-700',
    accountant:  'bg-yellow-50 text-yellow-700',
  }
  return map[role] ?? 'bg-gray-100 text-gray-600'
}
</script>
