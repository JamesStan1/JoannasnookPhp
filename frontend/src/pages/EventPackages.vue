<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <!-- Page Header -->
    <div class="bg-white rounded-xl shadow-sm p-6">
      <!-- Title row -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
          <h1 class="text-xl font-semibold text-green-800">Manage Event Packages</h1>
        </div>
        <button @click="openAdd"
          class="flex items-center gap-2 bg-green-800 hover:bg-green-900 text-white px-4 py-2.5 rounded-lg text-sm font-light transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Add New Package
        </button>
      </div>

      <!-- Tab label -->
      <p class="text-sm font-medium text-amber-600 mb-4 border-b border-gray-100 pb-3">Event Packages</p>

      <!-- Loading state -->
      <div v-if="loading" class="flex items-center justify-center py-16 text-gray-400">
        <svg class="w-6 h-6 animate-spin text-green-500 mr-2" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg>
        Loading packages...
      </div>

      <!-- Empty state -->
      <div v-else-if="packages.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
        <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        <p class="text-sm font-light">No packages found. Click &ldquo;Add New Package&rdquo; to get started.</p>
      </div>

      <!-- Package cards grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="pkg in packages" :key="pkg.id"
          class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow bg-white flex flex-col justify-between">
          <!-- Package Image -->
          <div class="h-40 bg-gray-100 overflow-hidden">
            <img v-if="pkg.image_url" :src="pkgImageUrl(pkg)" class="w-full h-full object-cover" />
            <div v-else class="w-full h-full flex items-center justify-center">
              <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
          </div>
          <div class="p-5 flex flex-col flex-1">
            <h3 class="text-base font-semibold text-gray-800 mb-2">{{ pkg.package_name }}</h3>
            <p class="text-sm text-gray-700 font-light mb-1">
              &#x20B1;{{ formatMoney(pkg.price) }} &mdash; {{ pkg.max_guests }} Guests
            </p>
            <p class="text-sm text-amber-600 font-light mb-1">Max Per Dish: {{ pkg.max_per_dish }}</p>
            <p class="text-sm text-amber-600 font-light leading-relaxed">
              Description: {{ pkg.description || 'No description provided.' }}
            </p>
          </div>
          <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-100">
            <button @click="openEdit(pkg)" title="Edit"
              class="p-2 rounded-lg text-green-600 hover:bg-green-50 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </button>
            <button @click="deletePrompt(pkg)" title="Delete"
              class="p-2 rounded-lg text-red-500 hover:bg-red-50 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ===================== ADD / EDIT MODAL ===================== -->
    <div v-if="showFormModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md flex flex-col max-h-[90vh]">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
          <h3 class="text-base font-semibold text-green-800">{{ editingId ? 'Edit Package' : 'Add New Package' }}</h3>
          <button @click="closeModal" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div class="p-6 space-y-4 overflow-y-auto">
          <!-- Package Name -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Package Name <span class="text-red-500">*</span></label>
            <input v-model="form.package_name" type="text" placeholder="e.g. Basic Package"
              class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200"/>
          </div>
          <!-- Price -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Price (&#x20B1;) <span class="text-red-500">*</span></label>
            <input v-model.number="form.price" type="number" min="0" step="0.01" placeholder="0.00"
              class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200"/>
          </div>
          <!-- Max Guests -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Max Guests <span class="text-red-500">*</span></label>
            <input v-model.number="form.max_guests" type="number" min="1" placeholder="0"
              class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200"/>
          </div>
          <!-- Max Per Dish -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Max Per Dish</label>
            <input v-model.number="form.max_per_dish" type="number" min="0" placeholder="0"
              class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200"/>
          </div>
          <!-- Image Upload -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Package Image <span class="text-gray-400 font-normal">(optional)</span></label>
            <div v-if="imagePreview" class="relative mb-2 rounded-xl overflow-hidden border border-gray-200 bg-gray-50 h-36">
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
          <!-- Description -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
            <textarea v-model="form.description" rows="3"
              placeholder="Package inclusions and details..."
              class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-light focus:outline-none focus:ring-2 focus:ring-blue-200 resize-none"/>
          </div>
          <p v-if="formError" class="text-sm text-red-500 font-light">{{ formError }}</p>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 flex-shrink-0">
          <button @click="closeModal"
            class="px-5 py-2.5 border border-gray-200 rounded-lg text-sm font-light text-gray-600 hover:bg-gray-50 transition-colors">
            Cancel
          </button>
          <button @click="submitForm" :disabled="saving"
            class="px-5 py-2.5 bg-green-800 hover:bg-green-900 text-white rounded-lg text-sm font-light transition-colors disabled:opacity-50">
            {{ saving ? 'Saving...' : (editingId ? 'Save Changes' : 'Add Package') }}
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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-gray-800">Delete Package</h3>
            <p class="text-sm text-gray-500 font-light">
              Delete <strong>{{ deletingPkg?.package_name }}</strong>? This cannot be undone.
            </p>
          </div>
        </div>
        <div class="flex justify-end gap-3">
          <button @click="showDeleteModal = false"
            class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-light text-gray-600 hover:bg-gray-50">
            Cancel
          </button>
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
import { ref, onMounted } from 'vue'
import api from '../services/api'

// ---- State ----
const packages    = ref([])
const loading     = ref(false)
const saving      = ref(false)
const formError   = ref('')

const showFormModal   = ref(false)
const showDeleteModal = ref(false)
const editingId       = ref(null)
const deletingPkg     = ref(null)

const imageFile    = ref(null)
const imagePreview = ref('')

const IMG_BASE = 'http://localhost:8000/'
function pkgImageUrl(pkg) {
  if (!pkg.image_url) return ''
  if (pkg.image_url.startsWith('http') || pkg.image_url.startsWith('/')) return pkg.image_url
  return IMG_BASE + pkg.image_url
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

const defaultForm = () => ({
  package_name: '',
  price: '',
  max_guests: '',
  max_per_dish: '',
  description: ''
})
const form = ref(defaultForm())

// ---- Helpers ----
function formatMoney(v) {
  return Number(v || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

// ---- Fetch ----
async function fetchPackages() {
  loading.value = true
  try {
    const res = await api.get('/event-packages')
    packages.value = res.data.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

// ---- Modal helpers ----
function openAdd() {
  form.value = defaultForm()
  editingId.value = null
  formError.value = ''
  imageFile.value = null
  imagePreview.value = ''
  showFormModal.value = true
}

function openEdit(pkg) {
  form.value = {
    package_name: pkg.package_name,
    price:        pkg.price,
    max_guests:   pkg.max_guests,
    max_per_dish: pkg.max_per_dish,
    description:  pkg.description || ''
  }
  editingId.value = pkg.id
  formError.value = ''
  imageFile.value = null
  imagePreview.value = pkgImageUrl(pkg)
  showFormModal.value = true
}

function closeModal() {
  showFormModal.value = false
  form.value = defaultForm()
  editingId.value = null
  formError.value = ''
  imageFile.value = null
  imagePreview.value = ''
}

function deletePrompt(pkg) {
  deletingPkg.value = pkg
  showDeleteModal.value = true
}

// ---- Submit ----
async function submitForm() {
  formError.value = ''
  if (!form.value.package_name) {
    formError.value = 'Package name is required.'
    return
  }
  if (!form.value.price || !form.value.max_guests) {
    formError.value = 'Price and Max Guests are required.'
    return
  }
  saving.value = true
  try {
    if (imageFile.value) {
      const fd = new FormData()
      fd.append('package_name', form.value.package_name)
      fd.append('price', form.value.price)
      fd.append('max_guests', form.value.max_guests)
      fd.append('max_per_dish', form.value.max_per_dish || 0)
      fd.append('description', form.value.description || '')
      fd.append('image', imageFile.value)
      if (editingId.value) {
        await api.post(`/event-packages/${editingId.value}/update`, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
      } else {
        await api.post('/event-packages', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
      }
    } else {
      if (editingId.value) {
        await api.put(`/event-packages/${editingId.value}`, form.value)
      } else {
        await api.post('/event-packages', form.value)
      }
    }
    closeModal()
    await fetchPackages()
  } catch (e) {
    formError.value = e.response?.data?.error || 'Failed to save. Please try again.'
  } finally {
    saving.value = false
  }
}

async function confirmDelete() {
  if (!deletingPkg.value) return
  saving.value = true
  try {
    await api.delete(`/event-packages/${deletingPkg.value.id}`)
    showDeleteModal.value = false
    deletingPkg.value = null
    await fetchPackages()
  } catch (e) {
    console.error(e)
  } finally {
    saving.value = false
  }
}

onMounted(fetchPackages)
</script>
