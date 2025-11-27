<template>
  <div class="min-h-screen flex bg-gray-50">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg sticky top-0 h-screen flex flex-col justify-between">
      <div class="p-6">
        <!-- Logo -->
        <div class="flex items-center space-x-3 mb-10">
          <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
          <div>
            <h1 class="text-lg font-bold text-gray-900">Contribuable</h1>
            <p class="text-sm text-gray-500">Fiscalité</p>
          </div>
        </div>

        <!-- Navigation -->
        <nav class="space-y-2">
          <button class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-indigo-50 text-indigo-700 font-medium">
            <span>Tableau de bord</span>
          </button>
          <button @click="navigateTo('ListeImpots')" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-indigo-600 font-medium">
            <span>Mes Impôts</span>
          </button>
          <button @click="navigateTo('SimulationMontant')" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-indigo-600 font-medium">
            <span>Simulation</span>
          </button>
          <button @click="navigateTo('DeposerLitige')" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-indigo-600 font-medium">
            <span>Litiges</span>
          </button>
          <button @click="voirEcheances" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-indigo-600 font-medium">
            <span>Notifications</span>
          </button>
        </nav>
      </div>

      <!-- Logout Footer -->
      <div class="p-6 border-t border-gray-200">
        <button @click="handleLogout" class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg">
          <span>Déconnexion</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-6 relative">

      <!-- User info top-right -->
      <div class="absolute top-6 right-6">
        <div @click="toggleUserInfo" class="flex items-center space-x-3 cursor-pointer">
          <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
            <span class="text-indigo-700 font-bold text-sm">{{ user?.nom?.charAt(0) }}{{ user?.prenom?.charAt(0) }}</span>
          </div>
          <p class="text-gray-700 font-medium">{{ user?.prenom }}</p>
        </div>

        <div v-if="showUserInfo" class="mt-2 w-64 bg-white shadow-lg rounded-xl p-4 border border-gray-200">
          <p class="text-sm font-semibold text-gray-900">{{ user?.nom }} {{ user?.prenom }}</p>
          <p class="text-xs text-gray-500 mb-2">NIF: {{ user?.nif }}</p>
          <p class="text-xs text-gray-500 mb-2">Email: {{ user?.email }}</p>
          <p class="text-xs text-gray-500 mb-2">Téléphone: {{ user?.telephone || 'N/A' }}</p>
          <button @click="goToUserPage" class="w-full mt-2 px-3 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg text-sm font-medium text-center">
            Voir profil
          </button>
        </div>
      </div>

      <!-- Welcome -->
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-1">Bienvenue, {{ user?.prenom }} !</h2>
        <p class="text-gray-600">Voici un aperçu de votre activité fiscale</p>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-5 rounded-xl shadow hover:shadow-md transition">
          <p class="text-sm text-gray-500">Non valides</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.declarationsEnCours }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow hover:shadow-md transition">
          <p class="text-sm text-gray-500">Validées</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.declarationsValidees }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow hover:shadow-md transition">
          <p class="text-sm text-gray-500">Échéances proches</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.echeancesProches }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow hover:shadow-md transition">
          <p class="text-sm text-gray-500">Montant total dû</p>
          <p class="text-2xl font-bold text-gray-900">{{ formatMontant(stats.montantTotal) }}</p>
        </div>
      </div>

      <!-- Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Dernières déclarations -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow p-5">
          <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg text-gray-900">Dernières déclarations</h3>
            <button @click="voirToutesDeclarations" class="text-indigo-600 font-semibold text-sm flex items-center">
              Voir tout
            </button>
          </div>
          <div v-if="loading" class="text-center py-12">Chargement...</div>
          <div v-else-if="dernieresDeclarations.length > 0" class="space-y-3">
            <div v-for="declaration in dernieresDeclarations" :key="declaration.id_declaration" class="bg-gray-50 rounded-lg p-4 border-l-4 border-indigo-500 hover:bg-gray-100 transition">
              <div class="flex justify-between items-start mb-1">
                <div>
                  <h4 class="font-semibold text-gray-900">{{ declaration.type_impot?.nom || 'N/A' }}</h4>
                  <p class="text-sm text-gray-500">{{ formatDate(declaration.date_declaration) }}</p>
                </div>
                <span :class="[
                  'px-3 py-1 rounded-full text-xs font-semibold',
                  declaration.statut === 'non valide' ? 'bg-red-100 text-red-700' : 
                  declaration.statut === 'valider' ? 'bg-blue-100 text-blue-700' : 
                  'bg-green-100 text-green-700'
                ]">
                  {{ getStatutLabel(declaration.statut) }}
                </span>
              </div>
              <p class="text-lg font-bold text-gray-900">{{ formatMontant(declaration.montant) }}</p>
            </div>
          </div>
          <div v-else class="text-center py-12 text-gray-400">Aucune déclaration pour le moment</div>
        </div>

        <!-- Sidebar Column -->
        <div class="space-y-6">
          <!-- Prochaines échéances -->
          <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-bold text-lg text-gray-900 mb-4">Prochaines échéances</h3>
            <div v-if="prochainesEcheances.length > 0" class="space-y-3">
              <div v-for="echeance in prochainesEcheances" :key="echeance.id_echeance"
                   :class="['p-4 rounded-lg border-l-4 transition', isUrgent(echeance.date_limite) ? 'bg-red-50 border-red-500' : 'bg-gray-50 border-green-500']">
                <h4 class="text-sm font-semibold text-gray-900">{{ echeance.type_impot?.nom || 'N/A' }}</h4>
                <p class="text-xs text-gray-500 mb-1">{{ formatDate(echeance.date_limite) }}</p>
                <p class="font-bold text-gray-900">{{ formatMontant(echeance.montant_du + echeance.penalite + echeance.interet) }}</p>
              </div>
            </div>
            <div v-else class="text-center py-6 text-gray-400">Aucune échéance</div>
          </div>

          <!-- Litiges -->
          <div class="bg-white rounded-xl shadow p-5 text-center">
            <h3 class="font-bold text-lg text-gray-900 mb-4">Litiges</h3>
            <div class="flex flex-col items-center">
              <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                </svg>
              </div>
              <p class="text-gray-500 text-sm mb-2">Aucun litige en cours</p>
              <button @click="navigateTo('DeposerLitige')" class="text-indigo-600 font-semibold text-sm">Déposer un litige →</button>
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
  setup() {
    const router = useRouter()
    const { user, logout } = useAuth()
    const loading = ref(true)
    const dernieresDeclarations = ref([])
    const prochainesEcheances = ref([])
    const stats = ref({ declarationsEnCours:0, declarationsValidees:0, echeancesProches:0, montantTotal:0 })
    const impotsAValider = ref([])
    const showUserInfo = ref(false)
    const idContribuable = computed(() => user.value?.id_contribuable)

    const toggleUserInfo = () => showUserInfo.value = !showUserInfo.value
    const goToUserPage = () => router.push({ name:'UserPage', params:{ idContribuable: idContribuable.value } })

    const navigateTo = (routeName) => router.push({ name: routeName, params:{ idContribuable: idContribuable.value } })
    const voirToutesDeclarations = () => router.push({ name:'ListeImpots', params:{ idContribuable: idContribuable.value } })
    const voirEcheances = () => alert('Fonctionnalité à venir')

    const handleLogout = () => { if(confirm('Voulez-vous vous déconnecter ?')) logout() }

    const formatMontant = montant => new Intl.NumberFormat('fr-FR', { style:'decimal', minimumFractionDigits:0 }).format(montant) + ' Ar'
    const formatDate = date => new Date(date).toLocaleDateString('fr-FR', { day:'2-digit', month:'long', year:'numeric' })
    const getStatutLabel = statut => ({ 'valider':'valider','validee':'Validée','en_attente':'En attente' }[statut]||statut)
    const isUrgent = dateLimite => { const today=new Date(); const limite=new Date(dateLimite); return Math.ceil((limite-today)/(1000*60*60*24))<=7 && Math.ceil((limite-today)/(1000*60*60*24))>=0 }

    const loadDashboardData = async () => {
      if(!idContribuable.value) return
      loading.value = true
      try{
        const declRes = await axios.get('/api/declarations')
        const mesDecl = declRes.data.filter(d=>d.id_contribuable==idContribuable.value)
        dernieresDeclarations.value = mesDecl.sort((a,b)=>new Date(b.date_declaration)-new Date(a.date_declaration)).slice(0,5)
        stats.value.declarationsEnCours = mesDecl.filter(d=>d.statut==='valider').length
        stats.value.declarationsValidees = mesDecl.filter(d=>d.statut==='validee').length

        const echRes = await axios.get('/api/echeances')
        const mesEch = echRes.data.filter(e=>e.id_contribuable==idContribuable.value)
        const echeancesNonPayees = mesEch.filter(e=>!e.date_paiement)
        prochainesEcheances.value = echeancesNonPayees.sort((a,b)=>new Date(a.date_limite)-new Date(b.date_limite)).slice(0,3)
        const today = new Date(); const in30 = new Date(today.getTime()+30*24*60*60*1000)
        stats.value.echeancesProches = echeancesNonPayees.filter(e=>new Date(e.date_limite)<=in30 && new Date(e.date_limite)>=today).length
        stats.value.montantTotal = echeancesNonPayees.reduce((sum,e)=>sum+(e.montant_du+e.penalite+e.interet),0)

        const impotsRes = await axios.get('/api/type-impots')
        const idsDeclares = mesDecl.map(d=>d.id_type_impot)
        impotsAValider.value = impotsRes.data.filter(imp=>!idsDeclares.includes(imp.id_type_impot))
      } catch(err){ console.error(err) }
      finally{ loading.value=false }
    }

    onMounted(loadDashboardData)

    return { user, stats, dernieresDeclarations, prochainesEcheances, impotsAValider, loading, showUserInfo, toggleUserInfo, goToUserPage, navigateTo, voirToutesDeclarations, voirEcheances, handleLogout, formatMontant, formatDate, getStatutLabel, isUrgent }
  }
}
</script>
