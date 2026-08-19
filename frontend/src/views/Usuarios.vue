<script setup>
import { ref, computed } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import GerenciamentoTabs from '@/components/gerenciamento/GerenciamentoTabs.vue'
import TurmasManager from '@/components/gerenciamento/TurmasManager.vue'
import DiretoriasManager from '@/components/gerenciamento/DiretoriasManager.vue'
import MidiaManager from '@/components/gerenciamento/MidiaManager.vue'
import VisitasDashboard from '@/components/gerenciamento/VisitasDashboard.vue'
import UsuariosAdminManager from '@/components/gerenciamento/UsuariosAdminManager.vue'
import { useAdmin } from '@/composables/useAdmin'

const { isAdmin, isSuperAdmin } = useAdmin()

const tabs = computed(() => [
  { id: 'dashboard', label: 'Visitas', icon: 'mdi-chart-bar' },
  { id: 'turmas', label: 'Turmas', icon: 'mdi-school-outline' },
  { id: 'diretorias', label: 'Diretorias', icon: 'mdi-domain' },
  { id: 'midia', label: 'Mídia', icon: 'mdi-filmstrip' },
  ...(isSuperAdmin.value
    ? [{ id: 'administradores', label: 'Administradores', icon: 'mdi-shield-account-outline' }]
    : []),
])
const tab = ref('dashboard')
</script>

<template>
  <div class="gerenciamento-page">
    <PageHeader label="Admin" title="Gerenciamento" subtitle="Configurações administrativas do portal ABSL." />

    <div v-if="!isAdmin" class="sem-acesso">
      <v-icon size="32" color="#5A6A85" style="opacity: 0.35">mdi-lock-outline</v-icon>
      <p>Esta área é exclusiva para administradores. Faça login para continuar.</p>
    </div>

    <template v-else>
      <GerenciamentoTabs v-model="tab" :tabs="tabs" />
      <VisitasDashboard v-if="tab === 'dashboard'" />
      <TurmasManager v-else-if="tab === 'turmas'" />
      <DiretoriasManager v-else-if="tab === 'diretorias'" />
      <MidiaManager v-else-if="tab === 'midia'" />
      <UsuariosAdminManager v-else-if="tab === 'administradores' && isSuperAdmin" />
    </template>
  </div>
</template>

<style scoped>
.gerenciamento-page {
  font-family: 'DM Sans', sans-serif;
  padding: 24px;
  max-width: 900px;
  margin: 0 auto;
}

.sem-acesso {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  padding: 56px 16px;
  color: #5a6a85;
  text-align: center;
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 16px;
}

.sem-acesso p {
  font-size: 14px;
  margin: 0;
  max-width: 320px;
}

@media (max-width: 480px) {
  .gerenciamento-page {
    padding: 16px;
  }
}
</style>