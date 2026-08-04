<script setup>
import { ref, watch } from 'vue'
import { PROJECT_CATS } from '@/utils/projetoCategorias'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  projeto: { type: Object, default: null },
  categoriaPadrao: { type: String, default: PROJECT_CATS[0] },
})

const emit = defineEmits(['update:modelValue', 'salvar'])

const form = ref({ titulo: '', categoria: '', descricao: '', status: 'em_andamento', imagem_url: undefined })

watch(
  () => [props.modelValue, props.projeto],
  () => {
    if (props.modelValue) {
      form.value = props.projeto
        ? { ...props.projeto }
        : { titulo: '', categoria: props.categoriaPadrao, descricao: '', status: 'em_andamento', imagem_url: undefined }
    }
  },
  { immediate: true }
)

function close() {
  emit('update:modelValue', false)
}

function onFileChange(event) {
  const file = event.target.files?.[0]
  if (!file) return
  const reader = new FileReader()
  reader.onload = (ev) => { form.value.imagem_url = ev.target?.result }
  reader.readAsDataURL(file)
}

function removeImage() {
  form.value.imagem_url = undefined
}

function salvar() {
  if (!form.value.titulo.trim()) return
  emit('salvar', { ...form.value })
  close()
}
</script>

<template>
  <v-dialog :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" max-width="520">
    <v-card class="projeto-modal">
      <v-card-title class="modal-title">
        {{ projeto ? 'Editar projeto' : 'Novo projeto' }}
      </v-card-title>

      <v-card-text class="modal-body">
        <label class="field-label">Imagem do projeto (opcional)</label>
        <div v-if="form.imagem_url" class="upload-preview">
          <img :src="form.imagem_url" alt="Pré-visualização" />
          <button type="button" class="upload-remove" @click="removeImage">
            <v-icon size="13" color="white">mdi-close</v-icon>
          </button>
        </div>
        <label v-else class="upload-box">
          <v-icon size="20" color="#5a6a85">mdi-image-outline</v-icon>
          <span>Clique para enviar imagem</span>
          <input type="file" accept="image/*" hidden @change="onFileChange" />
        </label>

        <label class="field-label">Título do projeto</label>
        <input v-model="form.titulo" type="text" class="field-input" placeholder="Título do projeto" />

        <label class="field-label">Diretoria / categoria responsável</label>
        <select v-model="form.categoria" class="field-select">
          <option v-for="c in PROJECT_CATS" :key="c" :value="c">{{ c }}</option>
        </select>

        <label class="field-label">Descrição</label>
        <textarea v-model="form.descricao" rows="3" class="field-textarea" placeholder="Descrição do projeto" />

        <label class="field-label">Status</label>
        <select v-model="form.status" class="field-select">
          <option value="em_andamento">Em andamento</option>
          <option value="concluido">Concluído</option>
        </select>

        <template v-if="form.status === 'concluido'">
          <label class="field-label">Data de conclusão</label>
          <input v-model="form.data_conclusao" type="text" class="field-input" placeholder="Ex: Maio 2026" />
        </template>
      </v-card-text>

      <v-card-actions class="modal-actions">
        <button type="button" class="btn-cancelar" @click="close">Cancelar</button>
        <button type="button" class="btn-salvar" @click="salvar">Salvar</button>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>
.projeto-modal {
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
.field-textarea,
.field-select {
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
.field-textarea:focus,
.field-select:focus {
  outline: none;
  border-color: #1a3f8f;
}

.upload-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  border: 2px dashed rgba(13, 31, 60, 0.15);
  border-radius: 12px;
  padding: 24px 12px;
  cursor: pointer;
  color: #5a6a85;
  font-size: 12px;
  transition: border-color 0.15s ease, background-color 0.15s ease;
  margin-bottom: 4px;
}
.upload-box:hover {
  border-color: rgba(26, 63, 143, 0.4);
  background: rgba(238, 243, 251, 0.6);
}

.upload-preview {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  height: 140px;
  margin-bottom: 4px;
}
.upload-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.upload-remove {
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(0, 0, 0, 0.5);
  border: none;
  border-radius: 999px;
  padding: 4px;
  cursor: pointer;
}
.upload-remove:hover {
  background: rgba(0, 0, 0, 0.7);
}

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
