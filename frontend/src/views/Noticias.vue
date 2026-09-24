<script setup>
import { ref, onMounted } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import NoticiaCard from '@/components/noticias/NoticiaCard.vue'
import NoticiaFormModal from '@/components/noticias/NoticiaFormModal.vue'
import { useNoticias } from '@/composables/useNoticias'
import { useAdmin } from '@/composables/useAdmin'

const { isAdmin } = useAdmin()
const { noticias, loading, error, fetchNoticias, atualizar, remover } = useNoticias()

const showModal = ref(false)
const editing = ref(null)
const erroServidorModal = ref('')

onMounted(() => fetchNoticias(true))

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

function onCriada(nova) {
  noticias.value = [nova, ...noticias.value]
  editing.value = nova // modal permanece aberto, agora em modo "gerenciar mídias"
}

async function onSalvarTexto({ id, payload }) {
  erroServidorModal.value = ''
  try {
    await atualizar(id, payload)
  } catch (err) {
    erroServidorModal.value = err?.response?.data?.message || 'Erro ao salvar notícia.'
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
      <button v-if="isAdmin" class="btn-add" @click="onAdd">Nova notícia</button>
    </div>

    <p v-if="loading">Carregando notícias...</p>
    <p v-else-if="error" class="status-erro">{{ error }}</p>

    <div v-else class="feed">
      <NoticiaCard v-for="n in noticias" :key="n.id" :noticia="n" :is-admin="isAdmin"
        @editar="() => onEdit(n)" @excluir="onDelete" />
      <p v-if="!noticias.length" class="empty">Nenhuma notícia encontrada</p>
    </div>

    <NoticiaFormModal :modelValue="showModal" @update:modelValue="val => (showModal = val)" :noticia="editing"
      :erro-servidor="erroServidorModal" @criada="onCriada" @salvar="onSalvarTexto" />
  </div>
</template>

<style scoped>
.news-page {
  max-width: 620px;
  margin: 0 auto;
  padding: 24px 16px 48px;
  font-family: 'DM Sans', sans-serif;
}

.controls {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 16px;
}

.btn-add {
  background: #1a3f8f;
  color: #fff;
  border: none;
  padding: 9px 18px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

.status-erro {
  color: #dc2626;
}

.feed {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.empty {
  color: #5a6a85;
  text-align: center;
  padding: 32px 0;
}
</style>