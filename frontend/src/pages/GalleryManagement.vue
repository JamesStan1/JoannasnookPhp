<template>
  <div class="min-h-screen bg-gray-50 p-6">

    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center gap-2 text-xs text-gray-400 uppercase tracking-widest mb-2">
        <router-link to="/settings" class="hover:text-green-600 transition">Settings</router-link>
        <span>/</span>
        <span class="text-gray-600">Gallery Management</span>
      </div>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Gallery Management
          </h1>
          <p class="text-sm text-gray-500 mt-1">Create albums and upload photos that appear on the homepage gallery.</p>
        </div>
        <span class="text-xs font-semibold bg-amber-100 text-amber-700 px-3 py-1.5 rounded-full">Gallery</span>
      </div>
    </div>

    <!-- Create Album Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 max-w-2xl">
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">New Album</p>
      <div class="flex gap-2 mb-3">
        <input
          v-model="newAlbumName"
          type="text"
          placeholder="Album name"
          class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400"
          @keyup.enter="createAlbum"
        />
        <button
          @click="createAlbum"
          :disabled="!newAlbumName.trim() || galleryLoading"
          class="bg-green-700 hover:bg-green-800 disabled:bg-gray-300 text-white text-xs font-medium px-5 py-2 rounded-lg transition whitespace-nowrap"
        >
          + Create Album
        </button>
      </div>
      <input
        v-model="newAlbumDescription"
        type="text"
        placeholder="Description (optional)"
        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400"
      />
    </div>

    <!-- Loading -->
    <div v-if="galleryLoading && galleryAlbums.length === 0" class="flex items-center justify-center py-20 text-gray-400 gap-3">
      <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
      </svg>
      <span class="text-sm font-light">Loading albums…</span>
    </div>

    <!-- Empty state -->
    <div v-else-if="galleryAlbums.length === 0" class="text-sm text-gray-400 italic text-center py-12 border border-dashed border-gray-200 rounded-2xl bg-white">
      No albums yet. Create one above.
    </div>

    <!-- Albums Grid -->
    <div v-else>
      <!-- Album count + page info -->
      <div class="flex items-center justify-between mb-4">
        <p class="text-xs text-gray-400">
          Showing albums {{ (albumPage - 1) * 6 + 1 }}–{{ Math.min(albumPage * 6, galleryAlbums.length) }} of {{ galleryAlbums.length }}
        </p>
        <!-- Album pagination controls -->
        <div v-if="totalAlbumPages > 1" class="flex items-center gap-1">
          <button
            @click="setAlbumPage(albumPage - 1)"
            :disabled="albumPage === 1"
            class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-200 hover:text-gray-700 disabled:opacity-30 disabled:cursor-not-allowed transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          </button>
          <template v-for="p in totalAlbumPages" :key="p">
            <button
              @click="setAlbumPage(p)"
              :class="p === albumPage
                ? 'bg-green-700 text-white'
                : 'text-gray-500 hover:bg-gray-200'"
              class="w-7 h-7 rounded-lg text-xs font-medium transition"
            >{{ p }}</button>
          </template>
          <button
            @click="setAlbumPage(albumPage + 1)"
            :disabled="albumPage === totalAlbumPages"
            class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-200 hover:text-gray-700 disabled:opacity-30 disabled:cursor-not-allowed transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div
          v-for="album in pagedAlbums"
          :key="album.id"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
        >
          <!-- Album header -->
          <div class="flex items-center justify-between bg-gray-50 px-4 py-3 border-b border-gray-100">
            <div class="min-w-0 flex-1">
              <p class="text-sm font-semibold text-gray-800 truncate">{{ album.name }}</p>
              <p v-if="album.description" class="text-xs text-gray-400 truncate">{{ album.description }}</p>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0 ml-3">
              <span class="text-xs text-gray-400 whitespace-nowrap">
                {{ album.photos?.length ?? 0 }} photo{{ (album.photos?.length ?? 0) === 1 ? '' : 's' }}
              </span>
              <label class="text-xs bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1 rounded-lg cursor-pointer transition font-medium whitespace-nowrap">
                + Add Photos
                <input type="file" accept="image/*" multiple class="hidden" @change="e => handleAlbumPhotoUpload(album.id, e)" />
              </label>
              <button @click="galleryAlbumToDelete = album" class="text-red-400 hover:text-red-600 transition p-1 rounded hover:bg-red-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Photos grid -->
          <div v-if="album.photos?.length" class="p-4">
            <div class="grid grid-cols-4 gap-2 mb-3">
              <div
                v-for="photo in pagedPhotos(album)"
                :key="photo.id"
                class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100 cursor-pointer"
                @click="galleryLightbox = photo.filename"
              >
                <img
                  :src="photoUrl(photo.filename)"
                  class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                  @error="$event.target.src='/placeholder-image.jpg'"
                />
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                  <button
                    @click.stop="deleteGalleryPhoto(album, photo)"
                    class="bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 transition"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>
                <p v-if="photo.caption" class="absolute bottom-0 left-0 right-0 text-[10px] text-white bg-black/60 px-1.5 py-1 truncate">
                  {{ photo.caption }}
                </p>
              </div>
            </div>
            <!-- Per-album photo pagination -->
            <div v-if="totalPhotoPages(album) > 1" class="flex items-center justify-between pt-2 border-t border-gray-100">
              <p class="text-[11px] text-gray-400">
                Page {{ getPhotoPage(album.id) }} of {{ totalPhotoPages(album) }}
                <span class="ml-1 text-gray-300">&middot;</span>
                <span class="ml-1">{{ album.photos.length }} photos total</span>
              </p>
              <div class="flex items-center gap-1">
                <button
                  @click="setPhotoPage(album.id, getPhotoPage(album.id) - 1, album)"
                  :disabled="getPhotoPage(album.id) === 1"
                  class="p-1 rounded text-gray-400 hover:bg-gray-100 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed transition"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <template v-for="p in totalPhotoPages(album)" :key="p">
                  <button
                    @click="setPhotoPage(album.id, p, album)"
                    :class="p === getPhotoPage(album.id)
                      ? 'bg-green-700 text-white'
                      : 'text-gray-500 hover:bg-gray-100'"
                    class="w-6 h-6 rounded text-[11px] font-medium transition"
                  >{{ p }}</button>
                </template>
                <button
                  @click="setPhotoPage(album.id, getPhotoPage(album.id) + 1, album)"
                  :disabled="getPhotoPage(album.id) === totalPhotoPages(album)"
                  class="p-1 rounded text-gray-400 hover:bg-gray-100 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed transition"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
              </div>
            </div>
          </div>
          <div v-else class="p-6 text-xs text-gray-400 text-center italic">
            No photos in this album yet. Click "+ Add Photos" to upload.
          </div>
        </div>
      </div>

      <!-- Bottom album pagination (repeated for convenience) -->
      <div v-if="totalAlbumPages > 1" class="flex items-center justify-center gap-1 mt-8">
        <button
          @click="setAlbumPage(1)"
          :disabled="albumPage === 1"
          class="px-3 py-1.5 rounded-lg text-xs text-gray-500 hover:bg-gray-200 disabled:opacity-30 disabled:cursor-not-allowed transition"
        >« First</button>
        <button
          @click="setAlbumPage(albumPage - 1)"
          :disabled="albumPage === 1"
          class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-200 hover:text-gray-700 disabled:opacity-30 disabled:cursor-not-allowed transition"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <template v-for="p in totalAlbumPages" :key="p">
          <button
            @click="setAlbumPage(p)"
            :class="p === albumPage
              ? 'bg-green-700 text-white shadow-sm'
              : 'text-gray-500 hover:bg-gray-200'"
            class="w-8 h-8 rounded-lg text-xs font-medium transition"
          >{{ p }}</button>
        </template>
        <button
          @click="setAlbumPage(albumPage + 1)"
          :disabled="albumPage === totalAlbumPages"
          class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-200 hover:text-gray-700 disabled:opacity-30 disabled:cursor-not-allowed transition"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
        <button
          @click="setAlbumPage(totalAlbumPages)"
          :disabled="albumPage === totalAlbumPages"
          class="px-3 py-1.5 rounded-lg text-xs text-gray-500 hover:bg-gray-200 disabled:opacity-30 disabled:cursor-not-allowed transition"
        >Last »</button>
      </div>
    </div>

    <!-- Delete Album Confirm Modal -->
    <div v-if="galleryAlbumToDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
      <div class="bg-white rounded-2xl shadow-xl p-6 w-80">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Delete Album</h3>
        <p class="text-sm text-gray-500 mb-5">
          This will permanently delete "<strong>{{ galleryAlbumToDelete.name }}</strong>" and all its photos. This cannot be undone.
        </p>
        <div class="flex gap-3">
          <button @click="galleryAlbumToDelete = null" class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm hover:bg-gray-50 transition">Cancel</button>
          <button @click="doDeleteGalleryAlbum" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg text-sm font-medium transition">Delete</button>
        </div>
      </div>
    </div>

    <!-- Photo Lightbox -->
    <div
      v-if="galleryLightbox"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/80"
      @click.self="galleryLightbox = null"
    >
      <button class="absolute top-4 right-4 text-white/70 hover:text-white transition" @click="galleryLightbox = null">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
      <img :src="photoUrl(galleryLightbox)" class="max-h-[85vh] max-w-[90vw] rounded-xl shadow-2xl object-contain" />
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import api from '../services/api.js'
import { useToastStore } from '../stores/toast'

const toast = useToastStore()

// -- State ---------------------------------------------------------------------
const galleryAlbums        = ref([])
const galleryLoading       = ref(false)
const newAlbumName         = ref('')
const newAlbumDescription  = ref('')
const galleryAlbumToDelete = ref(null)
const galleryLightbox      = ref(null)

// -- Pagination ----------------------------------------------------------------
const ALBUMS_PER_PAGE = 6
const PHOTOS_PER_PAGE = 8

const albumPage  = ref(1)
const photoPages = reactive({}) // albumId -> current page

const totalAlbumPages = computed(() =>
  Math.max(1, Math.ceil(galleryAlbums.value.length / ALBUMS_PER_PAGE))
)

const pagedAlbums = computed(() => {
  const start = (albumPage.value - 1) * ALBUMS_PER_PAGE
  return galleryAlbums.value.slice(start, start + ALBUMS_PER_PAGE)
})

function getPhotoPage(albumId) {
  return photoPages[albumId] ?? 1
}

function pagedPhotos(album) {
  const page  = getPhotoPage(album.id)
  const start = (page - 1) * PHOTOS_PER_PAGE
  return (album.photos ?? []).slice(start, start + PHOTOS_PER_PAGE)
}

function totalPhotoPages(album) {
  return Math.max(1, Math.ceil((album.photos?.length ?? 0) / PHOTOS_PER_PAGE))
}

function setAlbumPage(n) {
  albumPage.value = Math.min(Math.max(1, n), totalAlbumPages.value)
}

function setPhotoPage(albumId, n, album) {
  const max = totalPhotoPages(album)
  photoPages[albumId] = Math.min(Math.max(1, n), max)
}

// -- Helpers -------------------------------------------------------------------
function photoUrl(filename) {
  return `http://localhost:8000/uploads/gallery/${filename}`
}

// -- API -----------------------------------------------------------------------
async function fetchGalleryAlbums() {
  galleryLoading.value = true
  try {
    const res = await api.get('/admin/gallery/albums')
    galleryAlbums.value = res.data.data ?? res.data ?? []
  } catch {
    toast.error('Failed to load albums')
  } finally {
    galleryLoading.value = false
  }
}

async function createAlbum() {
  if (!newAlbumName.value.trim()) return
  galleryLoading.value = true
  try {
    const res = await api.post('/admin/gallery/albums', {
      name: newAlbumName.value.trim(),
      description: newAlbumDescription.value.trim(),
    })
    galleryAlbums.value.unshift({ ...(res.data.data ?? res.data), photos: [] })
    albumPage.value = 1
    newAlbumName.value = ''
    newAlbumDescription.value = ''
    toast.success('Album created')
  } catch {
    toast.error('Failed to create album')
  } finally {
    galleryLoading.value = false
  }
}

async function doDeleteGalleryAlbum() {
  const album = galleryAlbumToDelete.value
  galleryAlbumToDelete.value = null
  try {
    await api.delete(`/admin/gallery/albums/${album.id}`)
    galleryAlbums.value = galleryAlbums.value.filter(a => a.id !== album.id)
    // clamp page if last album on current page was deleted
    if (albumPage.value > totalAlbumPages.value) albumPage.value = totalAlbumPages.value
    toast.success('Album deleted')
  } catch {
    toast.error('Failed to delete album')
  }
}

async function handleAlbumPhotoUpload(albumId, event) {
  const files = Array.from(event.target.files)
  if (!files.length) return
  for (const file of files) {
    const fd = new FormData()
    fd.append('photo', file)
    try {
      const res = await api.post(`/admin/gallery/albums/${albumId}/photos`, fd, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      const photo = res.data.data ?? res.data
      const album = galleryAlbums.value.find(a => a.id === albumId)
      if (album) {
        if (!album.photos) album.photos = []
        album.photos.push(photo)
        album.photo_count = (album.photo_count ?? 0) + 1
      }
    } catch {
      toast.error(`Failed to upload ${file.name}`)
    }
  }
  event.target.value = ''
  toast.success('Photo(s) uploaded')
}

async function deleteGalleryPhoto(album, photo) {
  try {
    await api.delete(`/admin/gallery/photos/${photo.id}`)
    album.photos = album.photos.filter(p => p.id !== photo.id)
    album.photo_count = Math.max(0, (album.photo_count ?? 1) - 1)
    toast.success('Photo deleted')
  } catch {
    toast.error('Failed to delete photo')
  }
}

// -- Init ----------------------------------------------------------------------
onMounted(fetchGalleryAlbums)
</script>
