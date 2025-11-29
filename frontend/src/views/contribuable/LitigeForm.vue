<template>
  <div class="fixed inset-0 bg-black/50 flex justify-center items-center z-50" @click.self="$emit('fermer')">
    <div class="bg-white p-6 rounded-lg w-11/12 md:w-1/2 relative">
      <button @click="$emit('fermer')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">
        ✖
      </button>
      
      <h2 class="text-xl font-bold mb-4">Déposer un litige</h2>
      
      <!-- Message d'erreur -->
      <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ error }}
      </div>
      
      <div class="space-y-4">
        <div>
          <label class="block font-semibold mb-1">Sujet : <span class="text-red-500">*</span></label>
          <input 
            type="text" 
            v-model="form.sujet" 
            class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="Objet du litige"
            :disabled="loading"
          />
        </div>
        
        <div>
          <label class="block font-semibold mb-1">Description :</label>
          <textarea 
            v-model="form.description" 
            class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            rows="4"
            placeholder="Décrivez votre litige en détail..."
            :disabled="loading"
          ></textarea>
        </div>
        
        <button 
          @click="envoyerLitige" 
          class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed"
          :disabled="loading"
        >
          {{ loading ? 'Envoi en cours...' : 'Envoyer' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

// Configuration de l'URL de base de l'API
const API_BASE_URL = "http://127.0.0.1:8000/api"; // Utilisera le proxy
// OU si le backend CORS est configuré :
// const API_BASE_URL = "http://127.0.0.1:8000/api";

export default {
  props: ['idContribuable'],
  
  data() {
    return {
      form: {
        sujet: '',
        description: '',
      },
      loading: false,
      error: null
    }
  },
  
  methods: {
    async envoyerLitige() {
      // Validation
      if (!this.form.sujet || this.form.sujet.trim() === '') {
        this.error = "Le sujet est obligatoire.";
        return;
      }
      
      this.loading = true;
      this.error = null;
      
      try {
        const payload = {
          id_Contribuable: Number(this.idContribuable),
          sujet: this.form.sujet.trim(),
          description: this.form.description.trim(),
          statut: 'en_attente'
        };
        
        const res = await axios.post(`${API_BASE_URL}/litiges`, payload);
        
        // Émettre l'événement avec le nouveau litige
        this.$emit('litige-envoye', res.data);
        
        // Réinitialiser le formulaire
        this.form = { sujet: '', description: '' };
        
      } catch (err) {
        console.error('Erreur création litige:', err);
        
        if (err.response) {
          // Le serveur a répondu avec un code d'erreur
          this.error = `Erreur serveur: ${err.response.data?.message || err.response.statusText}`;
        } else if (err.request) {
          // La requête a été faite mais pas de réponse
          this.error = "Impossible de contacter le serveur. Vérifiez votre connexion.";
        } else {
          // Autre erreur
          this.error = "Une erreur est survenue lors de l'envoi du litige.";
        }
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>
