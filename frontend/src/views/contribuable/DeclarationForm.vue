<template>
  <div class="declaration-container">
    <div class="header">
      <button 
        @click="$router.push(`/impots/${idContribuable}`)"
        class="back-button"
      >
        ← Retour à la liste des impôts
      </button>
      <h2>📋 Déclarer un impôt</h2>
    </div>

    <!-- Sélection du type d'impôt (si pas passé en prop) -->
    <div v-if="!idTypeImpot" class="form-group">
      <label>Type d'impôt :</label>
      <select v-model.number="selectedTypeImpot" class="form-control">
        <option :value="1">IRSA (Impôt sur les Revenus Salariaux)</option>
        <option :value="2">IS (Impôt sur les Sociétés)</option>
      </select>
    </div>

    <!-- Message de succès/erreur -->
    <div v-if="message.text" :class="['alert', message.type]">
      {{ message.text }}
    </div>

    <!-- FORMULAIRE IRSA -->
    <div v-if="currentTypeImpot == 1" class="form-card">
      <h3>💼 Déclaration IRSA</h3>
      
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
        <small>Montant total des salaires versés</small>
      </div>

      <div class="form-group">
        <label>Avantages en nature (Ar) :</label>
        <input 
          type="number" 
          v-model.number="formIRSA.avantages" 
          class="form-control"
          min="0"
        />
        <small>Logement, voiture de fonction, etc.</small>
      </div>

      <div class="form-group">
        <label>Régularisations (Ar) :</label>
        <input 
          type="number" 
          v-model.number="formIRSA.regularisations" 
          class="form-control"
          min="0"
        />
        <small>Montant déjà payé ou déduction</small>
      </div>

      <!-- Aperçu du calcul IRSA -->
      <div class="calcul-preview">
        <p><strong>Base imposable :</strong> {{ baseIRSA.toLocaleString() }} Ar</p>
        <p><strong>IRSA estimé :</strong> {{ irsaEstime.toLocaleString() }} Ar</p>
      </div>

      <button 
        @click="validerIRSA" 
        :disabled="loading"
        class="btn btn-primary"
      >
        {{ loading ? '⏳ Envoi en cours...' : '✅ Valider la déclaration IRSA' }}
      </button>
    </div>

    <!-- FORMULAIRE IS -->
    <div v-else-if="currentTypeImpot == 2" class="form-card">
      <h3>🏢 Déclaration IS (Impôt sur les Sociétés)</h3>
      
      <div class="form-group">
        <label>Chiffre d'affaires annuel (Ar) :</label>
        <input 
          type="number" 
          v-model.number="formIS.chiffreAffaires" 
          class="form-control"
          required 
          min="0"
        />
        <small>Montant total des ventes de l'exercice fiscal</small>
      </div>

      <div class="form-group">
        <label>Charges déductibles (Ar) :</label>
        <input 
          type="number" 
          v-model.number="formIS.charges" 
          class="form-control"
          min="0"
        />
        <small>Salaires, loyers, fournitures, etc.</small>
      </div>

      <div class="form-group">
        <label>Autres revenus (Ar) :</label>
        <input 
          type="number" 
          v-model.number="formIS.autresRevenus" 
          class="form-control"
          min="0"
        />
        <small>Revenus financiers, subventions, etc.</small>
      </div>

      <!-- Aperçu du calcul IS -->
      <div class="calcul-preview">
        <p><strong>Chiffre d'affaires :</strong> {{ formIS.chiffreAffaires.toLocaleString() }} Ar</p>
        <p><strong>Charges déductibles :</strong> {{ formIS.charges.toLocaleString() }} Ar</p>
        <p><strong>Autres revenus :</strong> {{ formIS.autresRevenus.toLocaleString() }} Ar</p>
        <p class="divider"></p>
        <p><strong>Bénéfice imposable :</strong> {{ beneficeIS.toLocaleString() }} Ar</p>
        <p><strong>IS estimé (15%) :</strong> {{ isEstime.toLocaleString() }} Ar</p>
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
      // Si idTypeImpot est passé en prop, on l'utilise, sinon on utilise selectedTypeImpot
      selectedTypeImpot: this.idTypeImpot ? Number(this.idTypeImpot) : 1,
      loading: false,
      message: {
        type: '',
        text: ''
      },
      // Formulaire IRSA
      formIRSA: {
        nb_travailleurs: 0,
        remuneration: 0,
        avantages: 0,
        regularisations: 0
      },
      // Formulaire IS
      formIS: {
        chiffreAffaires: 0,
        charges: 0,
        autresRevenus: 0
      }
    };
  },

  computed: {
    // Type d'impôt courant (prop ou sélection)
    currentTypeImpot() {
      return this.idTypeImpot ? Number(this.idTypeImpot) : this.selectedTypeImpot;
    },

    // ============ CALCULS IRSA ============
    baseIRSA() {
      return this.formIRSA.remuneration + this.formIRSA.avantages;
    },
    
    irsaEstime() {
      const base = this.baseIRSA;
      let irsa = 0;
      
      if (base <= 350000) {
        irsa = 0;
      } else if (base <= 400000) {
        irsa = (base - 350000) * 0.05;
      } else if (base <= 500000) {
        irsa = 2500 + (base - 400000) * 0.10;
      } else if (base <= 600000) {
        irsa = 2500 + 10000 + (base - 500000) * 0.15;
      } else {
        irsa = 2500 + 10000 + 15000 + (base - 600000) * 0.20;
      }
      
      return Math.max(0, irsa - this.formIRSA.regularisations);
    },

    // ============ CALCULS IS ============
    beneficeIS() {
      return this.formIS.chiffreAffaires + this.formIS.autresRevenus - this.formIS.charges;
    },
    
    isEstime() {
      return Math.max(0, this.beneficeIS * 0.15);
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
          statut: "brouillon",
          data: {
            remuneration: this.formIRSA.remuneration,
            avantages: this.formIRSA.avantages,
            regularisations: this.formIRSA.regularisations
          }
        };

        console.log('📤 Envoi IRSA:', payload);

        const response = await axios.post("http://127.0.0.1:8000/api/declarations", payload);
        
        console.log('✅ Réponse serveur:', response.data);

        this.message = {
          type: 'success',
          text: `✅ Déclaration IRSA créée avec succès! Montant: ${response.data.declaration.montant.toLocaleString()} Ar`
        };

        // Réinitialiser le formulaire IRSA
        this.formIRSA = {
          nb_travailleurs: 0,
          remuneration: 0,
          avantages: 0,
          regularisations: 0
        };

        // Redirection vers la liste des déclarations IRSA
        setTimeout(() => {
          this.$router.push(`/declarations/${this.idContribuable}/1`);
        }, 2000);

      } catch (error) {
        console.error('❌ Erreur IRSA:', error);
        console.error('📋 Détails:', error.response?.data);
        
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
          statut: "brouillon",
          data: {
            chiffre_affaires: this.formIS.chiffreAffaires,
            charges: this.formIS.charges,
            autres_revenus: this.formIS.autresRevenus
          }
        };

        console.log('📤 Envoi IS:', payload);

        const response = await axios.post("http://127.0.0.1:8000/api/declarations", payload);
        
        console.log('✅ Réponse serveur:', response.data);

        this.message = {
          type: 'success',
          text: `✅ Déclaration IS créée avec succès! Montant: ${response.data.declaration.montant.toLocaleString()} Ar`
        };

        // Réinitialiser le formulaire IS
        this.formIS = {
          chiffreAffaires: 0,
          charges: 0,
          autresRevenus: 0
        };

        // Redirection vers la liste des déclarations IS
        setTimeout(() => {
          this.$router.push(`/declarations/${this.idContribuable}/2`);
        }, 2000);

      } catch (error) {
        console.error('❌ Erreur IS:', error);
        console.error('📋 Détails:', error.response?.data);
        
        this.message = {
          type: 'error',
          text: '❌ Erreur: ' + (error.response?.data?.message || error.message)
        };
      } finally {
        this.loading = false;
      }
    }
  },

  mounted() {
    console.log('📍 DeclarationForm mounted:', {
      idContribuable: this.idContribuable,
      idTypeImpot: this.idTypeImpot,
      currentTypeImpot: this.currentTypeImpot
    });
  }
};
</script>

<style scoped>
.declaration-container {
  max-width: 700px;
  margin: 30px auto;
  padding: 20px;
}

.header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 20px;
  border-radius: 10px 10px 0 0;
  margin-bottom: 20px;
}

.header h2 {
  margin: 10px 0 0 0;
  font-size: 24px;
}

.back-button {
  background: rgba(255, 255, 255, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: white;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.3s;
  margin-bottom: 10px;
}

.back-button:hover {
  background: rgba(255, 255, 255, 0.3);
}

.form-card {
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 25px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.form-card h3 {
  color: #333;
  margin-bottom: 20px;
  font-size: 20px;
  border-bottom: 2px solid #667eea;
  padding-bottom: 10px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-weight: 600;
  color: #555;
  margin-bottom: 8px;
}

.form-control {
  width: 100%;
  padding: 12px;
  border: 2px solid #e0e0e0;
  border-radius: 6px;
  font-size: 16px;
  transition: border-color 0.3s;
  box-sizing: border-box;
}

.form-control:focus {
  outline: none;
  border-color: #667eea;
}

.form-group small {
  display: block;
  color: #888;
  font-size: 13px;
  margin-top: 5px;
}

.calcul-preview {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 6px;
  margin: 20px 0;
  border-left: 4px solid #667eea;
}

.calcul-preview p {
  margin: 8px 0;
  color: #555;
}

.calcul-preview .divider {
  border-top: 1px solid #ddd;
  margin: 12px 0;
}

.btn {
  padding: 14px 24px;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  width: 100%;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.alert {
  padding: 15px;
  border-radius: 6px;
  margin-bottom: 20px;
  font-weight: 500;
}

.alert.success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.alert.error {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.alert.warning {
  background: #fff3cd;
  color: #856404;
  border: 1px solid #ffeeba;
}
</style>