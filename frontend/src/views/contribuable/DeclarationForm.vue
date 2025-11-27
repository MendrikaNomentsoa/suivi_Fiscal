<template>
  <div class="declaration-container">
    <div class="header">
      <button 
        @click="$router.push(`/impots/${idContribuable}`)"
        class="back-button"
      >
        ← Retour à la liste des impôts
      </button>
      <h2> Déclarer un impôt</h2>
    </div>

    <!-- Message de succès/erreur -->
    <div v-if="message.text" :class="['alert', message.type]">
      {{ message.text }}
    </div>

    <!-- Sélection du type d'impôt (si pas passé en prop) -->
    <div v-if="!idTypeImpot" class="form-group">
      <label>Type d'impôt :</label>
      <select v-model.number="selectedTypeImpot" class="form-control">
        <option :value="1">IRSA (Impôt sur les Revenus Salariaux)</option>
        <option :value="2">IS (Impôt sur les Sociétés)</option>
      </select>
    </div>

    <!-- FORMULAIRE IRSA -->
    <div v-if="currentTypeImpot == 1" class="form-card">
      <h3>Déclaration IRSA</h3>
      
      <div class="form-group">
        <label>Nombre de travailleurs :</label>
        <input 
          type="number" 
          v-model.number="formIRSA.nb_travailleurs" 
          class="form-control"
          min="0"
        />
      </div>

      <div class="form-group">
        <label>Rémunération totale (Ar) :</label>
        <input 
          type="number" 
          v-model.number="formIRSA.remuneration" 
          class="form-control"
          required 
          min="0"
        />
      </div>

      <div class="form-group">
        <label>Avantages en nature (Ar) :</label>
        <input 
          type="number" 
          v-model.number="formIRSA.avantages" 
          class="form-control"
          min="0"
        />
      </div>

      <div class="form-group">
        <label>Régularisations (Ar) :</label>
        <input 
          type="number" 
          v-model.number="formIRSA.regularisations" 
          class="form-control"
          min="0"
        />
      </div>

      <button 
        @click="validerIRSA" 
        :disabled="loading"
        class="btn btn-primary"
      >
        {{ loading ? '⏳ Envoi en cours...' : ' Valider la déclaration IRSA' }}
      </button>
    </div>

    <!-- FORMULAIRE IS -->
    <div v-else-if="currentTypeImpot == 2" class="form-card">
      <h3>Déclaration IS (Impôt sur les Sociétés)</h3>
      
      <div class="form-group">
        <label>Chiffre d'affaires annuel (Ar) :</label>
        <input 
          type="number" 
          v-model.number="formIS.chiffreAffaires" 
          class="form-control"
          required 
          min="0"
        />
      </div>

      <div class="form-group">
        <label>Charges déductibles (Ar) :</label>
        <input 
          type="number" 
          v-model.number="formIS.charges" 
          class="form-control"
          min="0"
        />
      </div>

      <div class="form-group">
        <label>Autres revenus (Ar) :</label>
        <input 
          type="number" 
          v-model.number="formIS.autresRevenus" 
          class="form-control"
          min="0"
        />
      </div>

      <button 
        @click="validerIS" 
        :disabled="loading"
        class="btn btn-primary"
      >
        {{ loading ? '⏳ Envoi en cours...' : '✅ Valider la déclaration IS' }}
      </button>
    </div>

    <div v-else class="alert warning">
      ⚠️ Type d'impôt non supporté
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: 'DeclarationForm',
  
  props: {
    idContribuable: {
      type: [Number, String],
      required: true
    },
    idTypeImpot: {
      type: [Number, String],
      default: null
    }
  },
  
  data() {
    return {
      selectedTypeImpot: this.idTypeImpot ? Number(this.idTypeImpot) : 1,
      loading: false,
      message: { type: '', text: '' },
      formIRSA: {
        nb_travailleurs: 0,
        remuneration: 0,
        avantages: 0,
        regularisations: 0
      },
      formIS: {
        chiffreAffaires: 0,
        charges: 0,
        autresRevenus: 0
      }
    };
  },

  computed: {
    currentTypeImpot() {
      return this.idTypeImpot ? Number(this.idTypeImpot) : this.selectedTypeImpot;
    }
  },

  methods: {
    async validerIRSA() {
      this.loading = true;
      this.message = { type: '', text: '' };

      try {
        const payload = {
          id_contribuable: Number(this.idContribuable),
          id_type_impot: 1,
          statut: "validee",
          data: {
            remuneration: this.formIRSA.remuneration,
            avantages: this.formIRSA.avantages,
            regularisations: this.formIRSA.regularisations
          }
        };

        const response = await axios.post("/api/declarations", payload);

        this.message = {
          type: 'success',
          text: `✅ Déclaration IRSA créée avec succès! Montant: ${response.data.declaration.montant.toLocaleString()} Ar`
        };

        this.formIRSA = { nb_travailleurs: 0, remuneration: 0, avantages: 0, regularisations: 0 };

        setTimeout(() => {
          this.$router.push(`/declarations/${this.idContribuable}/1`);
        }, 2000);

      } catch (error) {
        this.message = {
          type: 'error',
          text: '❌ Erreur: ' + (error.response?.data?.message || error.message)
        };
      } finally {
        this.loading = false;
      }
    },

    async validerIS() {
      this.loading = true;
      this.message = { type: '', text: '' };

      try {
        const payload = {
          id_contribuable: Number(this.idContribuable),
          id_type_impot: 2,
          statut: "validee",
          data: {
            chiffre_affaires: this.formIS.chiffreAffaires,
            charges: this.formIS.charges,
            autres_revenus: this.formIS.autresRevenus
          }
        };

        const response = await axios.post("/api/declarations", payload);

        this.message = {
          type: 'success',
          text: `✅ Déclaration IS créée avec succès! Montant: ${response.data.declaration.montant.toLocaleString()} Ar`
        };

        this.formIS = { chiffreAffaires: 0, charges: 0, autresRevenus: 0 };

        setTimeout(() => {
          this.$router.push(`/declarations/${this.idContribuable}/2`);
        }, 2000);

      } catch (error) {
        this.message = {
          type: 'error',
          text: '❌ Erreur: ' + (error.response?.data?.message || error.message)
        };
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.declaration-container { max-width: 700px; margin: 30px auto; padding: 20px; }
.header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px 10px 0 0; margin-bottom: 20px; }
.header h2 { margin: 10px 0 0 0; font-size: 24px; }
.back-button { background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: white; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px; transition: all 0.3s; margin-bottom: 10px; }
.back-button:hover { background: rgba(255, 255, 255, 0.3); }
.form-card { background: white; border: 1px solid #e0e0e0; border-radius: 8px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.form-card h3 { color: #333; margin-bottom: 20px; font-size: 20px; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-weight: 600; color: #555; margin-bottom: 8px; }
.form-control { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 16px; transition: border-color 0.3s; box-sizing: border-box; }
.form-control:focus { outline: none; border-color: #667eea; }
.btn { padding: 14px 24px; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; width: 100%; }
.btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.btn-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4); }
.btn:disabled { opacity: 0.6; cursor: not-allowed; }
.alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: 500; }
.alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.alert.warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
</style>
