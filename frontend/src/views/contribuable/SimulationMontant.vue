<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-8 px-4">

    <div class="max-w-7xl mx-auto">
      
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Simulateur de Paiement d'Impôts
        </h1>
        <p class="text-gray-600">
          Madagascar - Direction Générale des Impôts
        </p>
      </div>

      <div class="grid lg:grid-cols-3 gap-6">
        
        <!-- ==========================
             1) HISTORIQUE DES DÉCLARATIONS (À GAUCHE)
        =========================== -->
        <div class="lg:col-span-1 bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
               Historique
            </h2>
            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-semibold">
              {{ idTypeImpot }}
            </span>
          </div>

          <div v-if="loading" class="text-gray-500 text-center py-8">
            <div class="animate-pulse">Chargement...</div>
          </div>

          <div v-else-if="declarationsFiltrees.length === 0" class="text-center py-8">
            <div class="text-gray-400 text-4xl mb-2">📄</div>
            <p class="text-gray-500 text-sm">Aucune déclaration {{ idTypeImpot }}</p>
          </div>

          <div v-else class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
            <div 
              v-for="decl in declarationsFiltrees" 
              :key="decl.id_declaration" 
              class="p-4 rounded-lg border-2 transition-all hover:shadow-md"
              :class="{
                'bg-green-50 border-green-200': decl.statut_paiement === 'paye',
                'bg-orange-50 border-orange-200': decl.statut_paiement === 'non_paye'
              }"
            >
              <!-- En-tête -->
              <div class="flex items-start justify-between mb-2">
                <div class="flex-1">
                  <p class="font-bold text-gray-900 text-base">
                    {{ formatMontant(decl.montant) }}
                  </p>
                  <p class="text-xs text-gray-500 mt-0.5">
                    {{ getTypeImpotNom(decl) }}
                  </p>
                </div>
                
              </div>

              <!-- Détails -->
              <div class="space-y-1 text-xs text-gray-600 mb-3">
                <div class="flex items-center gap-2">
                  <span class="opacity-60">📅</span>
                  <span>Déclaré le {{ formatDate(decl.date_declaration) }}</span>
                </div>
                <div v-if="decl.date_echeance" class="flex items-center gap-2">
                  <span class="opacity-60">⏰</span>
                  <span>Échéance : {{ formatDate(decl.date_echeance) }}</span>
                </div>
              </div>

              <!-- Bouton paiement -->
              <button
                v-if="decl.statut_paiement === 'non_paye'"
                @click="payer(decl.id_declaration)"
                class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-3 py-2 rounded-lg text-sm font-medium transition-all"
              >
                Payer maintenant
              </button>
            </div>
          </div>

          <!-- Stats rapides -->
          <div v-if="declarationsFiltrees.length > 0" class="mt-4 pt-4 border-t grid grid-cols-2 gap-2">
            <div class="text-center p-2 bg-gray-50 rounded-lg">
              <p class="text-xs text-gray-500">Total</p>
              <p class="font-bold text-gray-900">{{ declarationsFiltrees.length }}</p>
            </div>
            <div class="text-center p-2 bg-orange-50 rounded-lg">
              <p class="text-xs text-orange-600">Non payés</p>
              <p class="font-bold text-orange-700">{{ declarationsNonPayees }}</p>
            </div>
          </div>
        </div>

        <!-- ==========================
             2) SIMULATION (AU CENTRE)
        =========================== -->
        <div class="lg:col-span-2 space-y-6">
          
          <!-- Formulaire de simulation -->
          <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
              Simulation du montant à payer
            </h2>
            
            <div class="grid md:grid-cols-2 gap-6">
              
              <!-- Colonne gauche : Formulaire -->
              <div class="space-y-4">
                
                <!-- Type d'impôt -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Type d'impôt
                  </label>
                  <select 
                    v-model="idTypeImpot"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                  >
                    <option value="IRSA">IRSA (mensuel)</option>
                    <option value="IS">IS (annuel)</option>
                  </select>
                </div>

                <!-- Champs spécifiques IRSA -->
                <div v-if="idTypeImpot === 'IRSA'" class="space-y-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      Mois travaillé
                    </label>
                    <input 
                      type="month"
                      v-model="form.moisTravaille"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    />
                    <p class="text-xs text-gray-500 mt-1">
                      📅 Échéance : 15 du mois suivant
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      Rémunération (Ar)
                    </label>
                    <input 
                      type="number"
                      v-model.number="form.remuneration"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                      min="0"
                      step="1000"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      Avantages en nature (Ar)
                    </label>
                    <input 
                      type="number"
                      v-model.number="form.avantages"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                      min="0"
                      step="1000"
                    />
                  </div>
                </div>

                <!-- Champs spécifiques IS -->
                <div v-else class="space-y-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      Année d'exercice
                    </label>
                    <input 
                      type="number"
                      v-model.number="form.anneeExercice"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                      min="2000"
                      max="2100"
                    />
                    <p class="text-xs text-gray-500 mt-1">
                      📅 Échéance : 15 mai {{ form.anneeExercice + 1 }}
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      Bénéfice imposable (Ar)
                    </label>
                    <input 
                      type="number"
                      v-model.number="form.benefice"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                      min="0"
                      step="10000"
                    />
                  </div>
                </div>

                <!-- Date de paiement -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Date de paiement simulée
                  </label>
                  <input 
                    type="date"
                    v-model="form.datePaiement"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                  />
                </div>

                <button 
                  @click="calculer"
                  class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 rounded-lg transition-colors"
                >
                  Calculer
                </button>
              </div>

              <!-- Colonne droite : Barème -->
              <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg p-4 border border-indigo-100">
                <h3 class="font-semibold text-gray-900 mb-3 text-sm">
                  📊 {{ idTypeImpot === 'IRSA' ? 'Barème progressif IRSA' : 'Barème IS' }}
                </h3>
                
                <!-- Barème IRSA -->
                <div v-if="idTypeImpot === 'IRSA'">
                  <table class="w-full text-xs">
                    <thead class="bg-white/60">
                      <tr>
                        <th class="px-2 py-2 text-left rounded-tl">Revenu mensuel</th>
                        <th class="px-2 py-2 text-right rounded-tr">Taux</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-100">
                      <tr class="bg-white/40">
                        <td class="px-2 py-2">0 - 350 000 Ar</td>
                        <td class="px-2 py-2 text-right font-medium">0%</td>
                      </tr>
                      <tr class="bg-white/40">
                        <td class="px-2 py-2">350 001 - 400 000 Ar</td>
                        <td class="px-2 py-2 text-right font-medium">5%</td>
                      </tr>
                      <tr class="bg-white/40">
                        <td class="px-2 py-2">400 001 - 500 000 Ar</td>
                        <td class="px-2 py-2 text-right font-medium">10%</td>
                      </tr>
                      <tr class="bg-white/40">
                        <td class="px-2 py-2">500 001 - 600 000 Ar</td>
                        <td class="px-2 py-2 text-right font-medium">15%</td>
                      </tr>
                      <tr class="bg-white/40">
                        <td class="px-2 py-2">&gt; 600 000 Ar</td>
                        <td class="px-2 py-2 text-right font-medium">20%</td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- Barème IS -->
                <div v-else class="bg-white/60 rounded-lg p-4 text-center">
                  <div class="text-4xl font-bold text-indigo-600 mb-2">20%</div>
                  <p class="text-xs text-gray-600">Taux fixe sur le bénéfice imposable</p>
                </div>

                <!-- Règles de retard -->
                <div class="mt-4 pt-4 border-t border-indigo-200">
                  <h4 class="font-semibold text-xs text-red-600 mb-2">⚠️ En cas de retard</h4>
                  <ul class="space-y-1 text-xs text-gray-700">
                    <li>• Pénalité : {{ idTypeImpot === 'IRSA' ? '100 000' : '20 000' }} Ar</li>
                    <li>• Intérêt : 1% / mois</li>
                  </ul>
                </div>
              </div>

            </div>
          </div>

          <!-- Résultats -->
          <div v-if="resultat" class="bg-white rounded-xl shadow-lg p-6 border-2" :class="resultat.enRetard ? 'border-red-300' : 'border-green-300'">
            <h3 class="font-bold text-gray-900 mb-4 text-lg flex items-center gap-2">
              {{ resultat.enRetard ? '⚠️' : '✅' }} Résultats de la simulation
            </h3>
            
            <div class="grid md:grid-cols-2 gap-4">
              
              <!-- Détails -->
              <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                  <span class="text-sm text-gray-600">Date d'échéance</span>
                  <span class="font-semibold">{{ formatDate(resultat.dateEcheance) }}</span>
                </div>
                
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                  <span class="text-sm text-gray-700">Montant de l'impôt</span>
                  <span class="font-bold text-blue-700">{{ formatMontant(resultat.montantImpot) }}</span>
                </div>

                <div v-if="resultat.penalite > 0" class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                  <span class="text-sm text-red-700">Pénalité de retard</span>
                  <span class="font-bold text-red-700">{{ formatMontant(resultat.penalite) }}</span>
                </div>

                <div v-if="resultat.interet > 0" class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                  <span class="text-sm text-red-700">Intérêt de retard</span>
                  <span class="font-bold text-red-700">{{ formatMontant(resultat.interet) }}</span>
                </div>
              </div>

              <!-- Total -->
              <div class="flex flex-col justify-center items-center bg-gradient-to-br from-indigo-600 to-blue-600 text-white rounded-xl p-6">
                <p class="text-sm opacity-90 mb-2">Total à payer</p>
                <p class="text-4xl font-bold">{{ formatMontant(resultat.total) }}</p>
                
                <div v-if="resultat.enRetard" class="mt-4 px-4 py-2 bg-red-500 rounded-lg text-xs text-center">
                  🚨 Paiement en retard
                </div>
                <div v-else class="mt-4 px-4 py-2 bg-green-500 rounded-lg text-xs text-center">
                  ✓ Dans les délais
                </div>
              </div>

            </div>
          </div>

          <!-- Info règles -->
          <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
              ℹ️ Règles de paiement
            </h3>
            
            <div class="grid md:grid-cols-2 gap-4 text-sm">
              <div class="p-4 bg-blue-50 rounded-lg">
                <h4 class="font-semibold text-blue-900 mb-2">IRSA (mensuel)</h4>
                <p class="text-gray-700">
                  Paiement au plus tard le <strong>15 du mois suivant</strong> le mois travaillé.
                </p>
                <p class="text-xs text-gray-500 mt-2">
                  Ex : juillet → 15 août
                </p>
              </div>

              <div class="p-4 bg-purple-50 rounded-lg">
                <h4 class="font-semibold text-purple-900 mb-2">IS (annuel)</h4>
                <p class="text-gray-700">
                  Paiement au plus tard le <strong>15 mai de l'année suivante</strong>.
                </p>
                <p class="text-xs text-gray-500 mt-2">
                  Ex : 2023 → 15 mai 2024
                </p>
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

export default {
  props: ["idContribuable"],

  data() {
    return {
      idTypeImpot: "IRSA",

      form: {
        // Pour IRSA mensuel
        remuneration: 0,
        avantages: 0,
        moisTravaille: dayjs().format("YYYY-MM"),
        
        // Pour IS annuel
        benefice: 0,
        anneeExercice: new Date().getFullYear() - 1,
        
        // Date de paiement simulée
        datePaiement: dayjs().format("YYYY-MM-DD"),
      },

      resultat: null,

      loading: false,
      declarations: [],
    };
  },

  mounted() {
    if (this.idContribuable) {
      this.loadDeclarations();
    }
  },

  computed: {
    // Filtrer les déclarations par type d'impôt
    declarationsFiltrees() {
      return this.declarations.filter(decl => {
        // Gérer différentes structures de données API
        let typeDecl = '';
        
        if (typeof decl.type_impot === 'string') {
          typeDecl = decl.type_impot;
        } else if (decl.type_impot && decl.type_impot.nom) {
          typeDecl = decl.type_impot.nom;
        } else if (decl.nom) {
          typeDecl = decl.nom;
        }
        
        // Vérifier si c'est une chaîne avant d'appeler toUpperCase
        if (typeof typeDecl === 'string' && typeDecl.length > 0) {
          return typeDecl.toUpperCase().includes(this.idTypeImpot.toUpperCase());
        }
        
        return false;
      });
    },

    // Compter les déclarations non payées
    declarationsNonPayees() {
      return this.declarationsFiltrees.filter(
        decl => decl.statut_paiement === 'non_paye'
      ).length;
    }
  },

  methods: {
    // Calcul de la date d'échéance selon le type d'impôt
    calculerDateEcheance() {
      if (this.idTypeImpot === 'IRSA') {
        // 15 jours après le mois travaillé (= 15 du mois suivant)
        const [annee, mois] = this.form.moisTravaille.split('-');
        const dateEcheance = dayjs(`${annee}-${mois}-01`).add(1, 'month').date(15);
        return dateEcheance.format("YYYY-MM-DD");
      } else if (this.idTypeImpot === 'IS') {
        // 15 mai de l'année suivante
        const anneeEcheance = this.form.anneeExercice + 1;
        return `${anneeEcheance}-05-15`;
      }
      return null;
    },

    // Calcul du montant de l'impôt
    calculerMontantImpot() {
      if (this.idTypeImpot === 'IRSA') {
        const totalRevenu = this.form.remuneration + this.form.avantages;
        let irsa = 0;

        if (totalRevenu > 600000) {
          irsa = 27500 + (totalRevenu - 600000) * 0.2;
        } else if (totalRevenu > 500000) {
          irsa = 12500 + (totalRevenu - 500000) * 0.15;
        } else if (totalRevenu > 400000) {
          irsa = 2500 + (totalRevenu - 400000) * 0.1;
        } else if (totalRevenu > 350000) {
          irsa = (totalRevenu - 350000) * 0.05;
        }

        return Math.round(irsa);
      } else if (this.idTypeImpot === 'IS') {
        // IS : 20% du bénéfice
        return Math.round(this.form.benefice * 0.2);
      }
      return 0;
    },

    // Calcul des pénalités et intérêts
    calculerPenalites(montantImpot, dateEcheance, datePaiement) {
      const echeance = dayjs(dateEcheance);
      const paiement = dayjs(datePaiement);
      
      let penalite = 0;
      let interet = 0;

      // Si le paiement est après l'échéance (même 1 jour)
      if (paiement.isAfter(echeance)) {
        // Pénalité de retard
        if (this.idTypeImpot === 'IRSA') {
          penalite = 100000;
        } else if (this.idTypeImpot === 'IS') {
          penalite = 20000;
        }

        // Intérêt de retard : 1% par mois
        const diffJours = paiement.diff(echeance, 'day');
        const moisRetard = Math.ceil(diffJours / 30);
        
        interet = Math.round(montantImpot * 0.01 * moisRetard);
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
      const total = montantImpot + penalite + interet;

      this.resultat = {
        montantImpot,
        dateEcheance,
        penalite,
        interet,
        total,
        enRetard: penalite > 0 || interet > 0
      };
    },

    formatDate(date) {
      return dayjs(date).format("DD/MM/YYYY");
    },

    formatMontant(montant) {
      return new Intl.NumberFormat('fr-MG').format(montant) + ' Ar';
    },

    getTypeImpotNom(decl) {
      if (typeof decl.type_impot === 'string') {
        return decl.type_impot;
      } else if (decl.type_impot && decl.type_impot.nom) {
        return decl.type_impot.nom;
      } else if (decl.nom) {
        return decl.nom;
      }
      return 'Impôt';
    },

    async loadDeclarations() {
      if (!this.idContribuable) return;
      
      this.loading = true;
      try {
        const res = await axios.get(`/api/impots/${this.idContribuable}`);
        
        if (Array.isArray(res.data)) {
          this.declarations = res.data;
        } else if (res.data.data) {
          this.declarations = res.data.data;
        } else if (res.data.declarations) {
          this.declarations = res.data.declarations;
        }
      } catch (e) {
        console.error("Erreur chargement déclarations :", e);
        this.declarations = [];
      } finally {
        this.loading = false;
      }
    },

    async payer(idDeclaration) {
      try {
        await axios.post(`/api/declarations/${this.idContribuable}/payer/${idDeclaration}`);
        this.loadDeclarations();
      } catch (e) {
        console.error("Erreur paiement :", e);
      }
    },
  },
};
</script>