<template>
  <transition name="fade-loader">
    <div v-if="visible" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-white">
      <!-- Logo -->
      <div class="flex flex-col items-center gap-5">
        <div class="flex items-center justify-center">
          <!-- Logo image -->
          <img src="/Joannaslogo.png" alt="Joanna's" class="w-28 h-28 rounded-2xl object-contain p-2" />
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
