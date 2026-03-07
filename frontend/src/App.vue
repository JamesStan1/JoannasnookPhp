<template>
  <div id="app">
    <!-- Global Toast Notifications -->
    <ToastContainer />

    <!-- Post-login loading screen -->
    <LoadingScreen :visible="showLoginLoader" />

    <!-- Authenticated layout -->
    <template v-if="isLoggedIn">
      <NavBar @toggle-sidebar="sidebarOpen = !sidebarOpen" />

      <!-- Mobile overlay backdrop -->
      <transition name="fade-overlay">
        <div
          v-if="sidebarOpen"
          class="fixed inset-0 bg-black/50 z-30 lg:hidden"
          @click="sidebarOpen = false"
        ></div>
      </transition>

      <Sidebar :open="sidebarOpen" @close="sidebarOpen = false" />

      <div class="flex pt-20 min-h-screen">
        <main class="flex-1 lg:ml-72 min-w-0 min-h-[calc(100vh-80px)] overflow-auto bg-gray-50">
          <router-view />
        </main>
      </div>

    </template>

    <!-- Unauthenticated: full page -->
    <template v-else>
      <router-view />
    </template>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useAuthStore } from './stores/auth'
import NavBar from './components/NavBar.vue'
import Sidebar from './components/Sidebar.vue'
import ToastContainer from './components/ToastContainer.vue'
import LoadingScreen from './components/LoadingScreen.vue'
const authStore = useAuthStore()
const isLoggedIn = computed(() => authStore.isAuthenticated)
const sidebarOpen = ref(false)

const showLoginLoader = ref(false)
let loaderTimer = null

watch(isLoggedIn, (newVal, oldVal) => {
  if (newVal && !oldVal) {
    showLoginLoader.value = true
    sidebarOpen.value = false
    clearTimeout(loaderTimer)
    loaderTimer = setTimeout(() => {
      showLoginLoader.value = false
    }, 3000)
  }
})
</script>

<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.fade-overlay-enter-active,
.fade-overlay-leave-active { transition: opacity 0.25s ease; }
.fade-overlay-enter-from,
.fade-overlay-leave-to { opacity: 0; }
</style>
