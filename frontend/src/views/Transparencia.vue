<script setup>
import { computed, reactive, ref } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import TransparenciaTabs from '@/components/transparencia/TransparenciaTabs.vue'
import TransparenciaItem from '@/components/transparencia/TransparenciaItem.vue'
import TransparenciaModal from '@/components/transparencia/TransparenciaModal.vue'

// Mesmo critério usado em Gabarito.vue / Ouvintes.vue / menuLateral.vue para detectar admin
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

// Aba ativa
const tab = ref('atas') // 'atas' | 'contas'

// TODO: substituir por chamadas à API quando o endpoint de transparência existir no backend
const atas = ref([
  { id: 1, data_referencia: '10 de março de 2026', descricao: 'Posse da nova diretoria e eleição interna de cargos complementares' },
  { id: 2, data_referencia: '02 de abril de 2026', descricao: 'Planejamento do Torneio Interclasses e Feira Cultural ABSL' },
  { id: 3, data_referencia: '15 de maio de 2026', descricao: 'Prestação de contas do 1º trimestre e aprovação do orçamento da Campanha do Agasalho' },
  { id: 4, data_referencia: '18 de junho de 2026', descricao: 'Avaliação dos projetos em andamento e pauta enviada à direção da escola' },
])

const contas = ref([
  { id: 1, periodo: '1º Trimestre 2026', descricao: 'Receitas, despesas e saldo referentes ao período de janeiro a março de 2026.' },
  { id: 2, periodo: 'Relatório Campanha do Agasalho', descricao: 'Prestação de contas da campanha solidária com totais arrecadados e destino das doações.' },
])

const itensAtuais = computed(() => (tab.value === 'atas' ? atas.value : contas.value))

// Modal de novo registro
const modalAberto = ref(false)
const form = reactive({ data_referencia: '', periodo: '', descricao: '' })

function abrirModal() {
  form.data_referencia = ''
  form.periodo = ''
  form.descricao = ''
  modalAberto.value = true
}

function fecharModal() {
  modalAberto.value = false
}

function salvar() {
  // TODO: enviar via API (multipart, com o PDF) quando o endpoint existir
  if (tab.value === 'atas') {
    atas.value.push({ id: Date.now(), data_referencia: form.data_referencia, descricao: form.descricao })
  } else {
    contas.value.push({ id: Date.now(), periodo: form.periodo, descricao: form.descricao })
  }
  modalAberto.value = false
}

function excluir(item) {
  // TODO: excluir via API quando o endpoint existir
  if (tab.value === 'atas') {
    atas.value = atas.value.filter((a) => a.id !== item.id)
  } else {
    contas.value = contas.value.filter((c) => c.id !== item.id)
  }
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
      <button type="button" class="btn-novo" @click="abrirModal">
        <v-icon size="14">mdi-plus</v-icon>
        {{ tab === 'atas' ? 'Nova Ata' : 'Nova Prestação de Contas' }}
      </button>
    </div>

    <TransparenciaTabs v-model="tab" />

    <div class="lista">
      <TransparenciaItem
        v-for="item in itensAtuais"
        :key="item.id"
        :titulo="tab === 'atas' ? item.data_referencia : item.periodo"
        :descricao="item.descricao"
        :is-admin="isAdmin"
        @excluir="excluir(item)"
      />
    </div>

    <TransparenciaModal
      v-if="modalAberto"
      :tab="tab"
      v-model="form"
      @salvar="salvar"
      @fechar="fecharModal"
    />
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700;1,900&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400;1,9..40,700&display=swap');

.transparencia-page {
  font-family: 'DM Sans', sans-serif;
  padding: 32px 40px 64px;
  max-width: 1180px;
  margin: 0 auto;
}

.admin-row {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 4px;
}

.admin-row :deep(.admin-banner) {
  flex: 1;
  min-width: 240px;
  margin-bottom: 20px;
}

.btn-novo {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  border-radius: 999px;
  border: none;
  background: #1a3f8f;
  color: #ffffff;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  white-space: nowrap;
  transition: background-color 0.15s ease;
}

.btn-novo:hover {
  background: #0d1f3c;
}

.lista {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

@media (max-width: 720px) {
  .transparencia-page {
    padding: 20px;
  }
}
</style>