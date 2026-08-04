<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import NoticiaCard from '@/components/noticias/NoticiaCard.vue'
import NoticiaFormModal from '@/components/noticias/NoticiaFormModal.vue'
import { useAdmin } from '@/composables/useAdmin'

const { isAdmin } = useAdmin()

// TODO: substituir por chamada à API (endpoint de notícias ainda não existe no backend)
const noticias = ref([
  { id: 1, data_publicacao: '14 de março de 2026', titulo: 'Resultado da eleição: chapa Movimento assume o grêmio', texto: 'Com 71% dos votos e alta participação, a nova gestão foi eleita em votação aberta no pátio da escola.' },
  { id: 2, data_publicacao: '03 de abril de 2026', titulo: 'Feira Cultural ABSL reúne 20 turmas no ginásio', texto: 'Apresentações de teatro, dança e exposição de artes marcaram a primeira edição do ano, organizada pela Diretoria de Cultura.' },
  { id: 3, data_publicacao: '12 de abril de 2026', titulo: 'Torneio Interclasses abre inscrições para o 1º semestre', texto: 'Futebol, vôlei e handebol. Inscrições até o dia 20, feitas com o representante de cada turma.' },
  { id: 4, data_publicacao: '28 de maio de 2026', titulo: 'Campanha do Agasalho: escola arrecada mais de 400 peças', texto: 'Em parceria com a Diretoria Social, a campanha bateu recorde e as doações foram entregues à ONG Recomeçar.' },
  { id: 5, data_publicacao: '10 de junho de 2026', titulo: 'Grêmio leva pauta de reforma do refeitório à direção', texto: 'Após 340 assinaturas de alunos, a presidência entregou documento formal e aguarda resposta até o fim do semestre.' },
  { id: 6, data_publicacao: '18 de junho de 2026', titulo: 'Semana da Saúde Mental: atividades gratuitas para todos', texto: 'Rodas de conversa, meditação guiada e atendimento com profissionais durante os intervalos da semana.' },
])

const modalOpen = ref(false)
const editando = ref(null)

function abrirNova() {
  editando.value = null
  modalOpen.value = true
}

function abrirEdicao(noticia) {
  editando.value = noticia
  modalOpen.value = true
}

function salvar(dados) {
  if (editando.value) {
    noticias.value = noticias.value.map((n) => (n.id === editando.value.id ? { ...n, ...dados } : n))
  } else {
    noticias.value.unshift({ id: Date.now(), ...dados })
  }
}

function excluir(id) {
  noticias.value = noticias.value.filter((n) => n.id !== id)
}
</script>

<template>
  <div class="noticias-page">
    <PageHeader
      label="ABSL"
      title="Notícias"
      subtitle="Tudo que acontece no grêmio e na escola, direto da Diretoria de Imprensa e Comunicação."
    />

    <div v-if="isAdmin" class="admin-row">
      <AdminBanner />
      <button type="button" class="btn-nova" @click="abrirNova">
        <v-icon size="15">mdi-plus</v-icon>
        Nova Notícia
      </button>
    </div>

    <div class="noticias-grid">
      <NoticiaCard
        v-for="noticia in noticias"
        :key="noticia.id"
        :noticia="noticia"
        :is-admin="isAdmin"
        @editar="abrirEdicao"
        @excluir="excluir"
      />
    </div>

    <NoticiaFormModal v-model="modalOpen" :noticia="editando" @salvar="salvar" />
  </div>
</template>

<style scoped>
.noticias-page {
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

.btn-nova {
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
.btn-nova:hover {
  background: #16509b;
}

.noticias-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
}

@media (min-width: 720px) {
  .noticias-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .noticias-page {
    padding: 16px;
  }
}
</style>
