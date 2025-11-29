<template>
  <div class="min-h-screen bg-gray-100 py-10 px-4">
    <div class="max-w-7xl mx-auto">

      <!-- HEADER -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
        <div>
          <h1 class="text-3xl font-bold flex items-center gap-2">
            <span class="w-1.5 h-10 bg-indigo-600 rounded-full"></span>
            Gestion des Paiements
          </h1>
          <p class="text-gray-500 text-sm mt-1">Suivi et validation des paiements</p>
        </div>
        <button 
          @click="exporterDonnees" 
          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2"
        >
          📥 Exporter CSV
        </button>
      </div>

      <!-- STATISTIQUES -->
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="p-5 bg-white rounded-xl shadow border-l-4 border-blue-500">
          <p class="text-gray-500 text-xs uppercase">Total déclarations</p>
          <p class="text-2xl font-bold">{{ stats.total }}</p>
        </div>
        <div class="p-5 bg-white rounded-xl shadow border-l-4 border-green-500">
          <p class="text-gray-500 text-xs uppercase">Payées</p>
          <p class="text-2xl font-bold text-green-600">{{ stats.paye }}</p>
          <p class="text-xs text-gray-400 mt-1">{{ pourcentage(stats.paye, stats.total) }}%</p>
        </div>
        <div class="p-5 bg-white rounded-xl shadow border-l-4 border-orange-500">
          <p class="text-gray-500 text-xs uppercase">Non payées</p>
          <p class="text-2xl font-bold text-orange-600">{{ stats.nonPaye }}</p>
          <p class="text-xs text-gray-400 mt-1">{{ pourcentage(stats.nonPaye, stats.total) }}%</p>
        </div>
        <div class="p-5 bg-white rounded-xl shadow border-l-4 border-red-500">
          <p class="text-gray-500 text-xs uppercase">En retard</p>
          <p class="text-2xl font-bold text-red-600">{{ stats.enRetard }}</p>
          <p class="text-xs text-gray-400 mt-1">{{ pourcentage(stats.enRetard, stats.total) }}%</p>
        </div>
        <div class="p-5 bg-white rounded-xl shadow border-l-4 border-purple-500">
          <p class="text-gray-500 text-xs uppercase">Montant total</p>
          <p class="text-xl font-bold text-purple-600">{{ formatMontant(stats.montantTotal) }}</p>
        </div>
      </div>

      <!-- FILTRES ET RECHERCHE -->
      <div class="bg-white rounded-xl shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Rechercher un contribuable..."
            class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
          />
          <select v-model="filterStatut" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            <option value="">Tous les statuts</option>
            <option value="paye">✅ Payées</option>
            <option value="non_paye">⏳ Non payées</option>
            <option value="en_retard">⚠️ En retard</option>
          </select>
          <select v-model.number="filterTypeImpot" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            <option value="">Tous les types d'impôt</option>
            <option v-for="type in typesImpots" :key="type.id" :value="type.id">
              {{ type.nom }}
            </option>
          </select>

          <button 
            @click="reinitialiserFiltres" 
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition"
          >
            🔄 Réinitialiser
          </button>
        </div>
      </div>

      <!-- LOADER -->
      <div v-if="loading" class="text-center py-20">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-indigo-600 border-t-transparent"></div>
        <p class="text-gray-600 mt-4">Chargement des données...</p>
      </div>

      <!-- TABLEAU / EMPTY STATE -->
      <div v-else class="bg-white rounded-2xl shadow overflow-hidden">
        <div v-if="filteredDeclarations.length" class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-200 text-gray-600 text-sm uppercase">
              <tr>
                <th class="px-6 py-3 text-left cursor-pointer hover:bg-gray-300" @click="trierPar('id_declaration')">
                  ID <span v-if="sortBy === 'id_declaration'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th class="px-6 py-3 text-left cursor-pointer hover:bg-gray-300" @click="trierPar('contribuable')">
                  Contribuable <span v-if="sortBy === 'contribuable'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th class="px-6 py-3 text-left">Type d'impôt</th>
                <th class="px-6 py-3 text-left cursor-pointer hover:bg-gray-300" @click="trierPar('montant')">
                  Montant <span v-if="sortBy === 'montant'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th class="px-6 py-3 text-left cursor-pointer hover:bg-gray-300" @click="trierPar('date_echeance')">
                  Échéance <span v-if="sortBy === 'date_echeance'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th class="px-6 py-3 text-left">Statut</th>
                <th class="px-6 py-3 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="d in paginatedDeclarations" 
                :key="d.id_declaration" 
                class="border-b hover:bg-gray-50 transition"
              >
                <td class="px-6 py-4 font-mono text-sm">#{{ d.id_declaration }}</td>
                <td class="px-6 py-4">
                  <div>
                    <p class="font-semibold">{{ getContribuableNom(d) }}</p>
                    <p class="text-xs text-gray-500">NIF: {{ d.contribuable?.nif || 'N/A' }}</p>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-sm">
                    {{ getTypeImpotNom(d) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-green-600 font-semibold">{{ formatMontant(d.montant) }}</td>
                <td class="px-6 py-4" :class="{'text-red-600 font-bold': isLate(d)}">
                  {{ formatDate(getEcheance(d)) }}
                  <span v-if="isLate(d)" class="block text-xs text-red-500">
                    {{ joursRetard(d) }} jours de retard
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span v-if="d.statut_paiement === 'paye'" class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold inline-flex items-center gap-1">
                    ✓ Payée
                  </span>
                  <span v-else-if="isLate(d)" class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold inline-flex items-center gap-1">
                    ⚠️ En retard
                  </span>
                  <span v-else class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold inline-flex items-center gap-1">
                    ⏳ Non payée
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                  <div class="flex gap-2 justify-center">
                    <button @click="voirDetails(d)" class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                      👁️ Voir
                    </button>
                    <button v-if="d.statut_paiement !== 'paye'" @click="marquerCommePaye(d)" class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                      ✓ Valider
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- EMPTY STATE -->
        <div v-else class="py-16 text-center text-gray-500">
          <div class="text-6xl mb-4">🔍</div>
          <p class="text-lg font-semibold">Aucune déclaration trouvée</p>
          <p class="text-sm">Essayez de modifier vos filtres de recherche</p>
        </div>

        <!-- PAGINATION -->
        <div v-if="filteredDeclarations.length > itemsPerPage" class="px-6 py-4 bg-gray-50 flex justify-between items-center border-t">
          <div class="text-sm text-gray-600">
            Affichage de {{ (currentPage - 1) * itemsPerPage + 1 }} à 
            {{ Math.min(currentPage * itemsPerPage, filteredDeclarations.length) }} 
            sur {{ filteredDeclarations.length }} résultats
          </div>
          <div class="flex gap-2">
            <button @click="currentPage--" :disabled="currentPage === 1" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
              ← Précédent
            </button>
            <span class="px-4 py-1 bg-indigo-600 text-white rounded">{{ currentPage }} / {{ totalPages }}</span>
            <button @click="currentPage++" :disabled="currentPage === totalPages" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
              Suivant →
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { ref, onMounted, computed } from "vue";

axios.defaults.baseURL = 'http://127.0.0.1:8000/api';

export default {
  name: 'AgentGestionPaiements',
  setup() {
    const declarations = ref([]);
    const typesImpots = ref([]);
    const searchQuery = ref('');
    const filterStatut = ref('');
    const filterTypeImpot = ref('');
    const loading = ref(true);
    const showModal = ref(false);
    const selectedDeclaration = ref(null);
    const currentPage = ref(1);
    const itemsPerPage = ref(10);
    const sortBy = ref('id_declaration');
    const sortDirection = ref('desc');

    // --- Helpers Type d'impôt ---
    const getTypeImpot = (d) => d.typeImpot || d.type_impot || {};
    const getTypeImpotNom = (d) => getTypeImpot(d).nom || "N/A";
    const getTypeImpotId = (d) => getTypeImpot(d).id || d.id_type_impot || null;

    // --- Load données ---
    const load = async () => {
      loading.value = true;
      try {
        const [declRes, typesRes] = await Promise.all([
          axios.get("/declarations"),
          axios.get("/types-impots")
        ]);
        declarations.value = Array.isArray(declRes.data) ? declRes.data : declRes.data.data || [];
        typesImpots.value = Array.isArray(typesRes.data) ? typesRes.data : typesRes.data.data || [];
      } catch (e) {
        console.error(e);
        alert("Erreur lors du chargement des données");
      } finally { loading.value = false; }
    };

    // --- Helpers généraux ---
    const getContribuableNom = (d) => d.contribuable ? `${d.contribuable.nom || ''} ${d.contribuable.prenom || ''}`.trim() : "Inconnu";
    const getEcheance = (d) => d.echeance?.date_limite || d.date_echeance || d.date_declaration;
    const isLate = (d) => d.statut_paiement !== "paye" && new Date(getEcheance(d)) < new Date();
    const joursRetard = (d) => Math.max(Math.floor((new Date() - new Date(getEcheance(d))) / (1000*60*60*24)), 0);
    const formatDate = (d) => d ? new Date(d).toLocaleDateString("fr-FR",{day:"2-digit",month:"long",year:"numeric"}) : "N/A";
    const formatMontant = (m) => new Intl.NumberFormat("fr-FR").format(m||0)+" Ar";
    const pourcentage = (val, total) => total === 0 ? 0 : Math.round((val/total)*100);

    // --- Filtrage et tri ---
    const filteredDeclarations = computed(() => {
      let result = [...declarations.value];

      // Recherche par nom/NIF
      if(searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        result = result.filter(d => 
          getContribuableNom(d).toLowerCase().includes(q) ||
          (d.contribuable?.nif || '').toLowerCase().includes(q)
        );
      }

      // Filtre par statut
      if(filterStatut.value === "paye") result = result.filter(d => d.statut_paiement === "paye");
      else if(filterStatut.value === "non_paye") result = result.filter(d => d.statut_paiement !== "paye" && !isLate(d));
      else if(filterStatut.value === "en_retard") result = result.filter(d => isLate(d));

      // Filtre Type d'impôt
      if(filterTypeImpot.value) {
        const selectedId = Number(filterTypeImpot.value);
        result = result.filter(d => getTypeImpotId(d) === selectedId);
      }

      // Tri
      result.sort((a,b)=>{
        let valA, valB;
        switch(sortBy.value){
          case "contribuable": valA = getContribuableNom(a); valB = getContribuableNom(b); break;
          case "montant": valA = a.montant; valB = b.montant; break;
          case "date_echeance": valA = new Date(getEcheance(a)); valB = new Date(getEcheance(b)); break;
          default: valA = a[sortBy.value]; valB = b[sortBy.value];
        }
        return sortDirection.value === "asc" ? (valA > valB ? 1 : -1) : (valA < valB ? 1 : -1);
      });

      return result;
    });

    const totalPages = computed(() => Math.ceil(filteredDeclarations.value.length / itemsPerPage.value));
    const paginatedDeclarations = computed(() => 
      filteredDeclarations.value.slice((currentPage.value - 1) * itemsPerPage.value, currentPage.value * itemsPerPage.value)
    );

    // --- Statistiques ---
    const stats = computed(() => {
      const total = declarations.value.length;
      const paye = declarations.value.filter(d => d.statut_paiement === "paye").length;
      const nonPaye = declarations.value.filter(d => d.statut_paiement !== "paye").length;
      const enRetard = declarations.value.filter(d => isLate(d)).length;
      const montantTotal = declarations.value.reduce((sum,d) => sum + (d.montant || 0), 0);
      return { total, paye, nonPaye, enRetard, montantTotal };
    });

    // --- Actions ---
    const voirDetails = (decl) => { selectedDeclaration.value = decl; showModal.value = true; };
    const closeModal = () => { showModal.value = false; selectedDeclaration.value = null; };
    const marquerCommePaye = async (decl) => {
      if(!confirm(`Confirmer le paiement de ${formatMontant(decl.montant)} ?`)) return;
      try {
        await axios.post(`/declarations/${decl.id_contribuable}/payer/${decl.id_declaration}`);
        const d = declarations.value.find(x => x.id_declaration === decl.id_declaration);
        if(d) d.statut_paiement = "paye";
        if(selectedDeclaration.value?.id_declaration === decl.id_declaration) selectedDeclaration.value.statut_paiement = "paye";
        alert("Paiement validé !");
        closeModal();
      } catch(err) {
        alert("Erreur : " + (err.response?.data?.message || err.message));
      }
    };

    const reinitialiserFiltres = () => { searchQuery.value = ""; filterStatut.value = ""; filterTypeImpot.value = ""; currentPage.value = 1; };
    const trierPar = (col) => { 
      if(sortBy.value === col) sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc"; 
      else { sortBy.value = col; sortDirection.value = "asc"; } 
    };

    const exporterDonnees = () => {
      const csv = [
        ["ID","Contribuable","NIF","Type Impôt","Montant","Date Déclaration","Échéance","Statut"].join(";"),
        ...filteredDeclarations.value.map(d => [
          d.id_declaration,
          getContribuableNom(d),
          d.contribuable?.nif || "N/A",
          getTypeImpotNom(d),
          formatMontant(d.montant),
          formatDate(d.date_declaration),
          formatDate(getEcheance(d)),
          d.statut_paiement
        ].join(";"))
      ].join("\n");
      const blob = new Blob([csv], {type:"text/csv"});
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a"); a.href = url; a.download = "paiements.csv"; a.click(); URL.revokeObjectURL(url);
    };

    const imprimerRecu = () => { window.print(); };

    onMounted(load);

    return {
      declarations, typesImpots, searchQuery, filterStatut, filterTypeImpot, loading,
      showModal, selectedDeclaration, currentPage, itemsPerPage, sortBy, sortDirection,
      filteredDeclarations, paginatedDeclarations, totalPages, stats,
      getContribuableNom, getTypeImpotNom, formatMontant, formatDate, pourcentage,
      voirDetails, closeModal, marquerCommePaye, isLate, joursRetard, getEcheance,
      reinitialiserFiltres, trierPar, exporterDonnees, imprimerRecu
    };
  }
};
</script>
