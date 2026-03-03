<template>
  <div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow p-8">
      <h1 class="text-xl lg:text-3xl font-bold text-center mb-6">Register</h1>
      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-2">Name</label>
          <input v-model="form.name" type="text" required class="input-field" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">Email</label>
          <input v-model="form.email" type="email" required class="input-field" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">Phone</label>
          <input v-model="form.phone" type="tel" required class="input-field" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">Password</label>
          <input v-model="form.password" type="password" required class="input-field" />
        </div>
        <button type="submit" :disabled="loading" class="btn-primary w-full">
          {{ loading ? 'Registering...' : 'Register' }}
        </button>
      </form>
      <p class="text-center text-sm mt-4">
        Already have an account?
        <router-link to="/login" class="text-amber-600 hover:underline">Login</router-link>
      </p>
      <div v-if="error" class="mt-4 p-4 bg-red-100 text-red-800 rounded">{{ error }}</div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  name: '',
  email: '',
  phone: '',
  password: '',
})

const loading = ref(false)
const error = ref('')

const handleRegister = async () => {
  loading.value = true
  const success = await authStore.register(
    form.name,
    form.email,
    form.password,
    form.phone
  )
  loading.value = false

  if (success) {
    router.push('/login')
  } else {
    error.value = authStore.error
  }
}
</script>
