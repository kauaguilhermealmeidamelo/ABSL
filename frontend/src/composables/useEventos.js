import { ref } from 'vue'
import { eventosService } from '@/services/eventos'

// Estado único (singleton) compartilhado entre listagem e detalhe.
const eventos = ref([])
const loaded = ref(false)
const loading = ref(false)
const error = ref('')

async function fetchEventos(force = false) {
  if (loaded.value && !force) return
  loading.value = true
  error.value = ''
  try {
    eventos.value = await eventosService.list()
    loaded.value = true
  } catch {
    error.value = 'Não foi possível carregar os eventos. Contate o administrador.'
  } finally {
    loading.value = false
  }
}

export function useEventos() {
  async function adicionar(dados) {
    await eventosService.create(dados)
    await fetchEventos(true)
  }

  async function atualizar(id, dados) {
    await eventosService.update(id, dados)
    await fetchEventos(true)
  }

  async function remover(id) {
    await eventosService.remove(id)
    eventos.value = eventos.value.filter((e) => e.id !== id)
  }

  return { eventos, loading, error, fetchEventos, adicionar, atualizar, remover }
}
