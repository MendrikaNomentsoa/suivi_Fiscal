// composables/useNotifications.js
import { ref, computed } from 'vue'
import axios from 'axios'

export function useNotifications(idContribuable) {
  const notifications = ref([])
  const loading = ref(false)
  const error = ref(null)

  // Computed
  const unreadCount = computed(() => 
    notifications.value.filter(n => n.statut === 'non lu').length
  )

  const sortedNotifications = computed(() => 
    [...notifications.value].sort((a, b) => 
      new Date(b.date_envoi) - new Date(a.date_envoi)
    )
  )

  // Charger les notifications
  const loadNotifications = async () => {
    if (!idContribuable) {
      console.warn('ID Contribuable non fourni')
      return
    }
    
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.get(`/notifications/contribuable/${idContribuable}`)
      notifications.value = response.data
    } catch (err) {
      error.value = 'Erreur lors du chargement des notifications'
      console.error('Erreur chargement notifications:', err)
    } finally {
      loading.value = false
    }
  }

  // Marquer une notification comme lue
  const markAsRead = async (id_Notif) => {
    try {
      await axios.patch(`/notifications/${id_Notif}/mark-as-read`)
      
      // Mettre à jour localement
      const notif = notifications.value.find(n => n.id_Notif === id_Notif)
      if (notif) {
        notif.statut = 'lu'
        notif.date_lecture = new Date().toISOString()
      }
      
      return true
    } catch (err) {
      console.error('Erreur lors du marquage comme lu:', err)
      return false
    }
  }

  // Marquer toutes les notifications comme lues
  const markAllAsRead = async () => {
    const unreadNotifs = notifications.value.filter(n => n.statut === 'non lu')
    
    if (unreadNotifs.length === 0) return true
    
    try {
      await Promise.all(
        unreadNotifs.map(n => axios.patch(`/notifications/${n.id_Notif}/mark-as-read`))
      )
      
      // Mettre à jour localement
      notifications.value = notifications.value.map(n => ({
        ...n,
        statut: 'lu',
        date_lecture: n.statut === 'non lu' ? new Date().toISOString() : n.date_lecture
      }))
      
      return true
    } catch (err) {
      console.error('Erreur lors du marquage multiple:', err)
      return false
    }
  }

  // Supprimer une notification
  const deleteNotification = async (id_Notif) => {
    try {
      await axios.delete(`/notifications/${id_Notif}`)
      
      // Retirer localement
      notifications.value = notifications.value.filter(n => n.id_Notif !== id_Notif)
      
      return true
    } catch (err) {
      console.error('Erreur lors de la suppression:', err)
      return false
    }
  }

  // Créer une nouvelle notification
  const createNotification = async (data) => {
    try {
      const response = await axios.post('/notifications', {
        ...data,
        date_envoi: data.date_envoi || new Date().toISOString(),
        statut: data.statut || 'non lu'
      })
      
      // Ajouter localement
      if (response.data.notification) {
        notifications.value.unshift(response.data.notification)
      }
      
      return response.data.notification
    } catch (err) {
      console.error('Erreur lors de la création:', err)
      throw err
    }
  }

  // Obtenir le nombre de notifications non lues (depuis l'API)
  const fetchUnreadCount = async () => {
    if (!idContribuable) return 0
    
    try {
      const response = await axios.get(`/notifications/contribuable/${idContribuable}/unread-count`)
      return response.data.unread_count || 0
    } catch (err) {
      console.error('Erreur lors de la récupération du compteur:', err)
      return 0
    }
  }

  // Formater la date en relatif
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

  // Obtenir la classe CSS pour le type de notification
  const getNotificationColorClass = (type) => {
    const colors = {
      'echeance': 'bg-blue-100 text-blue-600',
      'validation': 'bg-green-100 text-green-600',
      'rappel': 'bg-orange-100 text-orange-600',
      'litige': 'bg-red-100 text-red-600',
      'information': 'bg-gray-100 text-gray-600'
    }
    return colors[type] || colors.information
  }

  return {
    // State
    notifications,
    loading,
    error,
    
    // Computed
    unreadCount,
    sortedNotifications,
    
    // Methods
    loadNotifications,
    markAsRead,
    markAllAsRead,
    deleteNotification,
    createNotification,
    fetchUnreadCount,
    
    // Helpers
    formatDateRelative,
    getNotificationColorClass
  }
}