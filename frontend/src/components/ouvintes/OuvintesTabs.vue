<script setup>
defineProps({
  modelValue: { type: String, required: true }, // 'enviar' | 'respondidas' | 'gerenciar'
  mostrarGerenciar: { type: Boolean, default: false },
})

defineEmits(['update:modelValue'])
</script>

<template>
  <div class="tabs">
    <button
      type="button"
      class="tab"
      :class="{ 'tab-active': modelValue === 'enviar' }"
      @click="$emit('update:modelValue', 'enviar')"
    >
      Enviar mensagem
    </button>
    <button
      type="button"
      class="tab"
      :class="{ 'tab-active': modelValue === 'respondidas' }"
      @click="$emit('update:modelValue', 'respondidas')"
    >
      Mensagens respondidas
    </button>
    <button
      v-if="mostrarGerenciar"
      type="button"
      class="tab"
      :class="{ 'tab-active': modelValue === 'gerenciar' }"
      @click="$emit('update:modelValue', 'gerenciar')"
    >
      Gerenciar
    </button>
  </div>
</template>

<style scoped>
.tabs {
  display: inline-flex;
  gap: 4px;
  padding: 4px;
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.1);
  border-radius: 12px;
  margin-bottom: 24px;
  width: fit-content;
  max-width: 100%;
  overflow-x: auto;
  font-family: 'DM Sans', sans-serif;
}

.tab {
  border: none;
  background: transparent;
  padding: 8px 20px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  color: #5a6a85;
  cursor: pointer;
  white-space: nowrap;
  transition: background-color 0.15s ease, color 0.15s ease;
}

.tab:hover {
  color: #0d1f3c;
}

.tab-active,
.tab-active:hover {
  background: #1a3f8f;
  color: #ffffff;
}

@media (max-width: 480px) {
  .tabs {
    width: 100%;
  }
  .tab {
    flex: 1;
    text-align: center;
    padding: 8px 12px;
  }
}
</style>