<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  documento: { type: Object, required: true }, // { id, grupo_turma, tipo_prova, tipo_documento, documento_url }
  isAdmin: { type: Boolean, default: false },
})

const emit = defineEmits(['substituir'])

const fileInput = ref(null)
const enviando = ref(false)

const isGabarito = computed(() => props.documento.tipo_documento === 'gabarito')

const nomeArquivo = computed(() => {
  const prefixo = isGabarito.value ? 'Gabarito' : 'Prova'
  return `${prefixo} ${props.documento.tipo_prova} – ${props.documento.grupo_turma}.pdf`
})

const descricaoTipo = computed(() => (isGabarito.value ? 'Gabarito' : 'Prova para consulta'))

function abrirSeletor() {
  fileInput.value?.click()
}

function onFileChange(event) {
  const file = event.target.files?.[0]
  event.target.value = '' // permite selecionar o mesmo arquivo de novo depois
  if (!file) return
  enviando.value = true
  emit('substituir', props.documento.id, file, () => {
    enviando.value = false
  })
}
</script>

<template>
  <div class="doc-row">
    <div class="doc-icon" :class="isGabarito ? 'doc-icon-gabarito' : 'doc-icon-prova'">
      <v-icon size="15" :color="isGabarito ? '#1a3f8f' : '#d97706'">mdi-file-document-outline</v-icon>
    </div>

    <div class="doc-info">
      <p class="doc-name">{{ nomeArquivo }}</p>
      <p class="doc-type">{{ descricaoTipo }}</p>
    </div>

    <div class="doc-actions">
      <a :href="documento.documento_url" target="_blank" rel="noreferrer" class="doc-download">
        <v-icon size="12">mdi-eye-outline</v-icon>
        Ver
      </a>
      <a :href="documento.documento_url" download class="doc-download">
        <v-icon size="12">mdi-download</v-icon>
        Baixar
      </a>

      <button v-if="isAdmin" type="button" class="doc-replace" :disabled="enviando" @click="abrirSeletor">
        {{ enviando ? 'Enviando...' : 'Substituir' }}
      </button>
      <input ref="fileInput" type="file" accept="application/pdf" hidden @change="onFileChange" />
    </div>
  </div>
</template>

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
  text-decoration: none;
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

.doc-replace:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.doc-replace:hover:not(:disabled) {
  background: #fffbeb;
}
</style>