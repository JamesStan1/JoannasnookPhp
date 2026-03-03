<template>
  <div class="min-h-screen bg-gray-50 p-6">

    <!-- Toast -->
    <div v-if="toast.show"
         class="fixed top-5 right-5 z-50 flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg text-white text-sm font-medium"
         :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'">
      <span>{{ toast.type === 'success' ? '✓' : '✕' }}</span>
      {{ toast.message }}
    </div>

    <!-- Page Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
        <span class="text-green-600">📁</span> Export Data
      </h1>
      <p class="text-sm text-gray-500 mt-1">Print or export hotel records by category and month.</p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-xl">
      <h2 class="text-base font-bold text-gray-800 flex items-center gap-2 mb-4">
        <span class="text-green-600">📁</span> Data Print
      </h2>

      <div class="mb-4">
        <label class="block text-sm text-gray-500 mb-1">Select Month (for Events &amp; Rooms)</label>
        <input v-model="printMonth" type="month"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
      </div>

      <div class="space-y-2">
        <button @click="printEventsData"
                :disabled="!printMonth"
                :class="printMonth ? 'bg-green-500 hover:bg-green-600 text-white' : 'bg-green-100 text-green-400 cursor-not-allowed'"
                class="w-full py-2.5 rounded-lg text-sm font-medium transition">
          Print Events Data
        </button>
        <button @click="printRoomsData"
                :disabled="!printMonth"
                :class="printMonth ? 'bg-green-500 hover:bg-green-600 text-white' : 'bg-green-100 text-green-400 cursor-not-allowed'"
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
</template>

<script setup>
import { ref } from 'vue'
import api from '../services/api.js'

const toast = ref({ show: false, message: '', type: 'success' })
function showToast(message, type = 'success') {
  toast.value = { show: true, message, type }
  setTimeout(() => toast.value.show = false, 3000)
}

const printMonth = ref('')

async function printEventsData() {
  if (!printMonth.value) return
  try {
    const res = await api.get('/admin/export', { params: { type: 'events', month: printMonth.value } })
    const rows = res.data.data || []
    if (!rows.length) { showToast('No events found for the selected month', 'error'); return }
    printTable('Events Data — ' + printMonth.value,
      ['Ref #', 'Event Type', 'Client Name', 'Contact', 'Venue', 'Event Date', 'Time', 'Guests', 'Price/Head', 'Total Amount', 'Down Payment', 'Balance', 'Status'],
      rows.map(r => [
        r.reference_number || ('EVT-' + String(r.id).padStart(5, '0')),
        r.event_type || '—',
        r.client_name || '—',
        r.client_phone || '—',
        r.venue || '—',
        r.event_date || '—',
        r.event_time || '—',
        r.number_of_guests ?? '—',
        r.price_per_head != null ? '₱' + parseFloat(r.price_per_head).toFixed(2) : '—',
        '₱' + parseFloat(r.total_amount || 0).toFixed(2),
        '₱' + parseFloat(r.down_payment || 0).toFixed(2),
        '₱' + parseFloat(r.remaining_balance || 0).toFixed(2),
        r.status || '—'
      ])
    )
  } catch (e) { showToast('Failed to fetch events data: ' + (e.response?.data?.message || e.message || 'Unknown error'), 'error') }
}

async function printRoomsData() {
  if (!printMonth.value) return
  try {
    const res = await api.get('/admin/export', { params: { type: 'reservations', month: printMonth.value } })
    const rows = res.data.data || []
    if (!rows.length) { showToast('No reservations found for the selected month', 'error'); return }
    printTable('Rooms / Reservations Data — ' + printMonth.value,
      ['Ref #', 'Guest Name', 'Email', 'Room #', 'Room Type', 'Rate/Night', 'Check-In', 'Check-Out', 'Nights', 'Total Amount', 'Down Payment', 'Balance', 'Status'],
      rows.map(r => [
        r.reference_number || ('RES-' + String(r.id).padStart(5, '0')),
        r.guest_name || '—',
        r.guest_email || '—',
        r.room_number || r.room_id || '—',
        r.room_type || '—',
        r.room_price != null ? '₱' + parseFloat(r.room_price).toFixed(2) : '—',
        r.check_in_date || '—',
        r.check_out_date || '—',
        r.nights ?? '—',
        '₱' + parseFloat(r.total_amount || 0).toFixed(2),
        '₱' + parseFloat(r.down_payment || 0).toFixed(2),
        '₱' + parseFloat(r.remaining_balance || 0).toFixed(2),
        r.status || '—'
      ])
    )
  } catch (e) { showToast('Failed to fetch rooms data: ' + (e.response?.data?.message || e.message || 'Unknown error'), 'error') }
}

async function printReceiptsData() {
  try {
    const res = await api.get('/admin/audit-logs', { params: { search: '' } })
    const rows = res.data.data || []
    if (!rows.length) { showToast('No receipt records found', 'error'); return }
    printTable('Receipts Data',
      ['Created', 'Name', 'Items', 'Sales', 'Source'],
      rows.map(r => [
        r.created_at,
        r.name,
        r.items,
        '₱' + parseFloat(r.sales || 0).toFixed(2),
        r.source
      ])
    )
  } catch (e) { showToast('Failed to fetch receipts data: ' + (e.response?.data?.message || e.message || 'Unknown error'), 'error') }
}

function printTable(title, headers, rows) {
  const html = `
    <html><head><title>${title}</title>
    <style>
      @page { size: A4 landscape; margin: 15mm; }
      body { font-family: Arial, sans-serif; padding: 16px; font-size: 12px; }
      h2 { margin-bottom: 12px; color: #1e3a5f; font-size: 16px; }
      p.meta { color: #6b7280; margin-bottom: 12px; font-size: 11px; }
      .table-wrap { overflow-x: auto; }
      table { width: 100%; border-collapse: collapse; font-size: 11px; }
      th { background: #1d4ed8; color: white; padding: 7px 10px; text-align: left; white-space: nowrap; }
      td { padding: 6px 10px; border-bottom: 1px solid #e5e7eb; white-space: nowrap; }
      tr:nth-child(even) td { background: #f8fafc; }
      .total-row { font-weight: bold; background: #eff6ff; }
      @media print { button { display: none; } }
    </style></head>
    <body>
      <h2>${title}</h2>
      <p class="meta">Generated: ${new Date().toLocaleString()} &nbsp;|&nbsp; Total records: ${rows.length}</p>
      <div class="table-wrap">
      <table>
        <thead><tr>${headers.map(h => `<th>${h}</th>`).join('')}</tr></thead>
        <tbody>${rows.map(r => `<tr>${r.map(c => `<td>${c ?? '—'}</td>`).join('')}</tr>`).join('')}</tbody>
      </table></div>
    </body></html>`
  const w = window.open('', '_blank')
  if (!w) { showToast('Pop-up blocked. Please allow pop-ups for this site.', 'error'); return }
  w.document.write(html)
  w.document.close()
  w.print()
}
</script>
