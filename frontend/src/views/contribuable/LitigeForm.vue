<template>
  <div class="container">
    <h2>Déposer un litige</h2>

    <label>Sujet :</label>
    <input type="text" v-model="form.sujet" required />

    <label>Description :</label>
    <textarea v-model="form.description" required></textarea>

    <button @click="envoyerLitige">Envoyer le litige</button>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: ["idContribuable"],

  data() {
    return {
      form: {
        sujet: "",
        description: ""
      }
    };
  },

  methods: {
    async envoyerLitige() {
      try {
        const payload = {
          id_Contribuable: this.idContribuable,
          sujet: this.form.sujet,
          description: this.form.description,
          statut: 'en_attente' // ajouté
        };

        await axios.post("http://127.0.0.1:8000/api/litiges", payload);

        alert("Litige envoyé avec succès !");
        this.$router.push(`/impots/${this.idContribuable}`);
      } catch (err) {
        console.error(err.response.data);
        alert("Erreur lors de l'envoi du litige");
      }
    }
  }
};
</script>
