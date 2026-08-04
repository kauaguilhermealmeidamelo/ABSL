<script setup>
import DiretoriaCard from './DiretoriaCard.vue'

defineProps({
  diretorias: { type: Array, required: true },
})

defineEmits(['selecionar'])
</script>

<template>
  <div class="dir-grid">
    <DiretoriaCard
      v-for="dir in diretorias"
      :key="dir.categoria"
      :icon="dir.icon"
      :nome="dir.nome"
      :total="dir.total"
      @selecionar="$emit('selecionar', dir.categoria)"
    />
  </div>
</template>

<style scoped>
.dir-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
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
</style>