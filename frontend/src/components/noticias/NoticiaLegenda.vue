<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  titulo: { type: String, required: true },
  descricao: { type: String, required: true },
  comentariosCount: { type: Number, default: 0 },
  dataPublicacao: { type: String, default: '' },
})

defineEmits(['abrir', 'verComentarios'])

const LIMITE = 90
const expandido = ref(false)
const isLongo = computed(() => props.descricao.length > 100)
const textoResumido = computed(() => props.descricao.slice(0, LIMITE))
</script>

<template>
  <div class="legenda">
    <p class="legenda-texto">
      <span class="legenda-titulo" @click="$emit('abrir')">{{ titulo }}.</span>
      <template v-if="isLongo && !expandido">
        {{ ' ' }}{{ textoResumido }}…
        <button type="button" class="btn-mais" @click="expandido = true">mais</button>
      </template>
      <template v-else>{{ ' ' }}{{ descricao }}</template>
    </p>

    <button
      v-if="comentariosCount > 0"
      type="button"
      class="ver-comentarios"
      @click="$emit('verComentarios')"
    >
      Ver {{ comentariosCount === 1 ? 'o comentário' : `todos os ${comentariosCount} comentários` }}
    </button>

    <p class="legenda-data">{{ dataPublicacao }}</p>
  </div>
</template>

<style scoped>
.legenda {
  padding: 6px 16px 14px;
  font-family: var(--font-body, 'DM Sans', sans-serif);
}

.legenda-texto {
  font-size: 13.5px;
  line-height: 1.5;
  color: var(--color-navy, #0f2038);
  margin: 0 0 4px;
}

.legenda-titulo {
  font-weight: 700;
  cursor: pointer;
}

.btn-mais {
  border: none;
  background: transparent;
  padding: 0;
  margin-left: 2px;
  font-size: 13.5px;
  font-weight: 700;
  color: var(--color-text-muted, #8a90a8);
  cursor: pointer;
}

.ver-comentarios {
  display: block;
  border: none;
  background: transparent;
  padding: 0;
  color: var(--color-text-muted, #8a90a8);
  font-size: 13px;
  cursor: pointer;
  margin-bottom: 4px;
}
.ver-comentarios:hover {
  text-decoration: underline;
}

.legenda-data {
  font-family: var(--font-mono, 'DM Mono', monospace);
  font-size: 10.5px;
  color: var(--color-text-muted, #8a90a8);
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin: 0;
}
</style>