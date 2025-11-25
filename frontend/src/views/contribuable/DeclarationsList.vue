<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8 px-4">
    <div class="max-w-6xl mx-auto">
      
      <!-- En-tête -->
      <div class="mb-8">
        <button 
          @click="$router.push(`/impots/${idContribuable}`)"
          class="flex items-center text-indigo-600 hover:text-indigo-700 mb-4 font-medium transition-colors"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Retour à la liste des impôts
        </button>
        
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-slate-900 mb-2">
              {{ typeImpot?.nom || 'Déclarations' }}
            </h1>
            <p class="text-slate-600">
              Historique de vos déclarations pour cet impôt
            </p>
          </div>
          
          <!-- Badge du type d'impôt -->
          <div v-if="typeImpot" class="hidden md:block">
            <div class="bg-white rounded-lg px-6 py-3 shadow-sm border border-slate-200">
              <p class="text-xs text-slate-600 mb-1">Taux applicable</p>
              <p class="text-2xl font-bold text-indigo-600">{{ typeImpot.taux }}%</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-16">
        <svg class="animate-spin h-12 w-12 text-indigo-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-slate-600">Chargement des déclarations...</p>
      </div>

      <!-- Contenu principal -->
      <div v-else>
        <!-- Carte des déclarations -->
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl border border-slate-200/50 shadow-sm overflow-hidden mb-6">
          
          <!-- Déclarations existantes -->
          <div v-if="declarations.length > 0">
            <!-- En-tête du tableau -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
              <h2 class="text-lg font-semibold text-slate-900">
                📋 Déclarations effectuées ({{ declarations.length }})
              </h2>
            </div>
            
            <!-- Tableau -->
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">N°</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Montant</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Date de déclaration</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Statut</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr 
                    v-for="(dec, index) in declarations" 
                    :key="dec.id_declaration"
                    class="border-b border-slate-100 hover:bg-slate-50 transition-colors"
                  >
                    <!-- Numéro -->
                    <td class="px-6 py-4">
                      <span class="text-slate-600 font-medium">#{{ dec.id_declaration }}</span>
                    </td>
                    
                    <!-- Montant -->
                    <td class="px-6 py-4">
                      <span class="text-lg font-bold text-slate-900">
                        {{ formatMontant(dec.montant) }}
                      </span>
                    </td>
                    
                    <!-- Date -->
                    <td class="px-6 py-4">
                      <div class="flex items-center text-slate-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ formatDate(dec.date_declaration) }}
                      </div>
                    </td>
                    
                    <!-- Statut -->
                    <td class="px-6 py-4">
                      <span 
                        :class="[
                          'px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center',
                          getStatutStyle(dec.statut)
                        ]"
                      >
                        <span class="mr-1">{{ getStatutIcon(dec.statut) }}</span>
                        {{ getStatutLabel(dec.statut) }}
                      </span>
                    </td>
                    
                    <!-- Actions -->
                    <td class="px-6 py-4">
                      <button
                        @click="voirDetails(dec)"
                        class="text-indigo-600 hover:text-indigo-700 font-medium text-sm flex items-center"
                      >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Détails
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Aucune déclaration -->
          <div v-else class="text-center py-16">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-900 mb-2">
              Aucune déclaration trouvée
            </h3>
            <p class="text-slate-600 mb-6">
              Vous n'avez pas encore effectué de déclaration pour cet impôt.
            </p>
          </div>
        </div>

        <!-- Section d'action -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-8 text-white mb-6 shadow-lg">
          <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
              <h3 class="text-xl font-bold mb-2">✨ Faire une nouvelle déclaration</h3>
              <p class="text-indigo-100">
                Déclarez facilement votre impôt en quelques clics
              </p>
            </div>
            <button 
              @click="nouvelleDeclaration"
              class="bg-white text-indigo-600 px-6 py-3 rounded-xl font-semibold hover:bg-indigo-50 transition-all hover:scale-105 flex items-center space-x-2 shadow-lg whitespace-nowrap"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span>Nouvelle déclaration</span>
            </button>
          </div>
        </div>

        <!-- Informations complémentaires -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Total des déclarations -->
          <div class="bg-white/80 backdrop-blur-lg rounded-xl p-5 border border-slate-200/50 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center space-x-3">
              <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <p class="text-xs text-slate-600 uppercase tracking-wide">Total déclarations</p>
                <p class="text-2xl font-bold text-slate-900">{{ declarations.length }}</p>
              </div>
            </div>
          </div>

          <!-- Montant total -->
          <div class="bg-white/80 backdrop-blur-lg rounded-xl p-5 border border-slate-200/50 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center space-x-3">
              <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <p class="text-xs text-slate-600 uppercase tracking-wide">Montant total</p>
                <p class="text-2xl font-bold text-slate-900">
                  {{ formatMontant(montantTotal) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Taux d'impôt -->
          <div v-if="typeImpot" class="bg-white/80 backdrop-blur-lg rounded-xl p-5 border border-slate-200/50 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center space-x-3">
              <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <p class="text-xs text-slate-600 uppercase tracking-wide">Taux applicable</p>
                <p class="text-2xl font-bold text-slate-900">{{ typeImpot.taux }}%</p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Modal de détails (optionnel) -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click="closeModal">
      <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl" @click.stop>
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-2xl font-bold text-slate-900">Détails de la déclaration</h3>
          <button @click="closeModal" class="text-slate-400 hover:text-slate-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div v-if="selectedDeclaration" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-slate-600">Numéro</p>
              <p class="text-lg font-semibold text-slate-900">#{{ selectedDeclaration.id_declaration }}</p>
            </div>
            <div>
              <p class="text-sm text-slate-600">Statut</p>
              <span :class="['px-3 py-1 rounded-full text-xs font-semibold inline-block', getStatutStyle(selectedDeclaration.statut)]">
                {{ getStatutLabel(selectedDeclaration.statut) }}
              </span>
            </div>
            <div>
              <p class="text-sm text-slate-600">Date de déclaration</p>
              <p class="text-lg font-semibold text-slate-900">{{ formatDate(selectedDeclaration.date_declaration) }}</p>
            </div>
            <div>
              <p class="text-sm text-slate-600">Montant</p>
              <p class="text-lg font-semibold text-slate-900">{{ formatMontant(selectedDeclaration.montant) }}</p>
            </div>
          </div>
          
          <!-- Données du formulaire -->
          <div v-if="selectedDeclaration.data" class="mt-6">
            <h4 class="font-semibold text-slate-900 mb-3">Données déclarées</h4>
            <div class="bg-slate-50 rounded-lg p-4 space-y-2">
              <div v-for="(value, key) in selectedDeclaration.data" :key="key" class="flex justify-between">
                <span class="text-slate-600">Champ {{ key }}</span>
                <span class="font-semibold text-slate-900">{{ formatMontant(value) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: 'DeclarationsExistantes',
  props: {
    idContribuable: {
      type: [String, Number],
      required: true
    },
    idTypeImpot: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      declarations: [],
      typeImpot: null,
      loading: true,
      showModal: false,
      selectedDeclaration: null
    };
  },
  computed: {
    montantTotal() {
      return this.declarations.reduce((sum, d) => sum + (d.montant || 0), 0);
    }
  },
  mounted() {
    console.log('DeclarationsExistantes mounted:', {
      idContribuable: this.idContribuable,
      idTypeImpot: this.idTypeImpot
    });
    this.loadData();
  },
  watch: {
    idTypeImpot() {
      this.loadData();
    }
  },
  methods: {
    async loadData() {
      this.loading = true;
      try {
        // Charger les déclarations
        const declRes = await axios.get("http://127.0.0.1:8000/api/declarations");
        
        // Filtrer avec conversion en Number
        this.declarations = declRes.data.filter(
          d => Number(d.id_contribuable) === Number(this.idContribuable) && 
               Number(d.id_type_impot) === Number(this.idTypeImpot)
        );
        
        // Trier par date décroissante
        this.declarations.sort((a, b) => new Date(b.date_declaration) - new Date(a.date_declaration));
        
        console.log('Déclarations filtrées:', this.declarations);
        
        // Charger les informations du type d'impôt
        const typeRes = await axios.get(`http://127.0.0.1:8000/api/types-impots/${this.idTypeImpot}`);
        this.typeImpot = typeRes.data;
        
        console.log('Type d\'impôt chargé:', this.typeImpot);
        
      } catch (err) {
        console.error('Erreur lors du chargement:', err);
        alert('Erreur lors du chargement des données');
      } finally {
        this.loading = false;
      }
    },
    
    nouvelleDeclaration() {
      console.log('Navigation vers nouvelle déclaration:', {
        idContribuable: this.idContribuable,
        idTypeImpot: this.idTypeImpot
      });
      this.$router.push(`/declarations/nouvelle/${this.idContribuable}/${this.idTypeImpot}`);
    },
    
    voirDetails(declaration) {
      this.selectedDeclaration = declaration;
      this.showModal = true;
    },
    
    closeModal() {
      this.showModal = false;
      this.selectedDeclaration = null;
    },
    
    getStatutLabel(statut) {
      const labels = {
        'validee': 'Validée',
        'valider': 'À valider',
        'en_attente': 'En attente',
        'brouillon': 'Brouillon'
      };
      return labels[statut] || statut;
    },
    
    getStatutIcon(statut) {
      const icons = {
        'validee': '✅',
        'valider': '⏳',
        'en_attente': '⏸️',
        'brouillon': '📝'
      };
      return icons[statut] || '📄';
    },
    
    getStatutStyle(statut) {
      const styles = {
        'validee': 'bg-green-100 text-green-700',
        'valider': 'bg-blue-100 text-blue-700',
        'en_attente': 'bg-yellow-100 text-yellow-700',
        'brouillon': 'bg-gray-100 text-gray-700'
      };
      return styles[statut] || 'bg-gray-100 text-gray-700';
    },
    
    formatMontant(montant) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'decimal',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
      }).format(montant || 0) + ' Ar';
    },
    
    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    }
  }
};
</script>

<style scoped>
/* Animations personnalisées */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.hover\:scale-105:hover {
  transform: scale(1.05);
}

/* Styles pour le modal */
.fixed {
  animation: fadeIn 0.2s ease-out;
}
</style>