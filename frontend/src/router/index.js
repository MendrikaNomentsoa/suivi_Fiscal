import { createRouter, createWebHistory } from 'vue-router'

// Import des vues
import Home from '@/views/Home.vue'

// Vues Contribuable
import ContribuableLogin from '@/views/contribuable/Login.vue'
import ContribuableSignup from '@/views/contribuable/Signup.vue'
import ContribuableDashboard from '@/views/contribuable/Dashboard.vue'
import ListeImpots from '@/views/contribuable/ImpotsList.vue'
import DeclarationsExistantes from '@/views/contribuable/DeclarationsList.vue'
import DeposerLitige from '@/views/contribuable/LitigeForm.vue'
import SimulationMontant from '@/views/contribuable/SimulationMontant.vue'

// Vues Agent
import AgentLogin from '@/views/agent/Login.vue'
import AgentSignup from '@/views/agent/Signup.vue' // importer la vue
import AgentDashboard from '@/views/agent/Dashboard.vue'
//import ListeLitiges from '@/views/agent/ListeLitiges.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  
  // ========== ROUTES CONTRIBUABLE ==========
  {
    path: '/contribuable/login',
    name: 'ContribuableLogin',
    component: ContribuableLogin
  },
  {
    path: '/contribuable/signup',
    name: 'ContribuableSignup',
    component: ContribuableSignup
  },

  {
    path: '/contribuable/dashboard',
    name: 'ContribuableDashboard',
    component: ContribuableDashboard,
    meta: { requiresAuth: true, userType: 'contribuable' }
  },
  {
    path: '/impots/:idContribuable',
    name: 'ListeImpots',
    component: ListeImpots,
    props: true,
    meta: { requiresAuth: true, userType: 'contribuable' }
  },
  {
    path: '/declarations/:idContribuable/:idTypeImpot',
    name: 'DeclarationsExistantes',
    component: DeclarationsExistantes,
    props: true,
    meta: { requiresAuth: true, userType: 'contribuable' }
  },
  {
    path: '/litige/:idContribuable',
    name: 'DeposerLitige',
    component: DeposerLitige,
    props: true,
    meta: { requiresAuth: true, userType: 'contribuable' }
  },
  {
    path: '/simulation/:idContribuable',
    name: 'SimulationMontant',
    component: SimulationMontant,
    props: true,
    meta: { requiresAuth: true, userType: 'contribuable' }
  },
  
  // ========== ROUTES AGENT ==========
  {
    path: '/agent/login',
    name: 'AgentLogin',
    component: AgentLogin
  },
  {
    path: '/agent/signup',
    name: 'AgentSignup',
    component: AgentSignup
  },

  {
    path: '/agent/dashboard',
    name: 'AgentDashboard',
    component: AgentDashboard,
    meta: { requiresAuth: true, userType: 'agent' }
  },
  /*{
    path: '/agent/litiges',
    name: 'ListeLitiges',
    component: ListeLitiges,
    meta: { requiresAuth: true, userType: 'agent' }
  }*/
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Guard de navigation - Protection des routes
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const userType = localStorage.getItem('userType')
  
  // Si la route nécessite une authentification
  if (to.meta.requiresAuth) {
    if (!token) {
      // Pas de token = redirection vers home
      next({ name: 'Home' })
    } else if (to.meta.userType && to.meta.userType !== userType) {
      // Mauvais type d'utilisateur
      alert('Accès non autorisé pour ce type d\'utilisateur')
      next({ name: 'Home' })
    } else {
      // Tout est OK
      next()
    }
  } else {
    // Route publique
    next()
  }
})

export default router