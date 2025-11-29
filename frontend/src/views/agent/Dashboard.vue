<template>
  <div class="min-h-screen bg-gray-100">

    <!-- HEADER -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Tableau de bord — Agent Fiscal</h1>

        <div class="flex items-center gap-4">
          <div class="text-right">
            <p class="font-semibold text-gray-800">{{ user.prenom }} {{ user.nom }}</p>
            <p class="text-sm text-gray-500">{{ getRoleLabel(user.role) }}</p>
          </div>

          <button 
            @click="logout"
            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg shadow">
            Déconnexion
          </button>
        </div>
      </div>
    </header>

    <!-- SECTIONS -->
    <main class="max-w-7xl mx-auto px-6 py-8">

      <!-- TITRE -->
      <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Bienvenue, Agent 👋</h2>
        <p class="text-gray-600">Voici un aperçu rapide de l’activité fiscale.</p>
      </div>

      <!-- STAT POURCENTAGE -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-10">

        <div class="card-stat border-blue-500">
          <div>
            <p class="label">Contribuables</p>
            <p class="value">{{ stats.totalContribuables }}</p>
          </div>
          <span class="icon">👥</span>
        </div>

        <div class="card-stat border-green-500">
          <div>
            <p class="label">Déclarations Validées</p>
            <p class="value text-green-600">{{ stats.declarationsValidees }}</p>
          </div>
          <span class="icon">✅</span>
        </div>

        <div class="card-stat border-orange-500">
          <div>
            <p class="label">En Attente</p>
            <p class="value text-orange-500">{{ stats.declarationsEnAttente }}</p>
          </div>
          <span class="icon">⏳</span>
        </div>

        <div class="card-stat border-red-500">
          <div>
            <p class="label">Litiges</p>
            <p class="value text-red-500">{{ stats.litigesOuverts }}</p>
          </div>
          <span class="icon">⚠️</span>
        </div>

      </div>

      <!-- ACTIONS RAPIDES -->
      <h3 class="text-xl font-semibold text-gray-800 mb-4">Actions rapides</h3>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        <div @click="goToPaiements"
          class="card-action">
          <span class="action-icon">💰</span>
          <h4 class="action-title">Gérer les Paiements</h4>
          <p class="action-desc">Suivi et validation des paiements fiscaux.</p>
        </div>

        <div @click="goToLitiges" class="card-action">
          <span class="action-icon">⚖️</span>
          <h4 class="action-title">Litiges</h4>
          <p class="action-desc">Consulter et traiter les litiges.</p>
          <span v-if="stats.litigesEnAttente > 0" class="notif">
            {{ stats.litigesEnAttente }}
          </span>
        </div>

        <div @click="goToDeclarations" class="card-action">
          <span class="action-icon">📋</span>
          <h4 class="action-title">Déclarations</h4>
          <p class="action-desc">Toutes les déclarations fiscales.</p>
        </div>

        <div @click="goToContribuables" class="card-action">
          <span class="action-icon">👤</span>
          <h4 class="action-title">Contribuables</h4>
          <p class="action-desc">Liste complète des contribuables.</p>
        </div>

      </div>
    </main>

  </div>
</template>

<script>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default {
  name: "AgentDashboard",

  setup() {
    const router = useRouter();

    const user = ref({
      prenom: localStorage.getItem("prenom"),
      nom: localStorage.getItem("nom"),
      role: localStorage.getItem("userType")
    });

    const stats = ref({
      totalContribuables: 0,
      declarationsValidees: 0,
      declarationsEnAttente: 0,
      litigesOuverts: 0,
      litigesEnAttente: 0,
    });

    const loadStats = async () => {
      try {
        const [c, d, l] = await Promise.all([
          axios.get("/api/agent/contribuables"),
          axios.get("/api/declarations"),
          axios.get("/api/agent/litiges")
        ]);

        stats.value = {
          totalContribuables: c.data.length,
          declarationsValidees: d.data.filter(x => x.statut === "validee").length,
          declarationsEnAttente: d.data.filter(x => x.statut !== "validee").length,
          litigesOuverts: l.data.filter(x => x.statut !== "resolu").length,
          litigesEnAttente: l.data.filter(x => x.statut === "en_attente").length,
        };
      } catch (e) {
        console.error("Erreur stats", e);
      }
    };

    const logout = () => {
      localStorage.clear();
      router.push("/agent/login");
    };

    const getRoleLabel = role =>
      ({ agent: "Agent Fiscal", admin: "Administrateur", superviseur: "Superviseur" }[role] || role);

    // REDIRECTIONS
    const goToPaiements = () => router.push("/agent/paiements");
    const goToLitiges = () => router.push("/agent/litigesAgent/0");
    const goToDeclarations = () => router.push("/agent/dashboard?tab=declarations");
    const goToContribuables = () => router.push("/agent/contribuables");

    onMounted(() => loadStats());

    return {
      user,
      stats,
      logout,
      goToPaiements,
      goToLitiges,
      goToDeclarations,
      goToContribuables,
      getRoleLabel
    };
  },
};
</script>

<style scoped>
.card-stat {
  @apply bg-white rounded-lg shadow p-6 border-l-4 flex justify-between items-center;
}
.label {
  @apply text-sm text-gray-500;
}
.value {
  @apply text-2xl font-bold text-gray-900;
}
.icon {
  @apply text-4xl;
}

.card-action {
  @apply bg-white shadow rounded-xl p-6 hover:shadow-lg transition-all cursor-pointer relative;
}
.action-icon {
  @apply text-4xl block mb-4;
}
.action-title {
  @apply font-bold text-gray-900 text-lg;
}
.action-desc {
  @apply text-gray-600 text-sm;
}
.notif {
  @apply absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full;
}
</style>
