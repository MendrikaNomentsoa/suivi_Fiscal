<template>
  <div class="min-h-screen bg-slate-50 py-6 px-4">
    <div class="max-w-5xl mx-auto">

      <!-- Header avec bouton -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-900">Mes Litiges</h1>
        <button @click="ouvrirFormulaire"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
          + Nouveau litige
        </button>
      </div>

      <!-- Navbar horizontale filtres -->
      <div class="flex space-x-4 mb-6 overflow-x-auto">
        <button v-for="tab in tabs" :key="tab"
                @click="statutActif = tab"
                :class="[
                  'px-4 py-2 rounded-lg font-medium whitespace-nowrap',
                  statutActif === tab ? 'bg-indigo-600 text-white' : 'bg-white shadow-sm text-slate-700 hover:bg-indigo-50'
                ]">
          {{ tabLabels[tab] }}
        </button>
      </div>

      <!-- Message d'erreur -->
      <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ error }}
      </div>

      <!-- Liste des litiges filtrés -->
      <div v-if="loading" class="text-center py-8 text-slate-600">
        Chargement...
      </div>

      <div v-else-if="litigesFiltres.length > 0" class="space-y-4">
        <div v-for="litige in litigesFiltres" :key="litige.id_Litige"
             class="bg-white shadow-md rounded-2xl p-4 flex justify-between items-center group hover:shadow-lg transition-shadow">
          <div>
            <p class="font-semibold text-slate-800">{{ litige.sujet }}</p>
            <span
              :class="[
                'text-sm font-medium px-2 py-1 rounded-full',
                getStatusColor(litige.statut)
              ]">
              {{ litige.statut }}
            </span>
          </div>
          <div class="flex space-x-2">
            <button @click="toggleDetails(litige)"
                    class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center space-x-1">
              <span>Détails</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <div v-else class="bg-white p-6 rounded-2xl shadow text-center text-slate-600">
        Aucun litige pour ce statut
      </div>

      <!-- Pop-up détails -->
      <transition name="fade">
        <div v-if="litigeDetails" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="litigeDetails = null">
          <div class="bg-white rounded-2xl shadow-xl p-6 w-11/12 md:w-1/2 relative">
            <h3 class="text-lg font-bold text-slate-900 mb-4">📌 Détails du litige</h3>
            <div class="space-y-2">
              <p><span class="font-semibold">Sujet:</span> {{ litigeDetails.sujet }}</p>
              <p><span class="font-semibold">Description:</span> {{ litigeDetails.description || 'Aucune description' }}</p>
              <p><span class="font-semibold">Statut:</span> {{ litigeDetails.statut }}</p>
              <p><span class="font-semibold">Date ouverture:</span> {{ formatDate(litigeDetails.date_ouverture) }}</p>
            </div>

            <button @click="litigeDetails = null"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-800">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </transition>

      <!-- Pop-up formulaire -->
      <LitigeForm 
        v-if="formVisible"
        :id-contribuable="idContribuable"
        @fermer="fermerFormulaire"
        @litige-envoye="handleNouveauLitige"
      />

    </div>
  </div>
</template>

<script>
import axios from "axios";
import LitigeForm from './LitigeForm.vue';

// Configuration de l'URL de base de l'API
const API_BASE_URL = "/api"; // Utilisera le proxy
// OU si le backend CORS est configuré :
// const API_BASE_URL = "http://127.0.0.1:8000/api";

export default {
  components: { LitigeForm },
  props: ["idContribuable"],
  
  data() {
    return {
      litiges: [],
      litigeDetails: null,
      formVisible: false,
      statutActif: 'en_attente',
      loading: false,
      error: null,
      tabs: ['en_attente','en_cours','resolu','rejete'],
      tabLabels: {
        'en_attente': 'En attente',
        'en_cours': 'En cours',
        'resolu': 'Résolu',
        'rejete': 'Rejeté'
      }
    };
  },

  computed: {
    litigesFiltres() {
      return this.litiges.filter(l => l.statut === this.statutActif);
    }
  },

  mounted() {
    this.loadLitiges();
  },

  methods: {
    async loadLitiges() {
      this.loading = true;
      this.error = null;
      
      try {
        const res = await axios.get(`${API_BASE_URL}/litiges`);
        // Filtrer uniquement les litiges du contribuable courant
        this.litiges = res.data.filter(l => l.id_Contribuable === Number(this.idContribuable));
      } catch (err) {
        console.error("Erreur lors du chargement des litiges:", err);
        this.error = "Impossible de charger les litiges. Vérifiez que le serveur est accessible.";
      } finally {
        this.loading = false;
      }
    },

    ouvrirFormulaire() { 
      this.formVisible = true; 
    },
    
    fermerFormulaire() { 
      this.formVisible = false; 
    },

    toggleDetails(litige) { 
      this.litigeDetails = litige; 
    },

    handleNouveauLitige(nouveauLitige) {
      this.litiges.push(nouveauLitige);
      this.formVisible = false;
      this.statutActif = 'en_attente';
    },

    getStatusColor(statut) {
      switch(statut) {
        case 'en_attente': return 'bg-yellow-100 text-yellow-700';
        case 'en_cours': return 'bg-blue-100 text-blue-700';
        case 'resolu': return 'bg-green-100 text-green-700';
        case 'rejete': return 'bg-red-100 text-red-700';
        default: return 'bg-gray-100 text-gray-700';
      }
    },

    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    }
  }
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.group:hover {
  transform: translateY(-2px);
  transition: transform 0.2s;
}
</style>