<template>
  <div>
    <h1>Liste des contribuables</h1>

    <router-link to="/contribuables/new">
      <button>Ajouter un contribuable</button>
    </router-link>

    <table border="1" cellpadding="6" style="margin-top: 20px;">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nom</th>
          <th>Prénom</th>
          <th>Email</th>
          <th>Téléphone</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="c in contribuables" :key="c.id_contribuable">
          <td>{{ c.id_contribuable }}</td>
          <td>{{ c.nom }}</td>
          <td>{{ c.prenom }}</td>
          <td>{{ c.email }}</td>
          <td>{{ c.telephone }}</td>

          <td>
            <button @click="supprimer(c.id_contribuable)">Supprimer</button>
          </td>
        </tr>
      </tbody>
    </table>

  </div>
</template>

<script>
import http from "../api/http";

export default {
  data() {
    return {
      contribuables: []
    };
  },

  mounted() {
    http.get("/contribuables")
      .then(res => this.contribuables = res.data)
      .catch(err => console.error(err));
  },

  methods: {
    supprimer(id) {
      if (confirm("Voulez-vous vraiment supprimer ce contribuable ?")) {
        http.delete(`/contribuables/${id}`)
          .then(() => {
            this.contribuables = this.contribuables.filter(c => c.id_contribuable !== id);
          })
          .catch(err => console.error(err));
      }
    }
  }
}
</script>
