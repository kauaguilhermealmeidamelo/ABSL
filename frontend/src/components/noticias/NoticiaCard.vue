<script setup>
import { ref } from 'vue'
import NoticiaImagem from './NoticiaImagem.vue'
import NoticiaAcoes from './NoticiaAcoes.vue'
import NoticiaLegenda from './NoticiaLegenda.vue'
import NoticiaComentariosDialog from './NoticiaComentariosDialog.vue'
import { noticiasService } from '@/services/noticias'

const props = defineProps({
  noticia: { type: Object, required: true },
  isAdmin: { type: Boolean, default: false },
  index: { type: Number, default: 0 },
})

const emit = defineEmits(['abrir', 'editar', 'excluir'])

// Estado local otimista: a UI reage na hora do clique, sem esperar a
// API. Se a chamada falhar (ex: sessão expirou), desfaz.
const curtido = ref(props.noticia.curtido)
const curtidasCount = ref(props.noticia.curtidas_count ?? 0)
const comentariosCount = ref(props.noticia.comentarios_count ?? 0)

async function alternarCurtida() {
  const estadoAnterior = curtido.value
  const contagemAnterior = curtidasCount.value

  curtido.value = !curtido.value
  curtidasCount.value += curtido.value ? 1 : -1

  try {
    const resultado = await noticiasService.curtir(props.noticia.id)
    curtido.value = resultado.curtido
    curtidasCount.value = resultado.curtidas_count
  } catch {
    curtido.value = estadoAnterior
    curtidasCount.value = contagemAnterior
  }
}

// Double-tap na imagem só curte (nunca descurte) — mesmo padrão do IG.
function curtirViaImagem() {
  if (!curtido.value) alternarCurtida()
}

const comentariosAbertos = ref(false)
function onComentarioAdicionado() {
  comentariosCount.value += 1
}

async function compartilhar() {
  const url = `${window.location.origin}/noticias/${props.noticia.id}`
  if (navigator.share) {
    try {
      await navigator.share({ title: props.noticia.titulo, url })
    } catch {
      // usuário cancelou — não é erro
    }
  } else {
    await navigator.clipboard.writeText(url)
  }
}
</script>

<template>
  <article class="noticia-card">
    <NoticiaImagem
      :imagem-url="noticia.imagem_url"
      :titulo="noticia.titulo"
      :curtido="curtido"
      :origem="noticia.categoria"
      :index="index"
      :is-admin="isAdmin"
      @curtir="curtirViaImagem"
      @abrir="$emit('abrir', noticia)"
      @editar="$emit('editar', noticia)"
      @excluir="$emit('excluir', noticia.id)"
    />

    <NoticiaAcoes
      :curtido="curtido"
      :curtidas-count="curtidasCount"
      @curtir="alternarCurtida"
      @comentar="comentariosAbertos = true"
      @compartilhar="compartilhar"
    />

    <NoticiaLegenda
      :titulo="noticia.titulo"
      :descricao="noticia.texto"
      :comentarios-count="comentariosCount"
      :data-publicacao="noticia.data_publicacao"
      @abrir="$emit('abrir', noticia)"
      @ver-comentarios="comentariosAbertos = true"
    />

    <NoticiaComentariosDialog
      v-model="comentariosAbertos"
      :noticia-id="noticia.id"
      @comentario-adicionado="onComentarioAdicionado"
    />
  </article>
</template>

<style scoped>
.noticia-card {
  position: relative;
  background: var(--color-surface, #ffffff);
  border-radius: var(--radius-card, 16px);
  overflow: hidden;
  font-family: var(--font-body, 'DM Sans', sans-serif);
  box-shadow: var(--shadow-card);
}
</style>