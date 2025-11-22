<template>
  <div>
    <h1>Test API Laravel</h1>
    <button @click="testAPI">Tester API</button>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: "testAPI",
  methods: {
    testAPI() {
      axios.get("http://127.0.0.1:8000/api/contribuables")
        .then(res => {
          console.log("Réponse API :", res.data);
          alert("Vérifie la console pour voir la réponse !");
        })
        .catch(err => {
          console.error("Erreur API :", err);
          alert("Erreur, regarde la console !");
        });
    }
  }
}
</script>
