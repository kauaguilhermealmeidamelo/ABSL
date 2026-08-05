<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import GerenciamentoTabs from '@/components/gerenciamento/GerenciamentoTabs.vue'
import TurmasManager from '@/components/gerenciamento/TurmasManager.vue'
import DiretoriasManager from '@/components/gerenciamento/DiretoriasManager.vue'
import GabaritoManager from '@/components/gerenciamento/GabaritoManager.vue'
import MidiaManager from '@/components/gerenciamento/MidiaManager.vue'
import { useAdmin } from '@/composables/useAdmin'

const { isAdmin } = useAdmin()

const tabs = [
  { id: 'turmas', label: 'Turmas', icon: 'mdi-school-outline' },
  { id: 'diretorias', label: 'Diretorias', icon: 'mdi-domain' },
  { id: 'gabarito', label: 'Gabarito', icon: 'mdi-file-document-outline' },
  { id: 'midia', label: 'Mídia', icon: 'mdi-filmstrip' },
]
const tab = ref('turmas')
</script>

<template>
  <div class="gerenciamento-page">
    <PageHeader
      label="Admin"
      title="Gerenciamento"
      subtitle="Configurações administrativas do portal ABSL."
    />

    <div v-if="!isAdmin" class="sem-acesso">
      <v-icon size="32" color="#5A6A85" style="opacity: 0.35">mdi-lock-outline</v-icon>
      <p>Esta área é exclusiva para administradores. Faça login para continuar.</p>
    </div>

    <template v-else>
      <GerenciamentoTabs v-model="tab" :tabs="tabs" />
      <TurmasManager v-if="tab === 'turmas'" />
      <DiretoriasManager v-else-if="tab === 'diretorias'" />
      <GabaritoManager v-else-if="tab === 'gabarito'" />
      <MidiaManager v-else-if="tab === 'midia'" />
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