<script setup>
import { ref, computed } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import TransparenciaItem from '@/components/transparencia/TransparenciaItem.vue'
import TransparenciaFormModal from '@/components/transparencia/TransparenciaFormModal.vue'
import { useAdmin } from '@/composables/useAdmin'

const { isAdmin } = useAdmin()

const aba = ref('atas') // 'atas' | 'contas'

// TODO: substituir por chamada à API (endpoint de transparência ainda não existe no backend)
const atas = ref([
  { id: 1, referencia: '10 de março de 2026', descricao: 'Posse da nova diretoria e eleição interna de cargos complementares' },
  { id: 2, referencia: '02 de abril de 2026', descricao: 'Planejamento do Torneio Interclasses e Feira Cultural ABSL' },
  { id: 3, referencia: '15 de maio de 2026', descricao: 'Prestação de contas do 1º trimestre e aprovação do orçamento da Campanha do Agasalho' },
  { id: 4, referencia: '18 de junho de 2026', descricao: 'Avaliação dos projetos em andamento e pauta enviada à direção da escola' },
])

const contas = ref([
  { id: 1, referencia: '1º Trimestre 2026', descricao: 'Receitas, despesas e saldo referentes ao período de janeiro a março de 2026.' },
  { id: 2, referencia: 'Relatório Campanha do Agasalho', descricao: 'Prestação de contas da campanha solidária com totais arrecadados e destino das doações.' },
])

const listaAtiva = computed(() => (aba.value === 'atas' ? atas : contas))

const modalOpen = ref(false)

function salvar(dados) {
  listaAtiva.value.value.unshift({ id: Date.now(), ...dados })
}

function excluir(id) {
  listaAtiva.value.value = listaAtiva.value.value.filter((item) => item.id !== id)
}
</script>

<template>
  <div class="transparencia-page">
    <PageHeader
      label="ABSL"
      title="Portal de Transparência"
      subtitle="Atas de reuniões e prestações de contas do Grêmio Athos Bulcão."
    />

    <div v-if="isAdmin" class="admin-row">
      <AdminBanner />
      <button type="button" class="btn-novo" @click="modalOpen = true">
        <v-icon size="15">mdi-plus</v-icon>
        {{ aba === 'atas' ? 'Nova Ata' : 'Nova Prestação de Contas' }}
      </button>
    </div>

    <div class="tabs">
      <button type="button" class="tab" :class="{ 'tab-active': aba === 'atas' }" @click="aba = 'atas'">
        Atas das Reuniões
      </button>
      <button type="button" class="tab" :class="{ 'tab-active': aba === 'contas' }" @click="aba = 'contas'">
        Prestação de Contas
      </button>
    </div>

    <div class="transp-lista">
      <TransparenciaItem
        v-for="item in listaAtiva.value"
        :key="item.id"
        :titulo="item.referencia"
        :descricao="item.descricao"
        :is-admin="isAdmin"
        @excluir="excluir(item.id)"
      />
    </div>

    <TransparenciaFormModal v-model="modalOpen" :tipo="aba === 'atas' ? 'ata' : 'contas'" @salvar="salvar" />
  </div>
</template>

<style scoped>
.transparencia-page {
  font-family: 'DM Sans', sans-serif;
  padding: 24px;
}

.admin-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}

.admin-row :deep(.admin-banner) {
  margin-bottom: 0;
  flex: 1;
  min-width: 240px;
}

.btn-novo {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 10px 20px;
  border-radius: 999px;
  background: #0d1f3c;
  color: #ffffff;
  font-size: 13px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  white-space: nowrap;
  margin-bottom: 24px;
  transition: background-color 0.15s ease;
}
.btn-novo:hover {
  background: #16509b;
}

.tabs {
  display: flex;
  gap: 4px;
  background: #eef3fb;
  border-radius: 999px;
  padding: 4px;
  margin-bottom: 24px;
  width: fit-content;
  max-width: 100%;
}

.tab {
  border: none;
  background: transparent;
  padding: 9px 18px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  color: #5a6a85;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.15s ease;
}
.tab:hover {
  color: #0d1f3c;
}
.tab-active,
.tab-active:hover {
  background: #1a3f8f;
  color: #ffffff;
}

.transp-lista {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

@media (max-width: 480px) {
  .transparencia-page {
    padding: 16px;
  }
}
</style>
