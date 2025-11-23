import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import axios from 'axios'
import { useAuth } from './composables/useAuth'
import './assets/tailwind.css'  // <-- import Tailwind ici


createApp(App).mount('#app')


// Configuration globale d'axios
axios.defaults.baseURL = 'http://127.0.0.1:8000'

// Intercepteur pour gérer les erreurs d'authentification
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      // Token expiré ou invalide
      localStorage.removeItem('token')
      localStorage.removeItem('userType')
      localStorage.removeItem('user')
      router.push({ name: 'Home' })
    }
    return Promise.reject(error)
  }
)

const app = createApp(App)

app.use(router)

// Initialiser l'authentification au démarrage
const { initAuth } = useAuth()
initAuth()

app.mount('#app')