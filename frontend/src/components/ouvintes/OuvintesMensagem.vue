<template>
  <div class="msg-card">
    <div class="msg-header">
      <div class="msg-badges">
        <span class="badge" :class="mensagem.anonimo ? 'badge-anonima' : 'badge-identificada'">
          <v-icon size="10">{{ mensagem.anonimo ? 'mdi-eye-off-outline' : 'mdi-account-outline' }}</v-icon>
          {{ mensagem.anonimo ? 'Anônima' : 'Identificada' }}
        </span>
        <span v-if="mensagem.nome" class="msg-nome">{{ mensagem.nome }}</span>
        <span v-if="mensagem.email" class="msg-email">· {{ mensagem.email }}</span>
        <span class="badge" :class="statusClass">{{ statusLabel }}</span>
      </div>

      <div class="msg-actions">
        <span class="msg-data">{{ mensagem.data_envio }}</span>
        <button type="button" class="btn-excluir" @click="$emit('excluir', mensagem.id)">
          <v-icon size="13">mdi-trash-can-outline</v-icon>
        </button>
      </div>
    </div>

    <p class="msg-texto">{{ mensagem.texto }}</p>

    <div v-if="mensagem.resposta && !respondendo" class="resposta-box">
      <v-icon size="13" color="#1a3f8f" class="resposta-icon">mdi-reply</v-icon>
      <div>
        <p class="resposta-titulo">Resposta do Grêmio</p>
        <p class="resposta-texto">{{ mensagem.resposta }}</p>
      </div>
    </div>

    <div v-if="respondendo" class="reply-form">
      <textarea
        v-model="replyText"
        rows="2"
        class="reply-textarea"
        placeholder="Escreva uma resposta..."
      />
      <div class="reply-actions">
        <button type="button" class="btn-reply-enviar" @click="salvarResposta">Enviar</button>
        <button type="button" class="btn-reply-cancelar" @click="respondendo = false">Cancelar</button>
      </div>
    </div>

    <button v-else type="button" class="btn-responder" @click="abrirResposta">
      <v-icon size="12">mdi-reply</v-icon>
      {{ mensagem.resposta ? 'Editar resposta' : 'Responder' }}
    </button>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  mensagem: { type: Object, required: true },
})

const emit = defineEmits(['excluir', 'responder'])

const respondendo = ref(false)
const replyText = ref('')

const statusLabel = computed(() => props.mensagem.status)
const statusClass = computed(() => `badge-status-${props.mensagem.status}`)

function abrirResposta() {
  replyText.value = props.mensagem.resposta ?? ''
  respondendo.value = true
}

function salvarResposta() {
  emit('responder', { id: props.mensagem.id, resposta: replyText.value })
  respondendo.value = false
}
</script>

<style scoped>
.msg-card {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid rgba(13, 31, 60, 0.08);
  padding: 16px 20px;
  font-family: 'DM Sans', sans-serif;
}

.msg-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 8px 12px;
  margin-bottom: 8px;
}

.msg-badges {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
}

.badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 10px;
  font-weight: 500;
  padding: 2px 8px;
  border-radius: 999px;
  white-space: nowrap;
}

.badge-anonima {
  background: rgba(13, 31, 60, 0.08);
  color: #5a6a85;
}

.badge-identificada {
  background: #dbeafe;
  color: #1a3f8f;
}

.badge-status-pendente {
  background: #fef3c7;
  color: #b45309;
}

.badge-status-respondida {
  background: #dcfce7;
  color: #15803d;
}

.badge-status-arquivada {
  background: #f3f4f6;
  color: #6b7280;
}

.msg-nome {
  font-size: 12px;
  font-weight: 500;
  color: #0d1f3c;
}

.msg-email {
  font-size: 12px;
  color: #5a6a85;
}

.msg-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

.msg-data {
  font-family: 'DM Mono', monospace;
  font-size: 12px;
  color: #5a6a85;
}

.btn-excluir {
  border: none;
  background: transparent;
  padding: 4px;
  border-radius: 6px;
  color: #f87171;
  cursor: pointer;
  display: flex;
  transition: background-color 0.15s ease, color 0.15s ease;
}

.btn-excluir:hover {
  background: #fef2f2;
  color: #dc2626;
}

.msg-texto {
  font-size: 14px;
  color: #0d1f3c;
  line-height: 1.6;
  margin: 0 0 8px;
}

.resposta-box {
  display: flex;
  gap: 8px;
  background: #eff6ff;
  border: 1px solid #dbeafe;
  border-radius: 8px;
  padding: 8px 12px;
  margin-top: 8px;
}

.resposta-icon {
  flex-shrink: 0;
  margin-top: 2px;
}

.resposta-titulo {
  font-size: 10px;
  font-weight: 600;
  color: #1a3f8f;
  margin: 0 0 2px;
}

.resposta-texto {
  font-size: 12px;
  color: #0d1f3c;
  margin: 0;
}

.reply-form {
  display: flex;
  gap: 8px;
  margin-top: 8px;
  flex-wrap: wrap;
}

.reply-textarea {
  flex: 1 1 200px;
  border: 1px solid rgba(13, 31, 60, 0.15);
  border-radius: 8px;
  padding: 8px 12px;
  font-size: 12px;
  background: #eef3fb;
  font-family: inherit;
  color: #0d1f3c;
  resize: none;
  box-sizing: border-box;
}

.reply-textarea:focus {
  outline: none;
  border-color: #1a3f8f;
}

.reply-actions {
  display: flex;
  flex-direction: row;
  gap: 6px;
  flex-shrink: 0;
}

@media (min-width: 480px) {
  .reply-actions {
    flex-direction: column;
  }
}

.btn-reply-enviar,
.btn-reply-cancelar {
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12px;
  cursor: pointer;
  white-space: nowrap;
}

.btn-reply-enviar {
  border: none;
  background: #1a3f8f;
  color: #ffffff;
}

.btn-reply-cancelar {
  border: 1px solid rgba(13, 31, 60, 0.15);
  background: transparent;
  color: #5a6a85;
}

.btn-responder {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-top: 4px;
  border: none;
  background: transparent;
  padding: 0;
  font-size: 12px;
  color: #1a3f8f;
  cursor: pointer;
}

.btn-responder:hover {
  text-decoration: underline;
}
</style>