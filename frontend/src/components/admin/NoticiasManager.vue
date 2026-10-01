<script setup>
import { computed, onMounted, ref } from 'vue'
import NoticiaCard from '@/components/noticias/NoticiaCard.vue'
import NoticiaFormModal from '@/components/noticias/NoticiaFormModal.vue'
import { useNoticias } from '@/composables/useNoticias'

const { noticias, loading, error, fetchNoticias, atualizar, remover } = useNoticias()
const pesquisa = ref('')
const showModal = ref(false)
const editing = ref(null)
const erroServidor = ref('')

const filtradas = computed(() => {
  const termo = pesquisa.value.trim().toLocaleLowerCase('pt-BR')
  if (!termo) return noticias.value
  return noticias.value.filter((n) => `${n.titulo || ''} ${n.texto || ''}`.toLocaleLowerCase('pt-BR').includes(termo))
})

onMounted(() => fetchNoticias(true))
function novo() { editing.value = null; erroServidor.value = ''; showModal.value = true }
function editar(n) { editing.value = { ...n }; erroServidor.value = ''; showModal.value = true }
function criada(n) { noticias.value = [n, ...noticias.value] }
async function salvar({ id, payload }) {
  try { await atualizar(id, payload) } catch (e) { erroServidor.value = e?.response?.data?.message || 'Erro ao salvar notícia.' }
}
async function excluir(id) {
  if (!confirm('Confirma exclusão da notícia?')) return
  await remover(id)
}
</script>

<template>
  <div class="manager">
    <div class="toolbar">
      <div class="search"><v-icon size="18">mdi-magnify</v-icon><input v-model="pesquisa" type="search" placeholder="Pesquisar notícias..." /></div>
      <button type="button" class="primary" @click="novo"><v-icon size="16">mdi-plus</v-icon> Nova notícia</button>
    </div>
    <p v-if="loading" class="state">Carregando notícias...</p>
    <p v-else-if="error" class="state error">{{ error }}</p>
    <div v-else class="list">
      <NoticiaCard v-for="n in filtradas" :key="n.id" :noticia="n" :is-admin="true" @editar="editar(n)" @excluir="excluir" />
      <p v-if="!filtradas.length" class="state">Nenhuma notícia cadastrada.</p>
    </div>
    <NoticiaFormModal v-model="showModal" :noticia="editing" :erro-servidor="erroServidor" @criada="criada" @salvar="salvar" />
  </div>
</template>

<style scoped>
.manager{display:flex;flex-direction:column;gap:18px;font-family:'DM Sans',sans-serif}.toolbar{display:flex;gap:10px}.search{display:flex;align-items:center;flex:1;min-width:0;height:42px;padding:0 12px;border:1px solid #d8dfeb;border-radius:10px;background:#fff;color:#5a6a85}.search input{width:100%;border:0;outline:0;padding:0 8px;background:transparent;font:inherit;color:#0d1f3c}.primary{display:flex;align-items:center;gap:7px;border:0;border-radius:10px;background:#1a3f8f;color:#fff;padding:0 16px;font-weight:600;cursor:pointer}.list{display:flex;flex-direction:column;gap:16px}.state{padding:28px 0;color:#5a6a85;text-align:center}.error{color:#dc2626}@media(max-width:600px){.toolbar{flex-direction:column}.primary{height:42px;justify-content:center}}
</style>