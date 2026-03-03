<template>
  <div class="min-h-screen bg-gray-100 p-6">
    <!-- Header card -->
    <div class="bg-white rounded-xl shadow-sm p-5 mb-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Title -->
        <div>
          <h1 class="text-xl font-semibold text-green-800">Inventory</h1>
          <p class="text-sm text-gray-500 font-light mt-0.5">Manage stock, view activity, and archive items</p>
        </div>
        <!-- Actions -->
        <div class="flex items-center gap-2 flex-wrap">
          <!-- Search -->
          <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input v-model="search" @input="fetchItems" type="text" placeholder="Search items..."
              class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200 bg-gray-50 w-52"/>
          </div>
          <!-- Add -->
          <button @click="openAdd"
            class="flex items-center gap-1.5 bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add
          </button>
          <!-- Activity -->
          <button @click="openActivity"
            class="flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Activity
          </button>
          <!-- Withdraw -->
          <button @click="openWithdraw"
            class="flex items-center gap-1.5 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Withdraw
          </button>
        </div>
      </div>

      <!-- Category tabs -->
      <div class="flex gap-2 mt-5 flex-wrap">
        <button v-for="cat in categories" :key="cat"
          @click="activeCategory = cat; fetchItems()"
          :class="[
            'px-5 py-2 rounded-lg text-sm font-medium transition-colors border',
            activeCategory === cat
              ? 'bg-green-700 text-white border-blue-600'
              : 'bg-white text-gray-600 border-gray-200 hover:border-green-300 hover:text-amber-600'
          ]">
          {{ cat }}
        </button>
      </div>
    </div>

    <!-- Table card -->
    <div class="bg-white rounded-xl shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100">
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <span class="flex items-center gap-1 cursor-pointer select-none hover:text-gray-700" @click="toggleSort">
                  Name
                  <svg class="w-3 h-3 transition-transform" :class="sortAsc ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                  </svg>
                </span>
              </th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Category</th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Quantity</th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Unit</th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Threshold</th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
              <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">On Delivery</th>
              <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="8" class="px-5 py-12 text-center text-gray-400 font-light">
                <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-green-500" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                Loading inventory...
              </td>
            </tr>
            <tr v-else-if="sortedItems.length === 0">
              <td colspan="8" class="px-5 py-14 text-center">
                <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p class="text-sm text-gray-400 font-light">No items found.</p>
              </td>
            </tr>
            <tr v-for="item in sortedItems" :key="item.id"
              class="border-t border-gray-50 hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3.5 font-medium text-amber-600 cursor-pointer hover:underline" @click="viewItem(item)">
                {{ item.name }}
              </td>
              <td class="px-5 py-3.5 font-light text-gray-600">{{ item.category }}</td>
              <td class="px-5 py-3.5 font-light text-gray-800">{{ item.current_stock }}</td>
              <td class="px-5 py-3.5 font-light text-gray-600">{{ item.unit || 'piece' }}</td>
              <td class="px-5 py-3.5 font-light text-gray-600">{{ item.minimum_stock }}</td>
              <td class="px-5 py-3.5">
                <span :class="statusBadge(item.status)"
                  class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap">
                  {{ item.status }}
                </span>
              </td>
              <td class="px-5 py-3.5 font-light text-gray-600">{{ item.on_delivery ? 'Yes' : 'No' }}</td>
              <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-2">
                  <!-- View -->
                  <button @click="viewItem(item)" title="View"
                    class="p-1.5 rounded-lg text-green-600 hover:bg-green-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                  <!-- Edit -->
                  <button @click="openEdit(item)" title="Edit"
                    class="p-1.5 rounded-lg text-orange-400 hover:bg-orange-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </button>
                  <!-- Delete -->
                  <button @click="deletePrompt(item)" title="Delete"
                    class="p-1.5 rounded-lg text-red-400 hover:bg-red-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ===================== ADD / EDIT MODAL ===================== -->
    <div v-if="showFormModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white rounded-t-2xl">
          <h3 class="text-base font-semibold text-green-800">{{ editingId ? 'Edit Item' : 'Add Inventory Item' }}</h3>
          <button @click="closeModals" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div class="p-6 space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Item Name <span class="text-red-500">*</span></label>
              <input v-model="form.name" type="text" placeholder="e.g. Towel"
                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200"/>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Category <span class="text-red-500">*</span></label>
              <select v-model="form.category"
                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-light bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                <option value="">Select category</option>
                <option v-for="c in itemCategories" :key="c" :value="c">{{ c }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Quantity <span class="text-red-500">*</span></label>
              <input v-model.number="form.quantity" type="number" min="0" placeholder="0"
                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200"/>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Unit</label>
              <input v-model="form.unit" type="text" placeholder="piece, kg, box, liter..."
                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200"/>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Threshold (Low Stock Alert)</label>
              <input v-model.number="form.minimum_stock" type="number" min="0" placeholder="5"
                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200"/>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Unit Price (&#x20B1;)</label>
              <input v-model.number="form.unit_price" type="number" min="0" step="0.01" placeholder="0.00"
                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200"/>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Supplier</label>
              <input v-model="form.supplier" type="text" placeholder="Supplier name"
                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200"/>
            </div>
            <div class="flex items-center gap-3 pt-5">
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="form.on_delivery" class="sr-only peer"/>
                <div class="w-10 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-200 rounded-full peer peer-checked:bg-green-700 transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
              </label>
              <span class="text-sm font-light text-gray-600">On Delivery</span>
            </div>
          </div>
          <p v-if="formError" class="text-sm text-red-500 font-light">{{ formError }}</p>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 sticky bottom-0 bg-white rounded-b-2xl">
          <button @click="closeModals" class="px-5 py-2.5 border border-gray-200 rounded-lg text-sm font-light text-gray-600 hover:bg-gray-50">Cancel</button>
          <button @click="submitForm" :disabled="saving"
            class="px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
            {{ saving ? 'Saving...' : (editingId ? 'Save Changes' : 'Add Item') }}
          </button>
        </div>
      </div>
    </div>

    <!-- ===================== VIEW MODAL ===================== -->
    <div v-if="showViewModal && viewingItem" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <h3 class="text-base font-semibold text-green-800">Item Details</h3>
          <button @click="showViewModal = false" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div class="p-6 space-y-4 text-sm">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">{{ viewingItem.name }}</h2>
            <span :class="statusBadge(viewingItem.status)" class="px-3 py-1 rounded-full text-xs font-medium">{{ viewingItem.status }}</span>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div><p class="text-xs text-gray-400 font-light mb-0.5">Category</p><p class="font-light text-gray-700">{{ viewingItem.category }}</p></div>
            <div><p class="text-xs text-gray-400 font-light mb-0.5">Quantity</p><p class="font-semibold text-gray-800">{{ viewingItem.current_stock }} {{ viewingItem.unit || 'piece' }}</p></div>
            <div><p class="text-xs text-gray-400 font-light mb-0.5">Threshold</p><p class="font-light text-gray-700">{{ viewingItem.minimum_stock }}</p></div>
            <div><p class="text-xs text-gray-400 font-light mb-0.5">Unit Price</p><p class="font-light text-gray-700">&#x20B1;{{ formatMoney(viewingItem.unit_price) }}</p></div>
            <div><p class="text-xs text-gray-400 font-light mb-0.5">Supplier</p><p class="font-light text-gray-700">{{ viewingItem.supplier || 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-400 font-light mb-0.5">On Delivery</p><p class="font-light text-gray-700">{{ viewingItem.on_delivery ? 'Yes' : 'No' }}</p></div>
            <div><p class="text-xs text-gray-400 font-light mb-0.5">Total Value</p><p class="font-semibold text-green-800">&#x20B1;{{ formatMoney(viewingItem.current_stock * viewingItem.unit_price) }}</p></div>
            <div><p class="text-xs text-gray-400 font-light mb-0.5">Last Updated</p><p class="font-light text-gray-600 text-xs">{{ viewingItem.last_updated ? formatDateTime(viewingItem.last_updated) : 'N/A' }}</p></div>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
          <button @click="openEdit(viewingItem); showViewModal = false"
            class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-light text-gray-600 hover:bg-gray-50">Edit</button>
          <button @click="showViewModal = false"
            class="px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-medium">Close</button>
        </div>
      </div>
    </div>

    <!-- ===================== WITHDRAW MODAL ===================== -->
    <div v-if="showWithdrawModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <h3 class="text-base font-semibold text-orange-600">Withdraw Stock</h3>
          <button @click="closeModals" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Select Item <span class="text-red-500">*</span></label>
            <select v-model="withdrawForm.itemId"
              class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-light bg-white focus:outline-none focus:ring-2 focus:ring-orange-200">
              <option value="">Select an item</option>
              <option v-for="item in allItems" :key="item.id" :value="item.id">
                {{ item.name }} ({{ item.current_stock }} {{ item.unit || 'pcs' }})
              </option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Quantity <span class="text-red-500">*</span></label>
            <input v-model.number="withdrawForm.quantity" type="number" min="1" placeholder="0"
              class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-orange-200"/>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
            <textarea v-model="withdrawForm.notes" rows="2" placeholder="Reason for withdrawal..."
              class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-orange-200 resize-none"/>
          </div>
          <p v-if="formError" class="text-sm text-red-500 font-light">{{ formError }}</p>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
          <button @click="closeModals" class="px-5 py-2.5 border border-gray-200 rounded-lg text-sm font-light text-gray-600 hover:bg-gray-50">Cancel</button>
          <button @click="submitWithdraw" :disabled="saving"
            class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
            {{ saving ? 'Processing...' : 'Withdraw' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ===================== ACTIVITY MODAL ===================== -->
    <div v-if="showActivityModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[85vh] flex flex-col">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <h3 class="text-base font-semibold text-teal-600">Activity Log</h3>
          <button @click="showActivityModal = false" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <!-- Filter tabs -->
        <div class="px-6 pt-3 pb-0 flex gap-2">
          <button v-for="f in [{key:'all',label:'All'},{key:'reduce',label:'Withdrawals'},{key:'add',label:'Added'}]" :key="f.key"
            @click="activityFilter = f.key"
            :class="activityFilter === f.key
              ? (f.key === 'reduce' ? 'bg-orange-500 text-white' : f.key === 'add' ? 'bg-green-600 text-white' : 'bg-teal-600 text-white')
              : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
            class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
            {{ f.label }}
            <span class="ml-1 opacity-75">
              ({{ f.key === 'all' ? activityLogs.length : activityLogs.filter(l => l.action === f.key).length }})
            </span>
          </button>
        </div>
        <!-- Log entries -->
        <div class="p-4 overflow-y-auto flex-1">
          <div v-if="activityLoading" class="flex justify-center py-8">
            <svg class="w-6 h-6 animate-spin text-teal-400" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
          </div>
          <div v-else-if="filteredActivityLogs.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-400">
            <svg class="w-10 h-10 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-sm font-light">No entries found.</p>
          </div>
          <div v-else class="space-y-2">
            <div v-for="log in filteredActivityLogs" :key="log.id"
              :class="log.action === 'reduce' ? 'border-orange-100 bg-orange-50/40' : log.action === 'add' ? 'border-green-100 bg-green-50/30' : 'border-gray-100'"
              class="flex items-start gap-3 p-3 rounded-lg border">
              <!-- Icon -->
              <div :class="log.action === 'add' ? 'bg-green-100 text-green-600' : log.action === 'adjust' ? 'bg-green-100 text-amber-600' : 'bg-orange-100 text-orange-600'"
                class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold">
                {{ log.action === 'add' ? '+' : log.action === 'adjust' ? '~' : '-' }}
              </div>
              <!-- Details -->
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800">{{ log.item_name || ('Item #' + log.inventory_id) }}</p>
                <p class="text-xs mt-0.5">
                  <span :class="log.action === 'reduce' ? 'text-orange-600 font-semibold' : 'text-green-700 font-semibold'">{{ log.performed_by || 'Unknown user' }}</span>
                  <span class="text-gray-500">
                    {{ log.action === 'add' ? ' added ' : log.action === 'reduce' ? ' withdrew ' : ' adjusted ' }}
                  </span>
                  <span class="font-bold text-gray-700">{{ log.quantity }} {{ log.quantity === 1 ? 'unit' : 'units' }}</span>
                  <span v-if="log.notes" class="text-gray-400"> &mdash; {{ log.notes }}</span>
                </p>
              </div>
              <!-- Time -->
              <span class="text-xs text-gray-400 font-light whitespace-nowrap flex-shrink-0 mt-0.5">{{ formatDateTime(log.created_at) }}</span>
            </div>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
          <button @click="printActivityLog"
            class="w-full flex items-center justify-center gap-2 py-2.5 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print
          </button>
        </div>
      </div>
    </div>

    <!-- ===================== DELETE CONFIRM ===================== -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-gray-800">Archive Item</h3>
            <p class="text-sm text-gray-500 font-light">Move <strong>{{ deletingItem?.name }}</strong> to the archive? You can restore it later.</p>
          </div>
        </div>
        <div class="flex justify-end gap-3">
          <button @click="showDeleteModal = false" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-light text-gray-600 hover:bg-gray-50">Cancel</button>
          <button @click="confirmDelete" :disabled="saving"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
            {{ saving ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api from '../services/api'

// ---- State ----
const items          = ref([])
const allItems       = ref([])
const loading        = ref(false)
const saving         = ref(false)
const search         = ref('')
const activeCategory = ref('All')
const sortAsc        = ref(true)

const categories     = ['All', 'Room', 'Kitchen', 'Event', 'Catering', 'Other']
const itemCategories = ['Room', 'Kitchen', 'Event', 'Catering', 'Other']

// Modals
const showFormModal     = ref(false)
const showViewModal     = ref(false)
const showWithdrawModal = ref(false)
const showActivityModal = ref(false)
const showDeleteModal   = ref(false)

const viewingItem  = ref(null)
const deletingItem = ref(null)
const editingId    = ref(null)
const formError    = ref('')

const activityLogs    = ref([])
const activityLoading = ref(false)
const activityFilter  = ref('all')

const defaultForm = () => ({
  name: '', category: '', quantity: '', unit: 'piece',
  minimum_stock: 5, unit_price: '', supplier: '', on_delivery: false
})
const form = ref(defaultForm())
const withdrawForm = ref({ itemId: '', quantity: '', notes: '' })

// ---- Computed ----
const filteredActivityLogs = computed(() => {
  if (activityFilter.value === 'all') return activityLogs.value
  return activityLogs.value.filter(l => l.action === activityFilter.value)
})

const sortedItems = computed(() =>
  [...items.value].sort((a, b) =>
    sortAsc.value ? a.name.localeCompare(b.name) : b.name.localeCompare(a.name)
  )
)
function toggleSort() { sortAsc.value = !sortAsc.value }

// ---- Helpers ----
function formatMoney(v) {
  return Number(v || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}
function formatDateTime(dt) {
  if (!dt) return ''
  const d = new Date(dt)
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) +
    ' ' + d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
}
function statusBadge(s) {
  if (s === 'In Stock')     return 'bg-green-100 text-green-800'
  if (s === 'Low Stock')    return 'bg-yellow-100 text-yellow-700'
  if (s === 'Out of Stock') return 'bg-red-100 text-red-600'
  return 'bg-gray-100 text-gray-600'
}

// ---- Fetch ----
async function fetchItems() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (activeCategory.value && activeCategory.value !== 'All') params.set('category', activeCategory.value)
    if (search.value) params.set('search', search.value)
    const res = await api.get(`/inventory?${params}`)
    items.value = res.data.data || []
  } catch (e) { console.error(e) }
  finally { loading.value = false }
}

async function fetchAllItems() {
  try {
    const res = await api.get('/inventory')
    allItems.value = res.data.data || []
  } catch (e) { console.error(e) }
}

async function fetchActivity() {
  activityLoading.value = true
  activityLogs.value = []
  try {
    const res = await api.get('/inventory/activity?limit=100')
    activityLogs.value = res.data.data || []
  } catch (e) { console.error(e) }
  finally { activityLoading.value = false }
}

// ---- Modal helpers ----
function openAdd() {
  form.value = defaultForm()
  form.value.category = activeCategory.value !== 'All' ? activeCategory.value : ''
  editingId.value = null
  formError.value = ''
  showFormModal.value = true
}
function openEdit(item) {
  form.value = {
    name: item.name, category: item.category,
    quantity: item.current_stock, unit: item.unit || 'piece',
    minimum_stock: item.minimum_stock, unit_price: item.unit_price,
    supplier: item.supplier || '', on_delivery: !!+item.on_delivery
  }
  editingId.value = item.id
  formError.value = ''
  showFormModal.value = true
}
function openWithdraw() {
  withdrawForm.value = { itemId: '', quantity: '', notes: '' }
  formError.value = ''
  showWithdrawModal.value = true
}
function openActivity() {
  activityFilter.value = 'all'
  showActivityModal.value = true
}
function viewItem(item) { viewingItem.value = item; showViewModal.value = true }
function deletePrompt(item) { deletingItem.value = item; showDeleteModal.value = true }
function closeModals() {
  showFormModal.value = false; showWithdrawModal.value = false
  form.value = defaultForm(); editingId.value = null; formError.value = ''
}

// ---- Submit ----
async function submitForm() {
  formError.value = ''
  if (!form.value.name || !form.value.category || form.value.quantity === '') {
    formError.value = 'Name, category, and quantity are required.'; return
  }
  saving.value = true
  try {
    const payload = { ...form.value, on_delivery: form.value.on_delivery ? 1 : 0 }
    if (editingId.value) {
      await api.put(`/inventory/${editingId.value}`, payload)
    } else {
      await api.post('/inventory', payload)
    }
    const savedCategory = form.value.category
    closeModals()
    // Switch to the saved item's category tab so it's always visible
    if (savedCategory && savedCategory !== activeCategory.value) {
      activeCategory.value = savedCategory
    }
    await fetchItems(); await fetchAllItems()
  } catch (e) {
    formError.value = e.response?.data?.message || 'Failed to save.'
  } finally { saving.value = false }
}

async function submitWithdraw() {
  formError.value = ''
  if (!withdrawForm.value.itemId || !withdrawForm.value.quantity || withdrawForm.value.quantity <= 0) {
    formError.value = 'Please select an item and enter a valid quantity.'; return
  }
  saving.value = true
  try {
    await api.put(`/inventory/${withdrawForm.value.itemId}/withdraw`, {
      quantity: withdrawForm.value.quantity,
      notes: withdrawForm.value.notes || 'Withdrawal'
    })
    closeModals(); await fetchItems(); await fetchAllItems()
  } catch (e) {
    formError.value = e.response?.data?.message || 'Failed to withdraw stock.'
  } finally { saving.value = false }
}

async function confirmDelete() {
  if (!deletingItem.value) return
  saving.value = true
  try {
    await api.delete(`/inventory/${deletingItem.value.id}`)
    showDeleteModal.value = false; deletingItem.value = null
    await fetchItems(); await fetchAllItems()
  } catch (e) { console.error(e) }
  finally { saving.value = false }
}

function printActivityLog() {
  const filterLabel = activityFilter.value === 'reduce' ? 'Withdrawals' : activityFilter.value === 'add' ? 'Added' : 'All Activity'
  const rows = filteredActivityLogs.value.map(log => {
    const action = log.action === 'add' ? 'Added' : log.action === 'reduce' ? 'Withdrew' : 'Adjusted'
    const d = new Date(log.created_at)
    const date = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) +
      ' ' + d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
    return `<tr>
      <td>${log.item_name || 'Item #' + log.inventory_id}</td>
      <td>${log.performed_by || 'Unknown'}</td>
      <td>${action}</td>
      <td>${log.quantity}</td>
      <td>${log.notes || '—'}</td>
      <td>${date}</td>
    </tr>`
  }).join('')

  const html = `<!DOCTYPE html><html><head><title>Inventory Activity Log</title>
  <style>
    body { font-family: sans-serif; font-size: 12px; padding: 24px; color: #1f2937; }
    h2 { font-size: 18px; margin-bottom: 4px; color: #0d4e9a; }
    p.sub { font-size: 11px; color: #6b7280; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f3f4f6; text-align: left; padding: 8px 10px; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; border-bottom: 2px solid #e5e7eb; }
    td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
    tr:nth-child(even) td { background: #f9fafb; }
    @media print { body { padding: 0; } }
  </style></head><body>
  <h2>Inventory Activity Log</h2>
  <p class="sub">Filter: ${filterLabel} &nbsp;&bull;&nbsp; Printed: ${new Date().toLocaleString()}</p>
  <table>
    <thead><tr><th>Item</th><th>Performed By</th><th>Action</th><th>Qty</th><th>Notes</th><th>Date &amp; Time</th></tr></thead>
    <tbody>${rows}</tbody>
  </table>
  </body></html>`

  const win = window.open('', '_blank', 'width=900,height=650')
  win.document.write(html)
  win.document.close()
  win.focus()
  win.print()
}

watch(showActivityModal, async (val) => { if (val) await fetchActivity() })

onMounted(async () => { await fetchItems(); await fetchAllItems() })
</script>

