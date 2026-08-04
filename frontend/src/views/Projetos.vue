<template>
  <div class="projetos-page">
    <button v-if="categoriaSelecionada !== undefined" type="button" class="btn-voltar" @click="voltar">
      <v-icon size="16">mdi-arrow-left</v-icon>
      Voltar às diretorias
    </button>

    <PageHeader
      label="ABSL"
      :title="tituloPagina"
      :subtitle="subtituloPagina"
    />

    <DiretoriasGrid v-if="categoriaSelecionada === undefined" :diretorias="diretorias" @selecionar="selecionarDiretoria" />

    <ProjetosGrid
      v-else
      :projetos="projetosFiltrados"
      :is-admin="isAdmin"
      @novo="abrirNovo"
      @editar="abrirEdicao"
      @excluir="excluirProjeto"
    />

    <ProjetoFormModal
      v-model="modalAberto"
      :projeto="projetoEmEdicao"
      :categorias="CATEGORIAS"
      :categoria-padrao="categoriaSelecionada || undefined"
      @salvar="salvarProjeto"
    />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import DiretoriasGrid from '@/components/projetos/DiretoriasGrid.vue'
import ProjetosGrid from '@/components/projetos/ProjetosGrid.vue'
import ProjetoFormModal from '@/components/projetos/ProjetoFormModal.vue'

// Mesmo critério usado em AdminCard.vue / menuLateral.vue / Gabarito.vue / Ouvintes.vue para detectar admin
const isAdmin = computed(() => {
  const raw = localStorage.getItem('usuario')
  if (!raw) return false

  try {
    const parsed = JSON.parse(raw)
    if (parsed && typeof parsed === 'object') {
      const role = String(
        parsed.role || parsed.tipo || parsed.perfil || parsed.is_admin || parsed.administrador || ''
      ).toLowerCase()
      return (
        role === 'admin' ||
        role === 'administrator' ||
        role === 'administrador' ||
        role === 'super_admin' ||
        role === 'super-admin' ||
        parsed.is_admin === true ||
        parsed.administrador === true
      )
    }
  } catch {
    // fallback para valores simples armazenados como texto
  }

  return String(raw).toLowerCase().includes('admin')
})

// Diretorias com os mesmos ícones mdi usados em Equipe.vue, para manter consistência visual
const DIRETORIAS_BASE = [
  { categoria: 'Presidência', icon: 'mdi-crown' },
  { categoria: 'Secretaria', icon: 'mdi-clipboard-text-outline' },
  { categoria: 'Tesouraria', icon: 'mdi-cash' },
  { categoria: 'Esporte e Lazer', icon: 'mdi-run-fast' },
  { categoria: 'Cultura', icon: 'mdi-theater' },
  { categoria: 'Políticas Educacionais', icon: 'mdi-school-outline' },
  { categoria: 'Saúde e Meio Ambiente', icon: 'mdi-leaf' },
  { categoria: 'Diretoria Social', icon: 'mdi-handshake-outline' },
  { categoria: 'Imprensa e Comunicação', icon: 'mdi-bullhorn-variant-outline' },
  { categoria: 'Tecnologia e Inovação', icon: 'mdi-laptop' },
]

const CATEGORIAS = DIRETORIAS_BASE.map((d) => d.categoria)

// TODO: substituir por chamadas à API (não há endpoint de projetos no backend ainda)
const projetos = ref([
  {
    id: 1,
    categoria: 'Diretoria Social',
    status: 'concluido',
    titulo: 'Campanha do Agasalho 2026',
    descricao: 'Arrecadação de roupas e calçados em todas as turmas, com entrega a instituições da cidade.',
  },
  {
    id: 2,
    categoria: 'Esporte e Lazer',
    status: 'em_andamento',
    titulo: 'Torneio Interclasses',
    descricao: 'Competição entre turmas de futsal, vôlei e handebol, com tabela publicada e arbitragem voluntária.',
  },
  {
    id: 3,
    categoria: 'Cultura',
    status: 'em_andamento',
    titulo: 'Feira Cultural ABSL',
    descricao: 'Mostra de música, teatro e artes visuais produzidos pelos alunos, com palco montado no ginásio.',
  },
  {
    id: 4,
    categoria: 'Presidência',
    status: 'em_andamento',
    titulo: 'Reforma do Espaço de Convivência',
    descricao: 'Bancos, sombrite e torneiras no pátio central, negociadas junto à direção a partir de pauta do estudantil.',
  },
  {
    id: 5,
    categoria: 'Saúde e Meio Ambiente',
    status: 'em_andamento',
    titulo: 'Semana da Saúde Mental',
    descricao: 'Rodas de conversa e atividades com profissionais convidados durante os intervalos.',
  },
  {
    id: 6,
    categoria: 'Tecnologia e Inovação',
    status: 'em_andamento',
    titulo: 'Portal do Estudante',
    descricao: 'Canal digital com horários, gabaritos e notícias do grêmio reunidos em um só lugar.',
  },
])

// Grid de seleção: "Geral" (total) + uma entrada por diretoria, com contagem dinâmica
const diretorias = computed(() => [
  { categoria: null, icon: 'mdi-bank', nome: 'Geral', total: projetos.value.length },
  ...DIRETORIAS_BASE.map((d) => ({
    categoria: d.categoria,
    icon: d.icon,
    nome: d.categoria,
    total: projetos.value.filter((p) => p.categoria === d.categoria).length,
  })),
])

// undefined = tela de seleção de diretorias; null = "Geral" (todos os projetos); string = diretoria específica
const categoriaSelecionada = ref(undefined)

const projetosFiltrados = computed(() =>
  categoriaSelecionada.value
    ? projetos.value.filter((p) => p.categoria === categoriaSelecionada.value)
    : projetos.value,
)

const tituloPagina = computed(() => 'Projetos')
const subtituloPagina = computed(() => {
  if (categoriaSelecionada.value === undefined) return 'Selecione uma diretoria para ver seus projetos.'
  if (categoriaSelecionada.value === null) return 'Todos os projetos do Grêmio, de todas as diretorias.'
  return `Projetos conduzidos pela diretoria de ${categoriaSelecionada.value}.`
})

function selecionarDiretoria(categoria) {
  categoriaSelecionada.value = categoria
}

function voltar() {
  categoriaSelecionada.value = undefined
}

const modalAberto = ref(false)
const projetoEmEdicao = ref(null)

function abrirNovo() {
  projetoEmEdicao.value = null
  modalAberto.value = true
}

function abrirEdicao(projeto) {
  projetoEmEdicao.value = projeto
  modalAberto.value = true
}

function salvarProjeto(dados) {
  if (projetoEmEdicao.value) {
    projetos.value = projetos.value.map((p) =>
      p.id === projetoEmEdicao.value.id ? { ...p, ...dados } : p,
    )
  } else {
    projetos.value.push({ id: Date.now(), ...dados })
  }
  projetoEmEdicao.value = null
}

function excluirProjeto(id) {
  projetos.value = projetos.value.filter((p) => p.id !== id)
}
</script>

<style scoped>
.projetos-page {
  font-family: 'DM Sans', sans-serif;
  padding: 24px;
}

.btn-voltar {
  display: flex;
  align-items: center;
  gap: 6px;
  border: none;
  background: transparent;
  color: #1a3f8f;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  padding: 0;
  margin-bottom: 16px;
}

.btn-voltar:hover {
  text-decoration: underline;
}

@media (max-width: 480px) {
  .projetos-page {
    padding: 16px;
  }
}
</style>