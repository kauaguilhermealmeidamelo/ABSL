<script setup>
import { ref, computed, onMounted } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import TransparenciaItem from '@/components/transparencia/TransparenciaItem.vue'
import TransparenciaFormModal from '@/components/transparencia/TransparenciaFormModal.vue'
import { useAdmin } from '@/composables/useAdmin'
import { transparenciaService } from '@/services/transparencia'

const { isAdmin } = useAdmin()

const aba = ref('atas') // 'atas' | 'contas'
const documentos = ref([])
const loading = ref(false)
const error = ref('')

async function carregar() {
  loading.value = true
  error.value = ''
  try {
    documentos.value = await transparenciaService.list()
  } catch {
    error.value = 'Não foi possível carregar os documentos.'
  } finally {
    loading.value = false
  }
}

onMounted(carregar)

const listaAtiva = computed(() => documentos.value.filter((d) => d.categoria === aba.value))

const modalOpen = ref(false)

async function salvar(dados) {
  try {
    const criado = await transparenciaService.create(dados, aba.value)
    documentos.value = [criado, ...documentos.value]
  } catch {
    error.value = 'Não foi possível salvar o documento.'
  }
}

async function excluir(id) {
  try {
    await transparenciaService.remove(id)
    documentos.value = documentos.value.filter((item) => item.id !== id)
  } catch {
    error.value = 'Não foi possível excluir o documento.'
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

    <p v-if="loading" class="status-msg">Carregando documentos...</p>
    <p v-else-if="error" class="status-msg status-erro">{{ error }}</p>

    <div v-else class="transp-lista">
      <TransparenciaItem
        v-for="item in listaAtiva"
        :key="item.id"
        :titulo="item.referencia"
        :descricao="item.descricao"
        :is-admin="isAdmin"
        @excluir="excluir(item.id)"
      />

      <p v-if="listaAtiva.length === 0" class="vazio">Nenhum documento publicado nesta categoria ainda.</p>
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

.status-msg {
  color: #5a6a85;
  font-size: 14px;
  padding: 24px 0;
}
.status-erro {
  color: #dc2626;
}

.transp-lista {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.vazio {
  color: #5a6a85;
  font-size: 14px;
  padding: 12px 0 0;
}

@media (max-width: 480px) {
  .transparencia-page {
    padding: 16px;
  }
}
</style>