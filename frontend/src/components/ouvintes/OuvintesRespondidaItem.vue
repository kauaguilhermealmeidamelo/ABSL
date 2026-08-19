<script setup>
defineProps({
  mensagem: { type: Object, required: true }, // { id, texto, turma, tipo, resposta, data_resposta, data_envio }
})

const TIPO_LABEL = {
  sugestao: 'Sugestão',
  reclamacao: 'Reclamação',
  elogio: 'Elogio',
  duvida: 'Dúvida',
}

function tipoLabel(tipo) {
  return TIPO_LABEL[tipo] || 'Mensagem'
}
</script>

<template>
  <div class="resp-card">
    <div class="resp-header">
      <span class="badge badge-tipo">{{ tipoLabel(mensagem.tipo) }}</span>
      <span v-if="mensagem.turma" class="badge badge-turma">Turma {{ mensagem.turma }}</span>
      <span class="resp-data">{{ mensagem.data_envio }}</span>
    </div>

    <p class="resp-pergunta">{{ mensagem.texto }}</p>

    <div class="resp-resposta">
      <v-icon size="13" color="#1a3f8f" class="resp-icon">mdi-reply</v-icon>
      <div>
        <p class="resp-resposta-titulo">Resposta do Grêmio</p>
        <p class="resp-resposta-texto">{{ mensagem.resposta }}</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.resp-card {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid rgba(13, 31, 60, 0.08);
  padding: 16px 20px;
  font-family: 'DM Sans', sans-serif;
}

.resp-header {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 8px;
}

.badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 10px;
  font-weight: 600;
  padding: 3px 9px;
  border-radius: 999px;
  white-space: nowrap;
}

.badge-tipo {
  background: #eef3fb;
  color: #1a3f8f;
}

.badge-turma {
  background: #f5c518;
  color: #6b4a00;
}

.resp-data {
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  color: #5a6a85;
  margin-left: auto;
}

.resp-pergunta {
  font-size: 14px;
  color: #0d1f3c;
  line-height: 1.6;
  margin: 0 0 10px;
}

.resp-resposta {
  display: flex;
  gap: 10px;
  background: #eff6ff;
  border: 1px solid #dbeafe;
  border-radius: 8px;
  padding: 10px 14px;
}

.resp-icon {
  flex-shrink: 0;
  margin-top: 2px;
}

.resp-resposta-titulo {
  font-size: 10px;
  font-weight: 700;
  color: #1a3f8f;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin: 0 0 3px;
}

.resp-resposta-texto {
  font-size: 13px;
  color: #0d1f3c;
  line-height: 1.6;
  margin: 0;
}

@media (max-width: 480px) {
  .resp-data {
    margin-left: 0;
  }
}
</style>