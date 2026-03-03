<template>
  <teleport to="body">
    <div class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 w-80 pointer-events-none">
      <transition-group
        name="toast"
        tag="div"
        class="flex flex-col gap-3"
      >
        <div
          v-for="toast in toastStore.toasts"
          :key="toast.id"
          class="pointer-events-auto flex items-start gap-3 rounded-xl shadow-xl px-4 py-3.5 border text-sm font-light leading-snug relative overflow-hidden"
          :class="styles[toast.type].wrapper"
          @click="toastStore.dismiss(toast.id)"
          role="alert"
        >
          <!-- Icon -->
          <div class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-full flex items-center justify-center" :class="styles[toast.type].iconBg">
            <!-- Success checkmark -->
            <svg v-if="toast.type === 'success'" class="w-3 h-3" :class="styles[toast.type].icon" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <!-- Error X -->
            <svg v-else-if="toast.type === 'error'" class="w-3 h-3" :class="styles[toast.type].icon" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <!-- Warning ! -->
            <svg v-else-if="toast.type === 'warning'" class="w-3 h-3" :class="styles[toast.type].icon" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <!-- Info i -->
            <svg v-else class="w-3 h-3" :class="styles[toast.type].icon" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>

          <!-- Message -->
          <p class="flex-1" :class="styles[toast.type].text">{{ toast.message }}</p>

          <!-- Dismiss button -->
          <button @click.stop="toastStore.dismiss(toast.id)"
            class="flex-shrink-0 opacity-50 hover:opacity-100 transition-opacity mt-0.5" :class="styles[toast.type].text">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>

          <!-- Progress bar -->
          <div class="absolute bottom-0 left-0 h-0.5 rounded-full" :class="styles[toast.type].bar"
            :style="{ animationDuration: (toast.duration || 4000) + 'ms' }"
            style="animation: toast-progress linear forwards; width: 100%">
          </div>
        </div>
      </transition-group>
    </div>
  </teleport>
</template>

<script setup>
import { useToastStore } from '../stores/toast'
const toastStore = useToastStore()

const styles = {
  success: {
    wrapper: 'bg-white border-green-200 shadow-green-100/60',
    iconBg:  'bg-green-100',
    icon:    'text-green-600',
    text:    'text-gray-700',
    bar:     'bg-green-400',
  },
  error: {
    wrapper: 'bg-white border-red-200 shadow-red-100/60',
    iconBg:  'bg-red-100',
    icon:    'text-red-600',
    text:    'text-gray-700',
    bar:     'bg-red-400',
  },
  warning: {
    wrapper: 'bg-white border-yellow-200 shadow-yellow-100/60',
    iconBg:  'bg-yellow-100',
    icon:    'text-yellow-600',
    text:    'text-gray-700',
    bar:     'bg-yellow-400',
  },
  info: {
    wrapper: 'bg-white border-green-200 shadow-blue-100/60',
    iconBg:  'bg-green-100',
    icon:    'text-amber-600',
    text:    'text-gray-700',
    bar:     'bg-green-500',
  },
}
</script>

<style scoped>
/* Slide in from right */
.toast-enter-active { transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
.toast-leave-active { transition: all 0.25s ease-in; }
.toast-enter-from  { opacity: 0; transform: translateX(100%) scale(0.9); }
.toast-leave-to    { opacity: 0; transform: translateX(60%) scale(0.95); }

/* Progress bar shrink */
@keyframes toast-progress {
  from { width: 100%; }
  to   { width: 0%; }
}
</style>
