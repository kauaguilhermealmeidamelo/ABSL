<script setup>
import Banner from '@/components/inicio/Banner.vue'
import Sobre from '@/components/inicio/Sobre.vue'
import Equipe from '@/components/inicio/Equipe.vue'
</script>

<template>
  <div class="page">
    <Banner/>
    <Sobre/>
    <Equipe/>
  </div>

</template>

<style scoped>
.page {
  padding: 32px 40px 64px;
  max-width: 1180px;
  margin: 0 auto;
}

@media (max-width: 720px) {
  .page {
    padding: 20px;
  }
}
</style>