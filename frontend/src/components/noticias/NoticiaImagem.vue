<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  imagemUrl: { type: String, default: '' },
  titulo: { type: String, required: true },
  curtido: { type: Boolean, default: false },
  origem: { type: String, default: 'gremio' }, // 'gremio' | 'escola'
  index: { type: Number, default: 0 }, // usado só pra variar o gradiente do placeholder
  isAdmin: { type: Boolean, default: false },
})

const emit = defineEmits(['curtir', 'abrir', 'editar', 'excluir'])

// Gradientes de placeholder — mesma paleta navy/dourado do projeto, variando
// por índice do post pra o feed não ficar repetitivo quando não há imagem.
const PHOTO_GRADIENTS = [
  'linear-gradient(160deg, #0a1628 0%, #1a3f8f 50%, #2563eb 100%)',
  'linear-gradient(160deg, #1a3f8f 0%, #0d1f3c 40%, #f5c518 100%)',
  'linear-gradient(160deg, #0f2038 0%, #1e4080 55%, #38bdf8 100%)',
  'linear-gradient(160deg, #0d1f3c 0%, #7c3aed 55%, #2563eb 100%)',
  'linear-gradient(160deg, #0d1f3c 0%, #14532d 55%, #16a34a 100%)',
  'linear-gradient(160deg, #4c1d3a 0%, #1a3f8f 55%, #f5c518 100%)',
]

const gradient = computed(() => PHOTO_GRADIENTS[props.index % PHOTO_GRADIENTS.length])
const badgeLabel = computed(() => (props.origem === 'escola' ? 'Escola' : 'Grêmio'))
const badgeGold = computed(() => props.origem !== 'escola')

const mostrarCoracao = ref(false)
let timeoutClique = null

// Mesmo padrão do Instagram: espera um instante pra ver se um segundo toque
// chega antes de decidir que foi só um toque simples (abre o detalhe).
function onClique() {
  if (timeoutClique) {
    clearTimeout(timeoutClique)
    timeoutClique = null
    dispararCoracao()
    if (!props.curtido) emit('curtir')
    return
  }

  timeoutClique = setTimeout(() => {
    timeoutClique = null
    emit('abrir')
  }, 260)
}

function dispararCoracao() {
  mostrarCoracao.value = false
  requestAnimationFrame(() => {
    mostrarCoracao.value = true
    setTimeout(() => { mostrarCoracao.value = false }, 700)
  })
}
</script>

<template>
  <div class="noticia-imagem" :style="{ background: gradient }" @click="onClique">
    <img v-if="imagemUrl" :src="imagemUrl" :alt="titulo" class="noticia-imagem-img" draggable="false" />
    <div v-else class="noticia-imagem-marca" aria-hidden="true">ABSL</div>

    <div v-if="isAdmin" class="admin-overlay" @click.stop>
      <button type="button" class="admin-btn admin-btn-editar" @click="$emit('editar')">
        <v-icon size="12">mdi-pencil-outline</v-icon>
        Editar
      </button>
      <button type="button" class="admin-btn admin-btn-excluir" @click="$emit('excluir')">
        <v-icon size="12">mdi-trash-can-outline</v-icon>
        Excluir
      </button>
    </div>

    <span class="badge-origem" :class="badgeGold ? 'badge-origem-gold' : 'badge-origem-escola'">
      {{ badgeLabel.toUpperCase() }}
    </span>

    <transition name="coracao-pop">
      <v-icon v-if="mostrarCoracao" size="80" color="#F5A623" class="coracao-flutuante">
        mdi-heart
      </v-icon>
    </transition>
  </div>
</template>

<style scoped>
.noticia-imagem {
  position: relative;
  width: 100%;
  aspect-ratio: 1 / 1;
  overflow: hidden;
  cursor: pointer;
  user-select: none;
}

.noticia-imagem-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.noticia-imagem-marca {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-heading, 'Playfair Display', serif);
  font-weight: 900;
  font-size: 100px;
  line-height: 1;
  color: #ffffff;
  opacity: 0.07;
  pointer-events: none;
}

.admin-overlay {
  position: absolute;
  top: 12px;
  left: 12px;
  z-index: 2;
  display: flex;
  gap: 6px;
}

.admin-btn {
  display: flex;
  align-items: center;
  gap: 4px;
  border: none;
  padding: 4px 10px;
  border-radius: 8px;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
  backdrop-filter: blur(3px);
  transition: opacity 0.15s ease;
}
.admin-btn:hover {
  opacity: 0.85;
}

.admin-btn-editar {
  background: rgba(245, 166, 35, 0.92);
  color: var(--color-navy, #0f2038);
}

.admin-btn-excluir {
  background: rgba(220, 38, 38, 0.88);
  color: #ffffff;
}

.badge-origem {
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 2;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.08em;
  padding: 4px 10px;
  border-radius: var(--radius-pill, 999px);
}

.badge-origem-gold {
  background: var(--color-gold, #f5a623);
  color: var(--color-navy, #0f2038);
}

.badge-origem-escola {
  background: rgba(255, 255, 255, 0.18);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.35);
  backdrop-filter: blur(4px);
}

.coracao-flutuante {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  filter: drop-shadow(0 2px 10px rgba(0, 0, 0, 0.35));
  pointer-events: none;
}

.coracao-pop-enter-active {
  transition: transform 0.25s cubic-bezier(0.2, 1.4, 0.4, 1), opacity 0.25s ease;
}
.coracao-pop-leave-active {
  transition: opacity 0.3s ease;
}
.coracao-pop-enter-from {
  transform: translate(-50%, -50%) scale(0.4);
  opacity: 0;
}
.coracao-pop-leave-to {
  opacity: 0;
}

@media (max-width: 420px) {
  .noticia-imagem-marca {
    font-size: 72px;
  }
}
</style>