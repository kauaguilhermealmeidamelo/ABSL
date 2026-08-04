<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  tipo: { type: String, default: 'ata' }, // 'ata' | 'contas'
})

const emit = defineEmits(['update:modelValue', 'salvar'])

const form = ref({ referencia: '', descricao: '', arquivo: '' })

watch(
  () => props.modelValue,
  (open) => {
    if (open) form.value = { referencia: '', descricao: '', arquivo: '' }
  }
)

function close() {
  emit('update:modelValue', false)
}

function salvar() {
  if (!form.value.referencia.trim()) return
  emit('salvar', { ...form.value })
  close()
}
</script>

<template>
  <v-dialog :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" max-width="480">
    <v-card class="transp-modal">
      <v-card-title class="modal-title">
        {{ tipo === 'ata' ? 'Nova Ata' : 'Nova Prestação de Contas' }}
      </v-card-title>

      <v-card-text class="modal-body">
        <label class="field-label">{{ tipo === 'ata' ? 'Data da reunião' : 'Período de referência' }}</label>
        <input v-model="form.referencia" type="text" class="field-input" placeholder="Ex: 29 de julho de 2026" />

        <label class="field-label">{{ tipo === 'ata' ? 'Pauta resumida' : 'Descrição' }}</label>
        <textarea v-model="form.descricao" rows="3" class="field-textarea" placeholder="Descrição breve" />

        <label class="field-label">Arquivo (PDF)</label>
        <label class="upload-box">
          <v-icon size="18" color="#5a6a85">mdi-file-upload-outline</v-icon>
          <span>Selecionar PDF</span>
          <input type="file" accept="application/pdf" hidden @change="form.arquivo = $event.target.files?.[0]?.name || ''" />
        </label>
        <span v-if="form.arquivo" class="upload-file-name">{{ form.arquivo }}</span>
      </v-card-text>

      <v-card-actions class="modal-actions">
        <button type="button" class="btn-cancelar" @click="close">Cancelar</button>
        <button type="button" class="btn-salvar" @click="salvar">Salvar</button>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>
.transp-modal {
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
