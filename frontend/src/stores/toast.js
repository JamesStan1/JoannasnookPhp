import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useToastStore = defineStore('toast', () => {
  const toasts = ref([])
  let nextId = 0

  function show(message, type = 'info', duration = 4000) {
    const id = ++nextId
    toasts.value.push({ id, message, type, duration })
    if (duration > 0) {
      setTimeout(() => dismiss(id), duration)
    }
    return id
  }

  function dismiss(id) {
    const idx = toasts.value.findIndex(t => t.id === id)
    if (idx !== -1) toasts.value.splice(idx, 1)
  }

  const success = (msg, dur)  => show(msg, 'success', dur)
  const error   = (msg, dur)  => show(msg, 'error',   dur ?? 5000)
  const warning = (msg, dur)  => show(msg, 'warning', dur)
  const info    = (msg, dur)  => show(msg, 'info',    dur)

  return { toasts, show, dismiss, success, error, warning, info }
})
