<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/common/PageHeader.vue'
import NoticiaCard from '@/components/noticias/NoticiaCard.vue'
import NoticiaTabs from '@/components/noticias/NoticiaTabs.vue'
import NoticiaFormModal from '@/components/noticias/NoticiaFormModal.vue'
import { useNoticias } from '@/composables/useNoticias'
import { useAdmin } from '@/composables/useAdmin'

const router = useRouter()
const { isAdmin } = useAdmin()
const { noticias, loading, error, fetchNoticias, adicionar, atualizar, remover } = useNoticias()

const activeTab = ref('gremio')
const showModal = ref(false)
const editing = ref(null)
const erroServidorModal = ref('')

onMounted(() => fetchNoticias(true))

function noticiasDaAba() {
  return noticias.value.filter((n) => n.categoria === activeTab.value)
}

function abrirNoticia(n) {
  router.push(`/noticias/${n.id}`)
}

function onAdd() {
  editing.value = null
  erroServidorModal.value = ''
  showModal.value = true
}

function onEdit(n) {
  editing.value = { ...n }
  erroServidorModal.value = ''
  showModal.value = true
}

async function onSave(payload) {
  erroServidorModal.value = ''
  try {
    const body = {
      ...payload,
      categoria: (editing.value && editing.value.categoria) || activeTab.value,
      ativo: payload.ativo === undefined ? true : payload.ativo,
    }

    if (editing.value && editing.value.id) {
      await atualizar(editing.value.id, body)
    } else {
      await adicionar(body)
    }

    // Só fecha o modal em caso de sucesso — se o backend rejeitar (ex: 422
    // por data_publicacao ausente), o modal continua aberto com o erro.
    showModal.value = false
  } catch (err) {
    erroServidorModal.value =
      err?.response?.data?.errors?.data_publicacao?.[0] ||
      err?.response?.data?.message ||
      'Erro ao salvar notícia.'
  }
}

async function onDelete(id) {
  if (!confirm('Confirma exclusão da notícia?')) return
  await remover(id)
}
</script>

<template>
  <div class="noticias-page">
    <PageHeader label="ABSL" title="Notícias" subtitle="Últimas notícias do Grêmio e da Escola" />

    <div class="controls">
      <NoticiaTabs v-model="activeTab" />
      <button v-if="isAdmin" type="button" class="btn-nova" @click="onAdd">
        <v-icon size="14">mdi-plus</v-icon>
        Nova Publicação
      </button>
    </div>

    <div v-if="isAdmin" class="admin-banner">
      <v-icon size="14">mdi-pencil-outline</v-icon>
      <span>Modo administrador — edite ou exclua publicações diretamente no card.</span>
    </div>

    <p v-if="loading" class="status-msg">Carregando notícias...</p>
    <p v-else-if="error" class="status-msg status-erro">{{ error }}</p>

    <div v-else class="feed">
      <NoticiaCard
        v-for="(n, idx) in noticiasDaAba()"
        :key="n.id"
        :noticia="n"
        :index="idx"
        :is-admin="isAdmin"
        @abrir="abrirNoticia"
        @editar="() => onEdit(n)"
        @excluir="onDelete"
      />
      <p v-if="!noticiasDaAba().length" class="status-msg status-vazio">
        Nenhuma notícia encontrada nesta categoria.
      </p>
    </div>

    <NoticiaFormModal
      :modelValue="showModal"
      @update:modelValue="(val) => (showModal = val)"
      :noticia="editing"
      :erro-servidor="erroServidorModal"
      @salvar="onSave"
    />
  </div>
</template>

<style scoped>
.noticias-page {
  font-family: var(--font-body, 'DM Sans', sans-serif);
  max-width: 1024px;
  margin: 0 auto;
  padding: 32px 40px 64px;
}

.controls {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 4px;
}

.btn-nova {
  display: flex;
  align-items: center;
  gap: 6px;
  border: none;
  background: var(--color-navy, #0f2038);
  color: #ffffff;
  font-size: 13px;
  font-weight: 600;
  padding: 9px 18px;
  border-radius: var(--radius-pill, 999px);
  cursor: pointer;
  white-space: nowrap;
  transition: background-color 0.15s ease;
}
.btn-nova:hover {
  background: var(--color-navy-soft, #16509b);
}

.admin-banner {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: #fffbeb;
  border: 1px solid #fde68a;
  border-radius: 12px;
  margin-bottom: 24px;
  color: #92400e;
  font-size: 13.5px;
  font-weight: 500;
}

.status-msg {
  color: var(--color-text-secondary, #6b7c93);
  font-size: 14px;
  padding: 24px 0;
  text-align: center;
}
.status-erro {
  color: #dc2626;
}
.status-vazio {
  color: var(--color-text-muted, #8a90a8);
}

.feed {
  max-width: 468px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

@media (max-width: 720px) {
  .noticias-page {
    padding: 20px;
  }
}
</style>