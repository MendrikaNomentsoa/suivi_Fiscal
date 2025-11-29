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
    <main class="flex-1 overflow-y-auto p-6">

      <!-- Top Bar -->
      <div class="flex justify-between items-center mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 mb-1">Bienvenue, {{ user?.prenom }} !</h2>
          <p class="text-gray-600">Voici un aperçu de votre activité fiscale</p>
        </div>

        <div class="flex items-center space-x-4">
          <!-- Notifications Bell -->
          <div class="relative">
            <button @click="toggleNotifications" class="relative p-2 rounded-lg hover:bg-gray-100 transition">
              <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <span v-if="unreadCount > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                {{ unreadCount > 9 ? '9+' : unreadCount }}
              </span>
            </button>

            <!-- Notifications Dropdown -->
            <div v-if="showNotifications" class="absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-xl border border-gray-200 z-50">
              <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-bold text-gray-900">Notifications</h3>
                <div class="flex items-center space-x-2">
                  <button v-if="unreadCount > 0" @click="markAllAsRead" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                    Tout marquer comme lu
                  </button>
                  <button @click="showNotifications = false" class="p-1 hover:bg-gray-100 rounded">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              <div class="max-h-96 overflow-y-auto">
                <div v-if="loadingNotifications" class="p-8 text-center">
                  <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
                  <p class="text-sm text-gray-500 mt-2">Chargement...</p>
                </div>

                <div v-else-if="notifications.length === 0" class="p-8 text-center">
                  <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                  </svg>
                  <p class="text-gray-500 text-sm">Aucune notification</p>
                </div>

                <div v-else class="divide-y divide-gray-100">
                  <div v-for="notif in notifications" :key="notif.id_Notif"
                       :class="['p-4 hover:bg-gray-50 transition cursor-pointer', notif.statut === 'non lu' ? 'bg-blue-50' : '']">
                    <div class="flex items-start space-x-3">
                      <div :class="['p-2 rounded-lg', getNotificationColorClass(notif.type)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="notif.type === 'echeance'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          <path v-else-if="notif.type === 'validation'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                          <path v-else-if="notif.type === 'rappel'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                      </div>
                      
                      <div class="flex-1 min-w-0">
                        <p :class="['text-sm', notif.statut === 'non lu' ? 'font-semibold text-gray-900' : 'text-gray-700']">
                          {{ notif.design }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                          {{ formatDateRelative(notif.date_envoi) }}
                        </p>
                      </div>

                      <div class="flex items-center space-x-1">
                        <button v-if="notif.statut === 'non lu'" @click.stop="markAsRead(notif.id_Notif)" 
                                class="p-1 hover:bg-gray-200 rounded" title="Marquer comme lu">
                          <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                          </svg>
                        </button>
                        <button @click.stop="deleteNotification(notif.id_Notif)" 
                                class="p-1 hover:bg-gray-200 rounded" title="Supprimer">
                          <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- User info -->
          <div class="relative">
            <div @click="toggleUserInfo" class="flex items-center space-x-3 cursor-pointer">
              <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                <span class="text-indigo-700 font-bold text-sm">{{ user?.nom?.charAt(0) }}{{ user?.prenom?.charAt(0) }}</span>
              </div>
              <p class="text-gray-700 font-medium">{{ user?.prenom }}</p>
            </div>

            <div v-if="showUserInfo" class="absolute right-0 mt-2 w-64 bg-white shadow-xl rounded-xl p-4 border border-gray-200 z-50">
              <p class="text-sm font-semibold text-gray-900">{{ user?.nom }} {{ user?.prenom }}</p>
              <p class="text-xs text-gray-500 mb-2">NIF: {{ user?.nif }}</p>
              <p class="text-xs text-gray-500 mb-2">Email: {{ user?.email }}</p>
              <p class="text-xs text-gray-500 mb-2">Téléphone: {{ user?.telephone || 'N/A' }}</p>
              <button @click="goToUserPage" class="w-full mt-2 px-3 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg text-sm font-medium text-center">
                Voir profil
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Notification Alert Banner -->
      <div v-if="unreadCount > 0" class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 mb-6">
        <div class="flex items-start space-x-3">
          <svg class="w-5 h-5 text-indigo-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <p class="text-sm font-medium text-indigo-900">
              Vous avez {{ unreadCount }} notification{{ unreadCount !== 1 ? 's' : '' }} non lue{{ unreadCount !== 1 ? 's' : '' }}
            </p>
            <p class="text-xs text-indigo-700 mt-1">
              Cliquez sur l'icône de cloche pour les consulter
            </p>
          </div>
        </div>
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
    const loadingNotifications = ref(false)
    const dernieresDeclarations = ref([])
    const prochainesEcheances = ref([])
    const stats = ref({ declarationsEnCours:0, declarationsValidees:0, echeancesProches:0, montantTotal:0 })
    const showUserInfo = ref(false)
    const showNotifications = ref(false)
    
    // Notifications
    const notifications = ref([])
    const unreadCount = computed(() => notifications.value.filter(n => n.statut === 'non lu').length)
    
    const idContribuable = computed(() => user.value?.id_contribuable)

    const toggleUserInfo = () => showUserInfo.value = !showUserInfo.value
    const toggleNotifications = () => {
      showNotifications.value = !showNotifications.value
      if (showNotifications.value && notifications.value.length === 0) {
        loadNotifications()
      }
    }
    
    const goToUserPage = () => router.push({ name:'UserPage', params:{ idContribuable: idContribuable.value } })
    const navigateTo = (routeName) => router.push({ name: routeName, params:{ idContribuable: idContribuable.value } })
    const voirToutesDeclarations = () => router.push({ name:'ListeImpots', params:{ idContribuable: idContribuable.value } })
    const handleLogout = () => { if(confirm('Voulez-vous vous déconnecter ?')) logout() }

    const formatMontant = montant => new Intl.NumberFormat('fr-FR', { style:'decimal', minimumFractionDigits:0 }).format(montant) + ' Ar'
    const formatDate = date => new Date(date).toLocaleDateString('fr-FR', { day:'2-digit', month:'long', year:'numeric' })
    const getStatutLabel = statut => ({ 'valider':'valider','validee':'Validée','en_attente':'En attente' }[statut]||statut)
    const isUrgent = dateLimite => { 
      const today=new Date()
      const limite=new Date(dateLimite)
      const jours = Math.ceil((limite-today)/(1000*60*60*24))
      return jours<=7 && jours>=0 
    }

    const formatDateRelative = (dateString) => {
      const date = new Date(dateString)
      const now = new Date()
      const diffMs = now - date
      const diffMins = Math.floor(diffMs / 60000)
      const diffHours = Math.floor(diffMs / 3600000)
      const diffDays = Math.floor(diffMs / 86400000)

      if (diffMins < 1) return 'À l\'instant'
      if (diffMins < 60) return `Il y a ${diffMins} min`
      if (diffHours < 24) return `Il y a ${diffHours}h`
      if (diffDays < 7) return `Il y a ${diffDays}j`
      return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' })
    }

    const getNotificationColorClass = (type) => {
      switch(type) {
        case 'echeance': return 'bg-blue-100 text-blue-600'
        case 'validation': return 'bg-green-100 text-green-600'
        case 'rappel': return 'bg-orange-100 text-orange-600'
        default: return 'bg-gray-100 text-gray-600'
      }
    }

    const loadNotifications = async () => {
      if(!idContribuable.value) return
      loadingNotifications.value = true
      try {
        const response = await axios.get(`/notifications/contribuable/${idContribuable.value}`)
        notifications.value = response.data
      } catch(err) {
        console.error('Erreur chargement notifications:', err)
      } finally {
        loadingNotifications.value = false
      }
    }

    const markAsRead = async (id_Notif) => {
      try {
        await axios.patch(`/notifications/${id_Notif}/mark-as-read`)
        const notif = notifications.value.find(n => n.id_Notif === id_Notif)
        if (notif) {
          notif.statut = 'lu'
          notif.date_lecture = new Date().toISOString()
        }
      } catch(err) {
        console.error('Erreur marquage comme lu:', err)
      }
    }

    const markAllAsRead = async () => {
      const unreadNotifs = notifications.value.filter(n => n.statut === 'non lu')
      try {
        await Promise.all(
          unreadNotifs.map(n => axios.patch(`/notifications/${n.id_Notif}/mark-as-read`))
        )
        notifications.value = notifications.value.map(n => ({
          ...n,
          statut: 'lu',
          date_lecture: n.statut === 'non lu' ? new Date().toISOString() : n.date_lecture
        }))
      } catch(err) {
        console.error('Erreur marquage multiple:', err)
      }
    }

    const deleteNotification = async (id_Notif) => {
      try {
        await axios.delete(`/notifications/${id_Notif}`)
        notifications.value = notifications.value.filter(n => n.id_Notif !== id_Notif)
      } catch(err) {
        console.error('Erreur suppression:', err)
      }
    }

    const loadDashboardData = async () => {
      if(!idContribuable.value) return
      loading.value = true
      try{
        const declRes = await axios.get('/declarations')
        const mesDecl = declRes.data.filter(d=>d.id_contribuable==idContribuable.value)
        dernieresDeclarations.value = mesDecl.sort((a,b)=>new Date(b.date_declaration)-new Date(a.date_declaration)).slice(0,5)
        stats.value.declarationsEnCours = mesDecl.filter(d=>d.statut==='valider').length
        stats.value.declarationsValidees = mesDecl.filter(d=>d.statut==='validee').length

        const echRes = await axios.get('/echeances')
        const mesEch = echRes.data.filter(e=>e.id_contribuable==idContribuable.value)
        const echeancesNonPayees = mesEch.filter(e=>!e.date_paiement)
        prochainesEcheances.value = echeancesNonPayees.sort((a,b)=>new Date(a.date_limite)-new Date(b.date_limite)).slice(0,3)
        const today = new Date(); const in30 = new Date(today.getTime()+30*24*60*60*1000)
        stats.value.echeancesProches = echeancesNonPayees.filter(e=>new Date(e.date_limite)<=in30 && new Date(e.date_limite)>=today).length
        stats.value.montantTotal = echeancesNonPayees.reduce((sum,e)=>sum+(e.montant_du+e.penalite+e.interet),0)
      } catch(err){ console.error(err) }
      finally{ loading.value=false }
    }

    onMounted(() => {
      loadDashboardData()
      loadNotifications()
    })

    return { 
      user, stats, dernieresDeclarations, prochainesEcheances, loading, loadingNotifications,
      showUserInfo, showNotifications, notifications, unreadCount,
      toggleUserInfo, toggleNotifications, goToUserPage, navigateTo, voirToutesDeclarations, handleLogout,
      formatMontant, formatDate, formatDateRelative, getStatutLabel, isUrgent, getNotificationColorClass,
      markAsRead, markAllAsRead, deleteNotification
    }
  }
}
</script>