<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PageHeader from '@/components/common/PageHeader.vue'
import ProjetoFormModal from '@/components/projetos/ProjetoFormModal.vue'
import { useAdmin } from '@/composables/useAdmin'
import { useProjetos } from '@/composables/useProjetos'
import { catEmoji } from '@/utils/projetoCategorias'

const route = useRoute()
const router = useRouter()
const { isAdmin } = useAdmin()
const { getById, fetchProjetos, atualizar, remover, error } = useProjetos()

const projeto = computed(() => getById(route.params.id))

onMounted(async () => {
  await fetchProjetos()
})

function voltar() {
  router.push('/projetos')
}

const modalAberto = ref(false)

function abrirEdicao() {
  modalAberto.value = true
}

async function salvar(dados) {
  if (!projeto.value) return
  try {
    await atualizar(projeto.value.id, dados)
  } catch {
    error.value = 'Não foi possível salvar o projeto.'
  }
}

async function excluir() {
  if (!projeto.value) return
  if (!confirm('Confirma exclusão deste projeto?')) return
  try {
    await remover(projeto.value.id)
    voltar()
  } catch {
    error.value = 'Não foi possível excluir o projeto.'
  }
}
</script>

<template>
  <div class="detalhe-page">
    <template v-if="projeto">
      <button type="button" class="btn-voltar" @click="voltar">
        <v-icon size="16">mdi-arrow-left</v-icon>
        Voltar para Projetos
      </button>

      <div class="detalhe-imagem">
        <img v-if="projeto.imagem_url" :src="projeto.imagem_url" :alt="projeto.titulo" />
        <div v-else class="detalhe-imagem-textura" />
      </div>

      <div class="detalhe-badges">
        <span class="detalhe-badge badge-categoria">
          {{ catEmoji(projeto.categoria) }} {{ projeto.categoria || projeto.diretoria }}
        </span>
        <span
          class="detalhe-badge"
          :class="projeto.status === 'concluido' ? 'badge-concluido' : 'badge-andamento'"
        >
          {{ projeto.status === 'concluido' ? 'CONCLUÍDO' : 'EM ANDAMENTO' }}
        </span>
      </div>

      <h1 class="detalhe-titulo">{{ projeto.titulo }}</h1>
      <p class="detalhe-texto">{{ projeto.descricao }}</p>
      <p v-if="projeto.conteudo_detalhado" class="detalhe-texto">{{ projeto.conteudo_detalhado }}</p>

      <div v-if="projeto.data_conclusao && projeto.status === 'concluido'" class="detalhe-conclusao">
        <v-icon size="14" color="#15803d">mdi-check</v-icon>
        <span>Concluído em: <strong>{{ projeto.data_conclusao }}</strong></span>
      </div>

      <div v-if="isAdmin" class="detalhe-acoes">
        <button type="button" class="btn-editar" @click="abrirEdicao">
          <v-icon size="14">mdi-pencil-outline</v-icon>
          Editar
        </button>
        <button type="button" class="btn-excluir" @click="excluir">
          <v-icon size="14">mdi-trash-can-outline</v-icon>
          Excluir
        </button>
      </div>

      <ProjetoFormModal v-model="modalAberto" :projeto="projeto" @salvar="salvar" />
    </template>

    <template v-else>
      <PageHeader label="ABSL" title="Projeto não encontrado" />
      <button type="button" class="btn-voltar" @click="voltar">
        <v-icon size="16">mdi-arrow-left</v-icon>
        Voltar para Projetos
      </button>
    </template>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700;1,900&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400;1,9..40,700&display=swap');

.detalhe-page {
  font-family: 'DM Sans', sans-serif;
  max-width: 720px;
  margin: 0 auto;
  padding: 32px 24px 64px;
}

.btn-voltar {
  display: flex;
  align-items: center;
  gap: 6px;
  border: none;
  background: transparent;
  color: #1a3f8f;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  padding: 0;
  margin-bottom: 20px;
}
.btn-voltar:hover {
  text-decoration: underline;
}

.detalhe-imagem {
  height: 220px;
  border-radius: 16px;
  overflow: hidden;
  background: linear-gradient(135deg, #1a3f8f, #16509b);
  position: relative;
  margin-bottom: 20px;
}
.detalhe-imagem img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.detalhe-imagem-textura {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(rgba(255, 255, 255, 0.18) 1.5px, transparent 1.5px);
  background-size: 16px 16px;
}

.detalhe-badges {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 12px;
}

.detalhe-badge {
  display: inline-block;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  padding: 4px 12px;
  border-radius: 999px;
}
.badge-categoria {
  background: #eef3fb;
  color: #1a3f8f;
  font-family: 'DM Mono', monospace;
  letter-spacing: 0.02em;
}
.badge-concluido {
  background: #dcfce7;
  color: #15803d;
}
.badge-andamento {
  background: #dbeafe;
  color: #1a3f8f;
}

.detalhe-titulo {
  font-family: 'Playfair Display', serif;
  font-weight: 700;
  font-size: 28px;
  color: #0d1f3c;
  line-height: 1.3;
  margin: 0 0 20px;
}

.detalhe-texto {
  color: #3d4a5c;
  font-size: 16px;
  line-height: 1.8;
  margin: 0 0 16px;
}

.detalhe-conclusao {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #15803d;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 12px;
  padding: 10px 16px;
  margin-bottom: 20px;
  width: fit-content;
}

.detalhe-acoes {
  display: flex;
  gap: 10px;
  padding-top: 16px;
  border-top: 1px solid rgba(13, 31, 60, 0.08);
}

.btn-editar,
.btn-excluir {
  padding: 9px 18px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  background: transparent;
  transition: background-color 0.15s ease;
}
.btn-editar {
  border: 1px solid #fbbf24;
  color: #b45309;
}
.btn-editar:hover {
  background: #fffbeb;
}
.btn-excluir {
  border: 1px solid #fca5a5;
  color: #dc2626;
}
.btn-excluir:hover {
  background: #fef2f2;
}

@media (max-width: 480px) {
  .detalhe-page {
    padding: 20px;
  }
  .detalhe-titulo {
    font-size: 22px;
  }
}
</style>