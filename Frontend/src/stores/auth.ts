import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios'

interface User {
  id?: number
  email: string
  name: string
  username?: string
  employee_id?: string
  access_permissions?: {
    id: number
    title: string
    name: string
  }[]
  created_at?: string
  created_by?: string | null
  deactivated_at?: string | null
  deactivated_by?: string | null
  deleted_at?: string | null
  deleted_by?: string | null
  is_active?: number
  updated_at?: string
  updated_by?: string | null
  role?: {
    id: number
    title: string
    name: string
    created_at?: string
    created_by?: string | null
    deactivated_at?: string | null
    deactivated_by?: string | null
    deleted_at?: string | null
    deleted_by?: string | null
    is_active?: number
    updated_at?: string
    updated_by?: string | null
  }
  employee?: {
    id: number
    employee_id: string
    department_id: number
    name: string
    address?: string
    created_at?: string
    created_by?: string | null
    deactivated_at?: string | null
    deactivated_by?: string | null
    deleted_at?: string | null
    deleted_by?: string | null
    is_active?: number
    updated_at?: string
    updated_by?: string | null
    department?: {
      id: number
      department_name: string
      max_clock_in_time: string
      max_clock_out_time: string
      created_at?: string
      created_by?: string | null
      deactivated_at?: string | null
      deactivated_by?: string | null
      deleted_at?: string | null
      deleted_by?: string | null
      is_active?: number
      updated_at?: string
      updated_by?: string | null
    }
  }
  department?: {
    id: number
    department_name: string
    max_clock_in_time: string
    max_clock_out_time: string
    created_at?: string
    created_by?: string | null
    deactivated_at?: string | null
    deactivated_by?: string | null
    deleted_at?: string | null
    deleted_by?: string | null
    is_active?: number
    updated_at?: string
    updated_by?: string | null
  }
}

interface AuthResponse {
  user?: User
  token: string
  role?: User['role']
  department?: User['department']
  email?: string
  name?: string
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(null)
  const role = ref<string | null>(null)
  const department = ref<User['department'] | null>(null)
  const isAuthenticated = computed(() => !!user.value && !!token.value)

  // Initialize from localStorage
  const initAuth = () => {
    const storedUser = localStorage.getItem('auth_user')
    const storedToken = localStorage.getItem('auth_token')
    const storedRole = localStorage.getItem('auth_role')
    const storedDepartment = localStorage.getItem('auth_department')

    if (storedUser && storedToken) {
      user.value = JSON.parse(storedUser)
      token.value = storedToken
      role.value = storedRole

      if (storedDepartment) {
        department.value = JSON.parse(storedDepartment)
      }

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
      department.value = data.user?.employee?.department || null

      // Store in localStorage
      localStorage.setItem('auth_user', JSON.stringify(user.value))
      localStorage.setItem('auth_token', data.token)
      if (role.value) {
        localStorage.setItem('auth_role', role.value)
      }
      if (department.value) {
        localStorage.setItem('auth_department', JSON.stringify(department.value))
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
  const setLogged = (data: AuthResponse) => {
    if (data.user) {
      user.value = data.user
    } else {
      // Create user object from auth response data
      user.value = {
        email: data.email || '',
        name: data.name || '',
        role: data.role,
        department: data.department,
      }
    }

    token.value = data.token
    role.value = data.user?.role?.name || data.role?.name || null
    department.value = data.user?.department || data.department || null

    // Store in localStorage
    localStorage.setItem('auth_user', JSON.stringify(user.value))
    localStorage.setItem('auth_token', data.token)
    if (role.value) {
      localStorage.setItem('auth_role', role.value)
    }
    if (department.value) {
      localStorage.setItem('auth_department', JSON.stringify(department.value))
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
    department.value = null

    localStorage.removeItem('auth_user')
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_role')
    localStorage.removeItem('auth_department')

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

  // Update user department
  const updateUserDepartment = (newDepartment: User['department']) => {
    if (user.value) {
      user.value.department = newDepartment
      department.value = newDepartment
      localStorage.setItem('auth_user', JSON.stringify(user.value))
      localStorage.setItem('auth_department', JSON.stringify(newDepartment))
    }
  }

  return {
    user,
    token,
    role,
    department,
    isAuthenticated,
    initAuth,
    login,
    logout,
    setLogout,
    setLogged,
    updateUserProfile,
    updateUserDepartment,
  }
})
