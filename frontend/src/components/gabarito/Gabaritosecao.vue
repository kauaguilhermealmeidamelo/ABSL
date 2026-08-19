<script setup>
import GabaritoCard from './Gabaritocard.vue'

defineProps({
  title: { type: String, required: true },
  grupos: { type: Array, required: true },
  isAdmin: { type: Boolean, default: false },
})

defineEmits(['substituir', 'excluir', 'excluir-secao'])
</script>

<template>
  <section v-if="grupos.length" class="gab-section">
    <div class="gab-section-heading">
      <span class="gab-section-title">{{ title }}</span>
      <div class="gab-section-divider" />
    </div>

    <div class="gab-section-grid">
      <GabaritoCard
        v-for="grupo in grupos"
        :key="grupo.label"
        :label="grupo.label"
        :documentos="grupo.documentos"
        :is-admin="isAdmin"
        @substituir="(...args) => $emit('substituir', ...args)"
        @excluir="(id) => $emit('excluir', id)"
        @excluir-secao="(ids) => $emit('excluir-secao', ids)"
      />
    </div>
  </section>
</template>



<style scoped>
/* estilos existentes mantidos sem alteração */
</style>