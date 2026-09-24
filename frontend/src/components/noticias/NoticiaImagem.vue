<script setup>
import { ref } from 'vue'

const props = defineProps({
  imagemUrl: { type: String, default: '' },
  titulo: { type: String, required: true },
  curtido: { type: Boolean, default: false },
})

const emit = defineEmits(['curtir', 'abrir'])

const mostrarCoracao = ref(false)
let timeoutClique = null

function onClique() {
  // Diferencia toque único (abre detalhe) de toque duplo (curte), do
  // mesmo jeito que o Instagram: espera um instante pra ver se um
  // segundo toque chega antes de decidir que foi só um toque simples.
  if (timeoutClique) {
    clearTimeout(timeoutClique)
    timeoutClique = null
    dispararCoracao()
    // Igual ao Instagram: double-tap sempre mostra o coração, mas só
    // curte se ainda não estava curtido — nunca descurte por double-tap.
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
  <div class="noticia-imagem" @click="onClique">
    <img v-if="imagemUrl" :src="imagemUrl" :alt="titulo" class="noticia-imagem-img" />
    <div v-else class="noticia-imagem-textura" />

    <transition name="coracao-pop">
      <v-icon v-if="mostrarCoracao" size="84" color="#ffffff" class="coracao-flutuante">
        mdi-heart
      </v-icon>
    </transition>
  </div>
</template>

<style scoped>
.noticia-imagem {
  position: relative;
  height: 260px;
  overflow: hidden;
  background: linear-gradient(135deg, #1a3f8f, #16509b);
  cursor: pointer;
}

.noticia-imagem-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.noticia-imagem-textura {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(rgba(255, 255, 255, 0.18) 1.5px, transparent 1.5px);
  background-size: 16px 16px;
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