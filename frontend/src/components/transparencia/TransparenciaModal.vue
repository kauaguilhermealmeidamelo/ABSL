<script setup>
const props = defineProps({
  tab: { type: String, required: true }, // 'atas' | 'contas'
  modelValue: { type: Object, required: true }, // { data_referencia, periodo, descricao }
})

const emit = defineEmits(['update:modelValue', 'salvar', 'fechar'])

function update(campo, valor) {
  emit('update:modelValue', { ...props.modelValue, [campo]: valor })
}
</script>

<template>
  <div class="overlay" @click.self="$emit('fechar')">
    <div class="modal">
      <div class="modal-header">
        <h3 class="modal-title">
          {{ tab === 'atas' ? 'Nova Ata' : 'Nova Prestação de Contas' }}
        </h3>
        <button type="button" class="btn-close" @click="$emit('fechar')">
          <v-icon size="20">mdi-close</v-icon>
        </button>
      </div>

      <div class="modal-body">
        <div v-if="tab === 'atas'" class="field">
          <label class="field-label">Data da reunião</label>
          <input
            class="field-input"
            :value="modelValue.data_referencia"
            placeholder="Ex: 29 de julho de 2026"
            @input="update('data_referencia', $event.target.value)"
          />
        </div>

        <div v-else class="field">
          <label class="field-label">Período de referência</label>
          <input
            class="field-input"
            :value="modelValue.periodo"
            placeholder="Ex: 2º Trimestre 2026"
            @input="update('periodo', $event.target.value)"
          />
        </div>

        <div class="field">
          <label class="field-label">Descrição / Pauta</label>
          <textarea
            class="field-textarea"
            rows="3"
            :value="modelValue.descricao"
            @input="update('descricao', $event.target.value)"
          />
        </div>

        <div class="upload-box">
          Clique para fazer upload do PDF
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-cancelar" @click="$emit('fechar')">Cancelar</button>
          <button type="button" class="btn-salvar" @click="$emit('salvar')">Salvar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.overlay {
  position: fixed;
  inset: 0;
  z-index: 50;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.4);
  padding: 16px;
}

.modal {
  background: #ffffff;
  border-radius: 16px;
  padding: 24px;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 20px 50px rgba(4, 20, 40, 0.25);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}

.modal-title {
  font-weight: 700;
  font-size: 18px;
  color: #0d1f3c;
  margin: 0;
}

.btn-close {
  border: none;
  background: transparent;
  color: #5a6a85;
  cursor: pointer;
  display: flex;
}

.modal-body {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.field-label {
  display: block;
  font-size: 12px;
  font-weight: 500;
  color: #5a6a85;
  margin-bottom: 4px;
}

.field-input,
.field-textarea {
  width: 100%;
  border: 1px solid rgba(13, 31, 60, 0.15);
  border-radius: 12px;
  padding: 8px 12px;
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
  border: 1.5px dashed rgba(13, 31, 60, 0.2);
  border-radius: 12px;
  padding: 16px;
  text-align: center;
  font-size: 14px;
  color: #5a6a85;
  cursor: pointer;
}

.modal-actions {
  display: flex;
  gap: 12px;
  padding-top: 8px;
}

.btn-cancelar,
.btn-salvar {
  flex: 1;
  padding: 10px;
  border-radius: 12px;
  font-size: 14px;
  cursor: pointer;
}

.btn-cancelar {
  border: 1px solid rgba(13, 31, 60, 0.15);
  background: transparent;
  color: #5a6a85;
}

.btn-cancelar:hover {
  background: #f9fafb;
}

.btn-salvar {
  border: none;
  background: #1a3f8f;
  color: #ffffff;
  font-weight: 500;
}

.btn-salvar:hover {
  background: #0d1f3c;
}
</style>