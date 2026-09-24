<script setup>
defineProps({
  titulo: { type: String, required: true },
  descricao: { type: String, required: true },
  comentariosCount: { type: Number, default: 0 },
  dataPublicacao: { type: String, default: '' },
})

defineEmits(['abrir', 'verComentarios'])
</script>

<template>
  <div class="legenda">
    <p class="legenda-texto" @click="$emit('abrir')">
      <strong>{{ titulo }}.</strong> {{ descricao }}
    </p>

    <button v-if="comentariosCount > 0" type="button" class="ver-comentarios" @click="$emit('verComentarios')">
      Ver {{ comentariosCount === 1 ? 'o comentário' : `todos os ${comentariosCount} comentários` }}
    </button>

    <p class="legenda-data">{{ dataPublicacao }}</p>
  </div>
</template>

<style scoped>
.legenda {
  padding: 6px 16px 14px;
  font-family: 'DM Sans', sans-serif;
}

.legenda-texto {
  font-size: 13.5px;
  line-height: 1.5;
  color: #0d1f3c;
  margin: 0 0 4px;
  cursor: pointer;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.legenda-texto strong {
  font-weight: 700;
}

.ver-comentarios {
  display: block;
  border: none;
  background: transparent;
  padding: 0;
  color: #5a6a85;
  font-size: 12.5px;
  cursor: pointer;
  margin-bottom: 4px;
}
.ver-comentarios:hover {
  text-decoration: underline;
}

.legenda-data {
  font-family: 'DM Mono', monospace;
  font-size: 10.5px;
  color: #8a90a8;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin: 0;
}
</style>