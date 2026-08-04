<script setup lang="ts">
import { ref } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import CardapioSemana from '@/components/cardapio/CardapioSemana.vue'
import CardapioTabela from '@/components/cardapio/CardapioTabela.vue'

const props = withDefaults(defineProps<{ isAdmin?: boolean }>(), {
  isAdmin: false,
})

const DIAS_SEMANA = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira']

const cardapio = ref<Record<string, string>>({
  'Segunda-feira': 'Galinhada',
  'Terça-feira': 'Strogonoff de frango',
  'Quarta-feira': 'Macarrão ao sugo com almôndegas',
  'Quinta-feira': 'Peixe assado com limão e alho',
  'Sexta-feira': 'Frango grelhado ao molho de ervas',
})

const semana = ref('28 jul – 01 ago 2026')

function updateSemana(valor: string) {
  semana.value = valor
}

function updateDia({ dia, valor }: { dia: string; valor: string }) {
  cardapio.value = { ...cardapio.value, [dia]: valor }
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
      message="Modo administrador ativo — você pode criar, editar e excluir conteúdo nesta página."
    />

    <CardapioSemana :semana="semana" :is-admin="isAdmin" @update="updateSemana" />

    <CardapioTabela
      :dias="DIAS_SEMANA"
      :cardapio="cardapio"
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