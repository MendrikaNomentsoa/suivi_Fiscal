<template>
  <div class="min-h-screen bg-gray-50 py-10 px-4">
    <div class="max-w-6xl mx-auto">

      <!-- BACK BUTTON -->
      <button 
        @click="$router.push(`/impots/${idContribuable}`)"
        class="flex items-center text-gray-600 hover:text-gray-900 mb-6 transition-colors duration-200"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7m-7 7h18" />
        </svg>
        Retour
      </button>

      <!-- HEADER -->
      <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
        <div>
          <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 flex items-center gap-3">
            <span class="w-1.5 h-10 bg-indigo-600 rounded-full"></span>
            {{ typeImpot?.nom || "Déclarations" }}
          </h1>
          <p class="text-gray-500 mt-1 text-sm md:text-base">Historique complet de vos déclarations</p>
        </div>

        <!-- INFO CARDS -->
        <div v-if="typeImpot" class="flex gap-3 md:gap-4 mt-4 md:mt-0">
          <div class="px-4 py-3 bg-white shadow-sm border rounded-lg text-center">
            <p class="text-xs text-gray-500 uppercase mb-1">Taux d'impôt</p>
            <p class="text-2xl font-semibold text-indigo-600">{{ typeImpot.taux }}%</p>
          </div>
          <div class="px-4 py-3 bg-white shadow-sm border rounded-lg text-center">
            <p class="text-xs text-gray-500 uppercase mb-1">Déclarations</p>
            <p class="text-2xl font-semibold text-gray-800">{{ declarations.length }}</p>
          </div>
        </div>
      </div>

      <!-- LOADER -->
      <div v-if="loading" class="py-20 text-center">
        <svg class="w-10 h-10 animate-spin text-indigo-600 mx-auto" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-30" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-70" fill="currentColor" d="M4 12a8 8 0 018-8V0A12 12 0 002 12h2z" />
        </svg>
        <p class="mt-4 text-gray-600">Chargement des déclarations…</p>
      </div>

      <!-- CONTENT -->
      <div v-else>

        <!-- STATS CARDS -->
        <div v-if="declarations.length" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
          <div class="p-5 bg-white rounded-lg shadow-sm text-center">
            <p class="text-sm text-gray-500">Validées</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ getStatutCount('validee') }}</p>
          </div>
          <div class="p-5 bg-white rounded-lg shadow-sm text-center">
            <p class="text-sm text-gray-500">À valider</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ getStatutCount('valide') }}</p>
          </div>
          <div class="p-5 bg-white rounded-lg shadow-sm text-center">
            <p class="text-sm text-gray-500">En attente</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">{{ getStatutCount('en_attente') }}</p>
          </div>
        </div>

        <!-- MAIN CARD -->
        <div class="bg-white shadow-lg border rounded-2xl overflow-hidden">

          <!-- LISTE -->
          <div v-if="declarations.length">
            <div class="px-6 py-4 border-b bg-gray-50">
              <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.6a1 1 0 01.7.3l5.4 5.4a1 1 0 01.3.7V19a2 2 0 01-2 2z" />
                </svg>
                Historique
              </h2>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full text-left divide-y">
                <thead>
                  <tr class="text-xs text-gray-500 uppercase bg-gray-50">
                    <th class="px-6 py-3">Déclaration</th>
                    <th class="px-6 py-3">Montant</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3">Statut</th>
                    <th class="px-6 py-3">Paiement</th>
                    <th class="px-6 py-3 text-center">Action</th>
                  </tr>
                </thead>
                <tbody class="divide-y">
                  <tr v-for="dec in declarations" :key="dec.id_declaration" class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-700">#{{ dec.id_declaration }}</td>
                    <td class="px-6 py-4 text-indigo-600 font-semibold">{{ formatMontant(dec.montant) }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ formatDateCourt(dec.date_declaration) }}</td>
                    <td class="px-6 py-4">
                      <span :class="['px-3 py-1 rounded-full text-xs font-semibold border inline-flex items-center gap-1', getStatutStyle(dec.statut)]">
                        {{ getStatutIcon(dec.statut) }} {{ getStatutLabel(dec.statut) }}
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <span v-if="dec.statut === 'validee'" 
                            :class="['px-3 py-1 rounded-full text-xs font-semibold border inline-flex items-center gap-1', getPaiementStyle(dec)]">
                        {{ getPaiementLabel(dec) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                      <button @click="voirDetails(dec)" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm transition">
                        Détails
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- EMPTY STATE -->
          <div v-else class="text-center py-20">
            <div class="text-6xl mb-4">📄</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Aucune déclaration</h3>
            <p class="text-gray-500 mb-6">Vous n'avez pas encore de déclaration pour cet impôt.</p>
            <button 
              @click="$router.push(`/impots/${idContribuable}`)"
              class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition"
            >
              + Créer une déclaration
            </button>
          </div>

        </div>
      </div>
    </div>

    <!-- MODAL -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50"
      @click="closeModal"
    >
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full animate-slideUp" @click.stop>
        <!-- MODAL HEADER -->
        <div class="px-6 py-4 bg-indigo-600 text-white flex justify-between items-center rounded-t-2xl">
          <h3 class="text-lg font-bold">Détails de la déclaration</h3>
          <button @click="closeModal" class="hover:bg-white/20 p-2 rounded-lg">✖</button>
        </div>

        <!-- MODAL BODY -->
        <div v-if="selectedDeclaration" class="p-6 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg border">
              <p class="text-xs text-gray-500 uppercase">Numéro</p>
              <p class="text-lg font-bold">#{{ selectedDeclaration.id_declaration }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg border">
              <p class="text-xs text-gray-500 uppercase">Montant</p>
              <p class="text-lg font-bold text-indigo-600">{{ formatMontant(selectedDeclaration.montant) }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg border">
              <p class="text-xs text-gray-500 uppercase">Date</p>
              <p class="text-lg font-semibold">{{ formatDate(selectedDeclaration.date_declaration) }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg border">
              <p class="text-xs text-gray-500 uppercase">Statut</p>
              <span :class="['px-3 py-1 text-sm rounded-full border font-semibold inline-flex items-center gap-1', getStatutStyle(selectedDeclaration.statut)]">
                {{ getStatutIcon(selectedDeclaration.statut) }}
                {{ getStatutLabel(selectedDeclaration.statut) }}
              </span>
            </div>
            <div v-if="selectedDeclaration.statut === 'validee'" class="p-4 bg-gray-50 rounded-lg border">
              <p class="text-xs text-gray-500 uppercase">Paiement</p>
              <span :class="['px-3 py-1 text-sm rounded-full border font-semibold inline-flex items-center gap-1', getPaiementStyle(selectedDeclaration)]">
                {{ getPaiementLabel(selectedDeclaration) }}
              </span>
            </div>
          </div>

          <div v-if="selectedDeclaration.data" class="space-y-4">
            <h4 class="text-md font-bold flex items-center gap-2">
              <span class="w-1.5 h-6 bg-indigo-600 rounded"></span>
              Données déclarées
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div
                v-for="(val, key) in selectedDeclaration.data"
                :key="key"
                class="p-4 bg-white border rounded-lg shadow-sm hover:shadow-md transition"
              >
                <p class="text-xs uppercase text-gray-500">{{ formatKey(key) }}</p>
                <p class="text-lg font-bold">{{ formatMontant(val) }}</p>
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
    idContribuable: { type: [String, Number], required: true },
    idTypeImpot: { type: [String, Number], required: true }
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
  mounted() {
    this.loadData();
  },
  methods: {
    async loadData() {
      this.loading = true;
      try {
        const [declarationsRes, typeRes] = await Promise.all([
          axios.get("http://127.0.0.1:8000/api/declarations"),
          axios.get(`http://127.0.0.1:8000/api/types-impots/${this.idTypeImpot}`)
        ]);

        this.declarations = declarationsRes.data
          .filter(d => 
            Number(d.id_contribuable) === Number(this.idContribuable) &&
            Number(d.id_type_impot) === Number(this.idTypeImpot)
          )
          .sort((a, b) => new Date(b.date_declaration) - new Date(a.date_declaration));

        this.typeImpot = typeRes.data;
      } catch (err) {
        console.error('Erreur de chargement:', err);
        alert('Erreur lors du chargement des déclarations');
      } finally {
        this.loading = false;
      }
    },

    voirDetails(declaration) {
      this.selectedDeclaration = declaration;
      this.showModal = true;
    },

    closeModal() {
      this.showModal = false;
      this.selectedDeclaration = null;
    },

    getStatutCount(statut) {
      return this.declarations.filter(d => d.statut === statut).length;
    },

    getStatutLabel(statut) {
      const labels = {
        'validee': 'Validée',
        'valide': 'À valider',
        'en_attente': 'En attente'
      };
      return labels[statut] || statut;
    },

    getStatutIcon(statut) {
      const icons = {
        'validee': '✅',
        'valide': '⏳',
        'en_attente': '⏸️'
      };
      return icons[statut] || '';
    },

    getStatutStyle(statut) {
      const styles = {
        'validee': 'bg-green-100 text-green-700 border border-green-200',
        'valide': 'bg-blue-100 text-blue-700 border border-blue-200',
        'en_attente': 'bg-yellow-100 text-yellow-700 border border-yellow-200'
      };
      return styles[statut] || 'bg-gray-100 text-gray-700 border border-gray-200';
    },

    // Paiement
    getPaiementLabel(declaration) {
      if (declaration.statut === 'validee') {
        return declaration.paye ? 'Payé ✅' : 'Non payé ❌';
      }
      return '';
    },

    getPaiementStyle(declaration) {
      if (declaration.statut === 'validee') {
        return declaration.paye 
          ? 'bg-green-100 text-green-700 border border-green-200'
          : 'bg-red-100 text-red-700 border border-red-200';
      }
      return '';
    },

    formatMontant(montant) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'decimal',
        minimumFractionDigits: 0
      }).format(montant || 0) + ' Ar';
    },

    formatDate(date) {
      return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
      });
    },

    formatDateCourt(date) {
      return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
      });
    },

    formatKey(key) {
      const labels = {
        'remuneration': 'Rémunération',
        'avantages': 'Avantages',
        'regularisations': 'Régularisations',
        'chiffre_affaires': 'Chiffre d\'affaires',
        'charges': 'Charges',
        'autres_revenus': 'Autres revenus'
      };
      return labels[key] || key;
    }
  }
};
</script>

<style scoped>
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.animate-slideUp { animation: slideUp 0.25s ease-out; }
.animate-fadeIn { animation: fadeIn 0.2s ease-out; }
.hover\:scale-105:hover { transform: scale(1.05); }
</style>
