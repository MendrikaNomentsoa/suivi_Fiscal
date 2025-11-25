<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8 px-4">
    <div class="max-w-5xl mx-auto">
      
      <!-- En-tête -->
      <div class="mb-8">
        <button 
          @click="retourDashboard"
          class="flex items-center text-indigo-600 hover:text-indigo-700 mb-4 font-medium transition-colors"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Retour au tableau de bord
        </button>
        
        <div class="text-center">
          <h1 class="text-4xl font-bold text-slate-900 mb-3">
            📋 Mes Impôts et Déclarations
          </h1>
          <p class="text-slate-600 text-lg">
            Gérez vos obligations fiscales en toute simplicité
          </p>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-16">
        <svg class="animate-spin h-12 w-12 text-indigo-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-slate-600">Chargement de vos impôts...</p>
      </div>

      <!-- Contenu principal -->
      <div v-else>
        <!-- Message si aucun impôt -->
        <div v-if="impots.length === 0" class="bg-white rounded-2xl p-12 text-center shadow-sm border border-slate-200">
          <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-slate-900 mb-2">
            Aucun impôt à déclarer
          </h3>
          <p class="text-slate-600">
            Vous n'avez actuellement aucune obligation fiscale enregistrée.
          </p>
        </div>

        <!-- Liste des impôts -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <div 
            v-for="impot in impots" 
            :key="impot.id_type_impot"
            class="bg-white/80 backdrop-blur-lg rounded-2xl border border-slate-200/50 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group"
          >
            <!-- En-tête de la carte -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white">
              <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                  <h3 class="text-2xl font-bold mb-1">{{ impot.nom }}</h3>
                  <p class="text-indigo-100 text-sm">
                    {{ getImpotDescription(impot.nom) }}
                  </p>
                </div>
                <div class="flex-shrink-0 ml-4">
                  <span class="text-4xl">{{ getImpotIcon(impot.nom) }}</span>
                </div>
              </div>
              
              <!-- Taux -->
              <div class="flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span class="text-indigo-100">Taux: <strong>{{ impot.taux }}%</strong></span>
              </div>
            </div>

            <!-- Corps de la carte -->
            <div class="p-6">
              <!-- Statut -->
              <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm font-medium text-slate-600">Statut</span>
                  <span 
                    :class="[
                      'px-3 py-1 rounded-full text-sm font-semibold inline-flex items-center',
                      impot.fait ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'
                    ]"
                  >
                    <span class="mr-1">{{ impot.fait ? '✅' : '⏳' }}</span>
                    {{ impot.fait ? 'Déclaré' : 'À déclarer' }}
                  </span>
                </div>
                
                <!-- Barre de progression -->
                <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                  <div 
                    :class="[
                      'h-2 transition-all duration-500',
                      impot.fait ? 'bg-green-500 w-full' : 'bg-orange-400 w-1/3'
                    ]"
                  ></div>
                </div>
              </div>

              <!-- Actions -->
              <div class="space-y-3">
                <!-- Bouton Voir les déclarations -->
                <button
                  @click="voirDeclarations(impot.id_type_impot)"
                  class="w-full px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-all flex items-center justify-center space-x-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <span>Voir l'historique</span>
                </button>

                <!-- Bouton Déclarer -->
                <button
                  @click="declarer(impot.id_type_impot)"
                  :class="[
                    'w-full px-4 py-3 font-semibold rounded-xl transition-all flex items-center justify-center space-x-2',
                    impot.fait 
                      ? 'bg-indigo-100 hover:bg-indigo-200 text-indigo-700' 
                      : 'bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white shadow-lg hover:shadow-xl'
                  ]"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  <span>{{ impot.fait ? 'Nouvelle déclaration' : 'Déclarer maintenant' }}</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions supplémentaires -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Déposer un litige -->
          <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-6 border border-slate-200/50 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start space-x-4">
              <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-slate-900 mb-1">
                  Déposer un litige
                </h3>
                <p class="text-sm text-slate-600 mb-3">
                  Contestez une décision ou signalez un problème fiscal
                </p>
                <button
                  @click="deposerLitige"
                  class="text-amber-600 hover:text-amber-700 font-medium text-sm flex items-center"
                >
                  <span>Ouvrir un litige</span>
                  <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Simuler un montant -->
          <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-6 border border-slate-200/50 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start space-x-4">
              <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-slate-900 mb-1">
                  Simuler un montant
                </h3>
                <p class="text-sm text-slate-600 mb-3">
                  Estimez le montant de vos impôts avant de déclarer
                </p>
                <button
                  @click="simulerMontant"
                  class="text-blue-600 hover:text-blue-700 font-medium text-sm flex items-center"
                >
                  <span>Lancer une simulation</span>
                  <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="mt-8 bg-white/80 backdrop-blur-lg rounded-2xl p-6 border border-slate-200/50 shadow-sm">
          <h3 class="text-lg font-semibold text-slate-900 mb-4">📊 Récapitulatif</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center">
              <p class="text-3xl font-bold text-indigo-600">{{ impots.length }}</p>
              <p class="text-sm text-slate-600">Types d'impôts</p>
            </div>
            <div class="text-center">
              <p class="text-3xl font-bold text-green-600">{{ impotsDejaFaits }}</p>
              <p class="text-sm text-slate-600">Déjà déclarés</p>
            </div>
            <div class="text-center">
              <p class="text-3xl font-bold text-orange-600">{{ impotsAFaire }}</p>
              <p class="text-sm text-slate-600">À déclarer</p>
            </div>
            <div class="text-center">
              <p class="text-3xl font-bold text-purple-600">{{ tauxCompletion }}%</p>
              <p class="text-sm text-slate-600">Complétion</p>
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
  name: 'ImpotsList',
  
  props: {
    idContribuable: {
      type: [Number, String],
      required: true
    }
  },
  
  data() {
    return {
      impots: [],
      loading: true
    };
  },

  computed: {
    impotsDejaFaits() {
      return this.impots.filter(i => i.fait).length;
    },
    impotsAFaire() {
      return this.impots.filter(i => !i.fait).length;
    },
    tauxCompletion() {
      if (this.impots.length === 0) return 0;
      return Math.round((this.impotsDejaFaits / this.impots.length) * 100);
    }
  },

  mounted() {
    console.log('📍 ImpotsList mounted - idContribuable:', this.idContribuable);
    this.loadImpots();
  },

  methods: {
    async loadImpots() {
      this.loading = true;
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/impots/${this.idContribuable}`);
        this.impots = res.data;
        console.log('✅ Impôts chargés:', this.impots);
      } catch (error) {
        console.error('❌ Erreur lors du chargement des impôts:', error);
        alert('Erreur lors du chargement des impôts. Veuillez réessayer.');
      } finally {
        this.loading = false;
      }
    },

    // Navigation vers l'historique des déclarations
    voirDeclarations(idTypeImpot) {
      console.log('📂 Navigation vers déclarations:', idTypeImpot);
      this.$router.push(`/declarations/${this.idContribuable}/${idTypeImpot}`);
    },

    // Navigation vers le formulaire de déclaration
    declarer(idTypeImpot) {
      console.log('📝 Navigation vers nouvelle déclaration:', idTypeImpot);
      this.$router.push(`/declarations/nouvelle/${this.idContribuable}/${idTypeImpot}`);
    },

    // Navigation vers le formulaire de litige
    deposerLitige() {
      console.log('⚠️ Navigation vers dépôt de litige');
      this.$router.push(`/litige/${this.idContribuable}`);
    },

    // Navigation vers la simulation
    simulerMontant() {
      console.log('🧮 Navigation vers simulation');
      this.$router.push(`/simulation/${this.idContribuable}`);
    },

    // Retour au dashboard
    retourDashboard() {
      this.$router.push('/contribuable/dashboard');
    },

    // Helpers pour l'affichage
    getImpotIcon(nom) {
      const icons = {
        'IRSA': '💼',
        'IS': '🏢',
        'TVA': '🧾',
        'Impôt sur les Revenus Salariaux': '💼',
        'Impôt sur les Sociétés': '🏢',
        'Taxe sur la Valeur Ajoutée': '🧾'
      };
      return icons[nom] || '📄';
    },

    getImpotDescription(nom) {
      const descriptions = {
        'IRSA': 'Impôt sur les revenus salariaux et traitements',
        'IS': 'Impôt sur les bénéfices des sociétés',
        'TVA': 'Taxe sur la valeur ajoutée',
        'Impôt sur les Revenus Salariaux': 'Déclaration mensuelle des salaires',
        'Impôt sur les Sociétés': 'Déclaration annuelle des bénéfices'
      };
      return descriptions[nom] || 'Déclaration fiscale obligatoire';
    }
  }
};
</script>

<style scoped>
/* Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.group:hover {
  transform: translateY(-2px);
}
</style>