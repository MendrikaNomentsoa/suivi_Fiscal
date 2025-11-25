<template>
  <div class="container">
    <h2>Simulation du montant à payer</h2>

    <label>Type d'impôt :</label>
    <select v-model="idTypeImpot">
      <option value="1">IRSA</option>
      <option value="2">IS</option>
    </select>

    <!-- Formulaire IRSA -->
    <div v-if="idTypeImpot == 1">
      <label>Rémunération totale :</label>
      <input type="number" v-model.number="form.remuneration" />

      <label>Avantages en nature :</label>
      <input type="number" v-model.number="form.avantages" />

      <label>Date limite de paiement :</label>
      <input type="date" v-model="form.date_limite" />
    </div>

    <!-- Formulaire IS -->
    <div v-else>
      <label>Bénéfice imposable :</label>
      <input type="number" v-model.number="form.benefice" />

      <label>Date limite de paiement :</label>
      <input type="date" v-model="form.date_limite" />
    </div>

    <button @click="calculer">Simuler</button>

    <div v-if="montant !== null" class="result">
      <p>Montant de l'impôt : <strong>{{ montant.toLocaleString() }} Ar</strong></p>

      <p v-if="penalite > 0">Pénalité de retard : {{ penalite.toLocaleString() }} Ar</p>
      <p v-if="interet > 0">Intérêt de retard : {{ interet.toLocaleString() }} Ar</p>

      <p v-if="penalite + interet > 0">
        <strong>Total à payer : {{ (montant + penalite + interet).toLocaleString() }} Ar</strong>
      </p>
    </div>
  </div>
</template>

<script>
import dayjs from "dayjs";

export default {
  props: ["idContribuable"],
  data() {
    return {
      idTypeImpot: 1,
      form: {
        remuneration: 0,
        avantages: 0,
        benefice: 0,
        date_limite: null,
      },
      montant: null,
      penalite: 0,
      interet: 0,
    };
  },

  methods: {
    calculer() {
      // CALCUL IRSA
      if (this.idTypeImpot == 1) {
        const total = Number(this.form.remuneration) + Number(this.form.avantages);
        let irsa = 0;

        if (total <= 350000) irsa = 0;
        else if (total <= 400000) irsa = (total - 350000) * 0.05;
        else if (total <= 500000) irsa = (total - 400000) * 0.10 + 2500;
        else if (total <= 600000) irsa = (total - 500000) * 0.15 + 12500;
        else irsa = (total - 600000) * 0.20 + 27500;

        this.montant = irsa;
      }

      // CALCUL IS
      else {
        const benefice = Number(this.form.benefice);
        // IS Madagascar = 5% CA ou 20% bénéfice selon régime
        this.montant = benefice * 0.20;
      }

      // CALCUL RETARD
      this.penalite = 0;
      this.interet = 0;

      if (this.form.date_limite) {
        const today = dayjs();
        const limite = dayjs(this.form.date_limite);

        if (today.isAfter(limite)) {
          const mois_retard = today.diff(limite, "month");
          this.penalite = this.idTypeImpot == 1 ? 100000 : 20000;
          this.interet = this.montant * 0.01 * mois_retard;
        }
      }
    },
  },
};
</script>

<style scoped>
.container {
  max-width: 500px;
  margin: auto;
}
input,
select {
  width: 100%;
  margin-top: 5px;
  margin-bottom: 10px;
  padding: 5px;
}
button {
  padding: 8px 12px;
  background-color: #1e90ff;
  color: white;
  border: none;
  margin-top: 10px;
}
.result {
  margin-top: 20px;
  font-weight: bold;
}
</style>
