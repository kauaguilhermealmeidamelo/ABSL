import { ref } from 'vue'
import { noticiasService } from '@/services/noticias'

// Estado único (singleton) compartilhado entre a listagem (Noticias.vue)
// e a página de detalhe (NoticiaDetalhe.vue).
const noticias = ref([])
const loaded = ref(false)
const loading = ref(false)
const error = ref('')

async function fetchNoticias(force = false) {
  if (loaded.value && !force) return
  loading.value = true
  error.value = ''
  try {
    noticias.value = await noticiasService.list()
    loaded.value = true
  } catch {
    error.value = 'Não foi possível carregar as notícias. Tente novamente.'
  } finally {
    loading.value = false
  }
}

export function useNoticias() {
  function getById(id) {
    return noticias.value.find((n) => String(n.id) === String(id))
  }

  async function adicionar(dados) {
    const criada = await noticiasService.create(dados)
    noticias.value = [criada, ...noticias.value]
  }

  async function atualizar(id, dados) {
    const atualizada = await noticiasService.update(id, dados)
    noticias.value = noticias.value.map((n) => (n.id === id ? atualizada : n))
  }

  async function remover(id) {
    await noticiasService.remove(id)
    noticias.value = noticias.value.filter((n) => n.id !== id)
  }

  return { noticias, loading, error, fetchNoticias, getById, adicionar, atualizar, remover }
}
