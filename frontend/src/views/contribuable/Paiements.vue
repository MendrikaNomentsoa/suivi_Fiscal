<template>
  <div class="min-h-screen bg-gray-100 p-6 flex justify-center items-start">
    <div class="bg-white rounded-2xl shadow p-6 w-full max-w-md mt-10">
      
      <h1 class="text-2xl font-bold mb-6 text-gray-800 text-center">Confirmation du Paiement</h1>

      <div v-if="loading" class="text-center py-10 text-gray-500">Chargement...</div>

      <div v-else>
        <p class="mb-3"><strong>Type d'impôt :</strong> {{ declaration.type_impot }}</p>
        <p class="mb-3"><strong>Montant :</strong> {{ formatMontant(declaration.montant) }}</p>
        <p class="mb-3"><strong>Date du paiement :</strong> {{ formatDate(datePaiement) }}</p>
        <p class="mb-3"><strong>Statut actuel :</strong> 
          <span 
            :class="declaration.statut_paiement==='paye' 
              ? 'text-green-600 font-semibold' 
              : 'text-red-600 font-semibold'"
          >
            {{ declaration.statut_paiement==='paye' ? 'Payé' : 'Non payé' }}
          </span>
        </p>

        <div class="flex justify-end gap-3 mt-6">
          <button @click="$router.back()" 
                  class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">
            Annuler
          </button>

          <button @click="confirmerPaiement" 
                  class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg"
                  :disabled="declaration.statut_paiement==='paye'">
            Confirmer
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: {
    idContribuable: { type: [Number, String], required: true },
    idDeclaration: { type: [Number, String], required: true }
  },
  data() {
    return {
      declaration: {},
      datePaiement: new Date(),
      loading: true
    };
  },
  async mounted() {
    await this.loadDeclaration();
  },
  methods: {
// Confirmer le paiement
async confirmerPaiement() {
  try {
    // ✅ Route de paiement correcte
    await axios.post(
      `/api/declarations/${this.idContribuable}/payer/${this.modalData.id_declaration}`
    );

    this.showModal = false;
    await this.loadDeclarations(); // Met à jour le statut à "paye"
  } catch (err) {
    console.error("Erreur paiement :", err);
  }
},

// Charger les déclarations pour le tableau
async loadDeclarations() {
  try {
    const res = await axios.get(`/api/impots/${this.idContribuable}`);
    this.declarations = res.data;
  } catch (err) {
    console.error("Erreur chargement déclarations :", err);
  }
}
,

    formatDate(d) {
      return new Date(d).toLocaleDateString("fr-FR");
    },

    formatMontant(m) {
      return Number(m).toLocaleString("fr-FR") + " Ar";
    }

  }
};
</script>

<style scoped>
/* Animation simple pour l'apparition */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
