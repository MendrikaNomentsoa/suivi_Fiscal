<template>
  <div class="container">
    <h2>Déclarer un impôt</h2>

    <!-- FORMULAIRE IRSA -->
    <form v-if="idTypeImpot == 1" @submit.prevent="validerIRSA">
      <h3>Déclaration IRSA</h3>

      <label>Nombre de travailleurs :</label>
      <input type="number" v-model.number="form.nb_travailleurs" required />

      <label>Rémunération totale :</label>
      <input type="number" v-model.number="form.remuneration" required />

      <label>Avantages en nature :</label>
      <input type="number" v-model.number="form.avantages" required />

      <label>Régularisations :</label>
      <input type="number" v-model.number="form.regularisations" />

      <button type="submit">Valider IRSA</button>
    </form>

    <!-- FORMULAIRE IS -->
    <form v-else-if="idTypeImpot == 2" @submit.prevent="validerIS">
      <h3>Déclaration IS</h3>

      <label>Chiffre d'affaires :</label>
      <input type="number" v-model.number="form.chiffreAffaires" required />

      <button type="submit">Valider IS</button>
    </form>

    <!-- IMPÔT NON SUPPORTE -->
    <div v-else>
      <p>Type d'impôt inconnu.</p>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: ["idContribuable", "idTypeImpot"],

  data() {
    return {
      form: {
        nb_travailleurs: 0,
        remuneration: 0,
        avantages: 0,
        regularisations: 0,
        chiffreAffaires: 0
      }
    };
  },

  methods: {
    async validerIRSA() {
      try {
        // Envoyer sous la forme attendue par Laravel : "data" obligatoire
        const payload = {
          id_contribuable: this.idContribuable,
          id_type_impot: 1,
          statut: "brouillon",
          data: {
            5: this.form.avantages,         // avantages en nature
            10: this.form.remuneration,     // rémunération nette
            19: this.form.regularisations   // régularisations
          }
        };

        await axios.post("http://127.0.0.1:8000/api/declarations", payload);

        this.$router.push(`/declarations/${this.idContribuable}/1`);
      } catch (error) {
        console.error(error);
        alert("Erreur lors de la déclaration IRSA");
      }
    },

    async validerIS() {
      try {
        const payload = {
          id_contribuable: this.idContribuable,
          id_type_impot: 2,
          statut: "brouillon",
          data: {
            1: this.form.chiffreAffaires
          }
        };

        await axios.post("http://127.0.0.1:8000/api/declarations", payload);

        this.$router.push(`/declarations/${this.idContribuable}/2`);
      } catch (error) {
        console.error(error);
        alert("Erreur lors de la déclaration IS");
      }
    }
  }
};
</script>

<style>
.container {
  max-width: 500px;
  margin: auto;
}
label {
  display: block;
  margin-top: 10px;
}
input {
  width: 100%;
  padding: 8px;
  margin-top: 5px;
}
button {
  margin-top: 15px;
  padding: 10px 16px;
  background-color: blue;
  color: white;
  border: none;
  cursor: pointer;
}
</style>
