<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Mes Notifications</h1>
        <p class="text-gray-600">Gérez toutes vos notifications fiscales</p>
      </div>

      <!-- Actions -->
      <div class="bg-white rounded-xl shadow p-4 mb-6 flex justify-between items-center">
        <div class="flex items-center space-x-4">
          <button @click="loadNotifications" :disabled="loading" 
                  class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Actualiser</span>
          </button>
          
          <button v-if="unreadCount > 0" @click="markAllAsRead" 
                  class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>Tout marquer comme lu ({{ unreadCount }})</span>
          </button>
        </div>

        <!-- Filters -->
        <div class="flex items-center space-x-2">
          <button @click="filter = 'all'" 
                  :class="['px-3 py-1 rounded-lg text-sm font-medium', filter === 'all' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100']">
            Toutes
          </button>
          <button @click="filter = 'unread'" 
                  :class="['px-3 py-1 rounded-lg text-sm font-medium', filter === 'unread' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100']">
            Non lues
          </button>
          <button @click="filter = 'read'" 
                  :class="['px-3 py-1 rounded-lg text-sm font-medium', filter === 'read' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100']">
            Lues
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="bg-white rounded-xl shadow p-12 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Chargement des notifications...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
        <div class="flex items-start space-x-3">
          <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <p class="text-sm font-medium text-red-900">{{ error }}</p>
            <button @click="loadNotifications" class="text-sm text-red-600 hover:text-red-700 underline mt-1">
              Réessayer
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredNotifications.length === 0" class="bg-white rounded-xl shadow p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune notification</h3>
        <p class="text-gray-500">Vous n'avez pas de notifications {{ filter === 'unread' ? 'non lues' : filter === 'read' ? 'lues' : '' }}</p>
      </div>

      <!-- Notifications List -->
      <div v-else class="space-y-4">
        <div v-for="notif in filteredNotifications" :key="notif.id_Notif"
             :class="['bg-white rounded-xl shadow hover:shadow-md transition', notif.statut === 'non lu' ? 'border-l-4 border-indigo-600' : '']">
          <div class="p-6">
            <div class="flex items-start justify-between">
              <div class="flex items-start space-x-4 flex-1">
                <!-- Icon -->
                <div :class="['p-3 rounded-lg', getNotificationColorClass(notif.type)]">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="notif.type === 'echeance'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    <path v-else-if="notif.type === 'validation'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    <path v-else-if="notif.type === 'rappel'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>

                <!-- Content -->
                <div class="flex-1">
                  <div class="flex items-start justify-between mb-2">
                    <h3 :class="['text-lg font-semibold', notif.statut === 'non lu' ? 'text-gray-900' : 'text-gray-700']">
                      {{ notif.design }}
                    </h3>
                    <span v-if="notif.statut === 'non lu'" class="ml-2 px-2 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full">
                      Nouveau
                    </span>
                  </div>
                  
                  <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <span class="flex items-center space-x-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>{{ formatDateRelative(notif.date_envoi) }}</span>
                    </span>
                    
                    <span v-if="notif.type" class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded">
                      {{ notif.type }}
                    </span>
                  </div>

                  <div v-if="notif.date_lecture" class="mt-2 text-xs text-gray-400">
                    Lu le {{ new Date(notif.date_lecture).toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex items-center space-x-2 ml-4">
                <button v-if="notif.statut === 'non lu'" @click="markAsRead(notif.id_Notif)"
                        class="p-2 hover:bg-green-100 text-green-600 rounded-lg transition"
                        title="Marquer comme lu">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </button>
                
                <button @click="confirmDelete(notif.id_Notif)"
                        class="p-2 hover:bg-red-100 text-red-600 rounded-lg transition"
                        title="Supprimer">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useAuth } from '@/composables/useAuth'
import { useNotifications } from '@/composables/useNotifications'

export default {
  setup() {
    const { user } = useAuth()
    const filter = ref('all')
    
    const {
      notifications,
      loading,
      error,
      unreadCount,
      loadNotifications,
      markAsRead,
      markAllAsRead,
      deleteNotification,
      formatDateRelative,
      getNotificationColorClass
    } = useNotifications(computed(() => user.value?.id_contribuable))

    const filteredNotifications = computed(() => {
      if (filter.value === 'unread') {
        return notifications.value.filter(n => n.statut === 'non lu')
      }
      if (filter.value === 'read') {
        return notifications.value.filter(n => n.statut === 'lu')
      }
      return notifications.value
    })

    const confirmDelete = async (id_Notif) => {
      if (confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
        await deleteNotification(id_Notif)
      }
    }

    onMounted(() => {
      loadNotifications()
    })

    return {
      filter,
      notifications,
      filteredNotifications,
      loading,
      error,
      unreadCount,
      loadNotifications,
      markAsRead,
      markAllAsRead,
      confirmDelete,
      formatDateRelative,
      getNotificationColorClass
    }
  }
}
</script>