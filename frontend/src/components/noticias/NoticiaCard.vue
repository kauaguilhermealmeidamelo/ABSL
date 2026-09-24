<script setup>
import { ref } from 'vue'
import NoticiaCarrossel from './NoticiaCarrossel.vue'
import NoticiaAcoes from './NoticiaAcoes.vue'
import NoticiaLegenda from './NoticiaLegenda.vue'
import { noticiasService } from '@/services/noticias'

const props = defineProps({
  noticia: { type: Object, required: true },
  isAdmin: { type: Boolean, default: false },
})

const emit = defineEmits(['abrir', 'editar', 'excluir'])

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
    <div v-if="isAdmin" class="admin-acoes" @click.stop>
      <button type="button" class="admin-btn" @click="$emit('editar', noticia)">
        <v-icon size="14">mdi-pencil-outline</v-icon>
      </button>
      <button type="button" class="admin-btn admin-btn-excluir" @click="$emit('excluir', noticia.id)">
        <v-icon size="14">mdi-trash-can-outline</v-icon>
      </button>
    </div>

    <NoticiaCarrossel :midias="noticia.midias" :titulo="noticia.titulo" :curtido="curtido" @curtir="curtirViaImagem"
      @abrir="$emit('abrir', noticia)" />

    <NoticiaAcoes :curtido="curtido" :curtidas-count="curtidasCount" @curtir="alternarCurtida"
      @comentar="comentariosAbertos = true" @compartilhar="compartilhar" />

    <NoticiaLegenda :titulo="noticia.titulo" :descricao="noticia.texto" :comentarios-count="comentariosCount"
      :data-publicacao="noticia.data_publicacao" @abrir="$emit('abrir', noticia)"
      @ver-comentarios="comentariosAbertos = true" />

    <NoticiaComentariosSheet v-model="comentariosAbertos" :noticia-id="noticia.id" :is-admin="isAdmin"
      @comentario-adicionado="onComentarioAdicionado" />
  </article>
</template>

<style scoped>
.noticia-card {
  position: relative;
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 16px;
  overflow: hidden;
  font-family: 'DM Sans', sans-serif;
}

.admin-acoes {
  position: absolute;
  top: 10px;
  right: 10px;
  z-index: 2;
  display: flex;
  gap: 6px;
}

.admin-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 999px;
  border: none;
  background: rgba(13, 31, 60, 0.55);
  color: #ffffff;
  cursor: pointer;
  backdrop-filter: blur(2px);
  transition: background-color 0.15s ease;
}

.admin-btn:hover {
  background: rgba(13, 31, 60, 0.75);
}

.admin-btn-excluir:hover {
  background: #dc2626;
}
</style>