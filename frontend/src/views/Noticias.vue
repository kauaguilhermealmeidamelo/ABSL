<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/common/PageHeader.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import NoticiaTabs from '@/components/noticias/NoticiaTabs.vue'
import NoticiaCard from '@/components/noticias/NoticiaCard.vue'
import NoticiaFormModal from '@/components/noticias/NoticiaFormModal.vue'
import { useAdmin } from '@/composables/useAdmin'
import { useNoticias } from '@/composables/useNoticias'

const { isAdmin } = useAdmin()
const { noticias, adicionar, atualizar, remover } = useNoticias()
const router = useRouter()

const aba = ref('gremio') // 'gremio' | 'escola'

const noticiasVisiveis = computed(() => noticias.value.filter((n) => n.categoria === aba.value))

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
    atualizar(editando.value.id, dados)
  } else {
    adicionar({ categoria: aba.value, ...dados })
  }
}

function excluir(id) {
  remover(id)
}

function abrirDetalhe(noticia) {
  router.push(`/noticias/${noticia.id}`)
}
</script>

<template>
  <div class="noticias-page">
    <PageHeader
      label="ABSL"
      title="Notícias"
      subtitle="Tudo que acontece no grêmio e na escola, direto da Diretoria de Imprensa e Comunicação."
    />

    <NoticiaTabs v-model="aba" />

    <div v-if="isAdmin" class="admin-row">
      <AdminBanner />
      <button type="button" class="btn-nova" @click="abrirNova">
        <v-icon size="15">mdi-plus</v-icon>
        Nova Notícia
      </button>
    </div>

    <div class="noticias-grid">
      <NoticiaCard
        v-for="noticia in noticiasVisiveis"
        :key="noticia.id"
        :noticia="noticia"
        :is-admin="isAdmin"
        @abrir="abrirDetalhe"
        @editar="abrirEdicao"
        @excluir="excluir"
      />
    </div>

    <p v-if="noticiasVisiveis.length === 0" class="vazio">
      Nenhuma notícia publicada nesta categoria ainda.
    </p>

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

.vazio {
  color: #5a6a85;
  font-size: 14px;
  padding: 24px 0;
}

@media (max-width: 480px) {
  .noticias-page {
    padding: 16px;
  }
}
</style>