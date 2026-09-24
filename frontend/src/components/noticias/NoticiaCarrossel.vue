<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  midias: { type: Array, default: () => [] }, // [{ id, tipo, url }]
  titulo: { type: String, required: true },
  curtido: { type: Boolean, default: false },
})

const emit = defineEmits(['curtir', 'abrir'])

const indiceAtual = ref(0)
const trilhaRef = ref(null)
const videoRefs = ref([])

function registrarVideo(el, i) {
  videoRefs.value[i] = el
}

function onScroll() {
  const el = trilhaRef.value
  if (!el) return
  const novoIndice = Math.round(el.scrollLeft / el.clientWidth)
  if (novoIndice === indiceAtual.value) return

  // Só o vídeo do slide visível toca — os outros pausam. Evita vários
  // vídeos rodando ao mesmo tempo fora de vista (gasto de bateria/dados).
  videoRefs.value[indiceAtual.value]?.pause()
  indiceAtual.value = novoIndice
  const atual = videoRefs.value[indiceAtual.value]
  if (atual) {
    atual.currentTime = 0
    atual.play().catch(() => { })
  }
}

onMounted(() => {
  videoRefs.value[0]?.play().catch(() => { })
})

const mostrarCoracao = ref(false)
let timeoutClique = null

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

const temMultiplas = computed(() => props.midias.length > 1)
</script>

<template>
  <div class="carrossel">
    <div v-if="midias.length" ref="trilhaRef" class="carrossel-trilha" @scroll="onScroll" @click="onClique">
      <div v-for="(m, i) in midias" :key="m.id ?? m.url" class="carrossel-item">
        <video v-if="m.tipo === 'video'" :ref="el => registrarVideo(el, i)" :src="m.url" muted loop playsinline />
        <img v-else :src="m.url" :alt="titulo" />
      </div>
    </div>
    <div v-else class="carrossel-textura" @click="onClique" />

    <span v-if="temMultiplas" class="carrossel-contador">{{ indiceAtual + 1 }}/{{ midias.length }}</span>

    <div v-if="temMultiplas" class="carrossel-dots">
      <span v-for="(m, i) in midias" :key="m.id ?? i" class="dot" :class="{ 'dot-ativo': i === indiceAtual }" />
    </div>

    <transition name="coracao-pop">
      <v-icon v-if="mostrarCoracao" size="84" color="#ffffff" class="coracao-flutuante">mdi-heart</v-icon>
    </transition>
  </div>
</template>

<style scoped>
.carrossel {
  position: relative;
  height: 340px;
  overflow: hidden;
  background: linear-gradient(135deg, #1a3f8f, #16509b);
}

.carrossel-trilha {
  display: flex;
  height: 100%;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  scrollbar-width: none;
  cursor: pointer;
}

.carrossel-trilha::-webkit-scrollbar {
  display: none;
}

.carrossel-item {
  flex: 0 0 100%;
  scroll-snap-align: start;
  height: 100%;
}

.carrossel-item img,
.carrossel-item video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.carrossel-textura {
  height: 100%;
  cursor: pointer;
  background-image: radial-gradient(rgba(255, 255, 255, 0.18) 1.5px, transparent 1.5px);
  background-size: 16px 16px;
}

.carrossel-contador {
  position: absolute;
  top: 12px;
  right: 12px;
  background: rgba(0, 0, 0, 0.5);
  color: #fff;
  font-size: 11px;
  font-weight: 600;
  padding: 3px 9px;
  border-radius: 999px;
  font-family: 'DM Mono', monospace;
}

.carrossel-dots {
  position: absolute;
  bottom: 10px;
  left: 0;
  right: 0;
  display: flex;
  justify-content: center;
  gap: 5px;
}

.dot {
  width: 5px;
  height: 5px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.5);
}

.dot-ativo {
  background: #ffffff;
  width: 6px;
  height: 6px;
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
</style>