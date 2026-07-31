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
            <td v-else v-for="day in days" :key="day" class="subject-cell">
              {{ schedule[day]?.[slot.time] ?? '—' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
defineProps({
  days: { type: Array, required: true },
  slots: { type: Array, required: true },
  schedule: { type: Object, required: true },
})
</script>

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

.break-cell {
  text-align: center;
  padding: 8px;
  color: #5a6a85;
  font-weight: 600;
  font-size: 11px;
  letter-spacing: 0.15em;
  text-transform: uppercase;
}

/* Keep the time column readable while scrolling horizontally on mobile */
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