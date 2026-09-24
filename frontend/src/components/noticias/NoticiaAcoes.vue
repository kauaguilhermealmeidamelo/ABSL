<script setup>
defineProps({
  curtido: { type: Boolean, default: false },
  curtidasCount: { type: Number, default: 0 },
})

defineEmits(['curtir', 'comentar', 'compartilhar'])
</script>

<template>
  <div class="acoes-wrap">
    <div class="acoes-row">
      <button type="button" class="acao-btn" :class="{ 'acao-btn-ativo': curtido }" @click="$emit('curtir')">
        <v-icon size="24">{{ curtido ? 'mdi-heart' : 'mdi-heart-outline' }}</v-icon>
      </button>
      <button type="button" class="acao-btn" @click="$emit('comentar')">
        <v-icon size="24">mdi-message-outline</v-icon>
      </button>
      <button type="button" class="acao-btn" @click="$emit('compartilhar')">
        <v-icon size="22">mdi-send-variant-outline</v-icon>
      </button>
    </div>

    <p class="curtidas-count">{{ curtidasCount }} curtida{{ curtidasCount !== 1 ? 's' : '' }}</p>
  </div>
</template>

<style scoped>
.acoes-wrap {
  font-family: var(--font-body, 'DM Sans', sans-serif);
}

.acoes-row {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 10px 16px 0;
}

.acao-btn {
  border: none;
  background: transparent;
  padding: 6px;
  margin-left: -6px;
  color: var(--color-text-secondary, #6b7c93);
  cursor: pointer;
  display: flex;
  transition: transform 0.1s ease;
}
.acao-btn:active {
  transform: scale(0.85);
}
.acao-btn-ativo {
  color: var(--color-gold, #f5a623);
}

.curtidas-count {
  padding: 4px 16px 0;
  font-size: 13px;
  font-weight: 700;
  color: var(--color-navy, #0f2038);
  margin: 0;
}
</style>