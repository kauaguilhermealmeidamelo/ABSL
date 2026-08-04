<script setup>
import { computed, ref } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import DiretoriaSelectorGrid from '@/components/projetos/DiretoriaSelectorGrid.vue'
import ProjetosDialog from '@/components/projetos/ProjetosDialog.vue'
import ProjetoDetalheDialog from '@/components/projetos/ProjetoDetalheDialog.vue'
import ProjetoFormModal from '@/components/projetos/ProjetoFormModal.vue'
import { useAdmin } from '@/composables/useAdmin'

const { isAdmin } = useAdmin()

// TODO: substituir por chamada à API (endpoint de projetos ainda não existe no backend)
const projetos = ref([
  { id: 1, categoria: 'Diretoria Social', status: 'concluido', titulo: 'Campanha do Agasalho 2026', descricao: 'Arrecadação de roupas e calçados em todas as turmas, com entrega a instituições da cidade.', data_conclusao: 'Maio 2026' },
  { id: 2, categoria: 'Esporte e Lazer', status: 'em_andamento', titulo: 'Torneio Interclasses', descricao: 'Competição entre turmas de futsal, vôlei e handebol, com tabela publicada e arbitragem voluntária.' },
  { id: 3, categoria: 'Cultura', status: 'em_andamento', titulo: 'Feira Cultural ABSL', descricao: 'Mostra de música, teatro e artes visuais produzidos pelos alunos, com palco montado no ginásio.' },
  { id: 4, categoria: 'Presidência', status: 'em_andamento', titulo: 'Reforma do Espaço de Convivência', descricao: 'Bancos, sombrite e torneiras no pátio central, negociadas junto à direção a partir de pauta do estudantil.' },
  { id: 5, categoria: 'Saúde e Meio Ambiente', status: 'em_andamento', titulo: 'Semana da Saúde Mental', descricao: 'Rodas de conversa e atividades com profissionais convidados durante os intervalos.' },
  { id: 6, categoria: 'Tecnologia e Inovação', status: 'em_andamento', titulo: 'Portal do Estudante', descricao: 'Canal digital com horários, gabaritos e notícias do grêmio reunidos em um só lugar.' },
])

// Categoria selecionada no grid: null (nada aberto) | '__geral__' | nome da categoria
const categoriaSelecionada = ref(null)
const projetoDetalhe = ref(null)
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
  projetoDetalhe.value = null
}

function salvar(dados) {
  if (projetoEditando.value) {
    projetos.value = projetos.value.map((p) => (p.id === projetoEditando.value.id ? { ...p, ...dados } : p))
  } else {
    projetos.value = [{ id: Date.now(), ...dados }, ...projetos.value]
  }
}

function excluir(id) {
  projetos.value = projetos.value.filter((p) => p.id !== id)
  projetoDetalhe.value = null
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

    <DiretoriaSelectorGrid
      :projetos="projetos"
      :selecionado="categoriaSelecionada"
      @selecionar="selecionarCategoria"
    />

    <div class="dica-vazia">
      <v-icon size="34" color="#5A6A85" style="opacity: 0.25">mdi-folder-multiple-outline</v-icon>
      <p>Escolha uma diretoria acima para ver os projetos.</p>
    </div>

    <ProjetosDialog
      v-if="categoriaSelecionada !== null"
      :categoria="categoriaSelecionada"
      :projetos="projetosVisiveis"
      :is-admin="isAdmin"
      @fechar="categoriaSelecionada = null"
      @novo="abrirNovo"
      @editar="abrirEdicao"
      @excluir="excluir"
      @abrir-detalhe="projetoDetalhe = $event"
    />

    <ProjetoDetalheDialog
      v-if="projetoDetalhe"
      :projeto="projetoDetalhe"
      :is-admin="isAdmin"
      @fechar="projetoDetalhe = null"
      @editar="abrirEdicao"
      @excluir="excluir"
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
