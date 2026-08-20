<script setup>
import PageHeader from '@/components/common/PageHeader.vue'
import mapa from '@/assets/mapa.png'

</script>

<template>
  <div class="mapa-page">
    <PageHeader label="ABSL" title="Mapa da Escola" subtitle="Centro de Ensino Médio Setor Leste" />

    <img :src="mapa" class="img">
  </div>
</template>

<style scoped>
.img {
  width: 100%;
  height: auto;
  clip-path: inset(0 2.32% 0 0);
}

.mapa-page {
  font-family: 'DM Sans', sans-serif;
  max-width: 1180px;
  margin: 0 auto;
  padding: 32px 40px 64px;
}

@media (max-width: 720px) {
  .mapa-page {
    padding: 20px;
  }
}
</style>