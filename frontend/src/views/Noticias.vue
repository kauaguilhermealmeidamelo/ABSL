<script setup>
import { ref, computed, onMounted } from 'vue'
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
const pesquisa = ref('')

const noticiasFiltradas = computed(() => {
  const termo = pesquisa.value.trim().toLocaleLowerCase('pt-BR')
  if (!termo) return noticias.value

  return noticias.value.filter((noticia) => {
    const titulo = noticia.titulo || ''
    const texto = noticia.texto || ''
    return `${titulo} ${texto}`.toLocaleLowerCase('pt-BR').includes(termo)
  })
})

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
  editing.value = nova
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
      <div class="search-box">
        <v-icon size="20">mdi-magnify</v-icon>
        <input v-model="pesquisa" type="search" placeholder="Pesquisar notícia..." aria-label="Pesquisar notícia" />
        <button v-if="pesquisa" type="button" class="clear-search" aria-label="Limpar pesquisa" @click="pesquisa = ''">
          <v-icon size="18">mdi-close</v-icon>
        </button>
      </div>
      <button v-if="isAdmin" class="btn-add" @click="onAdd">Nova notícia</button>
    </div>

    <p v-if="loading">Carregando notícias...</p>
    <p v-else-if="error" class="status-erro">{{ error }}</p>

    <div v-else class="feed">
      <NoticiaCard v-for="n in noticiasFiltradas" :key="n.id" :noticia="n" :is-admin="isAdmin"
        @editar="() => onEdit(n)" @excluir="onDelete" />
      <p v-if="!noticiasFiltradas.length" class="empty">
        {{ pesquisa ? 'Nenhuma notícia encontrada para a pesquisa.' : 'Nenhuma notícia encontrada' }}
      </p>
    </div>

    <NoticiaFormModal :modelValue="showModal" @update:modelValue="val => (showModal = val)" :noticia="editing"
      :erro-servidor="erroServidorModal" @criada="onCriada" @salvar="onSalvarTexto" />
  </div>
</template>

<style scoped>
.news-page{max-width:620px;margin:0 auto;padding:24px 16px 48px;font-family:'DM Sans',sans-serif}
.controls{display:flex;align-items:center;gap:10px;margin-bottom:16px}
.search-box{display:flex;align-items:center;flex:1;min-width:0;height:40px;padding:0 10px;border:1px solid #d8dfeb;border-radius:999px;background:#fff;color:#5a6a85;box-sizing:border-box}
.search-box:focus-within{border-color:#1a3f8f;box-shadow:0 0 0 3px rgba(26,63,143,.08)}
.search-box input{width:100%;min-width:0;border:0;outline:0;background:transparent;padding:0 8px;font:inherit;font-size:13px;color:#0d1f3c}
.search-box input::placeholder{color:#7b879c}
.clear-search{display:flex;align-items:center;justify-content:center;border:0;background:transparent;color:#64748b;cursor:pointer;padding:3px;border-radius:50%}
.clear-search:hover{background:#eef2f7}
.btn-add{flex:none;background:#1a3f8f;color:#fff;border:none;padding:9px 18px;border-radius:999px;font-size:13px;font-weight:600;cursor:pointer}
.status-erro{color:#dc2626}
.feed{display:flex;flex-direction:column;gap:20px}
.empty{color:#5a6a85;text-align:center;padding:32px 0}
@media (max-width:560px){.controls{align-items:stretch;flex-direction:column}.btn-add{width:100%}}
</style>