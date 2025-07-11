<template>
  <nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Left side - Logo/Brand -->
        <div class="flex items-center">
          <router-link to="/" class="flex items-center space-x-2">
            <span class="text-xl font-bold text-gray-900 dark:text-white">Tugas 1 - Aksamedia</span>
          </router-link>
        </div>

        <!-- Center - Navigation Links (Desktop) -->
        <div class="hidden md:flex items-center space-x-8">
          <router-link
            to="/"
            class="text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-gray-700"
            :class="{
              '!text-blue-600 dark:!text-blue-400': $route.path === '/',
            }"
          >
            Home
          </router-link>
          <router-link
            to="/crud"
            class="text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-gray-700"
            :class="{
              '!text-blue-600 dark:!text-blue-400': $route.path.startsWith('/crud'),
            }"
          >
            CRUD
          </router-link>
          <router-link
            to="/profile"
            class="text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-gray-700"
            :class="{
              '!text-blue-600 dark:!text-blue-400': $route.path === '/profile',
            }"
          >
            Profile
          </router-link>
        </div>

        <!-- Right side - User Menu -->
        <div class="flex items-center space-x-4">
          <!-- Mobile menu button -->
          <button
            @click="toggleMobileMenu"
            class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            aria-controls="mobile-menu"
            :aria-expanded="isMobileMenuOpen"
          >
            <span class="sr-only">Open main menu</span>
            <!-- Hamburger icon -->
            <svg
              v-show="!isMobileMenuOpen"
              class="block h-6 w-6 transition-opacity duration-200"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
            <!-- Close icon -->
            <svg
              v-show="isMobileMenuOpen"
              class="block h-6 w-6 transition-opacity duration-200"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>

          <!-- Theme Dropdown (Desktop only) -->
          <div class="hidden md:block relative" ref="themeDropdownRef">
            <button
              @click="toggleThemeDropdown"
              class="flex items-center space-x-2 p-2 rounded-md text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              <!-- Current Theme Icon -->
              <svg
                v-if="themeStore.currentTheme === 'dark'"
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                ></path>
              </svg>
              <svg
                v-else-if="themeStore.currentTheme === 'light'"
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                ></path>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                ></path>
              </svg>
              <svg
                class="w-4 h-4 transition-transform"
                :class="{ 'rotate-180': isThemeDropdownOpen }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"
                ></path>
              </svg>
            </button>

            <!-- Theme Dropdown Menu -->
            <div
              v-show="isThemeDropdownOpen"
              class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 z-50"
            >
              <div class="py-1">
                <button
                  @click="setTheme('light')"
                  class="w-full flex items-center space-x-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                  :class="{ 'bg-gray-100 dark:bg-gray-700': themeStore.selectedTheme === 'light' }"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                    ></path>
                  </svg>
                  <span>Light</span>
                </button>
                <button
                  @click="setTheme('dark')"
                  class="w-full flex items-center space-x-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                  :class="{ 'bg-gray-100 dark:bg-gray-700': themeStore.selectedTheme === 'dark' }"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                    ></path>
                  </svg>
                  <span>Dark</span>
                </button>
                <button
                  @click="setTheme('system')"
                  class="w-full flex items-center space-x-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                  :class="{ 'bg-gray-100 dark:bg-gray-700': themeStore.selectedTheme === 'system' }"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                    ></path>
                  </svg>
                  <span>System</span>
                </button>
              </div>
            </div>
          </div>

          <!-- User Dropdown (Desktop only) -->
          <div class="hidden md:block relative" ref="dropdownRef">
            <button
              @click="toggleDropdown"
              class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              <div
                class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium"
              >
                {{ userInitials }}
              </div>
              <span class="hidden sm:block text-sm font-medium">{{
                authStore.user?.fullName
              }}</span>
              <svg
                class="w-4 h-4 transition-transform"
                :class="{ 'rotate-180': isDropdownOpen }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"
                ></path>
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <div
              v-show="isDropdownOpen"
              class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 z-50"
            >
              <div class="py-1">
                <div
                  class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700"
                >
                  {{ authStore.user?.email }}
                </div>
                <router-link
                  to="/profile"
                  @click="closeDropdown"
                  class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                  :class="{
                    '!text-blue-600 dark:!text-blue-400': $route.path === '/profile',
                  }"
                >
                  Edit Profile
                </router-link>
                <button
                  @click="handleLogout"
                  class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                >
                  Logout
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div v-show="isMobileMenuOpen" class="md:hidden" id="mobile-menu">
      <div
        class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700"
      >
        <!-- Navigation Links -->
        <router-link
          to="/"
          @click="closeMobileMenu"
          class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          :class="{
            '!text-blue-600 dark:!text-blue-400': $route.path === '/',
          }"
        >
          Home
        </router-link>
        <router-link
          to="/crud"
          @click="closeMobileMenu"
          class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          :class="{
            '!text-blue-600 dark:!text-blue-400': $route.path.startsWith('/crud'),
          }"
        >
          CRUD
        </router-link>
        <router-link
          to="/profile"
          @click="closeMobileMenu"
          class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          :class="{
            '!text-blue-600 dark:!text-blue-400': $route.path === '/profile',
          }"
        >
          Profile
        </router-link>

        <!-- Divider -->
        <div class="border-t border-gray-200 dark:border-gray-700 my-3"></div>

        <!-- Theme Section -->
        <div class="px-3 py-2">
          <p
            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2"
          >
            Theme
          </p>
          <div class="space-y-1">
            <button
              @click="setTheme('light')"
              class="w-full flex items-center space-x-3 px-3 py-2 rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              :class="{ 'bg-gray-50 dark:bg-gray-700': themeStore.selectedTheme === 'light' }"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                ></path>
              </svg>
              <span>Light</span>
            </button>
            <button
              @click="setTheme('dark')"
              class="w-full flex items-center space-x-3 px-3 py-2 rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              :class="{ 'bg-gray-50 dark:bg-gray-700': themeStore.selectedTheme === 'dark' }"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                ></path>
              </svg>
              <span>Dark</span>
            </button>
            <button
              @click="setTheme('system')"
              class="w-full flex items-center space-x-3 px-3 py-2 rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              :class="{ 'bg-gray-50 dark:bg-gray-700': themeStore.selectedTheme === 'system' }"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                ></path>
              </svg>
              <span>System</span>
            </button>
          </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200 dark:border-gray-700 my-3"></div>

        <!-- User Section -->
        <div class="px-3 py-2">
          <div class="flex items-center space-x-3 mb-3">
            <div
              class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium"
            >
              {{ userInitials }}
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900 dark:text-white">
                {{ authStore.user?.fullName }}
              </p>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ authStore.user?.email }}
              </p>
            </div>
          </div>
          <div class="space-y-1">
            <router-link
              to="/profile"
              @click="closeMobileMenu"
              class="block px-3 py-2 rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              :class="{
                '!text-blue-600 dark:!text-blue-400': $route.path === '/profile',
              }"
            >
              Edit Profile
            </router-link>
            <button
              @click="handleLogout"
              class="w-full text-left px-3 py-2 rounded-md text-sm text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useThemeStore } from '../stores/theme'

const router = useRouter()
const authStore = useAuthStore()
const themeStore = useThemeStore()

const isDropdownOpen = ref(false)
const dropdownRef = ref<HTMLElement>()
const isThemeDropdownOpen = ref(false)
const themeDropdownRef = ref<HTMLElement>()
const isMobileMenuOpen = ref(false)

const userInitials = computed(() => {
  if (!authStore.user?.fullName) return 'A'
  return authStore.user.fullName
    .split(' ')
    .map((name) => name.charAt(0))
    .join('')
    .toUpperCase()
})

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value
}

const closeDropdown = () => {
  isDropdownOpen.value = false
}

const toggleThemeDropdown = () => {
  isThemeDropdownOpen.value = !isThemeDropdownOpen.value
}

const closeThemeDropdown = () => {
  isThemeDropdownOpen.value = false
}

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}

const setTheme = (theme: 'light' | 'dark' | 'system') => {
  themeStore.setTheme(theme)
  closeThemeDropdown()
}

const handleLogout = () => {
  authStore.logout()
  closeDropdown()
  router.push('/login')
}

// Close dropdowns when clicking outside
const handleClickOutside = (event: Event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    closeDropdown()
  }
  if (themeDropdownRef.value && !themeDropdownRef.value.contains(event.target as Node)) {
    closeThemeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
