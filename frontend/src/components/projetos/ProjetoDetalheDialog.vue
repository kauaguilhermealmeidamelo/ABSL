<script setup>
import { catEmoji } from '@/utils/projetoCategorias'

const props = defineProps({
  projeto: { type: Object, required: true },
  isAdmin: { type: Boolean, default: false },
})

const emit = defineEmits(['fechar', 'editar', 'excluir'])
</script>

<template>
  <div class="overlay" @click.self="$emit('fechar')">
    <div class="dialog">
      <div v-if="projeto.imagem_url" class="dialog-imagem">
        <img :src="projeto.imagem_url" :alt="projeto.titulo" />
      </div>

      <div class="dialog-body">
        <div class="dialog-topo">
          <div class="dialog-info">
            <div class="dialog-badges">
              <span class="dialog-categoria">{{ catEmoji(projeto.categoria) }} {{ projeto.categoria }}</span>
              <span class="dialog-status" :class="projeto.status === 'concluido' ? 'status-concluido' : 'status-andamento'">
                {{ projeto.status === 'concluido' ? 'CONCLUÍDO' : 'EM ANDAMENTO' }}
              </span>
            </div>
            <h2 class="dialog-titulo">{{ projeto.titulo }}</h2>
          </div>
          <button type="button" class="btn-fechar" @click="$emit('fechar')">
            <v-icon size="18">mdi-close</v-icon>
          </button>
        </div>

        <div class="dialog-divisor" />

        <p class="dialog-descricao">{{ projeto.descricao }}</p>

        <div v-if="projeto.data_conclusao" class="dialog-conclusao">
          <v-icon size="13" color="#15803d">mdi-check</v-icon>
          <span>Concluído em: <strong>{{ projeto.data_conclusao }}</strong></span>
        </div>

        <div v-if="isAdmin" class="dialog-acoes">
          <button type="button" class="btn-editar" @click="$emit('editar', projeto)">Editar</button>
          <button type="button" class="btn-excluir" @click="$emit('excluir', projeto.id)">Excluir</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.overlay {
  position: fixed;
  inset: 0;
  z-index: 60;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.5);
  padding: 16px;
}

.dialog {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 25px 50px rgba(4, 20, 40, 0.3);
  width: 100%;
  max-width: 560px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  font-family: 'DM Sans', sans-serif;
}

.dialog-imagem {
  height: 208px;
  flex-shrink: 0;
  overflow: hidden;
}

.dialog-imagem img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.dialog-body {
  overflow-y: auto;
  padding: 24px;
  flex: 1;
}

.dialog-topo {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
}

.dialog-info {
  flex: 1;
  min-width: 0;
}

.dialog-badges {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 8px;
}

.dialog-categoria {
  font-family: 'DM Mono', monospace;
  font-size: 10px;
  font-weight: 700;
  color: #1a3f8f;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.dialog-status {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  padding: 3px 10px;
  border-radius: 999px;
}

.status-concluido {
  background: #dcfce7;
  color: #15803d;
}

.status-andamento {
  background: #dbeafe;
  color: #1a3f8f;
}

.dialog-titulo {
  font-family: 'Playfair Display', serif;
  font-weight: 700;
  font-size: 21px;
  color: #0d1f3c;
  margin: 0;
  line-height: 1.3;
}

.btn-fechar {
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: transparent;
  color: #5a6a85;
  padding: 6px;
  border-radius: 8px;
  cursor: pointer;
  flex-shrink: 0;
  transition: background-color 0.15s ease;
}
.btn-fechar:hover {
  background: #eef3fb;
}

.dialog-divisor {
  width: 32px;
  height: 3px;
  background: #f5c518;
  border-radius: 4px;
  margin-bottom: 16px;
}

.dialog-descricao {
  color: #5a6a85;
  font-size: 14px;
  line-height: 1.65;
  margin: 0 0 20px;
}

.dialog-conclusao {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: #15803d;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 12px;
  padding: 10px 16px;
  margin-bottom: 20px;
}

.dialog-acoes {
  display: flex;
  gap: 10px;
  padding-top: 16px;
  border-top: 1px solid rgba(13, 31, 60, 0.08);
}

.btn-editar,
.btn-excluir {
  flex: 1;
  padding: 9px;
  border-radius: 12px;
  font-size: 13px;
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
</style>
