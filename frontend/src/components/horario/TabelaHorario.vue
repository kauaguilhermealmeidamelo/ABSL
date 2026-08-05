<script setup>
import { ref } from 'vue'

const props = defineProps({
  days: { type: Array, required: true },
  slots: { type: Array, required: true },
  schedule: { type: Object, required: true },
  isAdmin: { type: Boolean, default: false },
  subjects: { type: Array, default: () => [] },
})

const emit = defineEmits(['editar-aula'])

const editando = ref(null) // { day, time }

function iniciarEdicao(day, time) {
  if (!props.isAdmin) return
  editando.value = { day, time }
}

function salvar(day, time, valor) {
  emit('editar-aula', { day, time, subject: valor })
  editando.value = null
}

function estaEditando(day, time) {
  return editando.value?.day === day && editando.value?.time === time
}
</script>

<template>
  <div class="table-card">
    <p class="scroll-hint">arraste para o lado →</p>
    <div class="scroll-area">
      <table>
        <thead>
          <tr>
            <th class="col-time">Horário</th>
            <th v-for="day in days" :key="day">{{ day }}</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(slot, i) in slots"
            :key="slot.time"
            :class="{ break: slot.isBreak, odd: i % 2 !== 0 }"
          >
            <td class="col-time time-cell">{{ slot.time }}</td>
            <td v-if="slot.isBreak" class="break-cell" :colspan="days.length">INTERVALO</td>
            <td
              v-else
              v-for="day in days"
              :key="day"
              class="subject-cell"
              :class="{ 'subject-cell-editable': isAdmin }"
            >
              <select
                v-if="isAdmin && estaEditando(day, slot.time)"
                class="subject-select"
                autofocus
                :value="schedule[day]?.[slot.time]"
                @change="salvar(day, slot.time, $event.target.value)"
                @blur="editando = null"
              >
                <option v-for="s in subjects" :key="s" :value="s">{{ s }}</option>
              </select>
              <button
                v-else
                type="button"
                class="subject-btn"
                :disabled="!isAdmin"
                @click="iniciarEdicao(day, slot.time)"
              >
                {{ schedule[day]?.[slot.time] ?? '—' }}
                <v-icon v-if="isAdmin" size="11" class="subject-pencil">mdi-pencil</v-icon>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.table-card {
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.1);
  border-radius: 16px;
  overflow: hidden;
  font-family: 'DM Sans', sans-serif;
}

.scroll-hint {
  display: none;
  font-size: 11px;
  color: #5a6a85;
  padding: 8px 16px 0;
  margin: 0;
}

.scroll-area {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}

thead tr {
  border-bottom: 1px solid rgba(13, 31, 60, 0.08);
}

th {
  text-align: center;
  padding: 12px 10px;
  color: #0d1f3c;
  font-weight: 700;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  white-space: nowrap;
}
.col-time {
  text-align: left;
  padding-left: 16px;
  width: 110px;
}

tbody tr {
  border-bottom: 1px solid rgba(13, 31, 60, 0.06);
}
tbody tr.odd {
  background: rgba(238, 243, 251, 0.4);
}
tbody tr.break {
  background: rgba(238, 243, 251, 0.7);
}

.time-cell {
  padding: 10px 10px 10px 16px;
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  color: #5a6a85;
  white-space: nowrap;
  background: inherit;
}

.subject-cell {
  text-align: center;
  padding: 10px;
  color: #0d1f3c;
  white-space: nowrap;
}

.subject-btn {
  all: unset;
  cursor: default;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
.subject-cell-editable .subject-btn {
  cursor: pointer;
}
.subject-cell-editable .subject-btn:hover {
  color: #1a3f8f;
}
.subject-pencil {
  opacity: 0;
  transition: opacity 0.15s ease;
}
.subject-cell-editable:hover .subject-pencil {
  opacity: 1;
}
.subject-select {
  width: 100%;
  border: 1px solid #1a3f8f;
  border-radius: 8px;
  padding: 4px 6px;
  font-size: 12px;
  font-family: 'DM Sans', sans-serif;
  background: #eef3fb;
}

.break-cell {
  text-align: center;
  padding: 8px;
  color: #5a6a85;
  font-weight: 600;
  font-size: 11px;
  letter-spacing: 0.15em;
  text-transform: uppercase;
}

@media (max-width: 640px) {
  .scroll-hint {
    display: block;
  }
  .col-time {
    position: sticky;
    left: 0;
    z-index: 1;
    box-shadow: 1px 0 0 rgba(13, 31, 60, 0.06);
  }
  th.col-time {
    background: #ffffff;
  }
  td.col-time {
    background: #ffffff;
  }
  tbody tr.odd td.col-time {
    background: #f4f7fc;
  }
  tbody tr.break td.col-time {
    background: #eaf0fa;
  }
}
</style>