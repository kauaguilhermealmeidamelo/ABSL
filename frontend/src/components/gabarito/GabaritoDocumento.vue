<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  documento: { type: Object, required: true },
  isAdmin: { type: Boolean, default: false },
})

const emit = defineEmits(['substituir', 'excluir'])

const fileInput = ref(null)
const enviando = ref(false)

const isGabarito = computed(() => props.documento.tipo_documento === 'gabarito')

const nomeArquivo = computed(() => {
  const prefixo = isGabarito.value ? 'Gabarito' : 'Prova'
  return `${prefixo} ${props.documento.tipo_prova} – ${props.documento.serie}.pdf`
})

const descricaoTipo = computed(() => (isGabarito.value ? 'Gabarito' : 'Prova para consulta'))

function abrirSeletor() {
  fileInput.value?.click()
}

function onFileChange(event) {
  const file = event.target.files?.[0]
  event.target.value = ''
  if (!file) return
  enviando.value = true
  emit('substituir', props.documento.id, file, () => {
    enviando.value = false
  })
}

function excluir() {
  if (!confirm(`Excluir "${nomeArquivo.value}"? Essa ação não pode ser desfeita.`)) return
  emit('excluir', props.documento.id)
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
      <button v-if="isAdmin" type="button" class="doc-excluir" @click="excluir">
        <v-icon size="13">mdi-trash-can-outline</v-icon>
      </button>
      <input ref="fileInput" type="file" accept="application/pdf" hidden @change="onFileChange" />
    </div>
  </div>
</template>

<style scoped>
/* ... estilos existentes mantidos ... */
.doc-excluir {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 8px;
  border: none;
  background: transparent;
  color: #f87171;
  cursor: pointer;
  flex-shrink: 0;
  transition: background-color 0.15s ease, color 0.15s ease;
}
.doc-excluir:hover {
  background: #fef2f2;
  color: #dc2626;
}
</style>