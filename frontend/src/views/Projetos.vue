<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/common/PageHeader.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import DiretoriaSelectorGrid from '@/components/projetos/DiretoriaSelectorGrid.vue'
import ProjetosDialog from '@/components/projetos/ProjetosDialog.vue'
import ProjetoFormModal from '@/components/projetos/ProjetoFormModal.vue'
import { useAdmin } from '@/composables/useAdmin'
import { useProjetos } from '@/composables/useProjetos'

const router = useRouter()
const { isAdmin } = useAdmin()
const { projetos, loading, error, fetchProjetos, adicionar, atualizar, remover } = useProjetos()

onMounted(() => fetchProjetos(true))

// Categoria selecionada no grid: null (nada aberto) | '__geral__' | nome da categoria
const categoriaSelecionada = ref(null)
const modalAberto = ref(false)
const projetoEditando = ref(null)

const projetosVisiveis = computed(() => {
  if (categoriaSelecionada.value === null) return []
  if (categoriaSelecionada.value === '__geral__') return projetos.value
  return projetos.value.filter((p) => p.categoria === categoriaSelecionada.value)
})

function selecionarCategoria(categoria) {
  categoriaSelecionada.value = categoria
}

function abrirNovo() {
  projetoEditando.value = null
  modalAberto.value = true
}

function abrirEdicao(projeto) {
  projetoEditando.value = projeto
  modalAberto.value = true
}

// A divisão por diretoria (grid + dialog da categoria) continua igual.
// Só o que muda: em vez de abrir um segundo modal com o detalhe, agora
// navega pra página própria do projeto — mesma lógica de Notícias.
function abrirDetalhe(projeto) {
  router.push(`/projetos/${projeto.id}`)
}

async function salvar(dados) {
  try {
    if (projetoEditando.value) {
      await atualizar(projetoEditando.value.id, dados)
    } else {
      await adicionar(dados)
    }
  } catch {
    error.value = 'Não foi possível salvar o projeto.'
  }
}

async function excluir(id) {
  try {
    await remover(id)
  } catch {
    error.value = 'Não foi possível excluir o projeto.'
  }
}
</script>

<template>
  <div class="projetos-page">
    <PageHeader
      label="ABSL"
      title="Projetos"
      subtitle="Selecione uma diretoria para ver seus projetos."
    />

    <div v-if="isAdmin" class="admin-row">
      <AdminBanner />
      <button type="button" class="btn-novo" @click="abrirNovo">
        <v-icon size="15">mdi-plus</v-icon>
        Novo Projeto
      </button>
    </div>

    <p v-if="loading" class="status-msg">Carregando projetos...</p>
    <p v-else-if="error" class="status-msg status-erro">{{ error }}</p>

    <template v-else>
      <DiretoriaSelectorGrid
        :projetos="projetos"
        :selecionado="categoriaSelecionada"
        @selecionar="selecionarCategoria"
      />

      <div class="dica-vazia">
        <v-icon size="34" color="#5A6A85" style="opacity: 0.25">mdi-folder-multiple-outline</v-icon>
        <p>Escolha uma diretoria acima para ver os projetos.</p>
      </div>
    </template>

    <ProjetosDialog
      v-if="categoriaSelecionada !== null"
      :categoria="categoriaSelecionada"
      :projetos="projetosVisiveis"
      :is-admin="isAdmin"
      @fechar="categoriaSelecionada = null"
      @novo="abrirNovo"
      @editar="abrirEdicao"
      @excluir="excluir"
      @abrir-detalhe="abrirDetalhe"
    />

    <ProjetoFormModal
      v-model="modalAberto"
      :projeto="projetoEditando"
      :categoria-padrao="categoriaSelecionada && categoriaSelecionada !== '__geral__' ? categoriaSelecionada : undefined"
      @salvar="salvar"
    />
  </div>
</template>

<style scoped>
.projetos-page {
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

.status-msg {
  color: #5a6a85;
  font-size: 14px;
  padding: 24px 0;
}
.status-erro {
  color: #dc2626;
}

.dica-vazia {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 40px 16px;
  color: #5a6a85;
  text-align: center;
}
.dica-vazia p {
  font-size: 14px;
  margin: 0;
}

@media (max-width: 480px) {
  .projetos-page {
    padding: 16px;
  }
}
</style>