<script setup>
import { ref } from 'vue'

const props = defineProps({
  semana: { type: String, required: true },
  isAdmin: { type: Boolean, default: false },
})

const emit = defineEmits(['update'])

const editing = ref(false)
const draft = ref(props.semana)

function startEdit() {
  if (!props.isAdmin) return
  draft.value = props.semana
  editing.value = true
}

function confirm() {
  emit('update', draft.value)
  editing.value = false
}
</script>

<template>
  <div class="semana-row">
    <span class="accent-bar" />

    <input
      v-if="isAdmin && editing"
      v-model="draft"
      autofocus
      class="semana-input"
      @blur="confirm"
      @keydown.enter="confirm"
    />
    <button
      v-else
      type="button"
      class="semana-label"
      :class="{ 'semana-label-editable': isAdmin }"
      @click="startEdit"
    >
      Semana: {{ semana }}
      <v-icon v-if="isAdmin" size="12" class="pencil">mdi-pencil</v-icon>
    </button>
  </div>
</template>

<style scoped>
.semana-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
}

.accent-bar {
  width: 4px;
  height: 20px;
  border-radius: 4px;
  background: #f5c518;
  flex-shrink: 0;
}

.semana-label {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: none;
  border: none;
  padding: 0;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  font-weight: 600;
  color: #0d1f3c;
  cursor: default;
}

.semana-label-editable {
  cursor: pointer;
}
.semana-label-editable:hover {
  color: #1a3f8f;
}

.pencil {
  color: #5a6a85;
}

.semana-input {
  border: 1px solid rgba(13, 31, 60, 0.15);
  border-radius: 8px;
  padding: 4px 12px;
  font-size: 14px;
  font-family: 'DM Sans', sans-serif;
  background: #eef3fb;
  color: #0d1f3c;
}
</style>  