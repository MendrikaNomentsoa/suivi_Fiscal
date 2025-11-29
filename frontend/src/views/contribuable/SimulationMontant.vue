<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-8 px-4">
    <div class="max-w-7xl mx-auto">

      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Simulateur de Paiement d'Impôts</h1>
        <p class="text-gray-600">Madagascar - Direction Générale des Impôts</p>
        <div class="mt-2 text-sm text-gray-500">
          Contribuable ID: {{ idContribuable }} | Déclarations: {{ declarations.length }}
          <button @click="loadDeclarations" class="ml-4 px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">
            🔄 Recharger
          </button>
        </div>
      </div>

      <div class="grid lg:grid-cols-3 gap-6">

        <!-- HISTORIQUE DES DÉCLARATIONS -->
        <div class="lg:col-span-1 bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold mb-4">Historique des déclarations</h2>

          <div v-if="loading" class="text-gray-500 text-center py-8">
            <div class="animate-pulse">Chargement...</div>
          </div>

          <div v-else-if="filteredDeclarations.length === 0" class="text-center py-8">
            <div class="text-gray-400 text-4xl mb-2">📄</div>
            <p class="text-gray-500 text-sm">Aucune déclaration disponible pour {{ idTypeImpot }}</p>
            <p class="text-xs text-gray-400 mt-2">Total: {{ declarations.length }} déclarations</p>
          </div>

          <div v-else class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
            <div
              v-for="decl in filteredDeclarations"
              :key="decl.id_declaration"
              class="p-4 rounded-lg border-2 transition-all hover:shadow-md"
              :class="{
                'bg-green-50 border-green-200': decl.statut_paiement === 'paye',
                'bg-orange-50 border-orange-200': decl.statut_paiement === 'non_paye'
              }"
            >
              <div class="flex justify-between items-center mb-2">
                <div>
                  <p class="font-bold text-gray-900">{{ formatMontant(decl.montant) }}</p>
                  <p class="text-xs text-gray-500 mt-0.5">{{ decl.type_impot }}</p>
                  <p class="text-xs text-gray-600 mt-1">Échéance : {{ formatDate(decl.date_limite) }}</p>
                </div>

                <div>
                  <button
                    v-if="decl.statut_paiement === 'non_paye'"
                    @click="payerDeclaration(decl.id_declaration)"
                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg transition"
                  >
                    Payer
                  </button>
                  <span v-else class="text-green-600 font-medium text-xs">✔ Payé</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SIMULATION -->
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Simulation du montant à payer</h2>

            <div class="grid md:grid-cols-2 gap-6">

              <!-- Formulaire -->
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Type d'impôt</label>
                  <select v-model="idTypeImpot" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="IRSA">IRSA (mensuel)</option>
                    <option value="IS">IS (annuel)</option>
                  </select>
                </div>

                <!-- Formulaire IRSA -->
                <div v-if="idTypeImpot === 'IRSA'" class="space-y-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mois travaillé</label>
                    <input type="month" v-model="form.moisTravaille" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rémunération (Ar)</label>
                    <input type="number" v-model.number="form.remuneration" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Avantages (Ar)</label>
                    <input type="number" v-model.number="form.avantages" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" />
                  </div>
                </div>

                <!-- Formulaire IS -->
                <div v-else class="space-y-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Année d'exercice</label>
                    <input type="number" v-model.number="form.anneeExercice" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"/>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bénéfice imposable (Ar)</label>
                    <input type="number" v-model.number="form.benefice" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"/>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Date de paiement simulée</label>
                  <input type="date" v-model="form.datePaiement" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"/>
                </div>

                <button @click="calculer" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 rounded-lg transition">
                  Calculer
                </button>
              </div>

              <!-- Barème -->
              <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg p-4 border border-indigo-100">
                <h3 class="font-semibold text-gray-900 mb-3 text-sm">
                  {{ idTypeImpot === 'IRSA' ? 'Barème IRSA' : 'Barème IS' }}
                </h3>
                
                <div v-if="idTypeImpot === 'IRSA'">
                  <table class="w-full text-xs">
                    <thead class="bg-white/60">
                      <tr>
                        <th class="px-2 py-2 text-left rounded-tl">Revenu mensuel</th>
                        <th class="px-2 py-2 text-right rounded-tr">Taux</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-100">
                      <tr><td class="px-2 py-2">0 - 350 000 Ar</td><td class="px-2 py-2 text-right">0%</td></tr>
                      <tr><td class="px-2 py-2">350 001 - 400 000 Ar</td><td class="px-2 py-2 text-right">5%</td></tr>
                      <tr><td class="px-2 py-2">400 001 - 500 000 Ar</td><td class="px-2 py-2 text-right">10%</td></tr>
                      <tr><td class="px-2 py-2">500 001 - 600 000 Ar</td><td class="px-2 py-2 text-right">15%</td></tr>
                      <tr><td class="px-2 py-2">&gt; 600 000 Ar</td><td class="px-2 py-2 text-right">20%</td></tr>
                    </tbody>
                  </table>
                </div>
                
                <div v-else class="bg-white/60 rounded-lg p-4 text-center">
                  <div class="text-4xl font-bold text-indigo-600 mb-2">20%</div>
                  <p class="text-xs text-gray-600">Taux fixe sur le bénéfice imposable</p>
                </div>
              </div>

            </div>
          </div>

          <!-- Résultat -->
          <div v-if="resultat" class="bg-white rounded-xl shadow-lg p-6 border-2" 
               :class="resultat.enRetard ? 'border-red-300' : 'border-green-300'">
            <h3 class="font-bold text-gray-900 mb-4 text-lg">
              {{ resultat.enRetard ? '⚠️ Paiement en retard' : '✅ Dans les délais' }}
            </h3>
            <div class="grid md:grid-cols-2 gap-4">
              <div class="space-y-2">
                <p class="text-gray-700">
                  <span class="font-medium">Date d'échéance :</span> {{ formatDate(resultat.dateEcheance) }}
                </p>
                <p class="text-gray-700">
                  <span class="font-medium">Montant impôt :</span> {{ formatMontant(resultat.montantImpot) }}
                </p>
                <p v-if="resultat.penalite" class="text-red-600">
                  <span class="font-medium">Pénalité :</span> {{ formatMontant(resultat.penalite) }}
                </p>
                <p v-if="resultat.interet" class="text-red-600">
                  <span class="font-medium">Intérêt de retard :</span> {{ formatMontant(resultat.interet) }}
                </p>
              </div>
              <div class="flex items-center justify-end">
                <div class="text-right">
                  <p class="text-sm text-gray-500 mb-1">Total à payer</p>
                  <p class="text-3xl font-bold" :class="resultat.enRetard ? 'text-red-600' : 'text-green-600'">
                    {{ formatMontant(resultat.total) }}
                  </p>
                </div>
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
import dayjs from "dayjs";

// Configuration Axios
axios.defaults.baseURL = 'http://localhost:8000/api';
axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.headers.common['Accept'] = 'application/json';

export default {
  name: 'SimulationMontant',
  
  props: {
    idContribuable: {
      type: [String, Number],
      default: 1
    }
  },
  
  data() {
    return {
      idTypeImpot: "IRSA",
      form: {
        remuneration: 0,
        avantages: 0,
        moisTravaille: dayjs().format("YYYY-MM"),
        benefice: 0,
        anneeExercice: new Date().getFullYear() - 1,
        datePaiement: dayjs().format("YYYY-MM-DD")
      },
      declarations: [],
      resultat: null,
      loading: false
    };
  },
  
  computed: {
    filteredDeclarations() {
      if (!Array.isArray(this.declarations)) {
        console.warn('⚠️ declarations n\'est pas un tableau');
        return [];
      }
      
      const filtered = this.declarations.filter(d => {
        if (!d.type_impot) {
          console.warn('⚠️ Déclaration sans type_impot:', d);
          return false;
        }
        return d.type_impot === this.idTypeImpot;
      });
      
      console.log(`🔍 Filtré ${filtered.length} déclarations pour ${this.idTypeImpot}`);
      return filtered;
    }
  },
  
  mounted() {
    console.log('🚀 Composant monté');
    console.log('👤 ID Contribuable:', this.idContribuable);
    console.log('📡 Base URL:', axios.defaults.baseURL);
    this.loadDeclarations();
  },
  
  watch: {
    idContribuable(newVal, oldVal) {
      console.log(`🔄 ID Contribuable changé: ${oldVal} → ${newVal}`);
      this.loadDeclarations();
    },
    
    idTypeImpot(newVal) {
      console.log(`🔄 Type d'impôt changé: ${newVal}`);
    }
  },
  
  methods: {
    async loadDeclarations() {
      const contributorId = this.idContribuable || 1;
      this.loading = true;
      
      try {
        console.log(`🔍 Chargement des déclarations pour le contribuable ${contributorId}...`);
        
        const res = await axios.get(`/impots/${contributorId}`);
        
        console.log("📦 Réponse API complète:", res);
        console.log("📦 Données reçues:", res.data);
        
        // Extraire les données selon la structure
        let rawData = [];
        if (res.data?.success && Array.isArray(res.data.data)) {
          rawData = res.data.data;
          console.log("✅ Structure: {success, data}");
        } else if (Array.isArray(res.data)) {
          rawData = res.data;
          console.log("✅ Structure: tableau direct");
        } else {
          console.warn("⚠️ Structure API non reconnue:", res.data);
          this.declarations = [];
          return;
        }
        
        console.log(`📋 ${rawData.length} déclaration(s) brute(s) reçue(s)`);
        
        // Normaliser chaque déclaration
        this.declarations = rawData.map((decl, index) => {
          console.log(`🔍 Traitement déclaration ${index + 1}:`, decl);
          
          // Extraire le code du type d'impôt
          let typeImpotCode = 'INCONNU';
          
          if (decl.typeImpot && typeof decl.typeImpot === 'object') {
            // Relation Laravel chargée
            typeImpotCode = decl.typeImpot.code || decl.typeImpot.nom || 'INCONNU';
            console.log(`  → Type d'impôt (relation): ${typeImpotCode}`);
          } else if (typeof decl.type_impot === 'string') {
            // Déjà une chaîne
            typeImpotCode = decl.type_impot;
            console.log(`  → Type d'impôt (string): ${typeImpotCode}`);
          } else if (decl.id_type_impot) {
            // Fallback sur l'ID
            typeImpotCode = decl.id_type_impot === 1 ? 'IRSA' : 'IS';
            console.log(`  → Type d'impôt (ID): ${typeImpotCode}`);
          }
          
          const normalized = {
            id_declaration: decl.id_declaration,
            type_impot: typeImpotCode.toUpperCase(),
            montant: parseFloat(decl.montant) || 0,
            date_limite: decl.echeance?.date_limite || decl.date_declaration,
            statut_paiement: decl.statut_paiement || 'non_paye'
          };
          
          console.log(`  ✅ Normalisée:`, normalized);
          return normalized;
        });
        
        console.log(`✅ ${this.declarations.length} déclaration(s) normalisée(s)`);
        console.log("📋 Déclarations finales:", this.declarations);
        
      } catch (err) {
        console.error("❌ Erreur lors du chargement:", err);
        console.error("❌ Détails:", err.response?.data || err.message);
        console.error("❌ Status:", err.response?.status);
        
        this.declarations = [];
        
        // Afficher un message d'erreur à l'utilisateur
        if (err.response?.status === 404) {
          console.warn("⚠️ Contribuable non trouvé");
        } else if (err.response?.status === 500) {
          console.error("💥 Erreur serveur");
        }
        
      } finally {
        this.loading = false;
      }
    },
    
    async payerDeclaration(idDeclaration) {
      if (!confirm('Confirmer le paiement de cette déclaration ?')) {
        return;
      }
      
      try {
        console.log(`💰 Paiement de la déclaration ${idDeclaration}...`);
        
        const res = await axios.post(
          `/declarations/${this.idContribuable}/payer/${idDeclaration}`
        );
        
        console.log("✅ Paiement réussi:", res.data);
        alert("✅ Paiement effectué avec succès !");
        
        // Recharger les déclarations
        await this.loadDeclarations();
        
      } catch (err) {
        console.error("❌ Erreur paiement:", err);
        alert("❌ Erreur : " + (err.response?.data?.message || err.message));
      }
    },

    calculerDateEcheance() {
      if (this.idTypeImpot === "IRSA") {
        const [y, m] = this.form.moisTravaille.split("-");
        return dayjs(`${y}-${m}-01`).add(1, "month").date(15).format("YYYY-MM-DD");
      }
      return `${this.form.anneeExercice + 1}-05-15`;
    },
    
    calculerMontantImpot() {
      if (this.idTypeImpot === "IRSA") {
        const total = this.form.remuneration + this.form.avantages;
        let imp = 0;
        
        if (total > 600000) {
          imp = 27500 + (total - 600000) * 0.2;
        } else if (total > 500000) {
          imp = 12500 + (total - 500000) * 0.15;
        } else if (total > 400000) {
          imp = 2500 + (total - 400000) * 0.1;
        } else if (total > 350000) {
          imp = (total - 350000) * 0.05;
        }
        
        return Math.round(imp);
      } else {
        // IS
        return Math.round(this.form.benefice * 0.2);
      }
    },
    
    calculerPenalites(montant, echeance, paiement) {
      const diff = dayjs(paiement).diff(dayjs(echeance), 'day');
      let penalite = 0;
      let interet = 0;
      
      if (diff > 0) {
        // En retard
        penalite = this.idTypeImpot === "IRSA" ? 100000 : 20000;
        
        // Intérêt de 1% par mois de retard
        const moisRetard = Math.ceil(diff / 30);
        interet = Math.round(montant * 0.01 * moisRetard);
      }
      
      return { penalite, interet };
    },
    
    calculer() {
      const dateEcheance = this.calculerDateEcheance();
      const montantImpot = this.calculerMontantImpot();
      const { penalite, interet } = this.calculerPenalites(
        montantImpot,
        dateEcheance,
        this.form.datePaiement
      );
      
      this.resultat = {
        montantImpot,
        dateEcheance,
        penalite,
        interet,
        total: montantImpot + penalite + interet,
        enRetard: penalite > 0 || interet > 0
      };
      
      console.log("📊 Résultat du calcul:", this.resultat);
    },
    
    formatDate(date) {
      if (!date) return 'N/A';
      return dayjs(date).format("DD/MM/YYYY");
    },
    
    formatMontant(montant) {
      if (montant === null || montant === undefined) return '0 Ar';
      return new Intl.NumberFormat('fr-MG').format(montant) + ' Ar';
    }
  }
};
</script>

<style scoped>
/* Styles personnalisés si nécessaire */
</style>