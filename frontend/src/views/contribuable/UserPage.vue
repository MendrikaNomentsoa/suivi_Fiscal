<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Profil de {{ user?.prenom }}</h2>

    <div class="bg-white rounded-xl shadow p-6 w-full max-w-md">
      <p class="text-sm font-semibold text-gray-900 mb-1">Nom complet</p>
      <p class="text-gray-700 mb-3">{{ user?.nom }} {{ user?.prenom }}</p>

      <p class="text-sm font-semibold text-gray-900 mb-1">NIF</p>
      <p class="text-gray-700 mb-3">{{ user?.nif }}</p>

      <p class="text-sm font-semibold text-gray-900 mb-1">Email</p>
      <p class="text-gray-700 mb-3">{{ user?.email }}</p>

      <p class="text-sm font-semibold text-gray-900 mb-1">Téléphone</p>
      <p class="text-gray-700 mb-3">{{ user?.telephone || 'N/A' }}</p>

      <button @click="router.back()" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        Retour
      </button>
    </div>
  </div>
</template>

<script>
import { useAuth } from '@/composables/useAuth'
import { useRouter } from 'vue-router'

export default {
  name: 'UserPage',
  setup() {
    const { user } = useAuth()
    const router = useRouter()
    return { user, router }
  }
}
</script>
