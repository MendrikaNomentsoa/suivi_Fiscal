<template>
  <div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-7xl mx-auto">

      <!-- HEADER -->
      <div class="mb-8">
        <button 
          @click="retourDashboard"
          class="flex items-center text-gray-600 hover:text-gray-900 transition"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Retour
        </button>

        <h1 class="text-3xl font-semibold text-gray-900 mt-4">Mes Types d’Impôts</h1>
        <p class="text-gray-600">Accédez à vos déclarations, paiements et simulations.</p>
      </div>

      <!-- LOADING -->
      <div v-if="loading" class="text-center py-20">
        <div class="w-12 h-12 mx-auto mb-4 border-4 border-gray-300 border-t-indigo-600 rounded-full animate-spin"></div>
        <p class="text-gray-500">Chargement...</p>
      </div>

      <div v-else>

        <!-- AUCUN IMPOT -->
        <div v-if="typesImpots.length === 0"
          class="bg-white p-10 rounded-xl shadow text-center border border-gray-200"
        >
          <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7V5a2 2 0 012-2h5l5 5v14a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <h2 class="text-lg font-semibold mt-4">Aucun type d’impôt</h2>
          <p class="text-gray-500 text-sm">Aucune obligation fiscale pour le moment.</p>
        </div>

        <!-- STATISTIQUES -->
        <div v-else class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm mb-8">
          <h3 class="text-lg font-semibold text-gray-900 mb-5">Vue d’ensemble</h3>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            <div class="p-4 bg-gray-100 rounded-lg text-center">
              <p class="text-3xl font-bold text-gray-700">{{ typesImpots.length }}</p>
              <p class="text-sm text-gray-600">Types d’impôts</p>
            </div>

            <div class="p-4 bg-gray-100 rounded-lg text-center">
              <p class="text-3xl font-bold text-gray-700">{{ totalDeclarations }}</p>
              <p class="text-sm text-gray-600">Déclarations</p>
            </div>

            <div class="p-4 bg-gray-100 rounded-lg text-center">
              <p class="text-3xl font-bold text-orange-600">{{ totalNonPayes }}</p>
              <p class="text-sm text-gray-600">Non payées</p>
            </div>

            <div class="p-4 bg-gray-100 rounded-lg text-center">
              <p class="text-3xl font-bold text-green-600">{{ totalPayes }}</p>
              <p class="text-sm text-gray-600">Payées</p>
            </div>

          </div>
        </div>

        <!-- LISTE DES TYPES D’IMPOTS -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

          <div v-for="typeImpot in typesImpots"
               :key="typeImpot.id_type_impot"
               class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition p-6"
          >
            
            <!-- HEADER TYPE IMPOT -->
            <div class="mb-4">
              <div class="flex justify-between items-start">

                <div>
                  <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-3">
                    <span class="text-3xl">{{ getImpotIcon(typeImpot.nom) }}</span>
                    {{ typeImpot.nom }}
                  </h3>
                  <p class="text-gray-500 text-sm mt-1">
                    {{ getImpotDescription(typeImpot.nom) }}
                  </p>
                </div>

                <button
                  @click="declarer(typeImpot.id_type_impot)"
                  class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm shadow"
                >
                  Déclarer
                </button>

              </div>

              <div class="flex gap-3 mt-4 text-sm">
                <span class="px-3 py-1 bg-gray-100 rounded font-medium">Taux : {{ typeImpot.taux }}%</span>
                <span class="px-3 py-1 bg-gray-100 rounded font-medium">
                  {{ getDeclarationsCount(typeImpot.id_type_impot) }} déclaration(s)
                </span>
              </div>
            </div>

            <!-- LISTE DES DÉCLARATIONS -->
            <div class="space-y-4">

              <div
                v-if="getDeclarationsByType(typeImpot.id_type_impot).length === 0"
                class="text-center py-6 text-gray-500 text-sm"
              >
                Aucune déclaration enregistrée
              </div>

              <div v-for="declaration in getDeclarationsByType(typeImpot.id_type_impot)"
                   :key="declaration.id_declaration"
                   class="p-4 rounded-lg border text-sm flex justify-between items-start"
                   :class="{
                     'bg-green-50 border-green-200': declaration.statut_paiement === 'paye',
                     'bg-orange-50 border-orange-200': declaration.statut_paiement === 'non_paye'
                   }"
              >

                <div>
                  <p class="font-semibold text-gray-900">{{ formatMontant(declaration.montant) }}</p>
                  <p class="text-gray-600">Déclaré : {{ formatDate(declaration.date_declaration) }}</p>
                  <p v-if="declaration.date_echeance" class="text-gray-600">
                    Échéance : {{ formatDate(declaration.date_echeance) }}
                  </p>
                </div>

                <div class="flex flex-col gap-2">

                  <button
                    @click="voirDetails(declaration.id_declaration)"
                    class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm"
                  >
                    Détails
                  </button>

                  <button
                    @click="simuler(declaration.id_declaration)"
                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm"
                  >
                    Simuler
                  </button>

                </div>
              </div>

              <button
                v-if="getDeclarationsByType(typeImpot.id_type_impot).length > 0"
                @click="voirHistorique(typeImpot.id_type_impot)"
                class="w-full mt-4 px-6 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm text-gray-700"
              >
                Voir l’historique
              </button>

            </div>

          </div>

        </div>

      </div>

    </div>
  </div>
</template>







<script>
import axios from "axios";
import dayjs from "dayjs";

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
      typesImpots: [],
      declarations: [],
      loading: true
    };
  },

  computed: {
    totalDeclarations() {
      if (!Array.isArray(this.declarations)) return 0;
      return this.declarations.length;
    },

    totalNonPayes() {
      if (!Array.isArray(this.declarations)) return 0;
      return this.declarations.filter(d => d.statut_paiement === 'non_paye').length;
    },

    totalPayes() {
      if (!Array.isArray(this.declarations)) return 0;
      return this.declarations.filter(d => d.statut_paiement === 'paye').length;
    }
  },

  mounted() {
    this.loadData();
  },

  methods: {
    async loadData() {
      this.loading = true;

      try {
        // Charger les types d'impôts
        const resTypes = await axios.get(
          `http://127.0.0.1:8000/api/types-impots`
        );

        if (Array.isArray(resTypes.data)) {
          this.typesImpots = resTypes.data;
        } else if (resTypes.data.data && Array.isArray(resTypes.data.data)) {
          this.typesImpots = resTypes.data.data;
        } else {
          this.typesImpots = [];
        }

        // Charger toutes les déclarations du contribuable
        const resDeclarations = await axios.get(`http://127.0.0.1:8000/api/contribuables/${this.idContribuable}/impots`

        );

        if (Array.isArray(resDeclarations.data)) {
          this.declarations = resDeclarations.data;
        } else if (resDeclarations.data.data && Array.isArray(resDeclarations.data.data)) {
          this.declarations = resDeclarations.data.data;
        } else if (resDeclarations.data.declarations && Array.isArray(resDeclarations.data.declarations)) {
          this.declarations = resDeclarations.data.declarations;
        } else {
          this.declarations = [];
        }

        console.log('Types d\'impôts:', this.typesImpots);
        console.log('Déclarations:', this.declarations);
      } 
      catch (error) {
        console.error('Erreur chargement données:', error);
        this.typesImpots = [];
        this.declarations = [];
        alert('Erreur lors du chargement. Réessayez.');
      } 
      finally {
        this.loading = false;
      }
    },

    getDeclarationsByType(idTypeImpot) {
      if (!Array.isArray(this.declarations)) return [];
      
      return this.declarations.filter(d => {
        // Gérer différentes structures
        if (d.id_type_impot) {
          return d.id_type_impot === idTypeImpot;
        } else if (d.type_impot && d.type_impot.id_type_impot) {
          return d.type_impot.id_type_impot === idTypeImpot;
        }
        return false;
      });
    },

    getDeclarationsCount(idTypeImpot) {
      return this.getDeclarationsByType(idTypeImpot).length;
    },

    voirHistorique(idTypeImpot) {
      this.$router.push(`/declarations/${this.idContribuable}/${idTypeImpot}`);
    },

    declarer(idTypeImpot) {
      this.$router.push(`/declarations/nouvelle/${this.idContribuable}/${idTypeImpot}`);
    },

    voirDetails(idDeclaration) {
      this.$router.push(`/declaration/details/${idDeclaration}`);
    },

    async payerDeclaration(idDeclaration) {
      try {
        await axios.post(
          `http://127.0.0.1:8000/api/declarations/${this.idContribuable}/payer/${idDeclaration}`
        );
        alert('Paiement effectué avec succès !');
        this.loadData(); // Recharger les données
      } catch (error) {
        console.error('Erreur paiement:', error);
        alert('Erreur lors du paiement. Réessayez.');
      }
    },

    retourDashboard() {
      this.$router.push('/contribuable/dashboard');
    },

    formatDate(date) {
      if (!date) return 'N/A';
      return dayjs(date).format("DD/MM/YYYY");
    },

    formatMontant(montant) {
      if (!montant) return '0 Ar';
      return new Intl.NumberFormat('fr-MG').format(montant) + ' Ar';
    },

    getImpotIcon(nom) {
      const icons = {
        IRSA: "",
        IS: "",
      };
      return icons[nom] || "";
    },

    getImpotDescription(nom) {
      const descriptions = {
        IRSA: "Impôt sur les Revenus Salariaux et Assimilés",
        IS: "Impôt sur les Sociétés",
      };
      return descriptions[nom] || "Déclaration fiscale obligatoire";
    }
  }
};
</script>

<style scoped>
/* Animation au scroll */
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>