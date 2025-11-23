<template>
  <div class="page">

    <h1>Déclarations</h1>

    <!-- FORMULAIRE -->
    <DeclarationForm
      :id-contribuable="1"
      :id-type-impot="idTypeImpot"
      @refreshList="refreshList"
    />

    <!-- LISTE DES DECLARATIONS -->
    <DeclarationsList
      ref="list"
      :id-contribuable="1"
      :id-type-impot="idTypeImpot"
    />

  </div>
</template>

<script>
import { useRoute } from "vue-router"
import DeclarationForm from "../components/DeclarationForm.vue"
import DeclarationsList from "../components/DeclarationsList.vue"

export default {
  components: { DeclarationForm, DeclarationsList },

  setup() {
    const route = useRoute()

    return {
      idContribuable: route.params.idContribuable,
      idTypeImpot: Number(route.params.idTypeImpot)
    }
  },

  methods: {
    refreshList() {
      this.$refs.list.loadDeclarations()
    }
  }
}
</script>

