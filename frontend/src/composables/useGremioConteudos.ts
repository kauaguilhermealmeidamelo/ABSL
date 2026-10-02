import { ref } from 'vue'
import { gremioConteudosService } from '@/services/gremioConteudos.js'

export interface GremioConteudo {
  id: number
  titulo: string
  conteudo: string
  /** URL pública do PDF anexado (disco 'public', pasta gremio/) ou null. */
  arquivo_pdf: string | null
  ordem: number
}

export interface NovoGremioConteudo {
  titulo: string
  conteudo: string
  arquivo_pdf?: File | null
}

export interface EdicaoGremioConteudo {
  titulo?: string
  conteudo?: string
  arquivo_pdf?: File | null
  remover_arquivo_pdf?: boolean
}

// Estado singleton, mesmo padrão de useProjetos/useNoticias: o Manager
// (admin) e a página pública "O Grêmio" enxergam a mesma lista.
const itens = ref<GremioConteudo[]>([])
const loaded = ref(false)
const loading = ref(false)
const error = ref('')

async function fetchConteudos(force = false): Promise<void> {
  if (loaded.value && !force) return
  loading.value = true
  error.value = ''
  try {
    itens.value = await gremioConteudosService.list()
    loaded.value = true
  } catch {
    error.value = 'Não foi possível carregar as informações institucionais.'
  } finally {
    loading.value = false
  }
}

export function useGremioConteudos() {
  async function adicionar(dados: NovoGremioConteudo): Promise<void> {
    const criado = await gremioConteudosService.create(dados)
    itens.value = [...itens.value, criado]
  }

  async function atualizar(id: number, dados: EdicaoGremioConteudo): Promise<void> {
    const atualizado = await gremioConteudosService.update(id, dados)
    itens.value = itens.value.map((i) => (i.id === id ? atualizado : i))
  }

  async function remover(id: number): Promise<void> {
    await gremioConteudosService.remove(id)
    itens.value = itens.value.filter((i) => i.id !== id)
  }

  async function mover(index: number, direcao: 'cima' | 'baixo'): Promise<void> {
    const alvo = direcao === 'cima' ? index - 1 : index + 1
    const a = itens.value[index]
    const b = itens.value[alvo]
    if (!a || !b) return
    itens.value = await gremioConteudosService.reorder(a.id, b.id)
  }

  return { itens, loading, error, fetchConteudos, adicionar, atualizar, remover, mover }
}