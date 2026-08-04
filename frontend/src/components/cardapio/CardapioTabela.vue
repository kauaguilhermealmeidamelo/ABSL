<script setup>
import { ref } from 'vue'

const props = defineProps({
  dias: { type: Array, required: true },
  cardapio: { type: Object, required: true },
  isAdmin: { type: Boolean, default: false },
})

const emit = defineEmits(['update-dia'])

const editingDia = ref(null)
const editVal = ref('')

function startEdit(dia) {
  editingDia.value = dia
  editVal.value = props.cardapio[dia] ?? ''
}

function saveEdit() {
  if (!editingDia.value) return
  emit('update-dia', { dia: editingDia.value, valor: editVal.value })
  editingDia.value = null
}

function cancelEdit() {
  editingDia.value = null
}
</script>

<template>
  <div class="tabela-wrap">
    <table class="cardapio-table">
      <thead>
        <tr>
          <th class="th-dia">Dia</th>
          <th class="th-lanche">🥪 Lanche</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(dia, i) in dias"
          :key="dia"
          class="linha"
          :class="i % 2 === 0 ? 'linha-clara' : 'linha-escura'"
        >
          <td class="td-dia">{{ dia }}</td>
          <td class="td-lanche">
            <div v-if="isAdmin && editingDia === dia" class="edit-row">
              <input
                v-model="editVal"
                autofocus
                class="edit-input"
                @keydown.enter="saveEdit"
              />
              <button type="button" class="icon-btn icon-btn-confirm" @click="saveEdit">
                <v-icon size="16">mdi-check</v-icon>
              </button>
              <button type="button" class="icon-btn icon-btn-cancel" @click="cancelEdit">
                <v-icon size="16">mdi-close</v-icon>
              </button>
            </div>
            <div v-else class="view-row">
              <span>{{ cardapio[dia] || '—' }}</span>
              <button
                v-if="isAdmin"
                type="button"
                class="pencil-btn"
                @click="startEdit(dia)"
              >
                <v-icon size="13">mdi-pencil</v-icon>
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.tabela-wrap {
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.1);
  border-radius: 16px;
  overflow: hidden;
}

.cardapio-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

thead tr {
  background: #0d1f3c;
}

.th-dia {
  text-align: left;
  padding: 12px 24px;
  color: rgba(255, 255, 255, 0.7);
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  width: 192px;
}

.th-lanche {
  text-align: left;
  padding: 12px 24px;
  color: #ffffff;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

.linha {
  border-bottom: 1px solid rgba(13, 31, 60, 0.06);
}
.linha:last-child {
  border-bottom: none;
}
.linha-clara {
  background: #ffffff;
}
.linha-escura {
  background: rgba(238, 243, 251, 0.4);
}

.td-dia {
  padding: 16px 24px;
  font-weight: 600;
  color: #0d1f3c;
  border-right: 1px solid rgba(13, 31, 60, 0.08);
  white-space: nowrap;
}

.td-lanche {
  padding: 16px 24px;
}

.view-row {
  display: flex;
  align-items: center;
  gap: 8px;
}
.view-row span {
  color: #0d1f3c;
}

.pencil-btn {
  opacity: 0;
  background: none;
  border: none;
  padding: 4px;
  color: #5a6a85;
  cursor: pointer;
  transition: opacity 0.15s ease, color 0.15s ease;
}
.linha:hover .pencil-btn {
  opacity: 1;
}
.pencil-btn:hover {
  color: #1a3f8f;
}

.edit-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

.edit-input {
  flex: 1;
  border: 1px solid #1a3f8f;
  border-radius: 8px;
  padding: 6px 12px;
  font-size: 14px;
  font-family: 'DM Sans', sans-serif;
}

.icon-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 2px;
}
.icon-btn-confirm {
  color: #16a34a;
}
.icon-btn-confirm:hover {
  color: #15803d;
}
.icon-btn-cancel {
  color: #5a6a85;
}
.icon-btn-cancel:hover {
  color: #0d1f3c;
}

@media (max-width: 480px) {
  .th-dia,
  .td-dia {
    width: auto;
    padding: 12px 14px;
  }
  .th-lanche,
  .td-lanche {
    padding: 12px 14px;
  }
}
</style>