<template>
  <transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="transform scale-95 opacity-0"
    enter-to-class="transform scale-100 opacity-100"
    leave-active-class="transition-all duration-300 ease-in"
    leave-from-class="transform scale-100 opacity-100"
    leave-to-class="transform scale-95 opacity-0"
  >
    <div
      v-if="visible"
      :class="['rounded-lg p-4 flex items-start shadow-sm border', alertClasses]"
      role="alert"
      :aria-labelledby="title ? 'alert-title' : undefined"
    >
      <!-- Icon -->
      <div v-if="showIcon" :class="['flex-shrink-0 mr-3', iconClasses]">
        <svg v-if="type === 'success'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
            clip-rule="evenodd"
          />
        </svg>
        <svg v-else-if="type === 'error'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
            clip-rule="evenodd"
          />
        </svg>
        <svg v-else-if="type === 'warning'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path
            fill-rule="evenodd"
            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
            clip-rule="evenodd"
          />
        </svg>
        <svg v-else-if="type === 'info'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
            clip-rule="evenodd"
          />
        </svg>
      </div>

      <!-- Content -->
      <div class="flex-1 min-w-0">
        <h3 v-if="title" id="alert-title" :class="['text-sm font-medium mb-1', titleClasses]">
          {{ title }}
        </h3>
        <div :class="['text-sm', messageClasses]">
          <slot>{{ message }}</slot>
        </div>
      </div>

      <!-- Dismiss Button -->
      <div v-if="dismissible" class="flex-shrink-0 ml-3">
        <button
          @click="dismiss"
          :class="[
            'rounded-md inline-flex focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors',
            dismissButtonClasses,
          ]"
          aria-label="Dismiss alert"
        >
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { computed, ref, watch, onMounted } from 'vue'

defineOptions({
  name: 'AppAlert',
})

export interface AlertProps {
  type?: 'success' | 'error' | 'warning' | 'info'
  title?: string
  message?: string
  dismissible?: boolean
  showIcon?: boolean
  autoDismiss?: boolean
  autoDismissDelay?: number
  modelValue?: boolean
}

const props = withDefaults(defineProps<AlertProps>(), {
  type: 'info',
  title: '',
  message: '',
  dismissible: false,
  showIcon: true,
  autoDismiss: false,
  autoDismissDelay: 3000,
  modelValue: true,
})

const emit = defineEmits<{
  dismiss: []
  'update:modelValue': [value: boolean]
}>()

const visible = ref(props.modelValue)

// Watch for modelValue changes
watch(
  () => props.modelValue,
  (newValue) => {
    visible.value = newValue
  },
)

// Auto dismiss functionality
let autoDismissTimer: number | null = null

const startAutoDismiss = () => {
  if (props.autoDismiss && props.autoDismissDelay > 0) {
    autoDismissTimer = window.setTimeout(() => {
      dismiss()
    }, props.autoDismissDelay)
  }
}

const clearAutoDismiss = () => {
  if (autoDismissTimer) {
    clearTimeout(autoDismissTimer)
    autoDismissTimer = null
  }
}

onMounted(() => {
  startAutoDismiss()
})

const dismiss = () => {
  clearAutoDismiss()
  visible.value = false
  emit('dismiss')
  emit('update:modelValue', false)
}

// Computed classes based on alert type
const alertClasses = computed(() => {
  const baseClasses = 'dark:border-opacity-80'

  switch (props.type) {
    case 'success':
      return `${baseClasses} bg-green-50 dark:bg-green-800/80 border-green-200 dark:border-green-600`
    case 'error':
      return `${baseClasses} bg-red-50 dark:bg-red-800/80 border-red-200 dark:border-red-600`
    case 'warning':
      return `${baseClasses} bg-yellow-50 dark:bg-yellow-800/80 border-yellow-200 dark:border-yellow-600`
    case 'info':
    default:
      return `${baseClasses} bg-blue-50 dark:bg-blue-800/80 border-blue-200 dark:border-blue-600`
  }
})

const iconClasses = computed(() => {
  switch (props.type) {
    case 'success':
      return 'text-green-500 dark:text-green-200'
    case 'error':
      return 'text-red-500 dark:text-red-200'
    case 'warning':
      return 'text-yellow-500 dark:text-yellow-200'
    case 'info':
    default:
      return 'text-blue-500 dark:text-blue-200'
  }
})

const titleClasses = computed(() => {
  switch (props.type) {
    case 'success':
      return 'text-green-800 dark:text-green-100'
    case 'error':
      return 'text-red-800 dark:text-red-100'
    case 'warning':
      return 'text-yellow-800 dark:text-yellow-100'
    case 'info':
    default:
      return 'text-blue-800 dark:text-blue-100'
  }
})

const messageClasses = computed(() => {
  switch (props.type) {
    case 'success':
      return 'text-green-700 dark:text-green-200'
    case 'error':
      return 'text-red-700 dark:text-red-200'
    case 'warning':
      return 'text-yellow-700 dark:text-yellow-200'
    case 'info':
    default:
      return 'text-blue-700 dark:text-blue-200'
  }
})

const dismissButtonClasses = computed(() => {
  switch (props.type) {
    case 'success':
      return 'text-green-500 hover:text-green-700 dark:text-green-200 dark:hover:text-green-50 focus:ring-green-500 hover:bg-green-100 dark:hover:bg-green-700/30'
    case 'error':
      return 'text-red-500 hover:text-red-700 dark:text-red-200 dark:hover:text-red-50 focus:ring-red-500 hover:bg-red-100 dark:hover:bg-red-700/30'
    case 'warning':
      return 'text-yellow-500 hover:text-yellow-700 dark:text-yellow-200 dark:hover:text-yellow-50 focus:ring-yellow-500 hover:bg-yellow-100 dark:hover:bg-yellow-700/30'
    case 'info':
    default:
      return 'text-blue-500 hover:text-blue-700 dark:text-blue-200 dark:hover:text-blue-50 focus:ring-blue-500 hover:bg-blue-100 dark:hover:bg-blue-700/30'
  }
})
</script>
