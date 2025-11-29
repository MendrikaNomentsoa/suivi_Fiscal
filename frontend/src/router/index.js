import { createRouter, createWebHistory } from 'vue-router'

// Import des vues
import Home from '@/views/Home.vue'

// Vues Contribuable
import ContribuableLogin from '@/views/contribuable/Login.vue'
import ContribuableSignup from '@/views/contribuable/Signup.vue'
import ContribuableDashboard from '@/views/contribuable/Dashboard.vue'
import DeclarationForm from "@/views/contribuable/DeclarationForm.vue"
import ListeImpots from '@/views/contribuable/ImpotsList.vue'
import DeclarationsExistantes from '@/views/contribuable/DeclarationsList.vue'
import DeposerLitige from '@/views/contribuable/LitigeForm.vue'
import SimulationMontant from '@/views/contribuable/SimulationMontant.vue'
import LitigesList from '@/views/contribuable/LitigesList.vue'


// Vues Agent
import AgentLogin from '@/views/agent/Login.vue'
import AgentSignup from '@/views/agent/Signup.vue'
import AgentDashboard from '@/views/agent/Dashboard.vue'
import GestionPaiementsAgent from '@/views/agent/GestionPaiementsAgent.vue'
// Import de la vue agent Litiges
import AgentLitiges from '@/views/agent/LitigesAgent.vue' // ou AgentLitiges.vue selon le nom du fichier
// Import du composant
import ContribuablesList from '@/views/agent/ContribuablesList.vue'








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
  path: '/contribuable/user/:idContribuable',
  name: 'UserPage',
  component: () => import('@/views/contribuable/UserPage.vue'),
  props: route => ({ idContribuable: Number(route.params.idContribuable) }),
  meta: { requiresAuth: true, userType: 'contribuable' }
},


  // Liste des impôts disponibles
  {
    path: '/impots/:idContribuable',
    name: 'ListeImpots',
    component: ListeImpots,
    props: route => ({
      idContribuable: Number(route.params.idContribuable)
    }),
    meta: { requiresAuth: true, userType: 'contribuable' }
  },

  // ✅ NOUVELLE DÉCLARATION (avec type d'impôt spécifique)
  {
    path: '/declarations/nouvelle/:idContribuable/:idTypeImpot',
    name: 'NouvelleDeclaration',
    component: DeclarationForm,
    props: route => ({
      idContribuable: Number(route.params.idContribuable),
      idTypeImpot: Number(route.params.idTypeImpot)
    }),
    meta: { requiresAuth: true, userType: 'contribuable' }
  },

  // ✅ NOUVELLE DÉCLARATION (sans type d'impôt - choix dans le formulaire)
  {
    path: '/declarations/nouvelle/:idContribuable',
    name: 'DeclarationForm',
    component: DeclarationForm,
    props: route => ({
      idContribuable: Number(route.params.idContribuable)
    }),
    meta: { requiresAuth: true, userType: 'contribuable' }
  },

  // ✅ LISTE DES DÉCLARATIONS EXISTANTES (par type d'impôt)
  {
    path: '/declarations/:idContribuable/:idTypeImpot',
    name: 'DeclarationsExistantes',
    component: DeclarationsExistantes,
    props: route => ({
      idContribuable: Number(route.params.idContribuable),
      idTypeImpot: Number(route.params.idTypeImpot)
    }),
    meta: { requiresAuth: true, userType: 'contribuable' }
  },

  // Déposer un litige (nouvelle version)
// Au lieu de DeposerLitige, on utilise LitigesList


{
  path: '/litige/:idContribuable',
  name: 'DeposerLitige',
  component: LitigesList,
  props: route => ({
    idContribuable: Number(route.params.idContribuable)
  }),
  meta: { requiresAuth: true, userType: 'contribuable' }
},

 /* // Déposer un litige
  {
    path: '/litige/:idContribuable',
    name: 'DeposerLitige',
    component: DeposerLitige,
    props: route => ({
      idContribuable: Number(route.params.idContribuable)
    }),
    meta: { requiresAuth: true, userType: 'contribuable' }
  },*/

  // Simulation Montant
  {
    path: '/simulation/:idContribuable',
    name: 'SimulationMontant',
    component: SimulationMontant,
    props: route => ({
      idContribuable: Number(route.params.idContribuable)
    }),
    meta: { requiresAuth: true, userType: 'contribuable' }
  },

  // ========== ROUTES AGENT ==========
  // Routes Agent
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
  {
    path: '/agent/paiements',
    name: 'GestionPaiementsAgent',
    component: GestionPaiementsAgent,
    meta: { requiresAuth: true, userType: 'agent' }
  },
  // Liste des contribuables (Agent)
  {
    path: '/agent/contribuables',
    name: 'ContribuablesList',
    component: ContribuablesList,
    meta: { requiresAuth: true, userType: 'agent' }
  },

  // ... dans ton tableau routes
  {
    path: '/agent/litigesAgent/:idContribuable',
    name: 'AgentLitiges',
    component: AgentLitiges,
    props: route => ({
      idContribuable: Number(route.params.idContribuable)
    }),
    meta: { requiresAuth: true, userType: 'agent' }
  },

]


const router = createRouter({
  history: createWebHistory(),
  routes
})

// Middleware d'authentification
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const userType = localStorage.getItem('userType')

  // Vérifier si la route nécessite une authentification
  if (to.meta.requiresAuth) {
    if (!token) {
      console.warn('🔒 Accès refusé: Aucun token trouvé')
      return next({ name: 'Home' })
    }

    // Vérifier le type d'utilisateur
    if (to.meta.userType && to.meta.userType !== userType) {
      console.warn(`🔒 Accès refusé: Type d'utilisateur incorrect (attendu: ${to.meta.userType}, reçu: ${userType})`)
      alert("Accès refusé. Vous n'avez pas les permissions nécessaires.")
      return next({ name: 'Home' })
    }
  }

  

  // Autoriser l'accès
  next()
})

export default router