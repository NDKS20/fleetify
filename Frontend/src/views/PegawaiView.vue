<template>
  <div class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Pegawai</h1>
              <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola data pegawai</p>
            </div>
            <button
              @click="openCreateModal"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            >
              Tambah Pegawai
            </button>
          </div>
        </div>

        <!-- Search and Filter -->
        <div class="px-6 py-4">
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
              <input
                v-model="searchTerm"
                @input="handleSearch"
                type="text"
                placeholder="Cari pegawai"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              />
            </div>
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
          </div>
        </div>
      </div>

      <!-- Employee Table -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <!-- Loading State -->
        <div v-if="loading" class="p-8 text-center">
          <div
            class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
          ></div>
          <p class="mt-2 text-gray-600 dark:text-gray-400">Memuat data...</p>
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
              <tr>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  Nama
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  Employee ID
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  Departemen
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  Alamat
                </th>
                <th
                  class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  Aksi
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr
                v-for="employee in paginatedEmployees"
                :key="employee.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"
                >
                  {{ employee.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  {{ employee.employee_id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  {{ getDepartmentName(employee.department_id) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  {{ employee.address }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                  <button
                    @click.stop="toggleDropdown(employee.id, $event)"
                    class="inline-flex justify-center items-center w-8 h-8 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-gray-300 transition-colors"
                  >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path
                        d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
                      ></path>
                    </svg>
                  </button>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-if="paginatedEmployees.length === 0">
                <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                  {{
                    searchTerm ? 'Tidak ada data yang cocok dengan pencarian.' : 'Tidak ada data.'
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
              dari {{ paginationInfo.totalItems }} pegawai
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

    <!-- Dropdown Menu (Outside Table) -->
    <div
      v-show="openDropdownId"
      class="absolute z-50 w-32 rounded-md shadow-lg bg-white dark:bg-gray-700 focus:outline-none"
      @click.stop
      :style="{
        top: dropdownPosition.top + 'px',
        left: dropdownPosition.left + 'px',
      }"
    >
      <div class="py-1">
        <button
          @click="selectedEmployee && openEditModal(selectedEmployee)"
          class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
        >
          <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
            ></path>
          </svg>
          Edit
        </button>
        <button
          @click="selectedEmployee && confirmDelete(selectedEmployee)"
          class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
        >
          <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
            ></path>
          </svg>
          Hapus
        </button>
      </div>
    </div>

    <!-- Employee Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
      @keydown.esc="closeModal"
    >
      <div
        class="fixed inset-0 bg-black opacity-60 transition-opacity"
        aria-hidden="true"
        @click="closeModal"
      ></div>

      <div
        ref="modalContent"
        class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full max-h-full overflow-y-auto"
        @click.stop
      >
        <form @submit.prevent="submitForm">
          <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="w-full">
                <h3
                  id="modal-title"
                  class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4"
                >
                  {{ isEditing ? 'Edit Pegawai' : 'Tambah Pegawai Baru' }}
                </h3>

                <div class="space-y-4">
                  <!-- Name -->
                  <div>
                    <label
                      for="name"
                      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                    >
                      Nama <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="name"
                      ref="firstInput"
                      v-model="form.name"
                      type="text"
                      required
                      :class="[
                        'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors',
                        formErrors.name
                          ? 'border-red-300 dark:border-red-600'
                          : 'border-gray-300 dark:border-gray-600',
                      ]"
                      placeholder="Masukkan nama pegawai"
                      @blur="validateField('name')"
                    />
                    <p v-if="formErrors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                      {{ formErrors.name }}
                    </p>
                  </div>

                  <!-- Department -->
                  <div>
                    <label
                      for="department_id"
                      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                    >
                      Departemen <span class="text-red-500">*</span>
                    </label>
                    <select
                      id="department_id"
                      v-model="form.department_id"
                      required
                      :class="[
                        'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors',
                        formErrors.department_id
                          ? 'border-red-300 dark:border-red-600'
                          : 'border-gray-300 dark:border-gray-600',
                      ]"
                      @blur="validateField('department_id')"
                    >
                      <option value="">Pilih departemen</option>
                      <option
                        v-for="department in departments"
                        :key="department.id"
                        :value="department.id"
                      >
                        {{ department.department_name }}
                      </option>
                    </select>
                    <p
                      v-if="formErrors.department_id"
                      class="mt-1 text-sm text-red-600 dark:text-red-400"
                    >
                      {{ formErrors.department_id }}
                    </p>
                  </div>

                  <!-- Address -->
                  <div>
                    <label
                      for="address"
                      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                    >
                      Alamat <span class="text-red-500">*</span>
                    </label>
                    <textarea
                      id="address"
                      v-model="form.address"
                      rows="3"
                      required
                      :class="[
                        'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors',
                        formErrors.address
                          ? 'border-red-300 dark:border-red-600'
                          : 'border-gray-300 dark:border-gray-600',
                      ]"
                      placeholder="Masukkan alamat lengkap"
                      @blur="validateField('address')"
                    ></textarea>
                    <p
                      v-if="formErrors.address"
                      class="mt-1 text-sm text-red-600 dark:text-red-400"
                    >
                      {{ formErrors.address }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="submit"
              :disabled="submitting || !isFormValid"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <svg
                v-if="submitting"
                class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
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
              {{ submitting ? 'Menyimpan...' : isEditing ? 'Perbarui' : 'Simpan' }}
            </button>
            <button
              type="button"
              @click="closeModal"
              ref="cancelButton"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
            >
              Batal
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      aria-labelledby="delete-modal-title"
      role="dialog"
      aria-modal="true"
      @keydown.esc="cancelDelete"
    >
      <div
        class="fixed inset-0 bg-black opacity-60 transition-opacity"
        aria-hidden="true"
        @click="cancelDelete"
      ></div>

      <div
        ref="deleteModalContent"
        class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full"
        @click.stop
      >
        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div
              class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10"
            >
              <svg
                class="h-6 w-6 text-red-600 dark:text-red-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.888-.833-2.598 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"
                />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3
                id="delete-modal-title"
                class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
              >
                Hapus Pegawai
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  Apakah Anda yakin ingin menghapus pegawai
                  <strong>{{ employeeToDelete?.name }}</strong
                  >? Tindakan ini tidak dapat dibatalkan.
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="deleteEmployee"
            :disabled="deleting"
            ref="deleteButton"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <svg
              v-if="deleting"
              class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
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
            {{ deleting ? 'Menghapus...' : 'Hapus' }}
          </button>
          <button
            @click="cancelDelete"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
          >
            Batal
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, onUnmounted, computed, nextTick, watch } from 'vue'
import { useToastStore } from '../stores/toast'
import axios from 'axios'

interface Employee {
  id: string
  name: string
  employee_id: string
  department_id: string
  address: string
  createdAt?: string
  updatedAt?: string
}

interface Department {
  id: string
  department_name: string
}

interface EmployeeCreateData {
  name: string
  employee_id: string
  department_id: string
  address: string
}

interface PaginationInfo {
  currentPage: number
  itemsPerPage: number
  totalItems: number
  totalPages: number
}

interface FormErrors {
  name?: string
  employee_id?: string
  department_id?: string
  address?: string
}

// Utils
const toastStore = useToastStore()

// State
const employees = ref<Employee[]>([])
const departments = ref<Department[]>([])
const loading = ref(false)
const searchTerm = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const totalItems = ref(0)
const totalPages = ref(0)

const showModal = ref(false)
const showDeleteModal = ref(false)
const isEditing = ref(false)
const submitting = ref(false)
const deleting = ref(false)
const editingEmployee = ref<Employee | null>(null)
const employeeToDelete = ref<Employee | null>(null)
const openDropdownId = ref<string | null>(null)
const selectedEmployee = ref<Employee | null>(null)
const dropdownPosition = ref({ top: 0, left: 0 })

// Form state
const form = reactive({
  name: '',
  employee_id: '',
  department_id: '',
  address: '',
})

const formErrors = reactive<FormErrors>({})

// Template refs
const modalContent = ref<HTMLElement>()
const deleteModalContent = ref<HTMLElement>()
const firstInput = ref<HTMLInputElement>()
const cancelButton = ref<HTMLButtonElement>()
const deleteButton = ref<HTMLButtonElement>()

// Form validation
const validateField = (field: keyof FormErrors) => {
  delete formErrors[field]

  switch (field) {
    case 'name':
      if (!form.name.trim()) {
        formErrors.name = 'Nama harus diisi'
      } else if (form.name.trim().length < 2) {
        formErrors.name = 'Nama minimal 2 karakter'
      }
      break
    case 'employee_id':
      if (!form.employee_id.trim()) {
        formErrors.employee_id = 'Employee ID harus diisi'
      }
      break
    case 'department_id':
      if (!form.department_id) {
        formErrors.department_id = 'Departemen harus dipilih'
      }
      break
    case 'address':
      if (!form.address.trim()) {
        formErrors.address = 'Alamat harus diisi'
      }
      break
  }
}

const validateForm = () => {
  validateField('name')
  validateField('employee_id')
  validateField('department_id')
  validateField('address')
}

const isFormValid = computed(() => {
  return (
    form.name.trim() &&
    form.employee_id.trim() &&
    form.department_id &&
    form.address.trim() &&
    Object.keys(formErrors).length === 0
  )
})

// Focus management
const trapFocus = (event: KeyboardEvent) => {
  const modal = showModal.value ? modalContent.value : deleteModalContent.value
  if (!modal) return

  const focusableElements = modal.querySelectorAll(
    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])',
  )
  const firstFocusable = focusableElements[0] as HTMLElement
  const lastFocusable = focusableElements[focusableElements.length - 1] as HTMLElement

  if (event.key === 'Tab') {
    if (event.shiftKey) {
      if (document.activeElement === firstFocusable) {
        lastFocusable.focus()
        event.preventDefault()
      }
    } else {
      if (document.activeElement === lastFocusable) {
        firstFocusable.focus()
        event.preventDefault()
      }
    }
  }
}

// Body scroll management
const disableBodyScroll = () => {
  document.body.style.overflow = 'hidden'
}

const enableBodyScroll = () => {
  document.body.style.overflow = ''
}

// Computed
const paginationInfo = computed((): PaginationInfo => {
  return {
    currentPage: currentPage.value,
    itemsPerPage: itemsPerPage.value,
    totalItems: totalItems.value,
    totalPages: totalPages.value,
  }
})

const paginatedEmployees = computed(() => {
  return employees.value
})

// Watchers
watch(showModal, async (newVal) => {
  if (newVal) {
    disableBodyScroll()
    document.addEventListener('keydown', trapFocus)
    await nextTick()
    firstInput.value?.focus()
  } else {
    enableBodyScroll()
    document.removeEventListener('keydown', trapFocus)
  }
})

watch(showDeleteModal, async (newVal) => {
  if (newVal) {
    disableBodyScroll()
    document.addEventListener('keydown', trapFocus)
    await nextTick()
    deleteButton.value?.focus()
  } else {
    enableBodyScroll()
    document.removeEventListener('keydown', trapFocus)
  }
})

// API Methods
const fetchEmployees = async () => {
  try {
    loading.value = true
    const params = {
      page: currentPage.value,
      pageSize: itemsPerPage.value,
      search: searchTerm.value.trim(),
    }

    const response = await axios.get('/employees', { params })

    // Handle response structure - adjust based on your API response format
    if (response.data.data) {
      employees.value = response.data.data
      totalItems.value = response.data.totalItems || response.data.total || 0
      totalPages.value =
        response.data.totalPages || Math.ceil(totalItems.value / itemsPerPage.value)
    } else {
      employees.value = response.data
      totalItems.value = response.data.length
      totalPages.value = Math.ceil(totalItems.value / itemsPerPage.value)
    }
  } catch (error: unknown) {
    console.error('Error fetching employees:', error)
    toastStore.error('Gagal memuat data pegawai', {
      title: 'Error',
      duration: 4000,
    })
  } finally {
    loading.value = false
  }
}

const fetchDepartments = async () => {
  try {
    const response = await axios.get('/departments')
    departments.value = response.data.data || response.data
  } catch (error: unknown) {
    console.error('Error fetching departments:', error)
    toastStore.error('Gagal memuat data departemen', {
      title: 'Error',
      duration: 4000,
    })
  }
}

const createEmployee = async (employeeData: EmployeeCreateData): Promise<Employee> => {
  try {
    const response = await axios.post('/employees', employeeData)
    const newEmployee = response.data.data || response.data

    // Refresh the current page after creating
    await fetchEmployees()
    return newEmployee
  } catch (error: unknown) {
    console.error('Error creating employee:', error)
    throw error
  }
}

const updateEmployee = async (
  id: string,
  employeeData: Partial<EmployeeCreateData>,
): Promise<Employee> => {
  try {
    const response = await axios.put(`/employees/${id}`, employeeData)
    const updatedEmployee = response.data.data || response.data

    // Refresh the current page after updating
    await fetchEmployees()
    return updatedEmployee
  } catch (error: unknown) {
    console.error('Error updating employee:', error)
    throw error
  }
}

const deleteEmployeeApi = async (id: string): Promise<boolean> => {
  try {
    await axios.delete(`/employees/${id}`)

    // Check if we need to go to previous page if current page becomes empty
    if (employees.value.length === 1 && currentPage.value > 1) {
      currentPage.value = currentPage.value - 1
    }

    // Refresh the current page after deleting
    await fetchEmployees()
    return true
  } catch (error: unknown) {
    console.error('Error deleting employee:', error)
    throw error
  }
}

// Helper Methods
const getDepartmentName = (departmentId: string): string => {
  const department = departments.value.find((d) => d.id === departmentId)
  return department?.department_name || '-'
}

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

// Event Handlers
let searchTimeout: ReturnType<typeof setTimeout> | null = null

const handleSearch = () => {
  currentPage.value = 1 // Reset to first page when searching

  // Debounce search to avoid too many API calls
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  searchTimeout = setTimeout(() => {
    fetchEmployees()
  }, 300)
}

const handleItemsPerPageChange = () => {
  currentPage.value = 1 // Reset to first page when changing items per page
  fetchEmployees()
}

const setCurrentPage = (page: number) => {
  const maxPage = paginationInfo.value.totalPages
  if (page >= 1 && page <= maxPage) {
    currentPage.value = page
    fetchEmployees()
  }
}

// Handle dropdown toggle
const toggleDropdown = (employeeId: string, event: MouseEvent) => {
  if (openDropdownId.value === employeeId) {
    openDropdownId.value = null
    selectedEmployee.value = null
    return
  }

  const employee = employees.value.find((emp) => emp.id === employeeId)
  if (!employee) return

  const button = event.currentTarget as HTMLElement
  const rect = button.getBoundingClientRect()

  // Calculate position relative to viewport
  dropdownPosition.value = {
    top: rect.bottom + window.scrollY + 5,
    left: rect.right + window.scrollX - 128, // 128px is dropdown width (w-32)
  }

  selectedEmployee.value = employee
  openDropdownId.value = employeeId
}

// Close dropdown when clicking outside
const closeDropdown = () => {
  openDropdownId.value = null
  selectedEmployee.value = null
}

const resetForm = () => {
  form.name = ''
  form.employee_id = ''
  form.department_id = ''
  form.address = ''
  Object.keys(formErrors).forEach((key) => delete formErrors[key as keyof FormErrors])
}

const openCreateModal = () => {
  resetForm()
  isEditing.value = false
  editingEmployee.value = null
  showModal.value = true
}

const openEditModal = (employee: Employee) => {
  resetForm()
  form.name = employee.name
  form.employee_id = employee.employee_id
  form.department_id = employee.department_id
  form.address = employee.address

  isEditing.value = true
  editingEmployee.value = employee
  showModal.value = true
  closeDropdown()
}

const closeModal = () => {
  showModal.value = false
  resetForm()
  isEditing.value = false
  editingEmployee.value = null
}

const submitForm = async () => {
  validateForm()

  if (!isFormValid.value) {
    toastStore.error('Mohon perbaiki kesalahan pada form', {
      title: 'Validasi Error',
      duration: 4000,
    })
    return
  }

  submitting.value = true

  try {
    const employeeData: EmployeeCreateData = {
      name: form.name.trim(),
      employee_id: form.employee_id.trim(),
      department_id: form.department_id,
      address: form.address.trim(),
    }

    if (isEditing.value && editingEmployee.value) {
      await updateEmployee(editingEmployee.value.id, employeeData)
      toastStore.success('Pegawai berhasil diperbarui', {
        title: 'Berhasil',
        duration: 3000,
      })
    } else {
      await createEmployee(employeeData)
      toastStore.success('Pegawai berhasil ditambahkan', {
        title: 'Berhasil',
        duration: 3000,
      })
    }

    closeModal()
  } catch (error) {
    console.error('Error submitting form:', error)
    toastStore.error('Gagal menyimpan data pegawai', {
      title: 'Error',
      duration: 4000,
    })
  } finally {
    submitting.value = false
  }
}

const confirmDelete = (employee: Employee) => {
  employeeToDelete.value = employee
  showDeleteModal.value = true
  closeDropdown()
}

const cancelDelete = () => {
  employeeToDelete.value = null
  showDeleteModal.value = false
}

const deleteEmployee = async () => {
  if (!employeeToDelete.value) return

  deleting.value = true

  try {
    await deleteEmployeeApi(employeeToDelete.value.id)
    toastStore.success('Pegawai berhasil dihapus', {
      title: 'Berhasil',
      duration: 3000,
    })
    cancelDelete()
  } catch (error) {
    console.error('Error deleting employee:', error)
    toastStore.error('Gagal menghapus pegawai', {
      title: 'Error',
      duration: 4000,
    })
  } finally {
    deleting.value = false
  }
}

// Initialize
onMounted(async () => {
  try {
    await fetchDepartments()
    await fetchEmployees()
    document.addEventListener('click', closeDropdown)
  } catch (error) {
    console.error('Error initializing data:', error)
  }
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown)
  document.removeEventListener('keydown', trapFocus)
  enableBodyScroll()
})
</script>
