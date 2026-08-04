<script setup>
defineProps({
  titulo: { type: String, required: true },
  descricao: { type: String, default: '' },
  isAdmin: { type: Boolean, default: false },
})

defineEmits(['excluir'])
</script>

<template>
  <div class="item-row">
    <div class="item-icon">
      <v-icon size="18" color="#1a3f8f">mdi-file-document-outline</v-icon>
    </div>

    <div class="item-info">
      <p class="item-titulo">{{ titulo }}</p>
      <p class="item-descricao">{{ descricao }}</p>
    </div>

    <div class="item-actions">
      <button type="button" class="btn-download">
        <v-icon size="13">mdi-download</v-icon>
        PDF
      </button>

      <button
        v-if="isAdmin"
        type="button"
        class="btn-excluir"
        @click="$emit('excluir')"
      >
        <v-icon size="15">mdi-close</v-icon>
      </button>
    </div>
  </div>
</template>

<style scoped>
.item-row {
  display: flex;
  align-items: center;
  gap: 16px;
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 12px;
  padding: 16px 20px;
  transition: box-shadow 0.15s ease;
}

.item-row:hover {
  box-shadow: 0 1px 3px rgba(13, 31, 60, 0.06);
}

.item-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: #eef3fb;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.item-info {
  flex: 1;
  min-width: 0;
}

.item-titulo {
  color: #0d1f3c;
  font-weight: 600;
  font-size: 14px;
  margin: 0;
}

.item-descricao {
  color: #5a6a85;
  font-size: 12px;
  margin: 2px 0 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.item-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

.btn-download {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 8px;
  background: #eef3fb;
  color: #1a3f8f;
  font-size: 12px;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.btn-download:hover {
  background: #d6e4ff;
}

.btn-excluir {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 6px;
  border-radius: 8px;
  border: none;
  background: transparent;
  color: #f87171;
  cursor: pointer;
  transition: background-color 0.15s ease, color 0.15s ease;
}

.btn-excluir:hover {
  background: #fef2f2;
  color: #dc2626;
}
</style>    