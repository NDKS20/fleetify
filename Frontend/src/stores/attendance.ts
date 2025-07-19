import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios'
import { useAuthStore } from './auth'

interface Attendance {
  id: string
  clock_in: string | null
  clock_out: string | null
  created_at: string
  updated_at?: string
  employee?: {
    id: number
    name: string
    employee_id: string
    department: {
      id: number
      department_name: string
      max_clock_in_time: string
      max_clock_out_time: string
    }
  }
}

export const useAttendanceStore = defineStore('attendance', () => {
  const authStore = useAuthStore()
  const todayAttendance = ref<Attendance | null>(null)
  const attendanceHistory = ref<Attendance[]>([])
  const loading = ref(false)
  const historyLoading = ref(false)
  const totalItems = ref(0)
  const totalPages = ref(0)

  // Computed values
  const hasClockIn = computed(() => !!todayAttendance.value?.clock_in)
  const hasClockOut = computed(() => !!todayAttendance.value?.clock_out)
  const clockInTime = computed(() => {
    if (!todayAttendance.value?.clock_in) return null
    return new Date(todayAttendance.value.clock_in).toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      timeZone: 'Asia/Jakarta',
    })
  })
  const clockOutTime = computed(() => {
    if (!todayAttendance.value?.clock_out) return null
    return new Date(todayAttendance.value.clock_out).toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      timeZone: 'Asia/Jakarta',
    })
  })

  // Get today's date in YYYY-MM-DD format
  const getTodayDate = (): string => {
    return new Date().toISOString().split('T')[0]
  }

  // Get current datetime
  const formatIndonesiaDateTime = (): string => {
    return new Date().toISOString()
  }

  // Format date for display
  const formatDisplayDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('id-ID', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      timeZone: 'Asia/Jakarta',
    })
  }

  // Format time for display
  const formatDisplayTime = (dateString: string): string => {
    return new Date(dateString).toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      timeZone: 'Asia/Jakarta',
    })
  }

  // Fetch today's attendance
  const fetchTodayAttendance = async (): Promise<void> => {
    try {
      loading.value = true

      const todayDate = getTodayDate()

      const response = await axios.get('/attendances', {
        params: {
          filters: {
            created_at: `like:%${todayDate}%`,
            employee_id: authStore.user?.employee_id,
          },
        },
      })

      const attendances = response.data.data || response.data
      // Get the first (and should be only) attendance record for today
      todayAttendance.value =
        Array.isArray(attendances) && attendances.length > 0 ? attendances[0] : null
    } catch (error) {
      console.error('Error fetching today attendance:', error)
      todayAttendance.value = null
      throw error
    } finally {
      loading.value = false
    }
  }

  // Fetch attendance history with pagination
  const fetchAttendanceHistory = async (params?: {
    page?: number
    pageSize?: number
    search?: string
    date?: string
    department_id?: string | number
  }): Promise<void> => {
    try {
      historyLoading.value = true

      const apiParams: {
        page: number
        pageSize: number
        search?: string
        filters?: {
          created_at?: string
          'employee.department_id'?: string | number
        }
      } = {
        page: params?.page || 1,
        pageSize: params?.pageSize || 10,
      }

      // Add search if provided
      if (params?.search?.trim()) {
        apiParams.search = params.search.trim()
      }

      // Add filters if provided
      if (params?.date) {
        apiParams.filters = {
          ...apiParams.filters,
          created_at: `like:%${params.date}%`,
        }
      }

      if (params?.department_id) {
        apiParams.filters = {
          ...apiParams.filters,
          'employee.department_id': params.department_id,
        }
      }

      const response = await axios.get('/attendances', {
        params: apiParams,
      })

      // Handle response structure similar to PegawaiView
      if (response.data.data) {
        attendanceHistory.value = response.data.data
        totalItems.value = response.data.totalItems || response.data.total || 0
        totalPages.value =
          response.data.totalPages || Math.ceil(totalItems.value / (params?.pageSize || 10))
      } else {
        attendanceHistory.value = Array.isArray(response.data) ? response.data : []
        totalItems.value = attendanceHistory.value.length
        totalPages.value = Math.ceil(totalItems.value / (params?.pageSize || 10))
      }
    } catch (error) {
      console.error('Error fetching attendance history:', error)
      attendanceHistory.value = []
      throw error
    } finally {
      historyLoading.value = false
    }
  }

  // Clock in
  const clockIn = async (): Promise<void> => {
    try {
      loading.value = true
      // Get current datetime
      const timeString = formatIndonesiaDateTime()

      if (todayAttendance.value) {
        // Update existing attendance record
        const response = await axios.put(`/attendances/${todayAttendance.value.id}`, {
          clock_in: timeString,
        })
        todayAttendance.value = response.data.data || response.data
      } else {
        // Create new attendance record
        const response = await axios.post('/attendances', {
          clock_in: timeString,
        })
        todayAttendance.value = response.data.data || response.data
      }
    } catch (error) {
      console.error('Error clocking in:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  // Clock out
  const clockOut = async (): Promise<void> => {
    try {
      loading.value = true

      if (!todayAttendance.value) {
        throw new Error('No attendance record found for today')
      }

      // Get current datetime
      const timeString = formatIndonesiaDateTime()

      const response = await axios.put(`/attendances/${todayAttendance.value.id}`, {
        clock_out: timeString,
      })

      todayAttendance.value = response.data.data || response.data
    } catch (error) {
      console.error('Error clocking out:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  // Reset store
  const reset = () => {
    todayAttendance.value = null
    attendanceHistory.value = []
    loading.value = false
    historyLoading.value = false
  }

  return {
    todayAttendance,
    attendanceHistory,
    loading,
    historyLoading,
    totalItems,
    totalPages,
    hasClockIn,
    hasClockOut,
    clockInTime,
    clockOutTime,
    formatDisplayDate,
    formatDisplayTime,
    fetchTodayAttendance,
    fetchAttendanceHistory,
    clockIn,
    clockOut,
    reset,
  }
})
