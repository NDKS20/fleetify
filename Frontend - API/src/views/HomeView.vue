<template>
  <div class="flex-1 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Welcome Section -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
            Welcome back, {{ authStore.user?.name }}!
          </h1>
          <p class="text-gray-600 dark:text-gray-400 text-lg">
            This is your admin dashboard. Use the navigation above to manage employees or update
            your profile.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
</script>
