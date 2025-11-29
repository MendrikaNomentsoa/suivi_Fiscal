<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-slate-900 mb-2">Gestion des contribuables</h1>
      <p class="text-slate-600">Consultez et gérez les informations des contribuables</p>
    </div>

    <!-- Info Banner -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start mb-6">
      <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
      </svg>
      <div>
        <p class="text-sm font-semibold text-blue-900">Information</p>
        <p class="text-sm text-blue-700">Les contribuables s'inscrivent via la page publique. Vous pouvez consulter leurs détails, modifier leurs informations ou les supprimer.</p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      <p class="mt-4 text-slate-600">Chargement des contribuables...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg flex items-start">
      <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
      </svg>
      <div>
        <p class="font-semibold">Erreur</p>
        <p>{{ error }}</p>
      </div>
    </div>

    <!-- Table -->
    <div v-else class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nom complet</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">NIF</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Téléphone</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-slate-200">
            <tr v-for="c in contribuables" :key="c.id_contribuable" class="hover:bg-slate-50 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                #{{ c.id_contribuable }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                    <span class="text-indigo-600 font-semibold text-sm">
                      {{ c.nom.charAt(0) }}{{ c.prenom.charAt(0) }}
                    </span>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-slate-900">{{ c.nom }} {{ c.prenom }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                {{ c.nif }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                {{ c.email || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                {{ c.telephone || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <button
                  @click="voirDetails(c)"
                  class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors"
                  title="Voir les détails"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  Détails
                </button>
                <button
                  @click="modifierContribuable(c)"
                  class="inline-flex items-center px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors"
                  title="Modifier"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                  Modifier
                </button>
                <button
                  @click="supprimerContribuable(c.id_contribuable)"
                  class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors"
                  title="Supprimer"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                  Supprimer
                </button>
              </td>
            </tr>
            <tr v-if="contribuables.length === 0">
              <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="font-medium text-slate-900">Aucun contribuable trouvé</p>
                <p class="text-sm mt-1">Les contribuables apparaîtront ici après leur inscription</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal de détails -->
    <div v-if="showDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="showDetailsModal = false">
      <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header Modal -->
        <div class="sticky top-0 bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-6 rounded-t-2xl">
          <div class="flex justify-between items-start">
            <div>
              <h2 class="text-2xl font-bold">Détails du contribuable</h2>
              <p class="text-indigo-100 mt-1">Informations complètes et historique</p>
            </div>
            <button @click="showDetailsModal = false" class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Content Modal -->
        <div v-if="selectedContribuable" class="p-6 space-y-6">
          <!-- Informations personnelles -->
          <div class="bg-slate-50 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Informations personnelles
            </h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-slate-500">Nom complet</p>
                <p class="font-semibold text-slate-900">{{ selectedContribuable.nom }} {{ selectedContribuable.prenom }}</p>
              </div>
              <div>
                <p class="text-sm text-slate-500">NIF</p>
                <p class="font-semibold text-slate-900">{{ selectedContribuable.nif }}</p>
              </div>
              <div>
                <p class="text-sm text-slate-500">Email</p>
                <p class="font-semibold text-slate-900">{{ selectedContribuable.email || 'Non renseigné' }}</p>
              </div>
              <div>
                <p class="text-sm text-slate-500">Téléphone</p>
                <p class="font-semibold text-slate-900">{{ selectedContribuable.telephone || 'Non renseigné' }}</p>
              </div>
              <div>
                <p class="text-sm text-slate-500">Date d'inscription</p>
                <p class="font-semibold text-slate-900">{{ formatDate(selectedContribuable.date_inscription) }}</p>
              </div>
            </div>
          </div>

          <!-- Déclarations -->
          <div class="bg-slate-50 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Déclarations fiscales
            </h3>
            <div v-if="loadingDeclarations" class="text-center py-8">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
              <p class="mt-2 text-slate-600">Chargement des déclarations...</p>
            </div>
            <div v-else-if="declarations.length === 0" class="text-center py-8 text-slate-500">
              Aucune déclaration trouvée
            </div>
            <div v-else class="space-y-3">
              <div v-for="decl in declarations" :key="decl.id_declaration" class="bg-white rounded-lg p-4 border border-slate-200">
                <div class="flex justify-between items-start">
                  <div>
                    <p class="font-semibold text-slate-900">{{ decl.type_impot?.nom_impot || 'Type inconnu' }}</p>
                    <p class="text-sm text-slate-500">Date: {{ formatDate(decl.date_declaration) }}</p>
                    <p class="text-sm text-slate-500">Montant: {{ formatMontant(decl.montant_du) }} Ar</p>
                  </div>
                  <span :class="getStatutClass(decl.statut)" class="px-3 py-1 rounded-full text-xs font-semibold">
                    {{ decl.statut }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Types d'impôts associés -->
          <div class="bg-slate-50 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Types d'impôts concernés
            </h3>
            <div v-if="typesImpots.length === 0" class="text-center py-8 text-slate-500">
              Aucun type d'impôt trouvé
            </div>
            <div v-else class="grid grid-cols-2 gap-3">
              <div v-for="type in typesImpots" :key="type.id_type_impot" class="bg-white rounded-lg p-4 border border-slate-200">
                <p class="font-semibold text-slate-900">{{ type.nom_impot }}</p>
                <p class="text-sm text-slate-500 mt-1">Taux: {{ type.taux }}%</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Modal -->
        <div class="sticky bottom-0 bg-slate-50 p-6 rounded-b-2xl border-t border-slate-200">
          <button
            @click="showDetailsModal = false"
            class="w-full bg-slate-200 text-slate-700 py-3 rounded-xl font-semibold hover:bg-slate-300 transition-colors"
          >
            Fermer
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de modification -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="showEditModal = false">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full">
        <!-- Header -->
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white p-6 rounded-t-2xl">
          <div class="flex justify-between items-start">
            <div>
              <h2 class="text-2xl font-bold">Modifier le contribuable</h2>
              <p class="text-yellow-100 mt-1">Mettez à jour les informations</p>
            </div>
            <button @click="showEditModal = false" class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Form -->
        <form @submit.prevent="saveModification" class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Nom</label>
              <input v-model="editForm.nom" type="text" required class="w-full px-4 py-2 border-2 border-slate-200 rounded-lg focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100 transition-all outline-none">
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Prénom</label>
              <input v-model="editForm.prenom" type="text" required class="w-full px-4 py-2 border-2 border-slate-200 rounded-lg focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100 transition-all outline-none">
            </div>
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">NIF</label>
            <input v-model="editForm.nif" type="text" required class="w-full px-4 py-2 border-2 border-slate-200 rounded-lg focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100 transition-all outline-none">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
              <input v-model="editForm.email" type="email" class="w-full px-4 py-2 border-2 border-slate-200 rounded-lg focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100 transition-all outline-none">
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Téléphone</label>
              <input v-model="editForm.telephone" type="tel" class="w-full px-4 py-2 border-2 border-slate-200 rounded-lg focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100 transition-all outline-none">
            </div>
          </div>

          <div v-if="editError" class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
            {{ editError }}
          </div>

          <div class="flex gap-3 pt-4">
            <button type="button" @click="showEditModal = false" class="flex-1 bg-slate-200 text-slate-700 py-3 rounded-xl font-semibold hover:bg-slate-300 transition-colors">
              Annuler
            </button>
            <button type="submit" :disabled="editLoading" class="flex-1 bg-gradient-to-r from-yellow-500 to-orange-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition-all disabled:opacity-50">
              {{ editLoading ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import http from "../../api/http";

export default {
  name: "ContribuablesList",
  data() {
    return {
      contribuables: [],
      loading: false,
      error: null,
      
      // Modal détails
      showDetailsModal: false,
      selectedContribuable: null,
      declarations: [],
      typesImpots: [],
      loadingDeclarations: false,
      
      // Modal modification
      showEditModal: false,
      editForm: {
        id_contribuable: null,
        nom: '',
        prenom: '',
        nif: '',
        email: '',
        telephone: ''
      },
      editLoading: false,
      editError: null
    };
  },
  mounted() {
    this.loadContribuables();
  },
  methods: {
    async loadContribuables() {
      this.loading = true;
      this.error = null;

      try {
        const res = await http.get("/contribuables");
        this.contribuables = res.data;
        console.log("✅ Contribuables chargés:", this.contribuables.length);
      } catch (err) {
        console.error("❌ Erreur:", err);
        this.error = err.response?.data?.message || "Erreur lors du chargement des contribuables";
      } finally {
        this.loading = false;
      }
    },

    async voirDetails(contribuable) {
      this.selectedContribuable = contribuable;
      this.showDetailsModal = true;
      this.declarations = [];
      this.typesImpots = [];
      
      // Charger les déclarations
      this.loadingDeclarations = true;
      try {
        const declRes = await http.get(`/declarations?id_contribuable=${contribuable.id_contribuable}`);
        this.declarations = declRes.data;

        // Extraire les types d'impôts uniques
        const typesSet = new Set();
        this.declarations.forEach(decl => {
          if (decl.type_impot) {
            typesSet.add(JSON.stringify(decl.type_impot));
          }
        });
        this.typesImpots = Array.from(typesSet).map(t => JSON.parse(t));
        
        console.log("✅ Déclarations chargées:", this.declarations.length);
      } catch (err) {
        console.error("❌ Erreur déclarations:", err);
      } finally {
        this.loadingDeclarations = false;
      }
    },

    modifierContribuable(contribuable) {
      this.editForm = {
        id_contribuable: contribuable.id_contribuable,
        nom: contribuable.nom,
        prenom: contribuable.prenom,
        nif: contribuable.nif,
        email: contribuable.email || '',
        telephone: contribuable.telephone || ''
      };
      this.editError = null;
      this.showEditModal = true;
    },

    async saveModification() {
      this.editLoading = true;
      this.editError = null;

      try {
        await http.put(`/contribuables/${this.editForm.id_contribuable}`, {
          nom: this.editForm.nom,
          prenom: this.editForm.prenom,
          nif: this.editForm.nif,
          email: this.editForm.email,
          telephone: this.editForm.telephone
        });

        // Mettre à jour la liste locale
        const index = this.contribuables.findIndex(c => c.id_contribuable === this.editForm.id_contribuable);
        if (index !== -1) {
          this.contribuables[index] = { ...this.contribuables[index], ...this.editForm };
        }

        this.showEditModal = false;
        console.log("✅ Contribuable modifié");
      } catch (err) {
        console.error("❌ Erreur modification:", err);
        this.editError = err.response?.data?.message || "Erreur lors de la modification";
      } finally {
        this.editLoading = false;
      }
    },

    async supprimerContribuable(id) {
      if (!confirm("Voulez-vous vraiment supprimer ce contribuable ? Cette action est irréversible.")) return;

      try {
        await http.delete(`/contribuables/${id}`);
        this.contribuables = this.contribuables.filter(c => c.id_contribuable !== id);
        console.log("✅ Contribuable supprimé");
      } catch (err) {
        console.error("❌ Erreur suppression:", err);
        alert(err.response?.data?.message || "Erreur lors de la suppression");
      }
    },

    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },

    formatMontant(montant) {
      if (!montant) return '0';
      return Number(montant).toLocaleString('fr-FR');
    },

    getStatutClass(statut) {
      const classes = {
        'en_attente': 'bg-yellow-100 text-yellow-800',
        'validee': 'bg-green-100 text-green-800',
        'rejetee': 'bg-red-100 text-red-800',
        'payee': 'bg-blue-100 text-blue-800'
      };
      return classes[statut] || 'bg-slate-100 text-slate-800';
    }
  }
};
</script>

<style scoped>
table th,
table td {
  text-align: left;
}

/* Animation pour les modals */
.fixed {
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
</style>