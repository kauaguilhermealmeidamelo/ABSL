<script setup>
import PageHeader from '@/components/common/PageHeader.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import CardapioSemana from '@/components/cardapio/CardapioSemana.vue'
import CardapioTabela from '@/components/cardapio/CardapioTabela.vue'
import { useAdmin } from '@/composables/useAdmin'
import { cardapioSemana, cardapioDias } from '@/stores/appData'

const { isAdmin } = useAdmin()

const DIAS_SEMANA = Object.keys(cardapioDias)

function updateSemana(valor) {
  cardapioSemana.value = valor
}

function updateDia({ dia, valor }) {
  cardapioDias[dia] = valor
}
</script>

<template>
  <div class="cardapio-page">
    <PageHeader
      label="ABSL"
      title="Cardápio Semanal"
      subtitle="Lanche servido na cantina escolar."
    />

    <AdminBanner
      v-if="isAdmin"
      message="Modo administrador ativo — você pode editar a semana e os itens do cardápio."
    />

    <CardapioSemana :semana="cardapioSemana" :is-admin="isAdmin" @update="updateSemana" />

    <CardapioTabela
      :dias="DIAS_SEMANA"
      :cardapio="cardapioDias"
      :is-admin="isAdmin"
      @update-dia="updateDia"
    />
  </div>
</template>

<style scoped>
.cardapio-page {
  font-family: 'DM Sans', sans-serif;
  color: #0d1f3c;
  max-width: 1024px;
  margin: 0 auto;
  padding: 32px 40px 64px;
}

@media (max-width: 720px) {
  .cardapio-page {
    padding: 20px;
  }
}
</style>