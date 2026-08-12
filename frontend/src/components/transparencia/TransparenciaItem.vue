<script setup>
defineProps({
  titulo: { type: String, required: true },
  descricao: { type: String, required: true },
  arquivoUrl: { type: String, default: '' },
  isAdmin: { type: Boolean, default: false },
})

defineEmits(['excluir'])
</script>

<template>
  <div class="transp-item">
    <div class="transp-icon">
      <v-icon size="18" color="#1a3f8f">mdi-file-document-outline</v-icon>
    </div>

    <div class="transp-info">
      <p class="transp-titulo">{{ titulo }}</p>
      <p class="transp-descricao">{{ descricao }}</p>
    </div>

    <div class="transp-acoes">
      <a
        v-if="arquivoUrl"
        :href="arquivoUrl"
        target="_blank"
        rel="noreferrer"
        class="btn-pdf"
      >
        <v-icon size="13">mdi-download</v-icon>
        PDF
      </a>
      <button v-else type="button" class="btn-pdf btn-disabled" disabled>
        <v-icon size="13">mdi-file-outline</v-icon>
        PDF
      </button>
      <button v-if="isAdmin" type="button" class="btn-excluir" @click="$emit('excluir')">
        <v-icon size="14">mdi-close</v-icon>
      </button>
    </div>
  </div>
</template>

<style scoped>
.transp-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px 18px;
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 14px;
  font-family: 'DM Sans', sans-serif;
}

.transp-icon {
  width: 38px;
  height: 38px;
  flex-shrink: 0;
  border-radius: 10px;
  background: #eef3fb;
  display: flex;
  align-items: center;
  justify-content: center;
}

.transp-info {
  flex: 1;
  min-width: 0;
}

.transp-titulo {
  color: #0d1f3c;
  font-weight: 700;
  font-size: 14px;
  margin: 0 0 2px;
}

.transp-descricao {
  color: #5a6a85;
  font-size: 12.5px;
  line-height: 1.5;
  margin: 0;
}

.transp-acoes {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

.btn-pdf {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  border-radius: 999px;
  background: #eef3fb;
  color: #1a3f8f;
  font-size: 12px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: background-color 0.15s ease;
}
.btn-pdf:hover {
  background: #d6e4ff;
}

.btn-excluir {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border-radius: 999px;
  border: none;
  background: transparent;
  color: #dc2626;
  cursor: pointer;
  transition: background-color 0.15s ease;
}
.btn-excluir:hover {
  background: #fef2f2;
}

@media (max-width: 480px) {
  .transp-item {
    flex-wrap: wrap;
  }
  .transp-acoes {
    margin-left: 52px;
  }
}
</style>
