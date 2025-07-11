import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios'

interface User {
  email: string
  name: string
  role?: {
    name: string
  }
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(null)
  const role = ref<string | null>(null)
  const isAuthenticated = computed(() => !!user.value && !!token.value)

  // Initialize from localStorage
  const initAuth = () => {
    const storedUser = localStorage.getItem('auth_user')
    const storedToken = localStorage.getItem('auth_token')
    const storedRole = localStorage.getItem('auth_role')

    if (storedUser && storedToken) {
      user.value = JSON.parse(storedUser)
      token.value = storedToken
      role.value = storedRole

      // Set axios authorization header
      axios.defaults.headers.common['Authorization'] = `Bearer ${storedToken}`
    }
  }

  // Login function with API integration
  const login = async (email: string, password: string): Promise<boolean> => {
    try {
      const response = await axios.post('/auth/login', {
        email,
        password,
      })

      const data = response.data

      // Set user data
      user.value = data.user || data

      token.value = data.token
      role.value = data.user?.role?.name || data.role?.name || null

      // Store in localStorage
      localStorage.setItem('auth_user', JSON.stringify(user.value))
      localStorage.setItem('auth_token', data.token)
      if (role.value) {
        localStorage.setItem('auth_role', role.value)
      }

      // Set axios authorization header
      axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`

      return true
    } catch (error: unknown) {
      console.error('Login error:', error)
      return false
    }
  }

  // Method for setting logged user data (as referenced in requirements)
  const setLogged = (data: any) => {
    if (data.user) {
      user.value = data.user
    } else {
      user.value = data
    }

    token.value = data.token
    role.value = data.user?.role?.name || data.role?.name || null

    // Store in localStorage
    localStorage.setItem('auth_user', JSON.stringify(user.value))
    localStorage.setItem('auth_token', data.token)
    if (role.value) {
      localStorage.setItem('auth_role', role.value)
    }

    // Set axios authorization header
    axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`
  }

  // Logout function
  const logout = async () => {
    try {
      // Call logout API endpoint
      await axios.post('/auth/logout')
    } catch (error: unknown) {
      console.error('Logout API error:', error)
      // Continue with local logout even if API call fails
    }

    // Clear local data regardless of API response
    user.value = null
    token.value = null
    role.value = null

    localStorage.removeItem('auth_user')
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_role')

    // Remove axios authorization header
    delete axios.defaults.headers.common['Authorization']
  }

  // Legacy setLogout method (as referenced in main-reference.ts)
  const setLogout = async () => {
    await logout()
  }

  // Update user profile
  const updateUserProfile = (name: string) => {
    if (user.value) {
      user.value.name = name
      localStorage.setItem('auth_user', JSON.stringify(user.value))
    }
  }

  return {
    user,
    token,
    role,
    isAuthenticated,
    initAuth,
    login,
    logout,
    setLogout,
    setLogged,
    updateUserProfile,
  }
})
