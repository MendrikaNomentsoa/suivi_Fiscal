import { ref, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

// État global partagé
const user = ref(null)
const token = ref(localStorage.getItem('token') || '')
const userType = ref(localStorage.getItem('userType') || '')

// Configuration axios avec le token
if (token.value) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
}

export function useAuth() {
  const router = useRouter()

  // Computed
  const isAuthenticated = computed(() => !!token.value)
  const isContribuable = computed(() => userType.value === 'contribuable')
  const isAgent = computed(() => userType.value === 'agent')

  // ============================================================
  // 🔵 Connexion Contribuable
  // ============================================================
  const loginContribuable = async (credentials) => {
    try {
      const response = await axios.post('/api/auth/contribuable/login', {
        email: credentials.email,
        password: credentials.password
      })

      // Stocker infos
      user.value = response.data.user
      token.value = response.data.token
      userType.value = 'contribuable'

      localStorage.setItem('token', token.value)
      localStorage.setItem('userType', userType.value)
      localStorage.setItem('user', JSON.stringify(user.value))

      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`

      // 🔥 Redirection vers le dashboard contribuable
      router.push({
        name: 'ContribuableDashboard'
      })

      return { success: true }

    } catch (error) {
      console.error('Erreur de connexion:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Erreur de connexion'
      }
    }
  }

  // ============================================================
  // 🟢 Connexion Agent
  // ============================================================
  const loginAgent = async (credentials) => {
    try {
      const response = await axios.post('/api/auth/agent/login', {
        mail: credentials.mail,
        password: credentials.password
      })

      user.value = response.data.user
      token.value = response.data.token
      userType.value = 'agent'

      localStorage.setItem('token', token.value)
      localStorage.setItem('userType', userType.value)
      localStorage.setItem('user', JSON.stringify(user.value))

      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`

      // 🔥 Dashboard Agent
      router.push({ name: 'AgentDashboard' })

      return { success: true }

    } catch (error) {
      console.error('Erreur de connexion:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Erreur de connexion'
      }
    }
  }

  // ============================================================
  // 🔴 Déconnexion
  // ============================================================
  const logout = async () => {
    try {
      await axios.post('/api/auth/logout')
    } catch (error) {
      console.error('Erreur lors de la déconnexion', error)
    } finally {
      user.value = null
      token.value = ''
      userType.value = ''

      localStorage.removeItem('token')
      localStorage.removeItem('userType')
      localStorage.removeItem('user')

      delete axios.defaults.headers.common['Authorization']

      router.push({ name: 'Home' })
    }
  }

  // ============================================================
  // 🟡 Initialisation Auth
  // ============================================================
  const initAuth = () => {
    const storedUser = localStorage.getItem('user')
    const storedToken = localStorage.getItem('token')
    const storedUserType = localStorage.getItem('userType')

    if (storedUser && storedToken && storedUserType) {
      user.value = JSON.parse(storedUser)
      token.value = storedToken
      userType.value = storedUserType

      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    }
  }

  // ============================================================
  // 🧩 Récupération de l'ID User connecté
  // ============================================================
  const getCurrentUserId = () => {
    if (!user.value) return null

    return isContribuable.value
      ? user.value.id_contribuable
      : user.value.id_Agent
  }

  return {
    // État
    user,
    token,
    userType,
    isAuthenticated,
    isContribuable,
    isAgent,

    // Méthodes
    loginContribuable,
    loginAgent,
    logout,
    initAuth,
    getCurrentUserId
  }
}
