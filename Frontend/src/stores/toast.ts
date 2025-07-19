import { ref } from 'vue'
import { defineStore } from 'pinia'

export interface Toast {
  id: string
  type: 'success' | 'error' | 'warning' | 'info'
  title?: string
  message: string
  duration?: number
  dismissible?: boolean
}

export const useToastStore = defineStore('toast', () => {
  const toasts = ref<Toast[]>([])

  // Generate unique ID for toasts
  const generateId = (): string => {
    return Date.now().toString(36) + Math.random().toString(36).substr(2)
  }

  // Add a new toast
  const addToast = (toast: Omit<Toast, 'id'>): string => {
    const id = generateId()
    const newToast: Toast = {
      id,
      duration: 3000,
      dismissible: true,
      ...toast,
    }

    toasts.value.push(newToast)

    // Auto-remove toast after duration
    if (newToast.duration && newToast.duration > 0) {
      setTimeout(() => {
        removeToast(id)
      }, newToast.duration)
    }

    return id
  }

  // Remove a toast by ID
  const removeToast = (id: string) => {
    const index = toasts.value.findIndex((toast) => toast.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }

  // Clear all toasts
  const clearToasts = () => {
    toasts.value = []
  }

  // Convenience methods for different toast types
  const success = (message: string, options?: Partial<Omit<Toast, 'id' | 'type' | 'message'>>) => {
    return addToast({ type: 'success', message, ...options })
  }

  const error = (message: string, options?: Partial<Omit<Toast, 'id' | 'type' | 'message'>>) => {
    return addToast({ type: 'error', message, ...options })
  }

  const warning = (message: string, options?: Partial<Omit<Toast, 'id' | 'type' | 'message'>>) => {
    return addToast({ type: 'warning', message, ...options })
  }

  const info = (message: string, options?: Partial<Omit<Toast, 'id' | 'type' | 'message'>>) => {
    return addToast({ type: 'info', message, ...options })
  }

  return {
    toasts,
    addToast,
    removeToast,
    clearToasts,
    success,
    error,
    warning,
    info,
  }
})
