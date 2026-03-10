<template>
  <div class="p-6 bg-gray-50 min-h-screen font-light">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-gray-800">Edit Room</h1>
        <p class="text-gray-500 text-sm mt-1">Manage all hotel rooms, pricing, and availability</p>
      </div>
      <button v-if="activeTab === 'rooms'" @click="openAddModal"
        class="bg-green-800 hover:bg-green-900 text-white text-sm font-medium px-5 py-2.5 rounded-xl flex items-center gap-2 transition w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add New Room
      </button>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 mb-6 bg-white border border-gray-200 rounded-xl p-1 w-fit shadow-sm">
      <button @click="activeTab = 'rooms'"
        :class="['px-5 py-2 rounded-lg text-sm font-medium transition-all duration-200', activeTab === 'rooms' ? 'bg-green-800 text-white shadow' : 'text-gray-600 hover:bg-gray-100']">
        Room List
      </button>
      <button @click="activeTab = 'archived'; loadArchived()"
        :class="['px-5 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2', activeTab === 'archived' ? 'bg-green-800 text-white shadow' : 'text-gray-600 hover:bg-gray-100']">
        Archived
        <span v-if="archived.length" class="text-xs px-1.5 py-0.5 rounded-full"
          :class="activeTab === 'archived' ? 'bg-white/20' : 'bg-red-100 text-red-600'">
          {{ archived.length }}
        </span>
      </button>
    </div>

    <!-- ====== ROOM LIST TAB ====== -->
    <div v-if="activeTab === 'rooms'">

      <!-- Stats Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
        <div v-for="s in statusStats" :key="s.status"
          @click="activeFilter = activeFilter === s.status ? 'all' : s.status"
          :class="['bg-white rounded-xl border shadow-sm p-4 cursor-pointer transition hover:shadow-md', activeFilter === s.status ? 'border-green-400 ring-2 ring-blue-200' : 'border-gray-100']">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs text-gray-400 uppercase tracking-wide capitalize mb-1">{{ s.status }}</p>
              <p class="text-xl lg:text-3xl font-semibold" :class="s.color">{{ s.count }}</p>
            </div>
            <div :class="['p-2.5 rounded-xl', s.bg]">
              <svg class="w-5 h-5" :class="s.color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Search + Filter -->
      <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <div class="relative flex-1">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input v-model="search" type="text" placeholder="Search by room number or type..."
            class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-600 bg-white" />
        </div>
        <div class="flex gap-2 flex-wrap">
          <button v-for="f in ['all','available','occupied','maintenance','dirty']" :key="f"
            @click="activeFilter = f"
            :class="['px-4 py-2 rounded-xl text-xs font-medium border transition capitalize', activeFilter === f ? 'bg-green-800 text-white border-blue-700' : 'bg-white text-gray-600 border-gray-200 hover:border-green-400']">
            {{ f === 'all' ? 'All' : f }}
          </button>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-16 text-gray-400">Loading rooms...</div>

      <!-- Rooms Table -->
      <div v-else class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-green-800 text-white">
                <th class="px-4 py-3 text-left font-medium text-xs">#</th>
                <th class="px-4 py-3 text-left font-medium text-xs">Room No.</th>
                <th class="px-4 py-3 text-left font-medium text-xs">Type</th>
                <th class="px-4 py-3 text-center font-medium text-xs">Capacity</th>
                <th class="px-4 py-3 text-right font-medium text-xs">Price/Night</th>
                <th class="px-4 py-3 text-center font-medium text-xs">Status</th>
                <th class="px-4 py-3 text-left font-medium text-xs">Description</th>
                <th class="px-4 py-3 text-center font-medium text-xs">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="filteredRooms.length === 0">
                <td colspan="8" class="px-4 py-10 text-center text-gray-400">No rooms found</td>
              </tr>
              <tr v-for="(room, idx) in filteredRooms" :key="room.id"
                class="border-t border-gray-50 hover:bg-green-50/30 transition-colors">
                <td class="px-4 py-3 text-gray-400 text-xs">{{ idx + 1 }}</td>
                <td class="px-4 py-3 font-semibold text-green-800">{{ room.room_number }}</td>
                <td class="px-4 py-3 capitalize text-gray-700">{{ room.type }}</td>
                <td class="px-4 py-3 text-center text-gray-600">{{ room.capacity }}</td>
                <td class="px-4 py-3 text-right font-medium text-green-800">&#x20B1;{{ formatMoney(room.price) }}</td>
                <td class="px-4 py-3 text-center">
                  <div class="relative inline-block group">
                    <span :class="statusBadge(room.status)"
                      class="px-2.5 py-1 rounded-full text-xs font-medium capitalize cursor-pointer select-none">
                      {{ room.status }} &#x25BE;
                    </span>
                    <div class="absolute top-full mt-1 left-1/2 -translate-x-1/2 bg-white border border-gray-200 rounded-xl shadow-xl z-20 hidden group-hover:block w-36 py-1">
                      <button v-for="st in ['available','occupied','maintenance','dirty']" :key="st"
                        @click="changeRoomStatus(room.id, st)"
                        :class="['w-full text-left text-xs px-3 py-2 hover:bg-gray-50 capitalize flex items-center gap-2', room.status === st ? 'font-semibold text-green-800' : 'text-gray-700']">
                        <span :class="['w-2 h-2 rounded-full flex-shrink-0', statusDot(st)]"></span>{{ st }}
                      </button>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 text-gray-400 text-xs max-w-xs truncate">{{ room.description || '\u2014' }}</td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-center gap-1">
                    <!-- View -->
                    <button @click="viewRoom(room)" title="View Room"
                      class="p-1.5 rounded-lg text-green-600 hover:bg-green-50 border border-green-200 transition">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>
                    <!-- Edit -->
                    <button @click="openEditModal(room)" title="Edit Room"
                      class="p-1.5 rounded-lg text-yellow-600 hover:bg-yellow-50 border border-yellow-200 transition">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                    <!-- Delete / Archive -->
                    <button @click="confirmArchive(room)" title="Archive Room"
                      class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 border border-red-200 transition">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                    <!-- Print -->
                    <button @click="printRoom(room)" title="Print Room Details"
                      class="p-1.5 rounded-lg text-amber-600 hover:bg-green-50 border border-green-200 transition">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                      </svg>
                    </button>
                    <!-- Approve (mark available) -->
                    <button @click="changeRoomStatus(room.id, 'available')" title="Mark as Available"
                      :class="room.status === 'available' ? 'text-gray-300 border-gray-200 cursor-default' : 'text-green-600 hover:bg-green-50 border-green-200'"
                      class="p-1.5 rounded-lg border transition">
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
        <div class="px-4 py-3 border-t border-gray-50 text-xs text-gray-400">
          Showing {{ filteredRooms.length }} of {{ rooms.length }} rooms
        </div>
      </div>
    </div>

    <!-- ====== ARCHIVED TAB ====== -->
    <div v-if="activeTab === 'archived'">
      <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-5 flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        <p class="text-sm text-amber-700">Archived rooms are hidden from reservations. Restore to make them available again, or permanently delete to remove them forever.</p>
      </div>

      <div v-if="archivedLoading" class="text-center py-16 text-gray-400">Loading archived rooms...</div>

      <div v-else class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-gray-700 text-white">
                <th class="px-4 py-3 text-left font-medium text-xs">#</th>
                <th class="px-4 py-3 text-left font-medium text-xs">Room No.</th>
                <th class="px-4 py-3 text-left font-medium text-xs">Type</th>
                <th class="px-4 py-3 text-center font-medium text-xs">Capacity</th>
                <th class="px-4 py-3 text-right font-medium text-xs">Price/Night</th>
                <th class="px-4 py-3 text-left font-medium text-xs">Description</th>
                <th class="px-4 py-3 text-left font-medium text-xs">Archived On</th>
                <th class="px-4 py-3 text-center font-medium text-xs">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="archived.length === 0">
                <td colspan="8" class="px-4 py-10 text-center text-gray-400">No archived rooms</td>
              </tr>
              <tr v-for="(room, idx) in archived" :key="room.id"
                class="border-t border-gray-50 hover:bg-gray-50/60 transition-colors">
                <td class="px-4 py-3 text-gray-400 text-xs">{{ idx + 1 }}</td>
                <td class="px-4 py-3 font-semibold text-gray-600">{{ room.room_number }}</td>
                <td class="px-4 py-3 capitalize text-gray-500">{{ room.type }}</td>
                <td class="px-4 py-3 text-center text-gray-500">{{ room.capacity }}</td>
                <td class="px-4 py-3 text-right text-gray-500">&#x20B1;{{ formatMoney(room.price) }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs max-w-xs truncate">{{ room.description || '\u2014' }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ formatDate(room.deleted_at) }}</td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-center gap-1">
                    <button @click="restoreRoom(room)" title="Restore room"
                      class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 rounded-lg transition flex items-center gap-1">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                      Restore
                    </button>
                    <button @click="confirmForceDelete(room)" title="Permanently delete"
                      class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition flex items-center gap-1">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-50 text-xs text-gray-400">
          {{ archived.length }} archived room{{ archived.length !== 1 ? 's' : '' }}
        </div>
      </div>
    </div>

    <!-- ====== ADD/EDIT MODAL ====== -->
    <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
          <h3 class="text-base font-semibold text-gray-800">{{ editingRoom ? 'Edit Room' : 'Add New Room' }}</h3>
          <button @click="showModal = false" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <form @submit.prevent="saveRoom" class="p-5 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-500 mb-1">Room Number *</label>
              <input v-model="roomForm.room_number" required placeholder="101"
                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">Room Name / Type *</label>
              <input v-model="roomForm.type" required list="room-type-suggestions" placeholder="e.g. Deluxe, Suite, Triple..."
                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600" />
              <datalist id="room-type-suggestions">
                <option value="Standard"/>
                <option value="Deluxe"/>
                <option value="Suite"/>
                <option value="Family"/>
                <option value="Executive"/>
                <option value="Triple"/>
                <option value="Twin"/>
                <option value="Single"/>
              </datalist>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-500 mb-1">Price per Night (&#x20B1;) *</label>
              <input v-model="roomForm.price" type="number" min="0" step="0.01" required placeholder="0.00"
                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">Capacity (guests)</label>
              <input v-model="roomForm.capacity" type="number" min="1" placeholder="2"
                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600" />
            </div>
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">Description</label>
            <textarea v-model="roomForm.description" rows="2" placeholder="Room amenities and features..."
              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 resize-none"></textarea>
          </div>
          <div v-if="!editingRoom">
            <label class="block text-xs text-gray-500 mb-1">Initial Status</label>
            <select v-model="roomForm.status"
              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
              <option value="available">Available</option>
              <option value="maintenance">Maintenance</option>
            </select>
          </div>
          <!-- Image Upload -->
          <div>
            <label class="block text-xs text-gray-500 mb-1">Room Image <span class="text-gray-400">(optional)</span></label>
            <div v-if="imagePreview" class="relative mb-2 rounded-xl overflow-hidden border border-gray-200 bg-gray-50 h-32">
              <img :src="imagePreview" class="w-full h-full object-cover" />
              <button type="button" @click="clearImage"
                class="absolute top-1.5 right-1.5 w-6 h-6 bg-black/50 hover:bg-black/70 text-white rounded-full text-xs flex items-center justify-center transition">
                ✕
              </button>
            </div>
            <label class="flex items-center gap-2 cursor-pointer px-3 py-2 border border-dashed border-gray-300 rounded-lg hover:border-green-400 hover:bg-green-50 transition text-sm text-gray-500 hover:text-green-700">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              {{ imagePreview ? 'Change image' : 'Upload image' }}
              <input type="file" accept="image/*" class="hidden" @change="onImagePicked" />
            </label>
          </div>
          <p v-if="formError" class="text-xs text-red-500">{{ formError }}</p>
          <div class="flex gap-3 justify-end pt-2">
            <button type="button" @click="showModal = false"
              class="px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
            <button type="submit" :disabled="saving"
              class="px-5 py-2 text-sm bg-green-800 text-white rounded-xl hover:bg-green-900 transition disabled:opacity-60">
              {{ saving ? 'Saving...' : (editingRoom ? 'Save Changes' : 'Add Room') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ====== VIEW ROOM MODAL ====== -->
    <div v-if="viewingRoom" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
          <h3 class="text-base font-bold text-green-800">Room Details</h3>
          <button @click="viewingRoom = null" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="p-5 space-y-3">
          <div v-if="viewingRoom.image_url" class="rounded-xl overflow-hidden h-40 bg-gray-100 mb-1">
            <img :src="roomImageUrl(viewingRoom)" class="w-full h-full object-cover" />
          </div>
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <span class="text-xs text-gray-500">Room Number</span>
            <span class="text-sm font-semibold text-green-800">{{ viewingRoom.room_number }}</span>
          </div>
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <span class="text-xs text-gray-500">Type</span>
            <span class="text-sm capitalize text-gray-800">{{ viewingRoom.type }}</span>
          </div>
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <span class="text-xs text-gray-500">Capacity</span>
            <span class="text-sm text-gray-800">{{ viewingRoom.capacity }} person(s)</span>
          </div>
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <span class="text-xs text-gray-500">Price / Night</span>
            <span class="text-sm font-semibold text-green-800">&#x20B1;{{ formatMoney(viewingRoom.price) }}</span>
          </div>
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <span class="text-xs text-gray-500">Status</span>
            <span :class="statusBadge(viewingRoom.status)" class="px-2.5 py-1 rounded-full text-xs font-medium capitalize">{{ viewingRoom.status }}</span>
          </div>
          <div v-if="viewingRoom.description">
            <p class="text-xs text-gray-500 mb-1">Description</p>
            <p class="text-sm text-gray-700">{{ viewingRoom.description }}</p>
          </div>
        </div>
        <div class="px-5 pb-4 flex justify-end gap-3">
          <button @click="printRoom(viewingRoom)"
            class="px-4 py-2 text-sm bg-green-700 text-white rounded-xl hover:bg-green-800 transition">Print</button>
          <button @click="viewingRoom = null"
            class="px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Close</button>
        </div>
      </div>
    </div>

    <!-- ====== ARCHIVE CONFIRM ====== -->
    <div v-if="archiveTarget" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <p class="text-sm font-medium text-gray-800 mb-1">Archive Room {{ archiveTarget.room_number }}?</p>
        <p class="text-xs text-gray-500 mb-5">This room will be hidden from reservations. You can restore it from the Archived tab.</p>
        <div class="flex gap-3">
          <button @click="archiveTarget = null" class="flex-1 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
          <button @click="doArchive" class="flex-1 py-2 text-sm bg-red-600 text-white rounded-xl hover:bg-red-700 transition">Archive</button>
        </div>
      </div>
    </div>

    <!-- ====== FORCE DELETE CONFIRM ====== -->
    <div v-if="forceDeleteTarget" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        </div>
        <p class="text-sm font-medium text-gray-800 mb-1">Permanently Delete Room {{ forceDeleteTarget.room_number }}?</p>
        <p class="text-xs text-gray-500 mb-5">This action <strong>cannot be undone</strong>. All data for this room will be lost.</p>
        <div class="flex gap-3">
          <button @click="forceDeleteTarget = null" class="flex-1 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
          <button @click="doForceDelete" class="flex-1 py-2 text-sm bg-red-600 text-white rounded-xl hover:bg-red-700 transition">Delete Forever</button>
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

const activeTab = ref('rooms')

const rooms = ref([])
const archived = ref([])
const loading = ref(false)
const archivedLoading = ref(false)
const search = ref('')
const activeFilter = ref('all')
const showModal = ref(false)
const editingRoom = ref(null)
const saving = ref(false)
const formError = ref('')
const archiveTarget = ref(null)
const forceDeleteTarget = ref(null)
const viewingRoom = ref(null)

const roomForm = ref({ room_number: '', type: '', price: '', capacity: 2, description: '', status: 'available' })
const imageFile = ref(null)
const imagePreview = ref('')

const _imgApiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
const IMG_BASE = (_imgApiUrl.startsWith('http') ? _imgApiUrl.replace(/\/api$/, '') : _imgApiUrl) + '/'
function roomImageUrl(room) {
  if (!room.image_url) return ''
  if (room.image_url.startsWith('http') || room.image_url.startsWith('/')) return room.image_url
  return IMG_BASE + room.image_url
}
function onImagePicked(e) {
  const file = e.target.files[0]
  if (!file) return
  imageFile.value = file
  imagePreview.value = URL.createObjectURL(file)
}
function clearImage() {
  imageFile.value = null
  imagePreview.value = ''
}

const filteredRooms = computed(() => {
  let list = rooms.value
  if (activeFilter.value !== 'all') list = list.filter(r => r.status === activeFilter.value)
  if (search.value.trim()) {
    const s = search.value.toLowerCase()
    list = list.filter(r => r.room_number?.toLowerCase().includes(s) || r.type?.toLowerCase().includes(s))
  }
  return list
})

const statusStats = computed(() => {
  const configs = [
    { status: 'available', color: 'text-green-600', bg: 'bg-green-50' },
    { status: 'occupied',  color: 'text-amber-600',  bg: 'bg-green-50' },
    { status: 'maintenance', color: 'text-yellow-600', bg: 'bg-yellow-50' },
    { status: 'dirty',    color: 'text-red-500',    bg: 'bg-red-50' },
  ]
  return configs.map(c => ({ ...c, count: rooms.value.filter(r => r.status === c.status).length }))
})

async function loadRooms() {
  loading.value = true
  try {
    const res = await api.get('/rooms')
    rooms.value = res.data?.data ?? res.data ?? []
  } catch (e) { console.error(e) } finally { loading.value = false }
}

async function loadArchived() {
  archivedLoading.value = true
  try {
    const res = await api.get('/rooms/archived')
    archived.value = res.data?.data ?? res.data ?? []
  } catch (e) { console.error(e) } finally { archivedLoading.value = false }
}

function viewRoom(room) { viewingRoom.value = room }

function printRoom(room) {
  const w = window.open('', '_blank')
  w.document.write(`
    <html><head><title>Room ${room.room_number}</title>
    <style>body{font-family:sans-serif;padding:24px}h2{color:#1d4ed8}table{width:100%;border-collapse:collapse;margin-top:12px}td{padding:8px 12px;border-bottom:1px solid #e5e7eb}td:first-child{color:#6b7280;width:40%}</style></head>
    <body>
      <h2>Room Details</h2>
      <table>
        <tr><td>Room Number</td><td><strong>${room.room_number}</strong></td></tr>
        <tr><td>Type</td><td>${room.type}</td></tr>
        <tr><td>Capacity</td><td>${room.capacity} person(s)</td></tr>
        <tr><td>Price / Night</td><td>&#x20B1;${Number(room.price||0).toLocaleString('en-PH',{minimumFractionDigits:2})}</td></tr>
        <tr><td>Status</td><td>${room.status}</td></tr>
        <tr><td>Description</td><td>${room.description||'—'}</td></tr>
      </table>
      <script>window.onload=()=>window.print()<\/script>
    </body></html>`)
  w.document.close()
}

function openAddModal() {
  editingRoom.value = null
  roomForm.value = { room_number: '', type: '', price: '', capacity: 2, description: '', status: 'available' }
  formError.value = ''
  imageFile.value = null
  imagePreview.value = ''
  showModal.value = true
}

function openEditModal(room) {
  editingRoom.value = room
  roomForm.value = { room_number: room.room_number, type: room.type, price: room.price, capacity: room.capacity, description: room.description || '' }
  formError.value = ''
  imageFile.value = null
  imagePreview.value = roomImageUrl(room)
  showModal.value = true
}

async function saveRoom() {
  formError.value = ''
  saving.value = true
  try {
    if (editingRoom.value) {
      if (imageFile.value) {
        const fd = new FormData()
        fd.append('room_number', roomForm.value.room_number)
        fd.append('type', roomForm.value.type)
        fd.append('price', roomForm.value.price)
        fd.append('capacity', roomForm.value.capacity)
        fd.append('description', roomForm.value.description || '')
        fd.append('image', imageFile.value)
        await api.post(`/rooms/${editingRoom.value.id}/update`, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
      } else {
        await api.put(`/rooms/${editingRoom.value.id}`, roomForm.value)
      }
    } else {
      if (imageFile.value) {
        const fd = new FormData()
        fd.append('room_number', roomForm.value.room_number)
        fd.append('type', roomForm.value.type)
        fd.append('price', roomForm.value.price)
        fd.append('capacity', roomForm.value.capacity)
        fd.append('description', roomForm.value.description || '')
        fd.append('status', roomForm.value.status || 'available')
        fd.append('image', imageFile.value)
        await api.post('/rooms', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
      } else {
        await api.post('/rooms', roomForm.value)
      }
    }
    showModal.value = false
    await loadRooms()
  } catch (e) {
    formError.value = e.response?.data?.message ?? 'Error saving room'
  } finally { saving.value = false }
}

async function changeRoomStatus(id, status) {
  try {
    await api.put(`/rooms/${id}/status`, { status })
    const room = rooms.value.find(r => r.id == id)
    if (room) room.status = status
  } catch (e) { console.error(e) }
}

function confirmArchive(room) { archiveTarget.value = room }
async function doArchive() {
  try {
    await api.put(`/rooms/${archiveTarget.value.id}/archive`)
    archiveTarget.value = null
    await loadRooms()
    archived.value = []  // reset so badge updates on next open
    toast.success('Room archived successfully')
  } catch (e) { toast.error(e.response?.data?.message ?? 'Error archiving room') }
}

async function restoreRoom(room) {
  try {
    await api.put(`/rooms/${room.id}/restore`)
    await loadArchived()
    await loadRooms()
    toast.success('Room restored successfully')
  } catch (e) { toast.error(e.response?.data?.message ?? 'Error restoring room') }
}

function confirmForceDelete(room) { forceDeleteTarget.value = room }
async function doForceDelete() {
  try {
    await api.delete(`/rooms/${forceDeleteTarget.value.id}`)
    forceDeleteTarget.value = null
    await loadArchived()
    toast.success('Room permanently deleted')
  } catch (e) { toast.error(e.response?.data?.message ?? 'Error deleting room') }
}

function formatMoney(v) { return Number(v || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }
function formatDate(d) {
  if (!d) return '\u2014'
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
function statusBadge(status) {
  const map = { available:'bg-green-100 text-green-700', occupied:'bg-green-100 text-green-800', maintenance:'bg-yellow-100 text-yellow-700', dirty:'bg-red-100 text-red-600' }
  return map[status] ?? 'bg-gray-100 text-gray-500'
}
function statusDot(status) {
  const map = { available:'bg-green-400', occupied:'bg-green-600', maintenance:'bg-yellow-400', dirty:'bg-red-400' }
  return map[status] ?? 'bg-gray-300'
}

onMounted(loadRooms)
</script>
