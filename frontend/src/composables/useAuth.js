import { ref, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

// ============================================================
// ✅ Configuration Axios globale
// ============================================================
axios.defaults.baseURL = 'http://127.0.0.1:8000/api'

// ============================================================
// 🔹 État global partagé
// ============================================================
const user = ref(null)
const token = ref(localStorage.getItem('token') || '')
const userType = ref(localStorage.getItem('userType') || '')

// Si token existant, ajouter dans Axios
if (token.value) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
}

// ============================================================
// 🔹 Composable useAuth
// ============================================================
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
      const response = await axios.post('/auth/contribuable/login', {
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

      router.push({ name: 'ContribuableDashboard' })

      return { success: true }
    } catch (error) {
      console.error('Erreur de connexion contribuable:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Erreur de connexion'
      }
    }
  }

  // ============================================================
  // 🟢 Connexion Agent (corrigé)
  // ============================================================
  const loginAgent = async (credentials) => {
    try {
      // ✅ Laravel attend 'email' et non 'mail'
      const response = await axios.post('/auth/agent/login', {
        mail: credentials.mail,       // <- corrigé côté backend
        password: credentials.password
      })

      // Stockage des infos
      user.value = response.data.agent
      token.value = response.data.token
      userType.value = 'agent'

      localStorage.setItem('token', token.value)
      localStorage.setItem('userType', userType.value)
      localStorage.setItem('user', JSON.stringify(user.value))

      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`

      // Redirection vers dashboard agent
      router.push({ name: 'AgentDashboard' })

      return { success: true }
    } catch (err) {
      console.error("Erreur de connexion agent:", err)

      // Extraire les messages de validation
      if (err.response?.status === 422 && err.response.data.errors) {
        const messages = Object.values(err.response.data.errors)
                               .flat()
                               .join(' | ')
        return { success: false, message: messages }
      }

      // 401 Unauthorized = identifiants incorrects
      if (err.response?.status === 401) {
        return { success: false, message: 'Email ou mot de passe incorrect.' }
      }

      return { success: false, message: err.response?.data?.message || 'Erreur de connexion' }
    }
  }

  // ============================================================
  // 🔴 Déconnexion
  // ============================================================
  const logout = async () => {
    try {
      await axios.post('/auth/logout')
    } catch (error) {
      console.error('Erreur lors de la déconnexion:', error)
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
    user,
    token,
    userType,
    isAuthenticated,
    isContribuable,
    isAgent,
    loginContribuable,
    loginAgent,
    logout,
    initAuth,
    getCurrentUserId
  }
}
