<template>
  <div class="doc-row">
    <div class="doc-icon" :class="type === 'gabarito' ? 'doc-icon-gabarito' : 'doc-icon-prova'">
      <v-icon size="15" :color="type === 'gabarito' ? '#1a3f8f' : '#d97706'">mdi-file-document-outline</v-icon>
    </div>

    <div class="doc-info">
      <p class="doc-name">{{ name }}</p>
      <p class="doc-type">{{ type === 'gabarito' ? 'Gabarito' : 'Prova para consulta' }}</p>
    </div>

    <div class="doc-actions">
      <button type="button" class="doc-download">
        <v-icon size="12">mdi-download</v-icon>
        PDF
      </button>

      <button v-if="isAdmin" type="button" class="doc-replace">
        Substituir
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  name: { type: String, required: true },
  type: { type: String, required: true, validator: (v) => ['gabarito', 'prova'].includes(v) },
  isAdmin: { type: Boolean, default: false },
})
</script>

<style scoped>
.doc-row {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px 12px;
  padding: 10px 0;
  border-bottom: 1px solid rgba(13, 31, 60, 0.06);
}

.doc-row:last-child {
  border-bottom: none;
}

.doc-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.doc-icon-gabarito {
  background: #eef3fb;
}

.doc-icon-prova {
  background: #fffbeb;
}

.doc-info {
  flex: 1 1 140px;
  min-width: 0;
}

.doc-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
  margin-left: auto;
}

@media (max-width: 360px) {
  .doc-actions {
    margin-left: 44px;
    /* alinha com o texto, abaixo do ícone, quando quebra de linha */
  }
}

.doc-name {
  color: #0d1f3c;
  font-size: 12px;
  font-weight: 500;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.doc-type {
  color: #5a6a85;
  font-size: 10px;
  margin: 2px 0 0;
}

.doc-download {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 10px;
  border-radius: 8px;
  background: #eef3fb;
  color: #1a3f8f;
  font-size: 12px;
  border: none;
  cursor: pointer;
  flex-shrink: 0;
  transition: background-color 0.15s ease;
}

.doc-download:hover {
  background: #d6e4ff;
}

.doc-replace {
  font-size: 10px;
  padding: 6px 8px;
  border-radius: 8px;
  border: 1px solid #fbbf24;
  background: transparent;
  color: #b45309;
  cursor: pointer;
  flex-shrink: 0;
  transition: background-color 0.15s ease;
}

.doc-replace:hover {
  background: #fffbeb;
}
</style>