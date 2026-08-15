<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  noticia: { type: Object, default: null },
  erroServidor: { type: String, default: '' }, // erro vindo do backend (ex: 422), se houver
})

const emit = defineEmits(['update:modelValue', 'salvar'])

const form = ref({ titulo: '', data_publicacao: '', texto: '', imagem_url: '' })
const imagemFile = ref(null)
const imagemPreview = ref('')
const erroData = ref('')

// Converte qualquer data que o backend mande (ex: "2026-08-12T00:00:00.000000Z")
// para o formato yyyy-mm-dd exigido pelo <input type="date">. Sem isso, o
// input não reconhece o valor, mostra vazio visualmente, mas o form
// continua com uma string não-vazia — a validação de obrigatoriedade nunca
// dispara mesmo o campo "parecendo" vazio pro usuário.
function formatDateForInput(d) {
  if (!d) return ''

  // Já vem como dd/mm/aaaa (formato exibido pelo service)
  const brMatch = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(d)
  if (brMatch) {
    const [, day, month, year] = brMatch
    return `${year}-${month}-${day}`
  }

  // Vem como aaaa-mm-dd (ou aaaa-mm-ddTHH:mm:ss...) — extrai direto sem
  // passar por new Date(), que interpretaria como UTC e poderia voltar um
  // dia ao converter pro fuso local.
  const isoMatch = /^(\d{4})-(\d{2})-(\d{2})/.exec(d)
  if (isoMatch) {
    const [, year, month, day] = isoMatch
    return `${year}-${month}-${day}`
  }

  return ''
}

watch(
  () => [props.modelValue, props.noticia],
  () => {
    if (props.modelValue) {
      erroData.value = ''
      if (props.noticia) {
        form.value = {
          ...props.noticia,
          data_publicacao: formatDateForInput(props.noticia.data_publicacao),
        }
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
  // Preview local via blob: só serve para exibir no formulário. Nunca é
  // enviado ao backend nem salvo como imagem_url — um link blob: só existe
  // nesta aba/sessão do navegador e quebraria assim que a página recarregasse.
  imagemPreview.value = URL.createObjectURL(f)
}

const imagemPreviewName = computed(() => {
  if (imagemFile.value) return imagemFile.value.name
  if (form.value.imagem_url) return String(form.value.imagem_url).split('/').pop()
  return ''
})

function salvar() {
  erroData.value = ''

  if (!form.value.titulo.trim()) return

  // Validação local: barra antes de chamar a API, evitando o 500/422 por
  // 'data_publicacao' nula direto na constraint do banco. Agora funciona
  // igual em criação e edição, já que o form sempre guarda yyyy-mm-dd ou
  // vazio — nunca mais uma string ISO "fantasma" que engana o check.
  if (!form.value.data_publicacao) {
    erroData.value = 'Data de Publicação Obrigatória'
    return
  }

  const payload = {
    titulo: form.value.titulo,
    data_publicacao: form.value.data_publicacao,
    texto: form.value.texto,
  }

  if (imagemFile.value) {
    // Imagem nova selecionada: manda o arquivo real; o backend salva no
    // disco e devolve a URL definitiva.
    payload.imagem = imagemFile.value
  } else {
    // Sem alteração de imagem: mantém a URL que já existia (ou vazio).
    payload.imagem_url = form.value.imagem_url || ''
  }

  emit('salvar', payload)
  // Não fecha o modal aqui — quem decide fechar é o componente pai
  // (Noticias.vue), e só em caso de sucesso. Assim, se o backend rejeitar
  // (ex: 422), o modal continua aberto mostrando o erro do servidor.
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
        <input v-model="form.data_publicacao" type="date" class="field-input"
          :class="{ 'field-input-erro': erroData || erroServidor }" />
        <span v-if="erroData || erroServidor" class="field-erro">{{ erroData || erroServidor }}</span>

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

.field-input-erro {
  border-color: #dc2626 !important;
}

.field-erro {
  display: block;
  color: #dc2626;
  font-size: 11px;
  margin-top: 4px;
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

.upload-preview {
  max-width: 100%;
  border-radius: 8px;
  margin-top: 8px;
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