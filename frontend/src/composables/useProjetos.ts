import { ref } from 'vue'
import { projetosService } from '@/services/projetos.js'

export interface Projeto {
  id: number
  titulo: string
  descricao: string
  categoria?: string | null
  diretoria?: string | null
  imagem_url?: string | null
  conteudo_detalhado?: string | null
  status: 'em_andamento' | 'concluido'
  data_conclusao?: string | null
  destaque?: boolean
}

// Estado único (singleton) compartilhado entre a listagem (Projetos.vue) e
// a página de detalhe (ProjetoDetalhes.vue) — mesmo padrão de
// useNoticias.js. Isso evita que a navegação lista -> detalhe precise
// esperar um novo fetch: os dados já estão em memória.
const projetos = ref<Projeto[]>([])
const loaded = ref(false)
const loading = ref(false)
const error = ref('')

async function fetchProjetos(force = false): Promise<void> {
  if (loaded.value && !force) return
  loading.value = true
  error.value = ''
  try {
    projetos.value = await projetosService.list()
    loaded.value = true
  } catch {
    error.value = 'Não foi possível carregar os projetos. Contate o administrador.'
  } finally {
    loading.value = false
  }
}

export function useProjetos() {
  function getById(id: string | number): Projeto | undefined {
    return projetos.value.find((p) => String(p.id) === String(id))
  }

  async function adicionar(dados: Partial<Projeto>): Promise<void> {
    const criado = await projetosService.create(dados)
    projetos.value = [criado, ...projetos.value]
  }

  async function atualizar(id: number, dados: Partial<Projeto>): Promise<void> {
    const atualizado = await projetosService.update(id, dados)
    projetos.value = projetos.value.map((p) => (p.id === id ? atualizado : p))
  }

  async function remover(id: number): Promise<void> {
    await projetosService.remove(id)
    projetos.value = projetos.value.filter((p) => p.id !== id)
  }

  return { projetos, loading, error, fetchProjetos, getById, adicionar, atualizar, remover }
}