<template>
  <div class="container">
    <h2>Impôts à déclarer</h2>

    <ul>
      <li v-for="impot in impots" :key="impot.id_type_impot">
        {{ impot.nom }} - 
        <span v-if="impot.fait" class="fait">Déjà déclaré</span>
        <span v-else class="non-fait">Non déclaré</span>
        <button v-if="!impot.fait" @click="declarer(impot.id_type_impot)">Déclarer</button>
      </li>
    </ul>
  </div>
  <button @click="$router.push(`/litige/${idContribuable}`)">
  Déposer un litige
</button>

</template>

<script>
import axios from "axios";

export default {
  props: ['idContribuable'],
  data() {
    return {
      impots: [],
    };
  },
  mounted() {
    this.loadImpots();
  },
  methods: {
    async loadImpots() {
      const res = await axios.get(`http://127.0.0.1:8000/api/impots/${this.idContribuable}`);
      this.impots = res.data;
    },
    declarer(idTypeImpot) {
      // Redirection vers le formulaire de déclaration
      this.$router.push(`/declarer/${this.idContribuable}/${idTypeImpot}`);
    }
  }
};
</script>

<style>
.container {
  max-width: 600px;
  margin: 30px auto;
}

.fait {
  color: green;
  font-weight: bold;
}

.non-fait {
  color: red;
  font-weight: bold;
}

button {
  margin-left: 15px;
  padding: 5px 10px;
}
</style>
