<template>
  <div class="login-container">
    <h2>Connexion Contribuable</h2>
    <form @submit.prevent="submitNIF" class="form">
      <label for="nif">Numéro NIF :</label>
      <input type="text" id="nif" v-model="nif" placeholder="Entrez votre NIF" required />
      <button type="submit">Se connecter</button>
    </form>
    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "LoginContribuable",
  data() {
    return {
      nif: "",
      error: ""
    };
  },
  methods: {
    async submitNIF() {
      try {
        this.error = "";
        const res = await axios.get(`http://127.0.0.1:8000/api/contribuables/nif/${this.nif}`);
        const contribuable = res.data;
        localStorage.setItem("contribuable", JSON.stringify(contribuable));
        this.$router.push(`/impots/${contribuable.id_contribuable}`);
      } catch (err) {
        console.error(err);
        this.error = "NIF invalide ou contribuable non trouvé.";
      }
    }
  }
};
</script>

<style>
.login-container {
  max-width: 400px;
  margin: 50px auto;
  padding: 30px;
  border: 1px solid #ccc;
  border-radius: 8px;
  text-align: center;
  background-color: #f9f9f9;
}
.form { display: flex; flex-direction: column; gap: 12px; margin-top: 20px; }
input { padding: 8px; border-radius: 4px; border: 1px solid #aaa; }
button { padding: 10px; background: #1e88e5; color: white; border: none; cursor: pointer; border-radius: 4px; }
.error { color: red; margin-top: 10px; }
</style>
