<template>
  <div class="filtros">
    <div class="pill-group">
      <button
        v-for="t in turnos"
        :key="t.value"
        class="pill"
        :class="{ active: turno === t.value }"
        @click="$emit('update:turno', t.value)"
      >
        {{ t.label }}
      </button>
    </div>

    <div class="pill-group">
      <button
        v-for="a in anos"
        :key="a"
        class="pill"
        :class="{ active: ano === a }"
        @click="$emit('update:ano', a)"
      >
        {{ a }}
      </button>
    </div>

    <div class="select-wrap">
      <select
        class="turma-select"
        :value="turma"
        @change="$emit('update:turma', $event.target.value)"
      >
        <option v-for="t in turmas" :key="t" :value="t">{{ t }}</option>
      </select>
      <span class="chevron">⌄</span>
    </div>

    <span class="badge">{{ turno === 'matutino' ? '07:00 – 12:00' : '13:00 – 18:00' }}</span>
  </div>
</template>

<script setup>
defineProps({
  turno: { type: String, required: true },
  ano: { type: String, required: true },
  turma: { type: String, required: true },
  anos: { type: Array, required: true },
  turmas: { type: Array, required: true },
})
defineEmits(['update:turno', 'update:ano', 'update:turma'])

const turnos = [
  { value: 'matutino', label: 'Matutino' },
  { value: 'vespertino', label: 'Vespertino' },
]
</script>

<style scoped>
.filtros {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px;
  margin-bottom: 24px;
  font-family: 'DM Sans', sans-serif;
}

.pill-group {
  display: flex;
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.12);
  border-radius: 999px;
  padding: 2px;
}

.pill {
  border: none;
  background: transparent;
  padding: 7px 16px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 500;
  color: #5a6a85;
  cursor: pointer;
  transition: background-color 0.15s ease, color 0.15s ease;
  white-space: nowrap;
}
.pill:hover {
  color: #0d1f3c;
}
.pill.active {
  background: #1a3f8f;
  color: #ffffff;
}

.select-wrap {
  position: relative;
  display: inline-flex;
  align-items: center;
}
.turma-select {
  appearance: none;
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.12);
  border-radius: 999px;
  padding: 7px 30px 7px 16px;
  font-size: 13px;
  font-weight: 500;
  color: #0d1f3c;
  cursor: pointer;
}
.chevron {
  position: absolute;
  right: 12px;
  color: #5a6a85;
  font-size: 12px;
  pointer-events: none;
}

.badge {
  font-family: 'DM Mono', monospace;
  font-size: 12px;
  color: #5a6a85;
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.12);
  padding: 7px 14px;
  border-radius: 999px;
  white-space: nowrap;
}

@media (max-width: 480px) {
  .filtros {
    gap: 8px;
  }
  .pill {
    padding: 6px 12px;
    font-size: 12px;
  }
}
</style>