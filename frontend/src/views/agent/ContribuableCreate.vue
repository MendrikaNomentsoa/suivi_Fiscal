<template>
  <div>
    <h1>Ajouter un contribuable</h1>

    <form @submit.prevent="envoyer">

      <label>Nom :</label>
      <input v-model="form.nom" required />

      <label>Prénom :</label>
      <input v-model="form.prenom" required />

      <label>Email :</label>
      <input v-model="form.email" type="email" required />

      <label>Téléphone :</label>
      <input v-model="form.telephone" />

      <label>Mot de passe :</label>
      <input v-model="form.password" type="password" required />

      <label>Date d'inscription :</label>
      <input v-model="form.date_inscription" type="date" required />

      <button type="submit">Créer</button>
    </form>
  </div>
</template>

<script>
import http from "../../api/http";

export default {
  data() {
    return {
      form: {
        nom: "",
        prenom: "",
        email: "",
        telephone: "",
        password: "",
        date_inscription: "",
      }
    };
  },

  methods: {
    envoyer() {
      http.post("/contribuables", this.form)
        .then(() => {
          alert("Contribuable ajouté !");
          this.$router.push("/contribuables");
        })
        .catch(err => {
          console.error(err);
          alert("Erreur lors de l'ajout");
        });
    }
  }
}
</script>

<style>
form {
  display: flex;
  flex-direction: column;
  width: 300px;
}
label {
  margin-top: 10px;
}
button {
  margin-top: 20px;
}
</style>
