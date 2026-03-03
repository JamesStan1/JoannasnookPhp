<template>
  <transition name="fade-loader">
    <div v-if="visible" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-white">
      <!-- Logo -->
      <div class="flex flex-col items-center gap-5">
        <div class="relative w-36 h-36 flex items-center justify-center">
          <!-- Spinning ring -->
          <svg class="absolute inset-0 w-full h-full animate-spin-slow" viewBox="0 0 144 144" fill="none">
            <circle cx="72" cy="72" r="66" stroke="#dcfce7" stroke-width="5"/>
            <path d="M72 6 A66 66 0 0 1 138 72" stroke="#166534" stroke-width="5" stroke-linecap="round"/>
          </svg>
          <!-- Logo image -->
          <img src="/Joannaslogo.png" alt="Joanna's" class="w-28 h-28 rounded-2xl object-contain p-2 relative z-10" />
        </div>

        <!-- Hotel name -->
        <div class="text-center">
          <div class="text-2xl font-semibold tracking-widest text-gray-900">Joanna's Nook</div>
          <div class="text-xs text-gray-500 tracking-widest uppercase mt-0.5">Bed &amp; Breakfast</div>
        </div>

        <!-- Progress bar -->
        <div class="w-48 h-1 bg-gray-100 rounded-full overflow-hidden mt-2">
          <div class="h-full bg-gradient-to-r from-green-700 to-amber-500 rounded-full animate-progress"></div>
        </div>

        <p class="text-sm text-gray-400 font-light tracking-wide">Loading your dashboard…</p>
      </div>
    </div>
  </transition>
</template>

<script setup>
defineProps({
  visible: {
    type: Boolean,
    default: false,
  },
})
</script>

<style scoped>
.animate-spin-slow {
  animation: spin 1.4s linear infinite;
}
@keyframes spin {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}

.animate-progress {
  animation: progress 3s ease-in-out forwards;
}
@keyframes progress {
  from { width: 0%; }
  to   { width: 100%; }
}

.fade-loader-enter-active {
  transition: opacity 0.2s ease;
}
.fade-loader-leave-active {
  transition: opacity 0.5s ease;
}
.fade-loader-enter-from,
.fade-loader-leave-to {
  opacity: 0;
}
</style>
