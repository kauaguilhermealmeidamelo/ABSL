<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import MapaEstatisticas from '@/components/mapa/MapaEstatisticas.vue'
import MapaLegenda from '@/components/mapa/MapaLegenda.vue'
import MapaPlanta from '@/components/mapa/MapaPlanta.vue'
import MapaInfoPainel from '@/components/mapa/MapaInfoPainel.vue'

const selecionado = ref(null)
const categoriaAtiva = ref(null)

function onSelecionarArea(id) {
  selecionado.value = id
}

function onSelecionarCategoria(categoria) {
  categoriaAtiva.value = categoria
  // Trocar o filtro de categoria limpa o ambiente selecionado, pra não
  // deixar o painel mostrando um ambiente que está esmaecido no mapa.
  selecionado.value = null
}
</script>

<template>
  <div class="mapa-page">
    <PageHeader
      label="ABSL"
      title="Mapa da Escola"
      subtitle="Centro de Ensino Médio Athos Bulcão — Brasília, DF. Toque nos ambientes para identificá-los."
    />

    <MapaEstatisticas />

    <MapaLegenda :categoria-ativa="categoriaAtiva" @selecionar-categoria="onSelecionarCategoria" />

    <MapaPlanta
      :selecionado="selecionado"
      :categoria-ativa="categoriaAtiva"
      @selecionar="onSelecionarArea"
    />

    <MapaInfoPainel :selecionado="selecionado" />
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700;1,900&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400;1,9..40,700&display=swap');

.mapa-page {
  padding: 24px;
  max-width: 900px;
  margin: 0 auto;
}

@media (max-width: 480px) {
  .mapa-page {
    padding: 16px;
  }
}
</style>