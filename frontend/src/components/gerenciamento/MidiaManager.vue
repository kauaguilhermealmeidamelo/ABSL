<script setup>
import { ref } from 'vue'
import { inicioMedia, setInicioMedia, clearInicioMedia } from '@/stores/appData'

const fileInput = ref(null)
const error = ref('')

function triggerFileSelect() {
  fileInput.value?.click()
}

function handleFile(file) {
  if (!file) return
  if (!file.type.startsWith('video/')) {
    error.value = 'Selecione um arquivo de vídeo válido.'
    return
  }

  setInicioMedia(file)
  error.value = ''
}

function onFileChange(event) {
  const file = event.target.files?.[0]
  handleFile(file)
}

function handleDrop(event) {
  const file = event.dataTransfer.files?.[0]
  handleFile(file)
}
</script>

<template>
  <div class="midia-manager card">
    <h3 class="card-title">Vídeo da tela inicial</h3>
    <p class="description">
      Faça o upload do arquivo de vídeo. O botão "Ver vídeo" aparecerá na página inicial quando um vídeo estiver carregado.
    </p>

    <div
      class="upload-zone"
      @click="triggerFileSelect"
      @drop.prevent="handleDrop"
      @dragover.prevent
    >
      <input
        ref="fileInput"
        type="file"
        accept="video/*"
        hidden
        @change="onFileChange"
      />
      <div class="upload-content">
        <v-icon size="28">mdi-cloud-upload-outline</v-icon>
        <div>
          <strong>{{ inicioMedia.fileName || 'Clique para enviar vídeo' }}</strong>
          <span>MP4, MOV, WebM — tamanho livre</span>
        </div>
      </div>
    </div>

    <div v-if="inicioMedia.videoUrl" class="preview-card">
      <video class="preview-player" controls :src="inicioMedia.videoUrl"></video>
      <div class="preview-actions">
        <span>{{ inicioMedia.fileName }}</span>
        <button type="button" class="btn-remove" @click="clearInicioMedia()">
          Remover vídeo
        </button>
      </div>
    </div>

    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<style scoped>
.midia-manager {
  display: flex;
  flex-direction: column;
  gap: 20px;
  font-family: 'DM Sans', sans-serif;
}
.card {
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 16px;
  padding: 24px;
}
.card-title {
  font-weight: 700;
  color: #0d1f3c;
  font-size: 14px;
  margin: 0 0 12px;
}
.description {
  margin: 0;
  color: #5a6a85;
  font-size: 14px;
  line-height: 1.6;
  max-width: 620px;
}
.upload-zone {
  border: 2px dashed rgba(26, 63, 143, 0.28);
  border-radius: 20px;
  padding: 32px;
  text-align: center;
  cursor: pointer;
  transition: background-color 0.15s ease, border-color 0.15s ease;
}
.upload-zone:hover {
  background: rgba(26, 63, 143, 0.04);
  border-color: rgba(26, 63, 143, 0.5);
}
.upload-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  flex-wrap: wrap;
  color: #0d1f3c;
}
.upload-content strong {
  display: block;
  font-size: 14px;
  margin-bottom: 4px;
}
.upload-content span {
  display: block;
  color: #5a6a85;
  font-size: 13px;
}
.preview-card {
  display: grid;
  gap: 16px;
}
.preview-player {
  width: 100%;
  border-radius: 16px;
  background: #000;
}
.preview-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}
.preview-actions span {
  color: #0d1f3c;
  font-size: 13px;
}
.btn-remove {
  border: none;
  background: #f8d7da;
  color: #b91c1c;
  padding: 10px 16px;
  border-radius: 999px;
  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
}
.btn-remove:hover {
  background: #f1a5ab;
}
.error {
  color: #b91c1c;
  font-size: 13px;
}
</style>
