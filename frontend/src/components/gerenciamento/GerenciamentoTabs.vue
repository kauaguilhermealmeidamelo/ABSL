<script setup>
defineProps({
  modelValue: { type: String, required: true },
  tabs: { type: Array, required: true }, // [{ id, label, icon }]
})
defineEmits(['update:modelValue'])
</script>

<template>
  <div class="tabs">
    <button
      v-for="t in tabs"
      :key="t.id"
      type="button"
      class="tab"
      :class="{ 'tab-active': modelValue === t.id }"
      @click="$emit('update:modelValue', t.id)"
    >
      <v-icon size="14">{{ t.icon }}</v-icon>
      {{ t.label }}
    </button>
  </div>
</template>

<style scoped>
.tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  padding: 4px;
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.1);
  border-radius: 12px;
  margin-bottom: 28px;
  width: fit-content;
  max-width: 100%;
  font-family: 'DM Sans', sans-serif;
}
.tab {
  display: flex;
  align-items: center;
  gap: 6px;
  border: none;
  background: transparent;
  padding: 8px 16px;
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
    justify-content: center;
    padding: 8px 10px;
  }
}
</style>