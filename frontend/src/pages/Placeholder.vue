<template>
  <div class="bg-gray-50 min-h-full">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
      <div class="p-8 max-w-7xl mx-auto">
        <h1 class="text-2xl lg:text-4xl font-light text-gray-900 tracking-tight">{{ route.meta.title || 'Module' }}</h1>
        <p class="text-gray-500 font-light mt-1">Under development</p>
      </div>
    </div>

    <!-- Content -->
    <div class="p-8 max-w-7xl mx-auto flex items-center justify-center" style="min-height: 420px">
      <div class="text-center">
        <div class="w-20 h-20 rounded-full bg-green-50 border border-green-100 flex items-center justify-center mx-auto mb-6 shadow-sm">
          <svg class="w-9 h-9 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
          </svg>
        </div>
        <h2 class="text-2xl font-light text-gray-700 mb-3">{{ route.meta.title || 'Module' }}</h2>
        <p class="text-gray-400 font-light text-sm leading-relaxed max-w-xs mx-auto">
          This module is currently under development.<br>It will be available soon.
        </p>
        <button
          @click="router.back()"
          class="mt-8 px-5 py-2 bg-green-800 hover:bg-green-900 text-white text-sm font-light rounded-lg transition"
        >
          ? Go Back
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
const route = useRoute()
const router = useRouter()
</script>
