<script setup>
import { PROJECT_CATS, catEmoji } from '@/utils/projetoCategorias'

const props = defineProps({
  projetos: { type: Array, required: true },
  selecionado: { type: String, default: null }, // '__geral__' | categoria | null
})

defineEmits(['selecionar'])

function contarPorCategoria(categoria) {
  return props.projetos.filter((p) => p.categoria === categoria).length
}
</script>

<template>
  <div class="dir-grid">
    <button
      type="button"
      class="dir-card"
      :class="{ 'dir-card-geral-ativo': selecionado === '__geral__' }"
      @click="$emit('selecionar', selecionado === '__geral__' ? null : '__geral__')"
    >
      <span class="dir-emoji">🏛️</span>
      <span class="dir-nome" :class="{ 'dir-nome-ativo': selecionado === '__geral__' }">Geral</span>
      <span class="dir-total">{{ projetos.length }} projetos</span>
    </button>

    <button
      v-for="cat in PROJECT_CATS"
      :key="cat"
      type="button"
      class="dir-card"
      :class="{ 'dir-card-ativo': selecionado === cat }"
      @click="$emit('selecionar', selecionado === cat ? null : cat)"
    >
      <span class="dir-emoji">{{ catEmoji(cat) }}</span>
      <span class="dir-nome" :class="{ 'dir-nome-ativo': selecionado === cat }">{{ cat }}</span>
      <span class="dir-total">{{ contarPorCategoria(cat) }} projeto{{ contarPorCategoria(cat) !== 1 ? 's' : '' }}</span>
    </button>
  </div>
</template>

<style scoped>
.dir-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin-bottom: 32px;
}

@media (min-width: 640px) {
  .dir-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 900px) {
  .dir-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.dir-card {
  height: 112px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 12px;
  border-radius: 12px;
  border: 2px solid rgba(13, 31, 60, 0.1);
  background: #ffffff;
  cursor: pointer;
  text-align: center;
  transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
  font-family: 'DM Sans', sans-serif;
}

.dir-card:hover {
  border-color: rgba(26, 63, 143, 0.4);
  box-shadow: 0 2px 6px rgba(13, 31, 60, 0.08);
}

.dir-card-ativo {
  border-color: #1a3f8f;
  background: rgba(26, 63, 143, 0.08);
  box-shadow: 0 2px 6px rgba(13, 31, 60, 0.08);
}

.dir-card-geral-ativo {
  border-color: #f5c518;
  background: rgba(245, 197, 24, 0.1);
  box-shadow: 0 2px 6px rgba(13, 31, 60, 0.08);
}

.dir-emoji {
  font-size: 22px;
  line-height: 1;
}

.dir-nome {
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.02em;
  line-height: 1.25;
  color: #5a6a85;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.dir-nome-ativo {
  color: #0d1f3c;
}

.dir-total {
  font-size: 10px;
  color: #5a6a85;
}
</style>
