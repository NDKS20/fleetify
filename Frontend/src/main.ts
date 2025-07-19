import './assets/main.css'

import { createApp, nextTick } from 'vue'
import { createPinia } from 'pinia'
import axios from 'axios'

import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/auth'
import { useThemeStore } from './stores/theme'
import { useDepartmentStore } from './stores/department'

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')

// Configure axios
axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL + '/api'

// Initialize stores after app is mounted
const authStore = useAuthStore()
const themeStore = useThemeStore()
const departmentStore = useDepartmentStore()
// Employee store will be used in the component

// Set initial authorization header if token exists
if (authStore.token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${authStore.token}`
}

nextTick(async () => {
  // Wait for the router to be ready (in case of any asynchronous routing)
  await router.isReady()

  // Setup axios interceptors
  axios.interceptors.response.use(
    (response: any) => response,
    async (error: any) => {
      if (error.response?.status === 401 && router.currentRoute.value.name !== 'login') {
        await authStore.logout()

        router.push({ name: 'login' })
      }
      return Promise.reject(error)
    },
  )

  axios.get('/auth/me').then((response) => {
    authStore.setLogged({
      user: response.data,
      token: authStore.token || '',
    })
  })
})

authStore.initAuth()
themeStore.initTheme()
departmentStore.init()
// Employee store will be initialized when the component is mounted
