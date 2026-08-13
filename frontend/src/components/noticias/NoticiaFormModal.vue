<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  noticia: { type: Object, default: null },
})

const emit = defineEmits(['update:modelValue', 'salvar'])

const form = ref({ titulo: '', data_publicacao: '', texto: '', imagem_url: '' })
const imagemFile = ref(null)
const imagemPreview = ref('')

watch(
  () => [props.modelValue, props.noticia],
  () => {
    if (props.modelValue) {
      if (props.noticia) {
        form.value = { ...props.noticia }
        imagemPreview.value = props.noticia.imagem_url || ''
        imagemFile.value = null
      } else {
        form.value = { titulo: '', data_publicacao: '', texto: '', imagem_url: '' }
        imagemPreview.value = ''
        imagemFile.value = null
      }
    }
  },
  { immediate: true }
)

function close() {
  emit('update:modelValue', false)
}

function onFileChange(e) {
  const f = e.target.files?.[0]
  if (!f) return
  imagemFile.value = f
  imagemPreview.value = URL.createObjectURL(f)
}

function formatDateForInput(d) {
  if (!d) return ''
  const dt = new Date(d)
  if (Number.isNaN(dt.getTime())) return ''
  const y = dt.getFullYear()
  const m = String(dt.getMonth() + 1).padStart(2, '0')
  const day = String(dt.getDate()).padStart(2, '0')
  return `${y}-${m}-${day}`
}

const imagemPreviewName = computed(() => {
  if (imagemFile.value) return imagemFile.value.name
  if (form.value.imagem_url) return String(form.value.imagem_url).split('/').pop()
  return ''
})

function salvar() {
  if (!form.value.titulo.trim()) return
  const payload = {
    titulo: form.value.titulo,
    data_publicacao: formatDateForInput(form.value.data_publicacao) || '',
    texto: form.value.texto,
    imagem_url: imagemPreview.value || form.value.imagem_url || '',
    methods: {
  onFileChange(event) {
    const file = event.target.files[0];
    if (file) {
      this.form.imagem = file; // O arquivo em si para envio ao backend
      this.imagemPreviewName = file.name;
      this.imagemPreview = URL.createObjectURL(file); // Gera a URL temporária para o preview
    }
  }
}   
  }

  
  emit('salvar', payload)
  close()
}
</script>

<template>
  <v-dialog :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" max-width="520">
    <v-card class="noticia-modal">
      <v-card-title class="modal-title">
        {{ noticia ? 'Editar notícia' : 'Nova notícia' }}
      </v-card-title>

      <v-card-text class="modal-body">
        <label class="field-label">Imagem de capa</label>
        <label class="upload-box">
          <v-icon size="20" color="#5a6a85">mdi-image-outline</v-icon>
          <span>Selecionar imagem</span>
          <input type="file" accept="image/*" hidden @change="onFileChange" />
        </label>
        <span v-if="imagemPreviewName" class="upload-file-name">{{ imagemPreviewName }}</span>
        <img v-if="imagemPreview" :src="imagemPreview" alt="preview" class="upload-preview" />

        <label class="field-label">Título</label>
        <input v-model="form.titulo" type="text" class="field-input" placeholder="Título da notícia" />

        <label class="field-label">Data de publicação</label>
        <input v-model="form.data_publicacao" type="date" class="field-input" placeholder="Ex: 29 de julho de 2026" />

        <label class="field-label">Resumo / texto</label>
        <textarea v-model="form.texto" rows="4" class="field-textarea" placeholder="Descrição breve da notícia" />
      </v-card-text>

      <v-card-actions class="modal-actions">
        <button type="button" class="btn-cancelar" @click="close">Cancelar</button>
        <button type="button" class="btn-salvar" @click="salvar">Salvar</button>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>
.noticia-modal {
  border-radius: 18px !important;
  font-family: 'DM Sans', sans-serif;
  padding: 8px;
}

.modal-title {
  color: #0d1f3c;
  font-weight: 700;
  font-size: 18px;
  padding: 20px 20px 4px;
}

.modal-body {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 8px 20px 4px;
}

.field-label {
  font-size: 12px;
  font-weight: 600;
  color: #5a6a85;
  margin-top: 12px;
  margin-bottom: 4px;
}

.field-input,
.field-textarea {
  width: 100%;
  border: 1px solid rgba(13, 31, 60, 0.15);
  border-radius: 12px;
  padding: 10px 12px;
  font-size: 14px;
  background: #eef3fb;
  font-family: inherit;
  color: #0d1f3c;
  box-sizing: border-box;
}

.field-textarea {
  resize: none;
  line-height: 1.6;
}

.field-input:focus,
.field-textarea:focus {
  outline: none;
  border-color: #1a3f8f;
}

.upload-box {
  display: flex;
  align-items: center;
  gap: 8px;
  border: 1px dashed rgba(13, 31, 60, 0.25);
  border-radius: 12px;
  padding: 12px;
  background: #eef3fb;
  color: #5a6a85;
  font-size: 13px;
  cursor: pointer;
}

.upload-file-name {
  font-size: 12px;
  color: #1a3f8f;
  margin-top: 4px;
}

.upload-preview { max-width: 100%; border-radius: 8px; margin-top: 8px }

.modal-actions {
  padding: 12px 20px 20px;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.btn-cancelar,
.btn-salvar {
  padding: 9px 20px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  border: none;
}

.btn-cancelar {
  background: transparent;
  color: #5a6a85;
  border: 1px solid rgba(13, 31, 60, 0.15);
}

.btn-salvar {
  background: #1a3f8f;
  color: #ffffff;
}
.btn-salvar:hover {
  background: #0d1f3c;
}
</style>
