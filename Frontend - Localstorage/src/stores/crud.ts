import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { faker } from '@faker-js/faker'

export interface CrudItem {
  id: string
  name: string
  email: string
  phone: string
  position: string
  department: string
  createdAt: string
  updatedAt: string
}

export interface PaginationInfo {
  currentPage: number
  itemsPerPage: number
  totalItems: number
  totalPages: number
}

export const useCrudStore = defineStore('crud', () => {
  const items = ref<CrudItem[]>([])
  const loading = ref(false)
  const searchTerm = ref('')
  const currentPage = ref(1)
  const itemsPerPage = ref(10)

  // Storage key
  const STORAGE_KEY = 'crud_items'

  // Computed filtered items
  const filteredItems = computed(() => {
    if (!searchTerm.value.trim()) {
      return items.value
    }

    const term = searchTerm.value.toLowerCase()
    return items.value.filter(
      (item) =>
        item.name.toLowerCase().includes(term) ||
        item.email.toLowerCase().includes(term) ||
        item.phone.toLowerCase().includes(term) ||
        item.position.toLowerCase().includes(term) ||
        item.department.toLowerCase().includes(term),
    )
  })

  // Computed pagination info
  const paginationInfo = computed((): PaginationInfo => {
    const totalItems = filteredItems.value.length
    const totalPages = Math.ceil(totalItems / itemsPerPage.value)

    return {
      currentPage: currentPage.value,
      itemsPerPage: itemsPerPage.value,
      totalItems,
      totalPages,
    }
  })

  // Computed paginated items
  const paginatedItems = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    const end = start + itemsPerPage.value
    return filteredItems.value.slice(start, end)
  })

  // Generate ID
  const generateId = (): string => {
    return Date.now().toString(36) + Math.random().toString(36).substr(2)
  }

  // Load data from localStorage
  const loadFromStorage = () => {
    try {
      const stored = localStorage.getItem(STORAGE_KEY)
      if (stored) {
        items.value = JSON.parse(stored)
      } else {
        // Initialize with sample data if no data exists
        initializeSampleData()
      }
    } catch (error) {
      console.error('Error loading data from storage:', error)
      initializeSampleData()
    }
  }

  // Save data to localStorage
  const saveToStorage = () => {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(items.value))
    } catch (error) {
      console.error('Error saving data to storage:', error)
    }
  }

  // Initialize with sample data
  const initializeSampleData = () => {
    const sampleData: CrudItem[] = []

    // Generate 30 sample items using forEach
    Array.from({ length: 30 }, (_, index) => index).forEach(() => {
      sampleData.push({
        id: generateId(),
        name: faker.person.fullName(),
        email: faker.internet.email(),
        phone: faker.phone.number(),
        position: faker.person.jobTitle(),
        department: faker.helpers.arrayElement([
          'Engineering',
          'Product',
          'Design',
          'Marketing',
          'Sales',
          'HR',
          'Finance',
          'Operations',
          'Legal',
          'Support',
        ]),
        createdAt: faker.date.past({ years: 1 }).toISOString(),
        updatedAt: faker.date.recent({ days: 30 }).toISOString(),
      })
    })

    items.value = sampleData
    saveToStorage()
  }

  // Create item
  const createItem = (itemData: Omit<CrudItem, 'id' | 'createdAt' | 'updatedAt'>) => {
    const newItem: CrudItem = {
      ...itemData,
      id: generateId(),
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
    }

    items.value.unshift(newItem)
    saveToStorage()
    return newItem
  }

  // Update item
  const updateItem = (id: string, itemData: Partial<Omit<CrudItem, 'id' | 'createdAt'>>) => {
    const index = items.value.findIndex((item) => item.id === id)
    if (index !== -1) {
      items.value[index] = {
        ...items.value[index],
        ...itemData,
        updatedAt: new Date().toISOString(),
      }
      saveToStorage()
      return items.value[index]
    }
    return null
  }

  // Delete item
  const deleteItem = (id: string) => {
    const index = items.value.findIndex((item) => item.id === id)
    if (index !== -1) {
      items.value.splice(index, 1)
      saveToStorage()
      return true
    }
    return false
  }

  // Get item by ID
  const getItemById = (id: string) => {
    return items.value.find((item) => item.id === id) || null
  }

  // Set search term
  const setSearchTerm = (term: string) => {
    searchTerm.value = term
    currentPage.value = 1 // Reset to first page when searching
  }

  // Set current page
  const setCurrentPage = (page: number) => {
    const maxPage = paginationInfo.value.totalPages
    if (page >= 1 && page <= maxPage) {
      currentPage.value = page
    }
  }

  // Set items per page
  const setItemsPerPage = (count: number) => {
    itemsPerPage.value = count
    currentPage.value = 1 // Reset to first page
  }

  // Initialize store
  const init = () => {
    loadFromStorage()
  }

  return {
    // State
    items,
    loading,
    searchTerm,
    currentPage,
    itemsPerPage,

    // Computed
    filteredItems,
    paginatedItems,
    paginationInfo,

    // Actions
    init,
    createItem,
    updateItem,
    deleteItem,
    getItemById,
    setSearchTerm,
    setCurrentPage,
    setItemsPerPage,
    loadFromStorage,
    saveToStorage,
  }
})
