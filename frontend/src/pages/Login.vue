<template>
  <div class="relative min-h-screen bg-cover bg-center flex items-center justify-center py-12 px-4" style="background-image: url('/food.jpg');">
    <div class="absolute inset-0 bg-black/40"></div>

    <!-- Success Notification -->
    <transition name="slide-down">
      <div v-if="showSuccess" class="fixed top-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 bg-green-50 border border-green-300 text-green-800 px-6 py-4 rounded-xl shadow-lg">
        <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span class="text-sm font-medium">Login successful! Redirecting...</span>
      </div>
    </transition>

    <div class="relative z-10 w-full max-w-md">
      <!-- Header -->
      <div class="text-center mb-10">
        <img src="/Joannaslogo.png" alt="Joanna's Logo" class="h-28 w-28 object-contain mx-auto mb-4 rounded-2xl shadow" />
        <h1 class="text-2xl lg:text-4xl font-light text-white mb-2 tracking-tight">Joanna's Nook Bed &amp; Breakfast</h1>
        <p class="text-gray-200 font-light">Staff Portal</p>
      </div>

      <!-- Login Card -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10">
        <h2 class="text-2xl font-light text-gray-900 mb-8 text-center">Welcome Back</h2>
        
        <form @submit.prevent="handleLogin" class="space-y-6">
          <div>
            <label class="block text-sm font-light text-gray-700 mb-2">Email Address</label>
            <input 
              v-model="email" 
              type="email" 
              required 
              placeholder="staff@srcbhotel.com"
              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-green-800 focus:ring-1 focus:ring-green-800 font-light transition"
            />
          </div>

          <div>
            <label class="block text-sm font-light text-gray-700 mb-2">Password</label>
            <div class="relative">
              <input 
                v-model="password" 
                :type="showPassword ? 'text' : 'password'" 
                required 
                placeholder="••••••••"
                class="w-full px-4 py-3 pr-12 border border-gray-200 rounded-lg focus:outline-none focus:border-green-800 focus:ring-1 focus:ring-green-800 font-light transition"
              />
              <button 
                type="button" 
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition"
              >
                <!-- Eye open -->
                <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <!-- Eye closed -->
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Remember Me + Forgot Password -->
          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer select-none">
              <input type="checkbox" v-model="rememberMe" class="w-4 h-4 rounded border-gray-300 accent-green-800 cursor-pointer" />
              <span class="text-sm font-light text-gray-600">Remember me</span>
            </label>
            <button type="button" @click="showForgotModal = true" class="text-sm text-green-800 hover:text-amber-700 font-light transition">
              Forgot password?
            </button>
          </div>

          <button 
            type="submit" 
            :disabled="loading" 
            class="w-full bg-green-800 hover:bg-green-900 disabled:bg-gray-400 text-white font-light py-3 rounded-lg transition duration-300"
          >
            {{ loading ? 'Signing in...' : 'Sign In' }}
          </button>
        </form>

        <!-- Error Message -->
        <div v-if="error" class="mt-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm font-light">
          {{ error }}
        </div>

        <!-- Footer Links -->
        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
          <p class="text-sm text-gray-600 font-light">
            Don't have an account?
            <button type="button" @click="showForgotModal = true" class="text-green-800 hover:text-amber-700 font-medium transition">Contact administrator</button>
          </p>
        </div>
      </div>

      <!-- Back to Homepage -->
      <div class="mt-6 text-center">
        <router-link to="/" class="inline-flex items-center gap-1.5 text-sm font-medium text-white bg-white/20 hover:bg-white/30 backdrop-blur-sm border border-white/40 px-4 py-2 rounded-full transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
          </svg>
          Back to Homepage
        </router-link>
      </div>

      <!-- Footer -->
      <div class="mt-6 text-center">
          <p class="text-xs text-gray-200 font-light">&copy; 2026 Joanna's Nook Bed &amp; Breakfast. All rights reserved.</p>
      </div>
    </div>

    <!-- Forgot Password Modal -->
    <div v-if="showForgotModal" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 p-4" @click.self="closeForgotModal">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-8">
        <h3 class="text-lg font-light text-gray-800 mb-1">Forgot Password?</h3>
        <p class="text-sm text-gray-400 mb-6">Submit a request and the IT team will assist you.</p>

        <div v-if="forgotSuccess" class="bg-green-50 border border-green-100 rounded-xl p-4 mb-4">
          <p class="text-sm text-green-800">Your request has been submitted. Please wait for the IT team to contact you.</p>
        </div>

        <form v-else @submit.prevent="submitForgotRequest" class="space-y-4">
          <div>
            <label class="block text-sm font-light text-gray-700 mb-1">Your Name</label>
            <input v-model="forgotName" type="text" required placeholder="e.g. Juan dela Cruz"
              class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-green-800 focus:ring-1 focus:ring-green-800" />
          </div>
          <div>
            <label class="block text-sm font-light text-gray-700 mb-1">Email Address</label>
            <input v-model="forgotEmail" type="email" required placeholder="your@email.com"
              class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-green-800 focus:ring-1 focus:ring-green-800" />
          </div>
          <div>
            <label class="block text-sm font-light text-gray-700 mb-1">Message (optional)</label>
            <textarea v-model="forgotMessage" rows="3" placeholder="Briefly describe your issue..."
              class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-green-800 focus:ring-1 focus:ring-green-800 resize-none"></textarea>
          </div>
          <div v-if="forgotError" class="text-sm text-red-600">{{ forgotError }}</div>
          <div class="flex gap-3 pt-1">
            <button type="button" @click="closeForgotModal"
              class="flex-1 border border-gray-200 text-gray-600 text-sm py-2 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            <button type="submit" :disabled="forgotLoading"
              class="flex-1 bg-green-800 hover:bg-green-900 disabled:bg-gray-400 text-white text-sm font-light py-2 rounded-lg transition">
              {{ forgotLoading ? 'Submitting...' : 'Submit Request' }}
            </button>
          </div>
        </form>

        <button v-if="forgotSuccess" @click="closeForgotModal"
          class="w-full mt-4 bg-green-800 hover:bg-green-900 text-white text-sm font-light py-2.5 rounded-lg transition">
          Close
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)
const showPassword = ref(false)
const showSuccess = ref(false)
const error = ref(null)
const rememberMe = ref(false)
const showForgotModal = ref(false)

// Forgot password form state
const forgotName = ref('')
const forgotEmail = ref('')
const forgotMessage = ref('')
const forgotLoading = ref(false)
const forgotError = ref(null)
const forgotSuccess = ref(false)

const closeForgotModal = () => {
  showForgotModal.value = false
  forgotName.value = ''
  forgotEmail.value = ''
  forgotMessage.value = ''
  forgotError.value = null
  forgotSuccess.value = false
}

const submitForgotRequest = async () => {
  forgotLoading.value = true
  forgotError.value = null
  try {
    await api.post('/it/forgot-password-requests', {
      name: forgotName.value,
      email: forgotEmail.value,
      message: forgotMessage.value || 'No additional details provided.',
    })
    forgotSuccess.value = true
  } catch (err) {
    forgotError.value = err.response?.data?.message || 'Failed to submit request. Please try again.'
  } finally {
    forgotLoading.value = false
  }
}

const handleLogin = async () => {
  loading.value = true
  error.value = null
  const success = await authStore.login(email.value, password.value)
  loading.value = false

  if (success) {
    showSuccess.value = true
    setTimeout(() => {
      router.push(authStore.roleHomePath)
    }, 3000)
  } else {
    error.value = authStore.error
  }
}
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}
.slide-down-enter-from {
  opacity: 0;
  transform: translateX(-50%) translateY(-20px);
}
.slide-down-leave-to {
  opacity: 0;
  transform: translateX(-50%) translateY(-20px);
}
</style>
