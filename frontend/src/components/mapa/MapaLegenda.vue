<script setup>
import { CATEGORIAS } from '@/utils/mapaEscolaAreas'

defineProps({
  categoriaAtiva: { type: String, default: null },
})

defineEmits(['selecionar-categoria'])

const entradas = Object.entries(CATEGORIAS)
</script>

<template>
  <div class="legenda">
    <button
      v-for="[chave, cat] in entradas"
      :key="chave"
      type="button"
      class="legenda-item"
      :class="{ 'legenda-item-ativa': categoriaAtiva === chave }"
      @click="$emit('selecionar-categoria', categoriaAtiva === chave ? null : chave)"
    >
      <span class="legenda-dot" :style="{ backgroundColor: cat.cor }" />
      <span>{{ cat.label }}</span>
    </button>
  </div>
</template>

<style scoped>
.legenda {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 16px;
  font-family: 'DM Sans', sans-serif;
}

.legenda-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 6px 12px;
  border-radius: 999px;
  border: 1px solid rgba(13, 31, 60, 0.1);
  background: #ffffff;
  color: #5a6a85;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: border-color 0.15s ease, color 0.15s ease, background-color 0.15s ease;
  white-space: nowrap;
}

.legenda-item:hover {
  border-color: rgba(26, 63, 143, 0.4);
  color: #0d1f3c;
}

.legenda-item-ativa {
  background: #eef3fb;
  border-color: #1a3f8f;
  color: #0d1f3c;
  font-weight: 700;
}

.legenda-dot {
  width: 10px;
  height: 10px;
  border-radius: 999px;
  flex-shrink: 0;
}
</style>