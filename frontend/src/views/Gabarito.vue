<script setup>
import PageHeader from '@/components/common/PageHeader.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import GabaritoSecao from '@/components/gabarito/Gabaritosecao.vue'
import { useAdmin } from '@/composables/useAdmin'
import { gabMatGroups, gabVesGroups } from '@/stores/appData'

const { isAdmin } = useAdmin()
</script>

<template>
  <div class="gabarito-page">
    <PageHeader
      label="ABSL"
      title="Gabarito"
      subtitle="Gabaritos e provas para consulta, organizados por turno."
    />

    <AdminBanner v-if="isAdmin" />

    <GabaritoSecao title="Matutino" :groups="gabMatGroups" :is-admin="isAdmin" />
    <GabaritoSecao title="Vespertino" :groups="gabVesGroups" :is-admin="isAdmin" />
  </div>
</template>

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