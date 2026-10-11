<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  projeto: { type: Object, required: true },
  isAdmin: { type: Boolean, default: false },
})

const emit = defineEmits(['editar', 'excluir', 'abrir'])
const imagemFalhou = ref(false)

watch(
  () => props.projeto.imagem_url,
  () => {
    imagemFalhou.value = false
  },
)

function abrirComTeclado(event) {
  if (event.target instanceof Element && event.target.closest('button, a, input, select, textarea')) {
    return
  }

  if (event.key === 'Enter' || event.key === ' ') {
    event.preventDefault()
    emit('abrir', props.projeto)
  }
}
</script>

<template>
  <article
    class="projeto-card"
    role="button"
    tabindex="0"
    @click="emit('abrir', projeto)"
    @keydown="abrirComTeclado"
  >
    <div v-if="projeto.imagem_url" class="projeto-imagem">
      <img
        v-if="!imagemFalhou"
        :src="projeto.imagem_url"
        :alt="projeto.titulo || 'Imagem do projeto'"
        loading="lazy"
        @error="imagemFalhou = true"
      />
      <div
        v-else
        class="projeto-imagem-fallback"
        role="img"
        :aria-label="`Imagem indisponível para o projeto ${projeto.titulo || ''}`"
      >
        <v-icon size="30" aria-hidden="true">mdi-image-off-outline</v-icon>
        <span>Imagem indisponível</span>
      </div>
    </div>

    <div class="projeto-corpo">
      <div class="projeto-topo">
        <span class="projeto-categoria">{{ projeto.categoria || 'Projeto' }}</span>
        <span class="projeto-status" :class="projeto.status === 'concluido' ? 'status-concluido' : 'status-andamento'">
          {{ projeto.status === 'concluido' ? 'CONCLUÍDO' : 'EM ANDAMENTO' }}
        </span>
      </div>

      <h3 class="projeto-titulo">{{ projeto.titulo }}</h3>
      <p class="projeto-descricao">{{ projeto.descricao }}</p>
      <p v-if="projeto.data_conclusao && projeto.status === 'concluido'" class="projeto-conclusao">
        Concluído em: {{ projeto.data_conclusao }}
      </p>

      <div v-if="isAdmin" class="projeto-acoes" @click.stop>
        <button type="button" class="btn-editar" @click="$emit('editar', projeto)">
          <v-icon size="13" aria-hidden="true">mdi-pencil-outline</v-icon>
          Editar
        </button>
        <button type="button" class="btn-excluir" @click="$emit('excluir', projeto.id)">
          <v-icon size="13" aria-hidden="true">mdi-trash-can-outline</v-icon>
          Excluir
        </button>
      </div>
    </div>
  </article>
</template>

<style scoped>
.projeto-card {
  min-width: 0;
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.1);
  border-radius: 16px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  transition: box-shadow 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
}

.projeto-card:hover,
.projeto-card:focus-visible {
  border-color: rgba(26, 63, 143, 0.25);
  box-shadow: 0 8px 24px rgba(13, 31, 60, 0.1);
}

.projeto-card:hover {
  transform: translateY(-2px);
}

.projeto-card:focus-visible {
  outline: 3px solid rgba(26, 63, 143, 0.45);
  outline-offset: 3px;
}

.projeto-imagem {
  height: 160px;
  overflow: hidden;
  background: #f1f5f9;
}

.projeto-imagem img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.projeto-card:hover .projeto-imagem img {
  transform: scale(1.04);
}

.projeto-imagem-fallback {
  display: flex;
  width: 100%;
  height: 100%;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: #64748b;
  font-size: 12px;
}

.projeto-corpo {
  padding: 20px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.projeto-topo {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 10px;
}

.projeto-categoria {
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  font-weight: 700;
  color: #1a3f8f;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.projeto-status {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  padding: 4px 10px;
  border-radius: 999px;
  white-space: nowrap;
}

.status-concluido {
  background: #dcfce7;
  color: #15803d;
}

.status-andamento {
  background: #dbeafe;
  color: #1a3f8f;
}

.projeto-titulo {
  color: #0d1f3c;
  font-weight: 700;
  font-size: 16px;
  line-height: 1.4;
  margin: 0 0 7px;
  transition: color 0.15s ease;
  overflow-wrap: anywhere;
}

.projeto-card:hover .projeto-titulo {
  color: #1a3f8f;
}

.projeto-descricao {
  color: #5a6a85;
  font-size: 13px;
  line-height: 1.6;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  overflow-wrap: anywhere;
}

.projeto-conclusao {
  font-size: 11px;
  color: #16a34a;
  margin: 10px 0 0;
}

.projeto-acoes {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 14px;
  padding-top: 14px;
  border-top: 1px solid rgba(13, 31, 60, 0.08);
}

.btn-editar,
.btn-excluir {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  background: transparent;
  transition: background-color 0.15s ease;
}

.btn-editar {
  border: 1px solid #fbbf24;
  color: #b45309;
}

.btn-editar:hover {
  background: #fffbeb;
}

.btn-excluir {
  border: 1px solid #fca5a5;
  color: #dc2626;
}

.btn-excluir:hover {
  background: #fef2f2;
}

@media (prefers-reduced-motion: reduce) {
  .projeto-card,
  .projeto-imagem img,
  .projeto-titulo,
  .btn-editar,
  .btn-excluir {
    transition: none;
  }

  .projeto-card:hover {
    transform: none;
  }
}
</style>
