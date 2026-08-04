<script setup>
import { reactive, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  projeto: { type: Object, default: null },
  categorias: { type: Array, required: true },
  categoriaPadrao: { type: String, default: undefined },
})

const emit = defineEmits(['update:modelValue', 'salvar'])

const form = reactive({
  titulo: '',
  categoria: props.categorias[0] ?? '',
  status: 'em_andamento',
  descricao: '',
})

watch(
  () => [props.modelValue, props.projeto],
  () => {
    if (props.modelValue) {
      form.titulo = props.projeto?.titulo ?? ''
      form.categoria = props.projeto?.categoria ?? props.categoriaPadrao ?? props.categorias[0] ?? ''
      form.status = props.projeto?.status ?? 'em_andamento'
      form.descricao = props.projeto?.descricao ?? ''
    }
  },
  { immediate: true },
)

function fechar() {
  emit('update:modelValue', false)
}

function salvar() {
  if (!form.titulo.trim()) return
  emit('salvar', { ...form })
  fechar()
}
</script>

<template>
  <div v-if="modelValue" class="modal-overlay" @click.self="fechar">
    <div class="modal-card">
      <div class="modal-header">
        <h3 class="modal-title">{{ projeto ? 'Editar Projeto' : 'Novo Projeto' }}</h3>
        <button type="button" class="modal-close" @click="fechar">
          <v-icon size="18">mdi-close</v-icon>
        </button>
      </div>

      <div class="modal-fields">
        <div>
          <label class="field-label">Título</label>
          <input v-model="form.titulo" type="text" class="field-input" placeholder="Título do projeto" />
        </div>

        <div>
          <label class="field-label">Diretoria / Categoria</label>
          <select v-model="form.categoria" class="field-input">
            <option v-for="c in categorias" :key="c" :value="c">{{ c }}</option>
          </select>
        </div>

        <div>
          <label class="field-label">Status</label>
          <select v-model="form.status" class="field-input">
            <option value="em_andamento">Em andamento</option>
            <option value="concluido">Concluído</option>
          </select>
        </div>

        <div>
          <label class="field-label">Descrição</label>
          <textarea v-model="form.descricao" rows="3" class="field-textarea" placeholder="Descrição breve do projeto" />
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-cancelar" @click="fechar">Cancelar</button>
          <button type="button" class="btn-salvar" :disabled="!form.titulo.trim()" @click="salvar">Salvar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 50;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.4);
  padding: 16px;
  font-family: 'DM Sans', sans-serif;
}

.modal-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 24px;
  width: 100%;
  max-width: 480px;
  max-height: 90vh;
  overflow-y: auto;
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

.modal-close {
  border: none;
  background: transparent;
  color: #5a6a85;
  cursor: pointer;
  display: flex;
}

.modal-fields {
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
  transition: background-color 0.15s ease;
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
.btn-salvar:hover:not(:disabled) {
  background: #0d1f3c;
}
.btn-salvar:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>