<template>
  <div class="container">
    <h2>Nouvelle Déclaration</h2>

    <form @submit.prevent="submitForm" class="form">

      <!-- TYPE IMPOT -->
      <label>Type d'impôt</label>
      <select v-model="form.id_type_impot" required>
        <option value="">-- Sélectionner --</option>
        <option v-for="t in types" :key="t.id" :value="t.id">
          {{ t.nom }}
        </option>
      </select>

      <!-- CONTRIBUABLE -->
      <label>Contribuable</label>
      <select v-model="form.id_contribuable" required>
        <option value="">-- Sélectionner --</option>
        <option v-for="c in contribuables" :key="c.id" :value="c.id">
          {{ c.nom }} {{ c.prenom }}
        </option>
      </select>

      <!-- MONTANT -->
      <label>Montant</label>
      <input type="number" v-model="form.montant" required />

      <!-- STATUT -->
      <label>Statut</label>
      <select v-model="form.statut">
        <option value="brouillon">Brouillon</option>
        <option value="validee">Validée</option>
      </select>

      <button type="submit">Créer la déclaration</button>
    </form>

    <hr />

    <h3>Liste des Déclarations</h3>
    <table v-if="declarations.length">
      <thead>
        <tr>
          <th>Type Impôt</th>
          <th>Contribuable</th>
          <th>Montant</th>
          <th>Statut</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="d in declarations" :key="d.id">
          <td>{{ d.type_impot.nom }}</td>
          <td>{{ d.contribuable.nom }} {{ d.contribuable.prenom }}</td>
          <td>{{ d.montant }}</td>
          <td>{{ d.statut }}</td>
        </tr>
      </tbody>
    </table>
    <p v-else>Aucune déclaration disponible.</p>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      types: [],
      contribuables: [],
      declarations: [],
      form: {
        id_type_impot: "",
        id_contribuable: "",
        montant: "",
        statut: "brouillon",
      },
    };
  },

  mounted() {
    this.loadTypes();
    this.loadContribuables();
    this.loadDeclarations();
  },

  methods: {
    async loadTypes() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/type-impots");
        this.types = res.data;
      } catch (error) {
        console.error("Erreur lors du chargement des types d'impôts:", error);
      }
    },

    async loadContribuables() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/contribuables");
        this.contribuables = res.data;
      } catch (error) {
        console.error("Erreur lors du chargement des contribuables:", error);
      }
    },

    async loadDeclarations() {
      try {
        const res = await axios.get(
          "http://127.0.0.1:8000/api/declarations"
        );
        this.declarations = res.data;
      } catch (error) {
        console.error("Erreur lors du chargement des déclarations:", error);
      }
    },

    async submitForm() {
      try {
        await axios.post("http://127.0.0.1:8000/api/declarations", this.form);
        alert("Déclaration créée avec succès !");
        this.resetForm();
        this.loadDeclarations(); // Recharge la liste après création
      } catch (error) {
        console.error("Erreur lors de la création de la déclaration:", error);
        alert("Impossible de créer la déclaration.");
      }
    },

    resetForm() {
      this.form.id_type_impot = "";
      this.form.id_contribuable = "";
      this.form.montant = "";
      this.form.statut = "brouillon";
    },
  },
};
</script>

<style>
.container {
  max-width: 600px;
  margin: auto;
  padding: 20px;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

label {
  font-weight: bold;
}

input,
select {
  padding: 8px;
  border-radius: 4px;
  border: 1px solid #ccc;
}

button {
  padding: 10px;
  background: #1e88e5;
  color: white;
  border: none;
  cursor: pointer;
  border-radius: 4px;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}

th,
td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: left;
}
</style>
