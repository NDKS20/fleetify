import { ref, computed, watch } from 'vue'
import { defineStore } from 'pinia'

type Theme = 'light' | 'dark' | 'system'

export const useThemeStore = defineStore('theme', () => {
  const selectedTheme = ref<Theme>('system')
  const isDarkMode = ref(false)

  // Get system preference
  const getSystemPreference = (): boolean => {
    return window.matchMedia('(prefers-color-scheme: dark)').matches
  }

  // Computed current theme
  const currentTheme = computed(() => {
    if (selectedTheme.value === 'system') {
      return isDarkMode.value ? 'dark' : 'light'
    }
    return selectedTheme.value
  })

  // Apply theme to document - following Tailwind CSS documentation pattern
  const applyTheme = () => {
    document.documentElement.classList.toggle(
      'dark',
      selectedTheme.value === 'dark' || (selectedTheme.value === 'system' && isDarkMode.value),
    )
  }

  // Set theme
  const setTheme = (theme: Theme) => {
    selectedTheme.value = theme
    localStorage.setItem('theme', theme)
    applyTheme()
  }

  // Toggle between light and dark
  const toggleTheme = () => {
    // Simple toggle: if currently showing dark (regardless of source), switch to light, otherwise switch to dark
    const currentlyDark =
      selectedTheme.value === 'dark' || (selectedTheme.value === 'system' && isDarkMode.value)

    if (currentlyDark) {
      setTheme('light')
    } else {
      setTheme('dark')
    }
  }

  // Initialize theme
  const initTheme = () => {
    // Check localStorage first
    const savedTheme = localStorage.getItem('theme') as Theme
    if (savedTheme && ['light', 'dark', 'system'].includes(savedTheme)) {
      selectedTheme.value = savedTheme
    }

    // Set initial system preference
    isDarkMode.value = getSystemPreference()

    // Apply initial theme immediately - following Tailwind CSS documentation pattern
    document.documentElement.classList.toggle(
      'dark',
      selectedTheme.value === 'dark' || (selectedTheme.value === 'system' && isDarkMode.value),
    )

    // Watch for system preference changes
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
    const handleSystemChange = (e: MediaQueryListEvent) => {
      isDarkMode.value = e.matches
      if (selectedTheme.value === 'system') {
        applyTheme()
      }
    }

    mediaQuery.addEventListener('change', handleSystemChange)

    // Watch for theme changes
    watch(selectedTheme, applyTheme)
    watch(isDarkMode, () => {
      if (selectedTheme.value === 'system') {
        applyTheme()
      }
    })
  }

  return {
    selectedTheme,
    currentTheme,
    isDarkMode,
    setTheme,
    toggleTheme,
    initTheme,
  }
})
