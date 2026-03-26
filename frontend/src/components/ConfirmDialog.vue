<template>
  <!-- H3 – User Control and Freedom: confirmation dialog for destructive / irreversible actions -->
  <Teleport to="body">
    <transition name="confirm-fade">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-[9998] flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        :aria-labelledby="`confirm-title-${uid}`"
        :aria-describedby="`confirm-desc-${uid}`"
        @keydown.esc="cancel"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="cancel"></div>

        <!-- Panel -->
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-7 text-center">

          <!-- Icon -->
          <div
            class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-5"
            :class="iconBg"
          >
            <!-- Danger -->
            <svg v-if="variant === 'danger'" class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <!-- Warning -->
            <svg v-else-if="variant === 'warning'" class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <!-- Info / default -->
            <svg v-else class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>

          <h2 :id="`confirm-title-${uid}`" class="text-lg font-semibold text-gray-900 mb-2">{{ title }}</h2>
          <p v-if="message" :id="`confirm-desc-${uid}`" class="text-sm text-gray-500 mb-7 leading-relaxed">{{ message }}</p>

          <div class="flex gap-3">
            <!-- Cancel – always the easy, safe exit -->
            <button
              ref="cancelBtn"
              @click="cancel"
              class="flex-1 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition"
            >
              {{ cancelLabel }}
            </button>
            <!-- Confirm -->
            <button
              @click="confirm"
              class="flex-1 py-2.5 text-sm font-medium text-white rounded-xl transition shadow-sm"
              :class="confirmBtnClass"
            >
              {{ confirmLabel }}
            </button>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
import { computed, watch, nextTick, ref } from 'vue'

const props = defineProps({
  /** v-model: whether the dialog is open */
  modelValue: { type: Boolean, default: false },
  title:        { type: String, default: 'Are you sure?' },
  message:      { type: String, default: '' },
  confirmLabel: { type: String, default: 'Confirm' },
  cancelLabel:  { type: String, default: 'Cancel' },
  /** 'danger' | 'warning' | 'info' */
  variant: { type: String, default: 'danger' },
})

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel'])

// Unique ID so multiple instances don't share ARIA label IDs
const uid = Math.random().toString(36).slice(2, 7)

const cancelBtn = ref(null)

// Auto-focus the Cancel button when opened (safe default per H3)
watch(() => props.modelValue, async (val) => {
  if (val) {
    await nextTick()
    cancelBtn.value?.focus()
  }
})

const iconBg = computed(() => {
  if (props.variant === 'danger')  return 'bg-red-100'
  if (props.variant === 'warning') return 'bg-amber-100'
  return 'bg-blue-100'
})

const confirmBtnClass = computed(() => {
  if (props.variant === 'danger')  return 'bg-red-600 hover:bg-red-700'
  if (props.variant === 'warning') return 'bg-amber-500 hover:bg-amber-600'
  return 'bg-blue-600 hover:bg-blue-700'
})

function confirm() {
  emit('confirm')
  emit('update:modelValue', false)
}

function cancel() {
  emit('cancel')
  emit('update:modelValue', false)
}
</script>

<style scoped>
.confirm-fade-enter-active { transition: all 0.2s ease; }
.confirm-fade-leave-active { transition: all 0.15s ease; }
.confirm-fade-enter-from,
.confirm-fade-leave-to { opacity: 0; }
.confirm-fade-enter-from .relative,
.confirm-fade-leave-to .relative { transform: scale(0.95); }
</style>
