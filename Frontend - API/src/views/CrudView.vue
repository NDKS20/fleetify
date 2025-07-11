<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Employee Management</h1>
              <p class="text-gray-600 dark:text-gray-400 mt-1">
                Manage employee data with search and pagination
              </p>
            </div>
            <button
              @click="showCreateModal = true"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            >
              Add Employee
            </button>
          </div>
        </div>

        <!-- Search and Filter -->
        <div class="px-6 py-4">
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
              <input
                v-model="searchInput"
                @input="handleSearchInput"
                type="text"
                placeholder="Search employees by name, email, phone, position, or department..."
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              />
            </div>
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600 dark:text-gray-400">Show:</span>
              <select
                v-model="selectedItemsPerPage"
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

      <!-- Data Table -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Name
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Email
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Phone
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Position
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Department
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr
                v-for="item in crudStore.paginatedItems"
                :key="item.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"
                >
                  {{ item.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                  {{ item.email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                  {{ item.phone }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                  {{ item.position }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                  {{ item.department }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                  <button
                    @click.stop="toggleDropdown(item.id, $event)"
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
              <tr v-if="crudStore.paginatedItems.length === 0">
                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                  {{
                    crudStore.searchTerm
                      ? 'No employees found matching your search.'
                      : 'No employees found.'
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
              Showing
              {{
                Math.min(
                  (crudStore.currentPage - 1) * crudStore.itemsPerPage + 1,
                  crudStore.paginationInfo.totalItems,
                )
              }}
              to
              {{
                Math.min(
                  crudStore.currentPage * crudStore.itemsPerPage,
                  crudStore.paginationInfo.totalItems,
                )
              }}
              of {{ crudStore.paginationInfo.totalItems }} employees
            </div>

            <div class="flex items-center space-x-2">
              <button
                @click="changePage(crudStore.currentPage - 1)"
                :disabled="crudStore.currentPage <= 1"
                class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                Previous
              </button>

              <template v-for="page in visiblePages" :key="page">
                <button
                  v-if="page !== '...'"
                  @click="changePage(Number(page))"
                  :class="[
                    'px-3 py-1 border rounded-md text-sm transition-colors',
                    page === crudStore.currentPage
                      ? 'bg-blue-600 border-blue-600 text-white'
                      : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                  ]"
                >
                  {{ page }}
                </button>
                <span v-else class="px-3 py-1 text-sm text-gray-400">...</span>
              </template>

              <button
                @click="changePage(crudStore.currentPage + 1)"
                :disabled="crudStore.currentPage >= crudStore.paginationInfo.totalPages"
                class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                Next
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
          @click="selectedItem && editItem(selectedItem)"
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
          @click="selectedItem && deleteItem(selectedItem)"
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
          Delete
        </button>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div
      v-if="showCreateModal || showEditModal"
      class="fixed inset-0 z-50 flex items-center justify-center"
    >
      <div class="fixed inset-0 bg-black opacity-60" @click="closeModal"></div>
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md mx-4 z-10">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
          {{ showCreateModal ? 'Add Employee' : 'Edit Employee' }}
        </h2>

        <form @submit.prevent="submitForm" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
              >Name</label
            >
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
              >Email</label
            >
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
              >Phone</label
            >
            <input
              v-model="form.phone"
              type="tel"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
              >Position</label
            >
            <input
              v-model="form.position"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
              >Department</label
            >
            <select
              v-model="form.department"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            >
              <option value="">Select Department</option>
              <option value="Engineering">Engineering</option>
              <option value="Product">Product</option>
              <option value="Design">Design</option>
              <option value="Marketing">Marketing</option>
              <option value="Sales">Sales</option>
              <option value="HR">HR</option>
              <option value="Finance">Finance</option>
            </select>
          </div>

          <div class="flex justify-end space-x-3 mt-6">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            >
              {{ showCreateModal ? 'Add' : 'Update' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="fixed inset-0 bg-black opacity-60" @click="showDeleteModal = false"></div>
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-sm mx-4 z-10">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Confirm Delete</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          Are you sure you want to delete <strong>{{ itemToDelete?.name }}</strong
          >? This action cannot be undone.
        </p>

        <div class="flex justify-end space-x-3">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="confirmDelete"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCrudStore, type CrudItem } from '../stores/crud'
import { useToastStore } from '../stores/toast'

const route = useRoute()
const router = useRouter()
const crudStore = useCrudStore()
const toastStore = useToastStore()

// Modal states
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)

// Form data
const form = reactive({
  name: '',
  email: '',
  phone: '',
  position: '',
  department: '',
})

// Local state for UI
const searchInput = ref('')
const selectedItemsPerPage = ref(10)
const itemToDelete = ref<CrudItem | null>(null)
const editingItem = ref<CrudItem | null>(null)
const openDropdownId = ref<string | null>(null)
const selectedItem = ref<CrudItem | null>(null)
const dropdownPosition = ref({ top: 0, left: 0 })

// Computed for pagination display
const visiblePages = computed(() => {
  const total = crudStore.paginationInfo.totalPages
  const current = crudStore.currentPage
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
})

// Debounced search
let searchTimeout: ReturnType<typeof setTimeout> | null = null
const handleSearchInput = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  searchTimeout = setTimeout(() => {
    updateQueryString()
  }, 300)
}

// Update query string with current state
const updateQueryString = () => {
  const query: Record<string, string> = {}

  if (searchInput.value.trim()) {
    query.search = searchInput.value.trim()
  }

  if (crudStore.currentPage > 1) {
    query.page = crudStore.currentPage.toString()
  }

  if (crudStore.itemsPerPage !== 10) {
    query.per_page = crudStore.itemsPerPage.toString()
  }

  router.push({ query })
}

// Load state from query string
const loadFromQueryString = () => {
  const { search, page, per_page } = route.query

  if (search && typeof search === 'string') {
    searchInput.value = search
    crudStore.setSearchTerm(search)
  }

  if (per_page && typeof per_page === 'string') {
    const perPage = parseInt(per_page)
    if ([5, 10, 20, 50].includes(perPage)) {
      selectedItemsPerPage.value = perPage
      crudStore.setItemsPerPage(perPage)
    }
  }

  if (page && typeof page === 'string') {
    const pageNum = parseInt(page)
    if (pageNum > 0) {
      crudStore.setCurrentPage(pageNum)
    }
  }
}

// Handle page change
const changePage = (page: number) => {
  crudStore.setCurrentPage(page)
  updateQueryString()
}

// Handle items per page change
const handleItemsPerPageChange = () => {
  crudStore.setItemsPerPage(selectedItemsPerPage.value)
  updateQueryString()
}

// Handle dropdown toggle
const toggleDropdown = (itemId: string, event: MouseEvent) => {
  if (openDropdownId.value === itemId) {
    openDropdownId.value = null
    selectedItem.value = null
    return
  }

  const item = crudStore.paginatedItems.find((emp) => emp.id === itemId)
  if (!item) return

  const button = event.currentTarget as HTMLElement
  const rect = button.getBoundingClientRect()

  // Calculate position relative to viewport
  dropdownPosition.value = {
    top: rect.bottom + window.scrollY + 5,
    left: rect.right + window.scrollX - 128, // 128px is dropdown width (w-32)
  }

  selectedItem.value = item
  openDropdownId.value = itemId
}

// Close dropdown when clicking outside
const closeDropdown = () => {
  openDropdownId.value = null
  selectedItem.value = null
}

// Modal functions
const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  resetForm()
}

const resetForm = () => {
  form.name = ''
  form.email = ''
  form.phone = ''
  form.position = ''
  form.department = ''
  editingItem.value = null
}

// CRUD operations
const submitForm = () => {
  if (showCreateModal.value) {
    crudStore.createItem({
      name: form.name,
      email: form.email,
      phone: form.phone,
      position: form.position,
      department: form.department,
    })
    toastStore.success(`Employee "${form.name}" has been created successfully!`, {
      title: 'Employee Created',
      duration: 4000,
    })
  } else if (showEditModal.value && editingItem.value) {
    crudStore.updateItem(editingItem.value.id, {
      name: form.name,
      email: form.email,
      phone: form.phone,
      position: form.position,
      department: form.department,
    })
    toastStore.success(`Employee "${form.name}" has been updated successfully!`, {
      title: 'Employee Updated',
      duration: 4000,
    })
  }

  closeModal()
}

const editItem = (item: CrudItem) => {
  editingItem.value = item
  form.name = item.name
  form.email = item.email
  form.phone = item.phone
  form.position = item.position
  form.department = item.department
  showEditModal.value = true
  closeDropdown()
}

const deleteItem = (item: CrudItem) => {
  itemToDelete.value = item
  showDeleteModal.value = true
  closeDropdown()
}

const confirmDelete = () => {
  if (itemToDelete.value) {
    const employeeName = itemToDelete.value.name
    crudStore.deleteItem(itemToDelete.value.id)
    toastStore.success(`Employee "${employeeName}" has been deleted successfully!`, {
      title: 'Employee Deleted',
      duration: 4000,
    })
    showDeleteModal.value = false
    itemToDelete.value = null
  }
}

// Watch for search term changes
watch(
  () => searchInput.value,
  (newValue) => {
    crudStore.setSearchTerm(newValue)
  },
)

// Initialize on mount
onMounted(() => {
  loadFromQueryString()
  document.addEventListener('click', closeDropdown)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown)
})
</script>
