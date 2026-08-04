<template>
  <div class="gabarito-page">
    <PageHeader
      label="ABSL"
      title="Gabarito"
      subtitle="Gabaritos divididos por turno e por grupo de turmas."
    />

    <AdminBanner v-if="isAdmin" />

    <GabaritoSecao title="Matutino" :groups="matGroups" :is-admin="isAdmin" />
    <GabaritoSecao title="Vespertino" :groups="vesGroups" :is-admin="isAdmin" />
  </div>
</template>

<script setup>
import PageHeader from '@/components/common/PageHeader.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import GabaritoSecao from '@/components/gabarito/Gabaritosecao.vue'
import { useAdmin } from '@/composables/useAdmin'

// Grupos de turmas conforme especificação do Figma
const matGroups = ['2A até 2D', '2E até 2H', '3A até 3H', '3I até 3P']
const vesGroups = ['1A até 1G', '1H até 1R']

const { isAdmin } = useAdmin()
</script>

<style scoped>
.gabarito-page {
  font-family: 'DM Sans', sans-serif;
  padding: 24px;
}

@media (max-width: 480px) {
  .gabarito-page {
    padding: 16px;
  }
}
</style>
