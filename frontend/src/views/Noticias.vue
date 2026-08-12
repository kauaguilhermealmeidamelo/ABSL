<script setup>
import { ref, onMounted } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import NoticiaCard from '@/components/noticias/NoticiaCard.vue'
import NoticiaTabs from '@/components/noticias/NoticiaTabs.vue'
import NoticiaFormModal from '@/components/noticias/NoticiaFormModal.vue'
import { useNoticias } from '@/composables/useNoticias'
import { useAdmin } from '@/composables/useAdmin'

const { isAdmin } = useAdmin()
const { noticias, loading, error, fetchNoticias, adicionar, atualizar, remover } = useNoticias()

const activeTab = ref('gremio')
const showModal = ref(false)
const editing = ref(null)

onMounted(() => fetchNoticias(true))

function abrirNoticia(n) {
  window.location.href = `/noticias/${n.id}`
}

function onAdd() {
  editing.value = null
  showModal.value = true
}

function onEdit(n) {
  editing.value = { ...n }
  showModal.value = true
}

async function onSave(payload) {
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

    showModal.value = false
  } catch (err) {
    const msg = err?.response?.data?.message || err?.response?.data || err?.message || 'Erro ao salvar notícia'
    // aviso simples para o usuário; evita erro não tratado no handler
    alert(`Erro ao salvar notícia: ${msg}`)
  }
}

async function onDelete(id) {
  if (!confirm('Confirma exclusão da notícia?')) return
  await remover(id)
}
</script>

<template>
  <div class="news-page">
    <PageHeader label="ABSL" title="Notícias" subtitle="Últimas notícias do Grêmio e da Escola" />

    <div class="controls">
      <NoticiaTabs v-model="activeTab" />
      <div style="margin-left:auto">
        <button v-if="isAdmin" class="btn-add" @click="onAdd">Nova notícia</button>
      </div>
    </div>

    <p v-if="loading">Carregando notícias...</p>
    <p v-else-if="error" class="status-erro">{{ error }}</p>

    <div v-else class="list-grid">
      <NoticiaCard
        v-for="n in noticias.filter(x => x.categoria === activeTab)"
        :key="n.id"
        :noticia="n"
        :is-admin="isAdmin"
        @abrir="abrirNoticia"
        @editar="() => onEdit(n)"
        @excluir="onDelete"
      />
      <div v-if="!noticias.length" class="empty">Nenhuma notícia encontrada</div>
    </div>

    <NoticiaFormModal :modelValue="showModal" @update:modelValue="val => (showModal = val)" :noticia="editing" @salvar="onSave" />
  </div>
</template>

<style scoped>
.news-page { max-width: 1024px; margin: 0 auto; padding: 32px; }
.controls { display:flex; gap:12px; align-items:center; margin-bottom:16px }
.btn-add { background:#1a3f8f; color:#fff; border:none; padding:8px 14px; border-radius:12px }
.list-grid { display:grid; grid-template-columns: repeat(3, 1fr); gap:16px }
.empty { color:#5a6a85 }
@media (max-width: 900px) { .list-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 560px) { .list-grid { grid-template-columns: 1fr; } }
</style>