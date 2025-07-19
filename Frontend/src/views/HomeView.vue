<template>
  <div class="flex-1 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Welcome Section -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-6 py-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
            Welcome back, {{ authStore.user?.name }}!
          </h1>
          <p class="text-gray-600 dark:text-gray-400 text-lg">
            {{ authStore.department?.department_name }}
          </p>
        </div>
      </div>

      <!-- Attendance Section -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-6">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Absensi Hari Ini</h2>

          <!-- Loading State -->
          <div v-if="attendanceStore.loading" class="flex justify-center py-8">
            <div
              class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"
            ></div>
          </div>

          <!-- Attendance Cards -->
          <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Clock In Card -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Absen Masuk</h3>
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-full">
                  <svg
                    class="w-5 h-5 text-green-600 dark:text-green-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
                    ></path>
                  </svg>
                </div>
              </div>

              <div v-if="attendanceStore.hasClockIn" class="mb-4">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Waktu Masuk:</p>
                <p
                  class="text-2xl font-bold"
                  :class="
                    getClockInTextColor(
                      attendanceStore.todayAttendance?.clock_in || '',
                      authStore.user?.employee?.department?.max_clock_in_time || '',
                    )
                  "
                >
                  {{ attendanceStore.clockInTime }}
                </p>
                <p
                  v-if="authStore.user?.employee?.department?.max_clock_in_time"
                  class="text-xs text-gray-400 dark:text-gray-500 mt-1"
                >
                  Max: {{ authStore.user?.employee?.department?.max_clock_in_time }}
                </p>
              </div>
              <div v-else class="mb-4">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Belum Absen</p>
                <p class="text-2xl font-bold text-gray-400 dark:text-gray-600">--:--</p>
                <p
                  v-if="authStore.user?.employee?.department?.max_clock_in_time"
                  class="text-xs text-gray-400 dark:text-gray-500 mt-1"
                >
                  Max: {{ authStore.user?.employee?.department?.max_clock_in_time }}
                </p>
              </div>

              <button
                @click="handleClockIn"
                :disabled="attendanceStore.hasClockIn || attendanceStore.loading"
                :class="[
                  'w-full px-4 py-2 rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2',
                  attendanceStore.hasClockIn
                    ? 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed'
                    : 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
                ]"
              >
                <svg
                  v-if="attendanceStore.loading"
                  class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                {{ attendanceStore.hasClockIn ? 'Sudah Absen Masuk' : 'Absen Masuk' }}
              </button>
            </div>

            <!-- Clock Out Card -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Absen Keluar</h3>
                <div class="p-2 bg-red-100 dark:bg-red-900 rounded-full">
                  <svg
                    class="w-5 h-5 text-red-600 dark:text-red-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013 3v1"
                    ></path>
                  </svg>
                </div>
              </div>

              <div v-if="attendanceStore.hasClockOut" class="mb-4">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Waktu Keluar:</p>
                <p
                  class="text-2xl font-bold"
                  :class="
                    getClockOutTextColor(
                      attendanceStore.todayAttendance?.clock_out || '',
                      authStore.user?.employee?.department?.max_clock_out_time || '',
                    )
                  "
                >
                  {{ attendanceStore.clockOutTime }}
                </p>
                <p
                  v-if="authStore.user?.employee?.department?.max_clock_out_time"
                  class="text-xs text-gray-400 dark:text-gray-500 mt-1"
                >
                  Max: {{ authStore.user?.employee?.department?.max_clock_out_time }}
                </p>
              </div>
              <div v-else class="mb-4">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Belum Absen</p>
                <p class="text-2xl font-bold text-gray-400 dark:text-gray-600">--:--</p>
                <p
                  v-if="authStore.user?.employee?.department?.max_clock_out_time"
                  class="text-xs text-gray-400 dark:text-gray-500 mt-1"
                >
                  Max: {{ authStore.user?.employee?.department?.max_clock_out_time }}
                </p>
              </div>

              <button
                @click="handleClockOut"
                :disabled="
                  !attendanceStore.hasClockIn ||
                  attendanceStore.hasClockOut ||
                  attendanceStore.loading
                "
                :class="[
                  'w-full px-4 py-2 rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2',
                  !attendanceStore.hasClockIn || attendanceStore.hasClockOut
                    ? 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed'
                    : 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500',
                ]"
              >
                <svg
                  v-if="attendanceStore.loading"
                  class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                {{
                  !attendanceStore.hasClockIn
                    ? 'Absen Masuk Dulu'
                    : attendanceStore.hasClockOut
                      ? 'Sudah Absen Keluar'
                      : 'Absen Keluar'
                }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Attendance History Section -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg mt-6">
        <div class="px-6 py-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
              Ketepatan Absensi Karyawan
            </h2>
          </div>

          <!-- Filter Controls -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div>
              <label
                for="search-filter"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
              >
                Cari Pegawai
              </label>
              <input
                id="search-filter"
                v-model="searchTerm"
                @input="handleSearch"
                type="text"
                placeholder="Cari pegawai..."
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
              />
            </div>

            <div>
              <label
                for="date-filter"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
              >
                Filter Tanggal
              </label>
              <input
                id="date-filter"
                v-model="selectedDate"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                @change="applyFilters"
              />
            </div>

            <div>
              <label
                for="department-filter"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
              >
                Filter Departemen
              </label>
              <select
                id="department-filter"
                v-model="selectedDepartment"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
              >
                <option value="">Semua Departemen</option>
                <option v-for="dept in availableDepartments" :key="dept.id" :value="dept.id">
                  {{ dept.department_name }}
                </option>
              </select>
            </div>

            <div class="flex items-end">
              <button
                @click="clearFilters"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Reset Filter
              </button>
            </div>
          </div>

          <!-- Items per page and total count -->
          <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600 dark:text-gray-400">Tampilkan:</span>
              <select
                v-model="itemsPerPage"
                @change="handleItemsPerPageChange"
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              >
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
              </select>
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
              Total: {{ paginationInfo.totalItems }} data
            </div>
          </div>

          <!-- Loading State -->
          <div
            v-if="attendanceStore.historyLoading && attendanceStore.attendanceHistory.length === 0"
            class="flex justify-center py-8"
          >
            <div
              class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"
            ></div>
          </div>

          <!-- No Data State -->
          <div
            v-else-if="
              !attendanceStore.historyLoading && attendanceStore.attendanceHistory.length === 0
            "
            class="text-center py-8"
          >
            <svg
              class="mx-auto h-12 w-12 text-gray-400"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              aria-hidden="true"
            >
              <path
                vector-effect="non-scaling-stroke"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
              />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
              Tidak ada riwayat absensi
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Belum ada data absensi yang tersimpan.
            </p>
          </div>

          <!-- Attendance History Table -->
          <div v-else class="overflow-hidden shadow ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Pegawai
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Departemen
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Tanggal
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Absen Masuk
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Absen Keluar
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Status Ketepatan
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Batas Waktu
                  </th>
                </tr>
              </thead>
              <tbody
                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600"
              >
                <tr
                  v-for="attendance in paginatedAttendance"
                  :key="attendance.id"
                  class="hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ attendance.employee?.name || '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ attendance.employee?.department?.department_name || '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ attendanceStore.formatDisplayDate(attendance.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span
                      v-if="attendance.clock_in"
                      class="inline-flex items-center"
                      :class="
                        getClockInTextColor(
                          attendance.clock_in,
                          attendance.employee?.department?.max_clock_in_time || '',
                        )
                      "
                    >
                      <svg
                        class="w-4 h-4 text-green-500 mr-1"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      {{ attendanceStore.formatDisplayTime(attendance.clock_in) }}
                    </span>
                    <span v-else class="text-gray-400 dark:text-gray-500">-</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span
                      v-if="attendance.clock_out"
                      class="inline-flex items-center"
                      :class="
                        getClockOutTextColor(
                          attendance.clock_out,
                          attendance.employee?.department?.max_clock_out_time || '',
                        )
                      "
                    >
                      <svg
                        class="w-4 h-4 text-red-500 mr-1"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      {{ attendanceStore.formatDisplayTime(attendance.clock_out) }}
                    </span>
                    <span v-else class="text-gray-400 dark:text-gray-500">-</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    <div class="flex items-center">
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="[
                          getPunctualityStatus(attendance).color,
                          getPunctualityStatus(attendance).status === 'Tepat Waktu'
                            ? 'bg-green-100 dark:bg-green-900'
                            : getPunctualityStatus(attendance).status === 'Terlambat'
                              ? 'bg-red-100 dark:bg-red-900'
                              : getPunctualityStatus(attendance).status === 'Tidak Absen Keluar'
                                ? 'bg-yellow-100 dark:bg-yellow-900'
                                : 'bg-gray-100 dark:bg-gray-900',
                        ]"
                      >
                        {{ getPunctualityStatus(attendance).icon }}
                        {{ getPunctualityStatus(attendance).status }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                    <div v-if="attendance.employee?.department">
                      <div class="mb-1">
                        <span class="font-medium">Masuk:</span>
                        {{ attendance.employee.department.max_clock_in_time }}
                      </div>
                      <div>
                        <span class="font-medium">Keluar:</span>
                        {{ attendance.employee.department.max_clock_out_time }}
                      </div>
                    </div>
                    <span v-else class="text-gray-400 dark:text-gray-500">-</span>
                  </td>
                </tr>

                <!-- Empty State -->
                <tr v-if="paginatedAttendance.length === 0">
                  <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                    {{
                      searchTerm || selectedDate || selectedDepartment
                        ? 'Tidak ada data yang cocok dengan filter.'
                        : 'Tidak ada data.'
                    }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
              <div class="text-sm text-gray-600 dark:text-gray-400">
                Menampilkan
                {{ Math.min((currentPage - 1) * itemsPerPage + 1, paginationInfo.totalItems) }}
                hingga
                {{ Math.min(currentPage * itemsPerPage, paginationInfo.totalItems) }}
                dari {{ paginationInfo.totalItems }} data
              </div>

              <div class="flex items-center space-x-2">
                <button
                  @click="setCurrentPage(currentPage - 1)"
                  :disabled="currentPage <= 1"
                  class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                  Sebelumnya
                </button>

                <template v-for="page in getVisiblePages()" :key="page">
                  <button
                    v-if="page !== '...'"
                    @click="setCurrentPage(Number(page))"
                    :class="[
                      'px-3 py-1 border rounded-md text-sm transition-colors',
                      page === currentPage
                        ? 'bg-blue-600 border-blue-600 text-white'
                        : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                    ]"
                  >
                    {{ page }}
                  </button>
                  <span v-else class="px-3 py-1 text-sm text-gray-400">...</span>
                </template>

                <button
                  @click="setCurrentPage(currentPage + 1)"
                  :disabled="currentPage >= paginationInfo.totalPages"
                  class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                  Selanjutnya
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, computed, watch } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useAttendanceStore } from '../stores/attendance'
import { useToastStore } from '../stores/toast'
import { useDepartmentStore } from '../stores/department'
import axios from 'axios'

const authStore = useAuthStore()
const attendanceStore = useAttendanceStore()
const toastStore = useToastStore()
const departmentStore = useDepartmentStore()

// State for filters and pagination
const selectedDate = ref('')
const selectedDepartment = ref('')
const searchTerm = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)

// Computed
const filteredAttendance = computed(() => {
  let filtered = attendanceStore.attendanceHistory

  // Apply frontend filters if needed
  if (selectedDepartment.value) {
    filtered = filtered.filter(
      (attendance) => attendance.employee?.department?.id?.toString() === selectedDepartment.value,
    )
  }

  if (searchTerm.value.trim()) {
    const search = searchTerm.value.toLowerCase()
    filtered = filtered.filter(
      (attendance) =>
        attendance.employee?.name?.toLowerCase().includes(search) ||
        attendance.employee?.department?.department_name?.toLowerCase().includes(search),
    )
  }

  return filtered
})

const paginationInfo = computed(() => {
  const filteredTotal = filteredAttendance.value.length
  return {
    currentPage: currentPage.value,
    itemsPerPage: itemsPerPage.value,
    totalItems: filteredTotal,
    totalPages: Math.ceil(filteredTotal / itemsPerPage.value),
  }
})

const paginatedAttendance = computed(() => {
  // Apply pagination to filtered results
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredAttendance.value.slice(start, end)
})

// Get unique departments from attendance data as fallback
const getUniqueDepartments = computed(() => {
  const departments = new Map()

  // Add departments from attendance data
  attendanceStore.attendanceHistory.forEach((attendance) => {
    if (attendance.employee?.department) {
      departments.set(attendance.employee.department.id.toString(), attendance.employee.department)
    }
  })

  // Convert to array for v-for
  return Array.from(departments.values())
})

// Use departments from attendance data if available, otherwise use department store
const availableDepartments = computed(() => {
  if (getUniqueDepartments.value.length > 0) {
    return getUniqueDepartments.value.map((dept) => ({
      id: dept.id.toString(),
      department_name: dept.department_name,
    }))
  }
  return departmentStore.items
})

// Helper for pagination
const getVisiblePages = (): (number | string)[] => {
  const total = paginationInfo.value.totalPages
  const current = currentPage.value
  const pages: (number | string)[] = []

  if (total <= 7) {
    for (let i = 1; i <= total; i++) {
      pages.push(i)
    }
  } else {
    if (current <= 4) {
      pages.push(1, 2, 3, 4, 5, '...', total)
    } else if (current >= total - 3) {
      pages.push(1, '...', total - 4, total - 3, total - 2, total - 1, total)
    } else {
      pages.push(1, '...', current - 1, current, current + 1, '...', total)
    }
  }

  return pages
}

const handleSearch = () => {
  // Reset page when search changes
  currentPage.value = 1
  // No need for API call since filtering is done on frontend
}

// Watch filters and reset pagination
watch([selectedDepartment], () => {
  currentPage.value = 1
})

watch([selectedDate], () => {
  currentPage.value = 1
  if (selectedDate.value) {
    fetchAttendanceData()
  }
})

const handleItemsPerPageChange = () => {
  currentPage.value = 1 // Reset to first page when changing items per page
  // No need to call fetchAttendanceData since we're doing frontend pagination
}

const setCurrentPage = (page: number) => {
  const maxPage = paginationInfo.value.totalPages
  if (page >= 1 && page <= maxPage) {
    currentPage.value = page
    fetchAttendanceData()
  }
}

// Fetch attendance data with filters and pagination
const fetchAttendanceData = async () => {
  try {
    const params: {
      page?: number
      pageSize?: number
      search?: string
      date?: string
    } = {
      page: currentPage.value,
      pageSize: itemsPerPage.value,
    }

    // Only pass date filter to API, do department and search filtering on frontend
    if (selectedDate.value) {
      params.date = selectedDate.value
    }

    await attendanceStore.fetchAttendanceHistory(params)
  } catch (error) {
    console.error('Error fetching attendance data:', error)
    toastStore.error('Gagal memuat data absensi', {
      title: 'Error',
      duration: 4000,
    })
  }
}

// Apply filters
const applyFilters = () => {
  // Only reset page for date filter since it requires API call
  if (selectedDate.value) {
    currentPage.value = 1
    fetchAttendanceData()
  }
  // Department and search filtering happens in computed property, no API call needed
}

// Clear filters
const clearFilters = () => {
  selectedDate.value = ''
  selectedDepartment.value = ''
  searchTerm.value = ''
  currentPage.value = 1
  fetchAttendanceData()
}

// Handle clock in
const handleClockIn = async () => {
  try {
    await attendanceStore.clockIn()
    toastStore.success('Berhasil absen masuk!', {
      title: 'Absensi',
      duration: 3000,
    })

    fetchAttendanceData()
  } catch (error) {
    console.error('Clock in error:', error)
    toastStore.error('Gagal absen masuk. Silakan coba lagi.', {
      title: 'Error',
      duration: 4000,
    })
  }
}

// Handle clock out
const handleClockOut = async () => {
  try {
    await attendanceStore.clockOut()
    toastStore.success('Berhasil absen keluar!', {
      title: 'Absensi',
      duration: 3000,
    })

    fetchAttendanceData()
  } catch (error) {
    console.error('Clock out error:', error)
    toastStore.error('Gagal absen keluar. Silakan coba lagi.', {
      title: 'Error',
      duration: 4000,
    })
  }
}

// Get punctuality status
const getPunctualityStatus = (attendance: {
  clock_in: string | null
  clock_out: string | null
  employee?: {
    department?: {
      max_clock_in_time: string
      max_clock_out_time: string
    }
  }
}) => {
  if (!attendance.employee?.department) {
    return { status: 'Unknown', color: 'text-gray-500', icon: '?' }
  }

  const clockInLate = attendance.clock_in
    ? isClockInLate(attendance.clock_in, attendance.employee.department.max_clock_in_time)
    : false
  const clockOutEarly = attendance.clock_out
    ? isClockOutEarly(attendance.clock_out, attendance.employee.department.max_clock_out_time)
    : false

  if (!attendance.clock_in && !attendance.clock_out) {
    return { status: 'Tidak Hadir', color: 'text-red-600', icon: '✗' }
  } else if (!attendance.clock_in) {
    return { status: 'Tidak Absen Masuk', color: 'text-red-600', icon: '✗' }
  } else if (!attendance.clock_out) {
    return { status: 'Tidak Absen Keluar', color: 'text-yellow-600', icon: '!' }
  } else if (clockInLate || clockOutEarly) {
    return { status: 'Terlambat', color: 'text-red-600', icon: '⚠' }
  } else {
    return { status: 'Tepat Waktu', color: 'text-green-600', icon: '✓' }
  }
}

// Check if clock in time is late (after max allowed time)
const isClockInLate = (clockInTime: string, maxClockInTime: string) => {
  if (!maxClockInTime || !clockInTime) {
    return false
  }

  const clockIn = new Date(clockInTime)
  const maxTime = maxClockInTime // Format: "HH:mm:ss"

  // Create a date object for the same day as clock in with the max allowed time
  const maxAllowedTime = new Date(clockIn)
  const [hours, minutes, seconds] = maxTime.split(':').map(Number)
  maxAllowedTime.setHours(hours, minutes, seconds, 0)

  return clockIn > maxAllowedTime
}

// Check if clock out time is early (before max allowed time)
const isClockOutEarly = (clockOutTime: string, maxClockOutTime: string) => {
  if (!maxClockOutTime || !clockOutTime) return false

  const clockOut = new Date(clockOutTime)
  const maxTime = maxClockOutTime // Format: "HH:mm:ss"

  // Create a date object for the same day as clock out with the max allowed time
  const maxAllowedTime = new Date(clockOut)
  const [hours, minutes, seconds] = maxTime.split(':').map(Number)
  maxAllowedTime.setHours(hours, minutes, seconds, 0)

  return clockOut > maxAllowedTime
}

// Get clock in text color class
const getClockInTextColor = (clockInTime: string, maxClockInTime: string) => {
  return isClockInLate(clockInTime, maxClockInTime)
    ? 'text-red-600 dark:text-red-400'
    : 'text-gray-900 dark:text-white'
}

// Get clock out text color class
const getClockOutTextColor = (clockOutTime: string, maxClockOutTime: string) => {
  return isClockOutEarly(clockOutTime, maxClockOutTime)
    ? 'text-red-600 dark:text-red-400'
    : 'text-gray-900 dark:text-white'
}

// Initialize attendance data when component mounts
onMounted(async () => {
  try {
    // Load today's attendance, department data, and attendance history
    await Promise.all([attendanceStore.fetchTodayAttendance(), fetchAttendanceData()])

    // Initialize department store and fetch from API
    departmentStore.init()

    // Try to fetch departments from API as well
    try {
      await axios.get('/departments')
    } catch (error) {
      console.log('Could not fetch departments from API, using local data:', error)
    }

    axios.get('/auth/me').then((response) => {
      authStore.setLogged({
        user: response.data,
        token: authStore.token || '',
      })
    })
  } catch (error) {
    console.error('Error fetching attendance:', error)
    toastStore.error('Gagal memuat data absensi', {
      title: 'Error',
      duration: 4000,
    })
  }
})
</script>
