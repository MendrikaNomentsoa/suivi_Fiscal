<template>
  <div class="container">
    <h2>Déclarations existantes</h2>

    <table v-if="declarations.length > 0">
      <thead>
        <tr>
          <th>Montant</th>
          <th>Date</th>
          <th>Statut</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="dec in declarations" :key="dec.id_declaration">
          <td>{{ dec.montant }}</td>
          <td>{{ dec.date_declaration }}</td>
          <td>{{ dec.statut }}</td>
        </tr>
      </tbody>
    </table>

    <div v-else>
      Aucune déclaration pour cet impôt.
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  props: ['idContribuable', 'idTypeImpot'],
  data() {
    return {
      declarations: []
    }
  },
  mounted() {
    this.loadDeclarations()
  },
  methods: {
    async loadDeclarations() {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/declarations`)
        // Filtrer pour ce contribuable et ce type d'impôt
        this.declarations = res.data.filter(d => d.id_contribuable == this.idContribuable && d.id_type_impot == this.idTypeImpot)
      } catch (error) {
        console.error(error)
      }
    }
  }
}
</script>

<style>
.container {
  max-width: 600px;
  margin: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

th, td {
  border: 1px solid #ddd;
  padding: 8px;
}
</style>
