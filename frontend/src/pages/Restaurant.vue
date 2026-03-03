<template>
  <div class="min-h-screen bg-gray-50 p-6">

    <!-- ── Top bar ── -->
    <div class="flex items-center justify-between mb-5">
      <!-- Search -->
      <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input v-model="search" @input="currentPage = 1" type="text" placeholder="Search dishes..."
          class="pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 w-72 shadow-sm"/>
      </div>

      <!-- Buttons -->
      <div class="flex items-center gap-3">
        <button @click="printMenu"
          class="flex items-center gap-2 px-4 py-2.5 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
          </svg>
          Print Menu
        </button>
        <button @click="openAdd"
          class="flex items-center gap-2 px-4 py-2.5 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Add Dish
        </button>
      </div>
    </div>

    <!-- ── Category tabs ── -->
    <div class="flex flex-wrap gap-2 mb-6">
      <button v-for="cat in ['All', ...CATEGORIES]" :key="cat"
        @click="activeCategory = cat; currentPage = 1"
        :class="[
          'px-4 py-1.5 rounded-full text-sm font-medium border transition-colors',
          activeCategory === cat
            ? 'bg-green-700 text-white border-blue-600'
            : 'bg-white text-gray-600 border-gray-300 hover:border-green-400 hover:text-amber-600'
        ]">
        {{ cat }}
      </button>
    </div>

    <!-- ── Loading ── -->
    <div v-if="loading" class="flex justify-center items-center py-24">
      <svg class="w-8 h-8 animate-spin text-green-500" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
      </svg>
    </div>

    <!-- ── Empty ── -->
    <div v-else-if="pagedItems.length === 0" class="flex flex-col items-center justify-center py-24 text-gray-400">
      <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
      </svg>
      <p class="text-sm">No dishes found.</p>
    </div>

    <!-- ── Cards grid ── -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
      <div v-for="item in pagedItems" :key="item.id"
        class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col hover:shadow-md transition-shadow overflow-hidden">

        <!-- Dish image -->
        <div class="relative h-36 bg-gray-100 flex items-center justify-center overflow-hidden">
          <img v-if="dishImageUrl(item)" :src="dishImageUrl(item)" :alt="item.name"
            class="w-full h-full object-cover" />
          <div v-else class="flex flex-col items-center gap-1 text-gray-300">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            <span class="text-xs">No image</span>
          </div>
          <span v-if="item.category"
            class="absolute top-2 left-2 text-xs px-2 py-0.5 bg-white/90 text-amber-600 rounded-full border border-green-100 font-medium shadow-sm">
            {{ item.category }}
          </span>
        </div>

        <div class="p-4 flex flex-col gap-1.5 flex-1">
          <!-- Name -->
          <h3 class="text-base font-bold text-green-800 leading-tight">{{ item.name }}</h3>

          <!-- Price -->
          <p class="text-sm font-semibold text-gray-800">&#x20B1;{{ formatMoney(item.price) }}</p>

          <!-- Availability -->
          <p class="text-sm text-gray-500">
            Availability:
            <span :class="item.active ? 'text-green-600 font-medium' : 'text-red-400 font-medium'">
              {{ item.active ? 'Available' : 'Unavailable' }}
            </span>
          </p>

          <!-- Actions -->
          <div class="flex gap-2 mt-auto pt-2">
            <button @click="openEdit(item)"
              class="flex-1 flex items-center justify-center gap-1.5 py-2 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-lg transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-1.414.586H9v-2a2 2 0 01.586-1.414z"/>
              </svg>
              Edit
            </button>
            <button @click="confirmRemove(item)"
              class="flex-1 flex items-center justify-center gap-1.5 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-10 0h14"/>
              </svg>
              Remove
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Pagination ── -->
    <div v-if="totalPages > 1" class="flex items-center justify-center gap-1 mt-8">
      <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1"
        class="px-4 py-1.5 text-sm border border-gray-200 rounded-lg text-gray-500 bg-white hover:bg-gray-50 disabled:opacity-40 shadow-sm">
        Previous
      </button>
      <button v-for="p in totalPages" :key="p" @click="currentPage = p"
        :class="[
          'w-9 h-9 text-sm rounded-lg border transition-colors shadow-sm',
          currentPage === p ? 'bg-green-700 text-white border-blue-600' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'
        ]">
        {{ p }}
      </button>
      <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages"
        class="px-4 py-1.5 text-sm border border-gray-200 rounded-lg text-gray-500 bg-white hover:bg-gray-50 disabled:opacity-40 shadow-sm">
        Next
      </button>
    </div>


    <!-- ══════════ ADD / EDIT MODAL ══════════ -->
    <transition name="fade">
      <div v-if="showFormModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
          <h2 class="text-lg font-bold text-gray-800 mb-5">{{ editItem ? 'Edit Dish' : 'Add Dish' }}</h2>

          <div class="space-y-4">
            <!-- Name -->
            <div>
              <label class="block text-xs text-gray-500 mb-1 font-medium">Dish Name <span class="text-red-400">*</span></label>
              <input v-model="form.name" type="text" placeholder="e.g. Chicken Adobo"
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
            </div>

            <!-- Category -->
            <div>
              <label class="block text-xs text-gray-500 mb-1 font-medium">Category</label>
              <select v-model="form.category"
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                <option value="">— Select category —</option>
                <option v-for="cat in CATEGORIES" :key="cat" :value="cat">{{ cat }}</option>
              </select>
            </div>

            <!-- Price -->
            <div>
              <label class="block text-xs text-gray-500 mb-1 font-medium">Price (₱) <span class="text-red-400">*</span></label>
              <input v-model.number="form.price" type="number" min="0" step="0.01" placeholder="0.00"
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
            </div>

            <!-- Description -->
            <div>
              <label class="block text-xs text-gray-500 mb-1 font-medium">Description</label>
              <textarea v-model="form.description" rows="2" placeholder="Optional description..."
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 resize-none"/>
            </div>

            <!-- Availability -->
            <div class="flex items-center gap-2">
              <input type="checkbox" v-model="form.active" id="activeChk" class="rounded border-gray-300 text-amber-600"/>
              <label for="activeChk" class="text-sm text-gray-600">Available</label>
            </div>

            <!-- Image Upload -->
            <div>
              <label class="block text-xs text-gray-500 mb-1 font-medium">Dish Photo <span class="text-gray-400 font-normal">(optional)</span></label>
              <!-- Preview -->
              <div v-if="imagePreview" class="relative mb-2 rounded-xl overflow-hidden border border-gray-200 bg-gray-50 h-36">
                <img :src="imagePreview" class="w-full h-full object-cover" />
                <button type="button" @click="clearImage"
                  class="absolute top-1.5 right-1.5 w-6 h-6 bg-black/50 hover:bg-black/70 text-white rounded-full text-xs flex items-center justify-center transition">
                  ✕
                </button>
              </div>
              <label class="flex items-center gap-2 cursor-pointer px-3 py-2 border border-dashed border-gray-300 rounded-lg hover:border-green-400 hover:bg-green-50 transition text-sm text-gray-500 hover:text-amber-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ imagePreview ? 'Change photo' : 'Upload photo' }}
                <input type="file" accept="image/*" class="hidden" @change="onImagePicked" />
              </label>
            </div>
          </div>

          <!-- Modal buttons -->
          <div class="flex gap-3 mt-6">
            <button @click="showFormModal = false"
              class="flex-1 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
              Cancel
            </button>
            <button @click="saveForm" :disabled="saving"
              class="flex-1 py-2 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50">
              {{ saving ? 'Saving...' : (editItem ? 'Save Changes' : 'Add Dish') }}
            </button>
          </div>
        </div>
      </div>
    </transition>


    <!-- ══════════ DELETE CONFIRM MODAL ══════════ -->
    <transition name="fade">
      <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 text-center">
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-10 0h14"/>
            </svg>
          </div>
          <h3 class="text-base font-bold text-gray-800 mb-1">Remove Dish</h3>
          <p class="text-sm text-gray-500 mb-5">Are you sure you want to remove <span class="font-semibold text-gray-700">{{ deleteTarget.name }}</span>? This cannot be undone.</p>
          <div class="flex gap-3">
            <button @click="deleteTarget = null"
              class="flex-1 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
              Cancel
            </button>
            <button @click="removeItem" :disabled="saving"
              class="flex-1 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50">
              {{ saving ? 'Removing...' : 'Remove' }}
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- ── Toast ── -->
    <transition name="fade">
      <div v-if="toast" class="fixed bottom-6 right-6 z-50 bg-green-600 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium">
        {{ toast }}
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'

const CATEGORIES = [
  'Main Course', 'Appetizer', 'Dessert', 'Beverage',
  'Pasta', 'Salad', 'Snacks', 'Rice Meal', 'Soup', 'Side Dish'
]

const PAGE_SIZE = 8

// ── State ──────────────────────────────────────────────────
const allItems      = ref([])
const loading       = ref(true)
const search        = ref('')
const activeCategory = ref('All')
const currentPage   = ref(1)

const showFormModal = ref(false)
const editItem      = ref(null)
const deleteTarget  = ref(null)
const saving        = ref(false)
const toast         = ref('')

const imageFile    = ref(null)
const imagePreview = ref('')

const form = ref({ name: '', category: '', price: '', description: '', active: true })

const IMG_BASE = 'http://localhost:8000/'
function dishImageUrl(item) {
  if (!item.image_url) return ''
  // Absolute URL → use as-is; frontend public asset (starts with /) → use as-is;
  // otherwise treat as a backend-hosted upload path
  if (item.image_url.startsWith('http') || item.image_url.startsWith('/')) return item.image_url
  return IMG_BASE + item.image_url
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

// ── Computed ───────────────────────────────────────────────
const filtered = computed(() => {
  let items = allItems.value
  if (activeCategory.value !== 'All') {
    items = items.filter(i => i.category === activeCategory.value)
  }
  if (search.value.trim()) {
    const q = search.value.toLowerCase()
    items = items.filter(i => i.name.toLowerCase().includes(q))
  }
  return items
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / PAGE_SIZE)))

const pagedItems = computed(() => {
  const start = (currentPage.value - 1) * PAGE_SIZE
  return filtered.value.slice(start, start + PAGE_SIZE)
})

// ── Helpers ────────────────────────────────────────────────
function formatMoney(v) {
  return Number(v || 0).toFixed(2)
}

function showToast(msg) {
  toast.value = msg
  setTimeout(() => { toast.value = '' }, 3000)
}

// ── Modal openers ──────────────────────────────────────────
function openAdd() {
  editItem.value = null
  form.value = { name: '', category: '', price: '', description: '', active: true }
  imageFile.value = null
  imagePreview.value = ''
  showFormModal.value = true
}

function openEdit(item) {
  editItem.value = item
  form.value = {
    name: item.name,
    category: item.category || '',
    price: item.price,
    description: item.description || '',
    active: !!item.active,
  }
  imageFile.value = null
  imagePreview.value = dishImageUrl(item)
  showFormModal.value = true
}

function confirmRemove(item) {
  deleteTarget.value = item
}

// ── Save (add / edit) ──────────────────────────────────────
async function saveForm() {
  if (!form.value.name || !form.value.price) return
  saving.value = true
  try {
    let response
    if (imageFile.value) {
      // Send as multipart/form-data so the image file can be uploaded
      const fd = new FormData()
      fd.append('name',        form.value.name)
      fd.append('category',    form.value.category || '')
      fd.append('price',       form.value.price)
      fd.append('description', form.value.description || '')
      fd.append('active',      form.value.active ? 1 : 0)
      fd.append('image',       imageFile.value)
      if (editItem.value) {
        response = await api.put(`/restaurant/menu-items/${editItem.value.id}`, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
      } else {
        response = await api.post('/restaurant/menu-items', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
      }
    } else {
      const payload = {
        name:        form.value.name,
        category:    form.value.category || null,
        price:       Number(form.value.price),
        description: form.value.description || null,
        active:      form.value.active ? 1 : 0,
      }
      if (editItem.value) {
        response = await api.put(`/restaurant/menu-items/${editItem.value.id}`, payload)
      } else {
        response = await api.post('/restaurant/menu-items', payload)
      }
    }

    if (editItem.value) {
      const idx = allItems.value.findIndex(i => i.id === editItem.value.id)
      if (idx !== -1) allItems.value[idx] = response.data.data
      showToast('Dish updated successfully!')
    } else {
      allItems.value.unshift(response.data.data)
      showToast('Dish added successfully!')
    }
    showFormModal.value = false
  } catch (e) {
    console.error(e)
  } finally {
    saving.value = false
  }
}

// ── Remove ─────────────────────────────────────────────────
async function removeItem() {
  if (!deleteTarget.value) return
  saving.value = true
  try {
    await api.delete(`/restaurant/menu-items/${deleteTarget.value.id}`)
    allItems.value = allItems.value.filter(i => i.id !== deleteTarget.value.id)
    showToast('Dish removed.')
    deleteTarget.value = null
  } catch (e) {
    console.error(e)
  } finally {
    saving.value = false
  }
}

// ── Print Menu ─────────────────────────────────────────────
function printMenu() {
  const grouped = {}
  allItems.value.filter(i => i.active).forEach(i => {
    const cat = i.category || 'Uncategorized'
    if (!grouped[cat]) grouped[cat] = []
    grouped[cat].push(i)
  })

  let html = `
    <html><head><title>Café Menu</title>
    <style>
      body { font-family: sans-serif; padding: 32px; color: #111; }
      h1 { font-size: 24px; text-align: center; margin-bottom: 4px; }
      p.sub { text-align: center; color: #666; font-size: 13px; margin-bottom: 24px; }
      h2 { font-size: 15px; font-weight: 700; border-bottom: 1px solid #ddd; padding-bottom: 4px; margin: 20px 0 10px; text-transform: uppercase; letter-spacing: 1px; }
      table { width: 100%; border-collapse: collapse; }
      td { padding: 6px 4px; font-size: 13px; border-bottom: 1px solid #f0f0f0; }
      td:last-child { text-align: right; font-weight: 600; }
    </style></head><body>
    <h1>Joanna's Nook Bed &amp; Breakfast</h1>
    <p class="sub">Menu — ${new Date().toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
  `
  for (const [cat, items] of Object.entries(grouped).sort()) {
    html += `<h2>${cat}</h2><div class="overflow-x-auto -mx-px"><table>`
    items.forEach(i => {
      html += `<tr><td>${i.name}</td><td>₱${formatMoney(i.price)}</td></tr>`
    })
    html += `</table></div>`
  }
  html += `</body></html>`

  const win = window.open('', '_blank', 'width=700,height=900')
  win.document.write(html)
  win.document.close()
  win.print()
}

// ── Load ───────────────────────────────────────────────────
onMounted(async () => {
  try {
    const res = await api.get('/restaurant/menu/all')
    allItems.value = res.data.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
