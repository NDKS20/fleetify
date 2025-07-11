import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/auth'
import { useThemeStore } from './stores/theme'
import { useCrudStore } from './stores/crud'

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')

// Initialize stores after app is mounted
const authStore = useAuthStore()
const themeStore = useThemeStore()
const crudStore = useCrudStore()

authStore.initAuth()
themeStore.initTheme()
crudStore.init()
