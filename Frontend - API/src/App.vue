<template>
  <div class="app-container">
    <AppNavbar v-if="authStore.isAuthenticated" />

    <main class="main-content" :class="{ 'pt-0': !authStore.isAuthenticated }">
      <RouterView />
    </main>

    <!-- Global Toast Notifications -->
    <ToastContainer />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { RouterView } from 'vue-router'
import AppNavbar from './components/AppNavbar.vue'
import ToastContainer from './components/ToastContainer.vue'
import { useAuthStore } from './stores/auth'
import { useThemeStore } from './stores/theme'

const authStore = useAuthStore()
const themeStore = useThemeStore()

// Initialize theme system when app mounts
onMounted(() => {
  themeStore.initTheme()
})
</script>

<style scoped>
.app-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
}
</style>
