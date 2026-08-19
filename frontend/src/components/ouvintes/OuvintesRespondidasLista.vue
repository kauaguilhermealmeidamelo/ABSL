<script setup>
import OuvintesRespondidaItem from './OuvintesRespondidaItem.vue'

defineProps({
  mensagens: { type: Array, required: true },
  loading: { type: Boolean, default: false },
  error: { type: String, default: '' },
})
</script>

<template>
  <div class="resp-lista-wrapper">
    <p class="resp-lista-intro">
      Central de suporte — perguntas e respostas já publicadas pelo Grêmio. Se sua dúvida já foi respondida aqui, não é preciso enviar de novo.
    </p>

    <p v-if="loading" class="status-msg">Carregando mensagens respondidas...</p>
    <p v-else-if="error" class="status-msg status-erro">{{ error }}</p>

    <template v-else>
      <div v-if="mensagens.length" class="resp-lista">
        <OuvintesRespondidaItem v-for="m in mensagens" :key="m.id" :mensagem="m" />
      </div>
      <p v-else class="status-msg status-vazio">
        Ainda não há mensagens respondidas publicadas.
      </p>
    </template>
  </div>
</template>

<style scoped>
.resp-lista-wrapper {
  font-family: 'DM Sans', sans-serif;
}

.resp-lista-intro {
  font-size: 13px;
  color: #5a6a85;
  line-height: 1.6;
  margin: 0 0 20px;
  max-width: 560px;
}

.resp-lista {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.status-msg {
  color: #5a6a85;
  font-size: 14px;
  padding: 12px 0;
}

.status-erro {
  color: #dc2626;
}

.status-vazio {
  color: #94a3b8;
}
</style>