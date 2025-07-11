<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Profile Settings</h1>
          <p class="text-gray-600 dark:text-gray-400 mt-2">Update your profile information</p>
        </div>

        <div class="p-6">
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Email (readonly) -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Email Address
              </label>
              <input
                type="email"
                :value="authStore.user?.email"
                readonly
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed"
              />
            </div>

            <!-- Full Name -->
            <div>
              <label
                for="fullName"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
              >
                Full Name
              </label>
              <input
                id="fullName"
                v-model="form.fullName"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              />
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="loading"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
              >
                {{ loading ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useToastStore } from '../stores/toast'

const authStore = useAuthStore()
const toastStore = useToastStore()

const form = reactive({
  fullName: '',
})

const loading = ref(false)

const handleSubmit = async () => {
  loading.value = true

  try {
    authStore.updateUserProfile(form.fullName)
    toastStore.success('Profile updated successfully!', {
      title: 'Profile Updated',
      duration: 3000,
    })
  } catch {
    toastStore.error('Failed to update profile. Please try again.', {
      title: 'Update Failed',
      duration: 4000,
    })
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  // Initialize form with current user data
  if (authStore.user) {
    form.fullName = authStore.user.fullName
  }
})
</script>
