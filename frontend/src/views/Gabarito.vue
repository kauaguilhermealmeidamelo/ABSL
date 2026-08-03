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
import { computed } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import GabaritoSecao from '@/components/gabarito/Gabaritosecao.vue'

const matGroups = ['2A até 2D', '2E até 2H', '3A até 3H', '3I até 3O']
const vesGroups = ['1A até 1H', '1I até 1P']

// Mesmo critério usado em AdminCard.vue / menuLateral.vue para detectar admin
const isAdmin = computed(() => {
  const raw = localStorage.getItem('usuario')
  if (!raw) return false

  try {
    const parsed = JSON.parse(raw)
    if (parsed && typeof parsed === 'object') {
      const role = String(
        parsed.role || parsed.tipo || parsed.perfil || parsed.is_admin || parsed.administrador || ''
      ).toLowerCase()
      return (
        role === 'admin' ||
        role === 'administrator' ||
        role === 'administrador' ||
        role === 'super_admin' ||
        role === 'super-admin' ||
        parsed.is_admin === true ||
        parsed.administrador === true
      )
    }
  } catch {
    // fallback para valores simples armazenados como texto
  }

  return String(raw).toLowerCase().includes('admin')
})
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