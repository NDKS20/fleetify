<template>
  <teleport to="body">
    <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 space-y-3">
      <transition-group
        name="toast"
        tag="div"
        class="space-y-3"
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="transform -translate-y-2 opacity-0 scale-95"
        enter-to-class="transform translate-y-0 opacity-100 scale-100"
        leave-active-class="transition-all duration-300 ease-in"
        leave-from-class="transform translate-y-0 opacity-100 scale-100"
        leave-to-class="transform -translate-y-2 opacity-0 scale-95"
      >
        <div v-for="toast in toastStore.toasts" :key="toast.id" class="min-w-80 max-w-md">
          <AppAlert
            :type="toast.type"
            :title="toast.title"
            :message="toast.message"
            :dismissible="toast.dismissible"
            :model-value="true"
            @dismiss="handleDismiss(toast.id)"
            class="shadow-2xl dark:shadow-black/50 border border-gray-200 dark:border-gray-600 backdrop-blur-sm"
          />
        </div>
      </transition-group>
    </div>
  </teleport>
</template>

<script setup lang="ts">
import { useToastStore } from '../stores/toast'
import AppAlert from './AppAlert.vue'

const toastStore = useToastStore()

const handleDismiss = (id: string) => {
  toastStore.removeToast(id)
}
</script>

<style scoped>
/* Additional toast-specific animations */
.toast-move,
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(-30px) scale(0.95);
}

.toast-leave-active {
  position: absolute;
  right: 0;
  left: 0;
}
</style>
