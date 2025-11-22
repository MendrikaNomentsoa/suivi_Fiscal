<template>
  <div>
    <h2>Formulaire de Déclaration IRSA</h2>
    <table border="1">
      <thead>
        <tr>
          <th>Num Ligne</th>
          <th>Description</th>
          <th>Valeur</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="ligne in lignes" :key="ligne.num_ligne">
          <td>{{ ligne.num_ligne }}</td>
          <td>{{ ligne.desc_ligne }}</td>
          <td>
            <input
              v-if="ligne.type_ligne === 'LIGNE DE SAISIE'"
              type="number"
              v-model.number="ligne.valeur"
              @input="calculerLignes"
            />
            <span v-else>{{ ligne.valeur.toFixed(2) }}</span>
          </td>
        </tr>
      </tbody>
    </table>

    <button @click="submitDeclaration">Valider la déclaration</button>
  </div>
</template>

<script>
export default {
  props: ['idContribuable', 'idTypeImpot'],
  data() {
    return {
      lignes: [
        { num_ligne: 5, desc_ligne: 'Nombre de travailleurs', type_ligne: 'LIGNE DE SAISIE', valeur: 0 },
        { num_ligne: 10, desc_ligne: 'Rémunération nette imposable', type_ligne: 'LIGNE DE SAISIE', valeur: 0 },
        { num_ligne: 15, desc_ligne: 'Impôt correspondant (selon barème IRSA)', type_ligne: 'LIGNE DE SAISIE', valeur: 0 },
        { num_ligne: 19, desc_ligne: 'Régularisations', type_ligne: 'LIGNE DE SAISIE', valeur: 0 },
        { num_ligne: 20, desc_ligne: 'Impôt net à payer', type_ligne: 'LIGNE DE CALCUL', valeur: 0, formule: () => {
            const l15 = this.lignes.find(l => l.num_ligne === 15).valeur;
            const l19 = this.lignes.find(l => l.num_ligne === 19).valeur;
            return l15 - l19;
        }},
        // Ajoute les autres lignes de manière similaire...
      ]
    };
  },
  methods: {
    calculerLignes() {
      this.lignes.forEach(ligne => {
        if (ligne.type_ligne === 'LIGNE DE CALCUL' && ligne.formule) {
          ligne.valeur = ligne.formule();
        }
      });
    },
    submitDeclaration() {
      // Ici tu envoies `this.lignes` vers ton backend
      console.log('Déclaration envoyée', this.lignes);
    }
  },
  mounted() {
    this.calculerLignes(); // calcul initial
  }
};
</script>
