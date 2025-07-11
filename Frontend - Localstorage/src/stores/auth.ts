import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

interface User {
  email: string
  fullName: string
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const isAuthenticated = computed(() => !!user.value)

  // Static credentials
  const STATIC_EMAIL = 'admin@gmail.com'
  const STATIC_PASSWORD = 'admin'

  // Initialize from localStorage
  const initAuth = () => {
    const storedUser = localStorage.getItem('auth_user')
    if (storedUser) {
      user.value = JSON.parse(storedUser)
    }
  }

  // Login function
  const login = (email: string, password: string): boolean => {
    if (email === STATIC_EMAIL && password === STATIC_PASSWORD) {
      const userData: User = {
        email: STATIC_EMAIL,
        fullName: 'Administrator',
      }
      user.value = userData
      localStorage.setItem('auth_user', JSON.stringify(userData))
      return true
    }
    return false
  }

  // Logout function
  const logout = () => {
    user.value = null
    localStorage.removeItem('auth_user')
  }

  // Update user profile
  const updateUserProfile = (fullName: string) => {
    if (user.value) {
      user.value.fullName = fullName
      localStorage.setItem('auth_user', JSON.stringify(user.value))
    }
  }

  return {
    user,
    isAuthenticated,
    initAuth,
    login,
    logout,
    updateUserProfile,
  }
})
