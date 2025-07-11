<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4">
    <div class="w-full max-w-md">
      <div
        class="bg-white dark:bg-gray-800 p-10 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700"
      >
        <!-- Title -->
        <div class="text-center mb-8">
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sign in to your account</h1>
        </div>

        <!-- Demo Credentials Info -->
        <AppAlert type="info" title="Demo Credentials" class="mb-6">
          <div class="text-sm">
            <div class="bg-blue-50 dark:bg-blue-900/30 rounded-md font-mono text-xs">
              <div><strong>Email:</strong> admin@gmail.com</div>
              <div><strong>Password:</strong> admin</div>
            </div>
          </div>
        </AppAlert>

        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Email Field -->
          <div>
            <label
              for="email"
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Email address
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-[0.5rem] text-base text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
            />
          </div>

          <!-- Password Field -->
          <div>
            <label
              for="password"
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Password
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-[0.5rem] text-base text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
            />
          </div>

          <!-- Sign In Button -->
          <div>
            <button
              type="submit"
              :disabled="loading"
              class="w-full py-2 px-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold text-base rounded-[0.5rem] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md"
            >
              {{ loading ? 'Signing in...' : 'Sign in' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useToastStore } from '../../stores/toast'
import AppAlert from '../../components/AppAlert.vue'

const router = useRouter()
const authStore = useAuthStore()
const toastStore = useToastStore()

const form = reactive({
  email: '',
  password: '',
})

const loading = ref(false)

const handleSubmit = async () => {
  loading.value = true

  try {
    const loginSuccess = authStore.login(form.email, form.password)

    if (loginSuccess) {
      toastStore.success('Login successful!', {
        title: 'Welcome!',
        duration: 2000,
      })

      router.push('/')
    } else {
      toastStore.error('Invalid email or password', {
        title: 'Login Failed',
        duration: 4000,
      })
    }
  } catch {
    toastStore.error('An error occurred during login', {
      title: 'System Error',
      duration: 4000,
    })
  } finally {
    loading.value = false
  }
}
</script>
