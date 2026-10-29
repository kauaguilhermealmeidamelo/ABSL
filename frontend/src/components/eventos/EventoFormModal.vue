<script setup>
import { ref, watch, computed } from 'vue'
import { paraInputLocal } from './eventoFormat'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  evento: { type: Object, default: null },
  erroServidor: { type: String, default: '' },
  salvando: { type: Boolean, default: false },
})
const emit = defineEmits(['update:modelValue', 'salvar'])

const vazio = () => ({ titulo: '', data_evento: '', local: '', descricao: '', link_ingresso: '' })
const form = ref(vazio())
const imagemArquivo = ref(null)
const imagemPreview = ref('')
const removerImagem = ref(false)
const erro = ref('')

watch(() => props.modelValue, (open) => {
  if (!open) return
  erro.value = ''
  imagemArquivo.value = null
  removerImagem.value = false
  if (props.evento) {
    const e = props.evento
    form.value = { titulo: e.titulo ?? '', data_evento: paraInputLocal(e.data_evento), local: e.local ?? '', descricao: e.descricao ?? '', link_ingresso: e.link_ingresso ?? '' }
    imagemPreview.value = e.imagem_url ?? ''
  } else {
    form.value = vazio()
    imagemPreview.value = ''
  }
}, { immediate: true })

const editando = computed(() => !!props.evento)

function onArquivo(ev) {
  const file = ev.target.files?.[0]
  if (!file) return
  imagemArquivo.value = file
  removerImagem.value = false
  imagemPreview.value = URL.createObjectURL(file)
}

function limparImagem() {
  imagemArquivo.value = null
  imagemPreview.value = ''
  removerImagem.value = true
}

function linkValido(v) {
  if (!v) return true
  try { return ['http:', 'https:'].includes(new URL(v).protocol) } catch { return false }
}

function salvar() {
  erro.value = ''
  if (!form.value.titulo.trim()) { erro.value = 'Informe o título do evento.'; return }
  if (!form.value.data_evento) { erro.value = 'Informe a data e hora do evento.'; return }
  if (!linkValido(form.value.link_ingresso.trim())) { erro.value = 'O link de ingresso deve começar com http:// ou https://'; return }
  emit('salvar', {
    id: props.evento?.id ?? null,
    payload: {
      ...form.value,
      titulo: form.value.titulo.trim(),
      link_ingresso: form.value.link_ingresso.trim(),
      imagem: imagemArquivo.value,
      removerImagem: removerImagem.value,
    },
  })
}

function fechar() { emit('update:modelValue', false) }
</script>

<template>
  <v-dialog :model-value="modelValue" max-width="520" persistent scrollable @update:model-value="$emit('update:modelValue', $event)">
    <v-card class="evento-modal">
      <v-card-title class="modal-title">{{ editando ? 'Editar evento' : 'Novo evento' }}</v-card-title>
      <v-card-text class="modal-body">
        <label class="field-label">Imagem</label>
        <div class="imagem-box">
          <img v-if="imagemPreview" :src="imagemPreview" alt="Pré-visualização" class="imagem-preview" />
          <div class="imagem-acoes">
            <label class="btn-arquivo">
              {{ imagemPreview ? 'Trocar imagem' : 'Escolher imagem' }}
              <input type="file" accept="image/*" hidden @change="onArquivo" />
            </label>
            <button v-if="imagemPreview" type="button" class="btn-remover" @click="limparImagem">Remover</button>
          </div>
        </div>

        <label class="field-label">Título</label>
        <input v-model="form.titulo" type="text" class="field-input" maxlength="255" placeholder="Nome do evento" />

        <label class="field-label">Data e hora</label>
        <input v-model="form.data_evento" type="datetime-local" class="field-input" />

        <label class="field-label">Local</label>
        <input v-model="form.local" type="text" class="field-input" maxlength="255" placeholder="Onde vai acontecer" />

        <label class="field-label">Descrição</label>
        <textarea v-model="form.descricao" rows="4" class="field-textarea" placeholder="Detalhes do evento" />

        <label class="field-label">Link de ingresso (Sympla, Eventbrite, Pix...)</label>
        <input v-model="form.link_ingresso" type="url" class="field-input" placeholder="https://www.sympla.com.br/..." />

        <span v-if="erro || erroServidor" class="field-erro">{{ erro || erroServidor }}</span>
      </v-card-text>
      <v-card-actions class="modal-actions">
        <button type="button" class="btn-cancelar" @click="fechar">Cancelar</button>
        <button type="button" class="btn-salvar" :disabled="salvando" @click="salvar">{{ salvando ? 'Salvando...' : 'Salvar' }}</button>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>
.evento-modal{border-radius:18px!important;font-family:'DM Sans',sans-serif;padding:8px}
.modal-title{color:#0d1f3c;font-weight:700;font-size:18px;padding:20px 20px 4px}
.modal-body{display:flex;flex-direction:column;gap:4px;padding:8px 20px 4px}
.field-label{font-size:12px;font-weight:600;color:#5a6a85;margin-top:12px;margin-bottom:4px}
.field-input,.field-textarea{width:100%;border:1px solid rgba(13,31,60,.15);border-radius:12px;padding:10px 12px;font-size:14px;background:#eef3fb;font-family:inherit;color:#0d1f3c;box-sizing:border-box}
.modal-body>*{flex-shrink:0}
.field-textarea{resize:none;line-height:1.6;min-height:110px;display:block}
.field-input:focus,.field-textarea:focus{outline:none;border-color:#1a3f8f}
.field-erro{display:block;color:#dc2626;font-size:12px;margin-top:8px}
.imagem-box{display:flex;flex-direction:column;gap:8px}
.imagem-preview{width:100%;aspect-ratio:16/9;object-fit:cover;border-radius:12px}
.imagem-acoes{display:flex;gap:8px}
.btn-arquivo,.btn-remover{padding:8px 16px;border-radius:999px;font-size:12px;font-weight:600;cursor:pointer;border:1px solid rgba(13,31,60,.15);background:#fff;color:#1a3f8f}
.btn-remover{color:#dc2626}
.modal-actions{padding:12px 20px 20px;display:flex;justify-content:flex-end;gap:10px}
.btn-cancelar,.btn-salvar{padding:9px 20px;border-radius:999px;font-size:13px;font-weight:600;cursor:pointer;border:none}
.btn-cancelar{background:transparent;color:#5a6a85;border:1px solid rgba(13,31,60,.15)}
.btn-salvar{background:#1a3f8f;color:#fff}
.btn-salvar:disabled{opacity:.6;cursor:not-allowed}
.btn-salvar:hover:not(:disabled){background:#0d1f3c}
</style>
