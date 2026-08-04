<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import ProjetoCard from '@/components/projetos/ProjetoCard.vue'
import ProjetoFormModal from '@/components/projetos/ProjetoFormModal.vue'
import { useAdmin } from '@/composables/useAdmin'

const { isAdmin } = useAdmin()

const categorias = [
  'Presidência', 'Secretaria', 'Tesouraria', 'Esporte e Lazer', 'Cultura',
  'Políticas Educacionais', 'Saúde e Meio Ambiente', 'Diretoria Social',
  'Imprensa e Comunicação', 'Tecnologia e Inovação',
]

// TODO: substituir por chamada à API (endpoint de projetos ainda não existe no backend)
const projetos = ref([
  { id: 1, categoria: 'Diretoria Social', status: 'concluido', titulo: 'Campanha do Agasalho 2026', descricao: 'Arrecadação de roupas e calçados em todas as turmas, com entrega a instituições da cidade.', data_conclusao: 'Maio 2026' },
  { id: 2, categoria: 'Esporte e Lazer', status: 'em_andamento', titulo: 'Torneio Interclasses', descricao: 'Competição entre turmas de futsal, vôlei e handebol, com tabela publicada e arbitragem voluntária.' },
  { id: 3, categoria: 'Cultura', status: 'em_andamento', titulo: 'Feira Cultural ABSL', descricao: 'Mostra de música, teatro e artes visuais produzidos pelos alunos, com palco montado no ginásio.' },
  { id: 4, categoria: 'Presidência', status: 'em_andamento', titulo: 'Reforma do Espaço de Convivência', descricao: 'Bancos, sombrite e torneiras no pátio central, negociadas junto à direção a partir de pauta do estudantil.' },
  { id: 5, categoria: 'Saúde e Meio Ambiente', status: 'em_andamento', titulo: 'Semana da Saúde Mental', descricao: 'Rodas de conversa e atividades com profissionais convidados durante os intervalos.' },
  { id: 6, categoria: 'Tecnologia e Inovação', status: 'em_andamento', titulo: 'Portal do Estudante', descricao: 'Canal digital com horários, gabaritos e notícias do grêmio reunidos em um só lugar.' },
])

const modalOpen = ref(false)
const editando = ref(null)

function abrirNovo() {
  editando.value = null
  modalOpen.value = true
}

function abrirEdicao(projeto) {
  editando.value = projeto
  modalOpen.value = true
}

function salvar(dados) {
  if (editando.value) {
    projetos.value = projetos.value.map((p) => (p.id === editando.value.id ? { ...p, ...dados } : p))
  } else {
    projetos.value.unshift({ id: Date.now(), ...dados })
  }
}

function excluir(id) {
  projetos.value = projetos.value.filter((p) => p.id !== id)
}
</script>

<template>
  <div class="projetos-page">
    <PageHeader
      label="ABSL"
      title="Projetos"
      subtitle="Ideias que saíram do papel — e as que estão em construção agora."
    />

    <div v-if="isAdmin" class="admin-row">
      <AdminBanner />
      <button type="button" class="btn-novo" @click="abrirNovo">
        <v-icon size="15">mdi-plus</v-icon>
        Novo Projeto
      </button>
    </div>

    <div class="projetos-grid">
      <ProjetoCard
        v-for="projeto in projetos"
        :key="projeto.id"
        :projeto="projeto"
        :is-admin="isAdmin"
        @editar="abrirEdicao"
        @excluir="excluir"
      />
    </div>

    <ProjetoFormModal v-model="modalOpen" :projeto="editando" :categorias="categorias" @salvar="salvar" />
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

.projetos-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
}

@media (min-width: 720px) {
  .projetos-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .projetos-page {
    padding: 16px;
  }
}
</style>
