<script setup>
defineProps({
  modelValue: { type: String, required: true }, // 'gremio' | 'escola'
})

defineEmits(['update:modelValue'])

const TABS = [
  { value: 'gremio', label: 'Notícias do Grêmio' },
  { value: 'escola', label: 'Notícias da Escola' },
]
</script>

<template>
  <div class="tabs">
    <button
      v-for="tab in TABS"
      :key="tab.value"
      type="button"
      class="tab"
      :class="{ 'tab-active': modelValue === tab.value }"
      @click="$emit('update:modelValue', tab.value)"
    >
      {{ tab.label }}
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
  margin-bottom: 20px;
  width: fit-content;
  max-width: 100%;
  overflow-x: auto;
}

.tab {
  border: none;
  background: transparent;
  padding: 8px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #5a6a85;
  cursor: pointer;
  white-space: nowrap;
  font-family: 'DM Sans', sans-serif;
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
</style>