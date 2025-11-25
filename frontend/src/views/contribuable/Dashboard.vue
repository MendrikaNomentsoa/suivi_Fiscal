<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 flex">
    
    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-white/80 backdrop-blur-lg border-r border-slate-200/50 flex-shrink-0 sticky top-0 h-screen">
      <div class="p-6">
        <!-- Logo -->
        <div class="flex items-center space-x-3 mb-8">
          <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
          <div>
            <h1 class="text-sm font-bold text-slate-900">Contribuable</h1>
            <p class="text-xs text-slate-500">Fiscalité</p>
          </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="space-y-2">
          <button class="w-full flex items-center space-x-3 px-4 py-3 bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700 rounded-xl font-medium transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>Tableau de bord</span>
          </button>

          <button @click="navigateTo('ListeImpots')" class="w-full flex items-center space-x-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-xl font-medium transition-all duration-200 group">
            <svg class="w-5 h-5 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span class="group-hover:text-slate-900">Mes Impôts</span>
          </button>

          <button @click="navigateTo('SimulationMontant')" class="w-full flex items-center space-x-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-xl font-medium transition-all duration-200 group">
            <svg class="w-5 h-5 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <span class="group-hover:text-slate-900">Simulation</span>
          </button>

          <button @click="navigateTo('DeposerLitige')" class="w-full flex items-center space-x-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-xl font-medium transition-all duration-200 group">
            <svg class="w-5 h-5 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
            </svg>
            <span class="group-hover:text-slate-900">Litiges</span>
          </button>

          <button @click="voirEcheances" class="w-full flex items-center space-x-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-xl font-medium transition-all duration-200 group">
            <svg class="w-5 h-5 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="group-hover:text-slate-900">Notification</span>
          </button>
        </nav>
      </div>

      <!-- User Section at Bottom -->
      <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-slate-200/50">
        <div class="flex items-center space-x-3 mb-3">
          <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center">
            <span class="text-indigo-700 font-bold text-sm">{{ user?.nom?.charAt(0) }}{{ user?.prenom?.charAt(0) }}</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-slate-900 truncate">{{ user?.nom }} {{ user?.prenom }}</p>
            <p class="text-xs text-slate-500 truncate">NIF: {{ user?.nif }}</p>
          </div>
        </div>
        <button @click="handleLogout" class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-colors duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span class="text-sm font-medium">Déconnexion</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">
      <div class="max-w-7xl mx-auto px-6 py-8">
        
        <!-- Welcome Section -->
        <div class="mb-8">
          <h2 class="text-3xl font-bold text-slate-900 mb-2">
            Bienvenue, {{ user?.prenom }} ! 
          </h2>
          <p class="text-slate-600">
            Voici un aperçu de votre activité fiscale
          </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Stat 1 -->
          <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-6 border border-slate-200/50 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600 mb-1">En cours</p>
                <p class="text-3xl font-bold text-slate-900">{{ stats.declarationsEnCours }}</p>
              </div>
              <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Stat 2 -->
          <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-6 border border-slate-200/50 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600 mb-1">Validées</p>
                <p class="text-3xl font-bold text-slate-900">{{ stats.declarationsValidees }}</p>
              </div>
              <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Stat 3 -->
          <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-6 border border-slate-200/50 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600 mb-1">Échéances proches</p>
                <p class="text-3xl font-bold text-slate-900">{{ stats.echeancesProches }}</p>
              </div>
              <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Stat 4 -->
          <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-6 border border-slate-200/50 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600 mb-1">Montant total dû</p>
                <p class="text-2xl font-bold text-slate-900">{{ formatMontant(stats.montantTotal) }}</p>
              </div>
              <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <!-- Dernières déclarations - Takes 2 columns -->
          <div class="lg:col-span-2">
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-6 border border-slate-200/50 shadow-sm">
              <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-900">Dernières déclarations</h3>
                <button @click="voirToutesDeclarations" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold flex items-center">
                  Voir tout
                  <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </div>

              <div v-if="loading" class="text-center py-12">
                <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-slate-600 mt-2">Chargement...</p>
              </div>

              <div v-else-if="dernieresDeclarations.length > 0" class="space-y-3">
                <div 
                  v-for="declaration in dernieresDeclarations" 
                  :key="declaration.id_declaration"
                  class="bg-slate-50 rounded-xl p-4 hover:bg-slate-100 transition-colors duration-200 border-l-4 border-indigo-500"
                >
                  <div class="flex justify-between items-start mb-2">
                    <div>
                      <h4 class="font-semibold text-slate-900">{{ declaration.type_impot?.nom || 'N/A' }}</h4>
                      <p class="text-sm text-slate-600">{{ formatDate(declaration.date_declaration) }}</p>
                    </div>
                    <span 
                      :class="[
                        'px-3 py-1 rounded-full text-xs font-semibold',
                        declaration.statut === 'non valide' ? 'bg-red-100 text-red-700' : 
                        declaration.statut === 'valider' ? 'bg-green-100 text-green-700' : 
                        'bg-blue-100 text-blue-700'
                      ]"
                    >
                      {{ getStatutLabel(declaration.statut) }}
                    </span>
                  </div>
                  <p class="text-lg font-bold text-slate-900">{{ formatMontant(declaration.montant) }}</p>
                </div>
              </div>

              <div v-else class="text-center py-12">
                <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-slate-500">Aucune déclaration pour le moment</p>
              </div>
            </div>
          </div>

          <!-- Sidebar Column -->
          <div class="space-y-6">
            
            <!-- Prochaines échéances -->
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-6 border border-slate-200/50 shadow-sm">
              <h3 class="text-xl font-bold text-slate-900 mb-4">Prochaines échéances</h3>

              <div v-if="prochainesEcheances.length > 0" class="space-y-3">
                <div 
                  v-for="echeance in prochainesEcheances" 
                  :key="echeance.id_echeance"
                  :class="[
                    'rounded-xl p-4 border-l-4 transition-colors duration-200',
                    isUrgent(echeance.date_limite) ? 'bg-red-50 border-red-500' : 'bg-slate-50 border-green-500'
                  ]"
                >
                  <h4 class="font-semibold text-slate-900 mb-2 text-sm">{{ echeance.type_impot?.nom || 'N/A' }}</h4>
                  <div class="flex items-center text-xs text-slate-600 mb-2">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ formatDate(echeance.date_limite) }}
                  </div>
                  <p class="text-base font-bold text-slate-900">
                    {{ formatMontant(echeance.montant_du + echeance.penalite + echeance.interet) }}
                  </p>
                </div>
              </div>

              <div v-else class="text-center py-8">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-slate-500 text-sm">Aucune échéance</p>
              </div>
            </div>

            <!-- Litiges section -->
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-6 border border-slate-200/50 shadow-sm">
              <h3 class="text-xl font-bold text-slate-900 mb-4">Litiges</h3>
              
              <div class="text-center py-8">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                  <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                  </svg>
                </div>
                <p class="text-slate-500 text-sm mb-4">Aucun litige en cours</p>
                <button @click="navigateTo('DeposerLitige')" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">
                  Déposer un litige →
                </button>
              </div>
            </div>

          </div>

        </div>

      </div>
    </main>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import axios from 'axios'

export default {
  name: 'ContribuableDashboard',
  setup() {
    const router = useRouter()
    const { user, logout } = useAuth()

    const loading = ref(true)
    const dernieresDeclarations = ref([])
    const prochainesEcheances = ref([])
    const stats = ref({
      declarationsEnCours: 0,
      declarationsValidees: 0,
      echeancesProches: 0,
      montantTotal: 0
    })

    const idContribuable = computed(() => user.value?.id_contribuable)

    const loadDashboardData = async () => {
      if (!idContribuable.value) return

      loading.value = true
      try {
        const declResponse = await axios.get('/api/declarations')
        const mesDeclarations = declResponse.data.filter(
          d => d.id_contribuable == idContribuable.value
        )

        dernieresDeclarations.value = mesDeclarations
          .sort((a, b) => new Date(b.date_declaration) - new Date(a.date_declaration))
          .slice(0, 5)

        stats.value.declarationsEnCours = mesDeclarations.filter(
          d => d.statut === 'valider'
        ).length
        stats.value.declarationsValidees = mesDeclarations.filter(
          d => d.statut === 'validee'
        ).length

        const echResponse = await axios.get('/api/echeances')
        const mesEcheances = echResponse.data.filter(
          e => e.id_contribuable == idContribuable.value
        )

        const echeancesNonPayees = mesEcheances.filter(e => !e.date_paiement)
        
        prochainesEcheances.value = echeancesNonPayees
          .sort((a, b) => new Date(a.date_limite) - new Date(b.date_limite))
          .slice(0, 3)

        const today = new Date()
        const in30Days = new Date(today.getTime() + 30 * 24 * 60 * 60 * 1000)
        stats.value.echeancesProches = echeancesNonPayees.filter(
          e => new Date(e.date_limite) <= in30Days && new Date(e.date_limite) >= today
        ).length

        stats.value.montantTotal = echeancesNonPayees.reduce(
          (sum, e) => sum + (e.montant_du + e.penalite + e.interet), 0
        )

      } catch (error) {
        console.error('Erreur lors du chargement des données:', error)
      } finally {
        loading.value = false
      }
    }

    const navigateTo = (routeName) => {
      if (routeName === 'ListeImpots') {
        router.push({ name: routeName, params: { idContribuable: idContribuable.value } })
      } else if (routeName === 'SimulationMontant') {
        router.push({ name: routeName, params: { idContribuable: idContribuable.value } })
      } else if (routeName === 'DeposerLitige') {
        router.push({ name: routeName, params: { idContribuable: idContribuable.value } })
      }
    }

    const voirToutesDeclarations = () => {
      router.push({ name: 'ListeImpots', params: { idContribuable: idContribuable.value } })
    }

    const voirEcheances = () => {
      alert('Fonctionnalité à venir : Vue détaillée des échéances')
    }

    const handleLogout = () => {
      if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
        logout()
      }
    }

    const formatMontant = (montant) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'decimal',
        minimumFractionDigits: 0
      }).format(montant) + ' Ar'
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
      })
    }

    const getStatutLabel = (statut) => {
      const labels = {
        'valider': 'valider',
        'validee': 'Validée',
        'en_attente': 'En attente'
      }
      return labels[statut] || statut
    }

    const isUrgent = (dateLimite) => {
      const today = new Date()
      const limite = new Date(dateLimite)
      const diffDays = Math.ceil((limite - today) / (1000 * 60 * 60 * 24))
      return diffDays <= 7 && diffDays >= 0
    }

    onMounted(() => {
      loadDashboardData()
    })

    return {
      user,
      loading,
      dernieresDeclarations,
      prochainesEcheances,
      stats,
      navigateTo,
      voirToutesDeclarations,
      voirEcheances,
      handleLogout,
      formatMontant,
      formatDate,
      getStatutLabel,
      isUrgent
    }
  }
}
</script>