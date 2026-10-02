<script setup>
import { onMounted, ref } from 'vue'
import { useGremioConteudos } from '@/composables/useGremioConteudos'

const { itens, loading, error, fetchConteudos, adicionar, atualizar, remover, mover } = useGremioConteudos()

const novo = ref({ titulo: '', conteudo: '', arquivo: null })
const novoArquivoInput = ref(null)
const salvando = ref(false)
const editandoId = ref(null)
const rascunho = ref({ titulo: '', conteudo: '', arquivo: null, remover: false })
const rascunhoArquivoInput = ref(null)

const feedback = ref(null)
let feedbackTimeoutId = null

function mostrarFeedback(tipo, mensagem) {
  feedback.value = { tipo, mensagem }
  clearTimeout(feedbackTimeoutId)
  feedbackTimeoutId = setTimeout(() => { feedback.value = null }, 3500)
}

// Erros de validação do Laravel vêm em response.data.errors (422).
function mensagemDeErro(err, fallback) {
  const dados = err?.response?.data
  const erros = dados?.errors
  if (erros) return Object.values(erros)[0]?.[0] || fallback
  return dados?.message || fallback
}

onMounted(() => fetchConteudos(true))

async function cadastrar() {
  if (!novo.value.titulo.trim() || (!novo.value.conteudo.trim() && !novo.value.arquivo)) {
    mostrarFeedback('erro', 'Preencha título e conteúdo (ou anexe um PDF).')
    return
  }
  salvando.value = true
  try {
    await adicionar({
      titulo: novo.value.titulo.trim(),
      conteudo: novo.value.conteudo.trim(),
      arquivo_pdf: novo.value.arquivo,
    })
    limparFormNovo()
    mostrarFeedback('sucesso', 'Conteúdo publicado.')
  } catch (err) {
    mostrarFeedback('erro', mensagemDeErro(err, 'Não foi possível publicar.'))
  } finally {
    salvando.value = false
  }
}

function limparFormNovo() {
  novo.value = { titulo: '', conteudo: '', arquivo: null }
  if (novoArquivoInput.value) novoArquivoInput.value.value = ''
}

function onNovoArquivo(event) {
  const file = event.target.files?.[0] || null
  if (file && file.type !== 'application/pdf') {
    event.target.value = ''
    mostrarFeedback('erro', 'Selecione um arquivo PDF.')
    return
  }
  novo.value.arquivo = file
}

function limparNovoArquivo() {
  novo.value.arquivo = null
  if (novoArquivoInput.value) novoArquivoInput.value.value = ''
}

function onRascunhoArquivo(event) {
  const file = event.target.files?.[0] || null
  if (file && file.type !== 'application/pdf') {
    event.target.value = ''
    mostrarFeedback('erro', 'Selecione um arquivo PDF.')
    return
  }
  rascunho.value.arquivo = file
  if (file) rascunho.value.remover = false
}

function limparRascunhoArquivo() {
  rascunho.value.arquivo = null
  if (rascunhoArquivoInput.value) rascunhoArquivoInput.value.value = ''
}

function abrirEdicao(item) {
  editandoId.value = item.id
  rascunho.value = { titulo: item.titulo, conteudo: item.conteudo, arquivo: null, remover: false }
}

function fecharEdicao() {
  editandoId.value = null
  rascunho.value = { titulo: '', conteudo: '', arquivo: null, remover: false }
}

async function salvarEdicao(item) {
  const r = rascunho.value
  const temPdf = r.arquivo instanceof File || (!r.remover && !!item.arquivo_pdf)
  if (!r.titulo.trim() || (!r.conteudo.trim() && !temPdf)) {
    mostrarFeedback('erro', 'Preencha título e conteúdo (ou anexe um PDF).')
    return
  }

  const payload = { titulo: r.titulo.trim(), conteudo: r.conteudo.trim() }
  if (r.arquivo instanceof File) payload.arquivo_pdf = r.arquivo
  else if (r.remover) payload.remover_arquivo_pdf = true

  try {
    await atualizar(item.id, payload)
    fecharEdicao()
    mostrarFeedback('sucesso', 'Conteúdo atualizado.')
  } catch (err) {
    mostrarFeedback('erro', mensagemDeErro(err, 'Não foi possível salvar.'))
  }
}

async function excluir(item) {
  if (!confirm(`Excluir "${item.titulo}"? Essa ação não pode ser desfeita.`)) return
  try {
    await remover(item.id)
    mostrarFeedback('sucesso', 'Conteúdo excluído.')
  } catch {
    mostrarFeedback('erro', 'Não foi possível excluir.')
  }
}

async function mudarOrdem(index, direcao) {
  try {
    await mover(index, direcao)
  } catch {
    mostrarFeedback('erro', 'Não foi possível reordenar.')
  }
}
</script>

<template>
  <div class="conteudos-manager">
    <div class="card">
      <h3 class="card-title">Novo conteúdo institucional</h3>

      <div class="form-stack">
        <div>
          <label class="field-label">Título</label>
          <input v-model="novo.titulo" class="field-input" maxlength="255" placeholder="Ex: Como o Grêmio funciona" />
        </div>
        <div>
          <label class="field-label">Conteúdo</label>
          <textarea v-model="novo.conteudo" rows="4" maxlength="5000" class="field-input field-textarea" />
        </div>
        <div>
          <label class="field-label">PDF (opcional)</label>
          <label class="upload-box">
            <v-icon size="16">mdi-file-pdf-box</v-icon>
            <span>{{ novo.arquivo ? novo.arquivo.name : 'Selecionar PDF' }}</span>
            <input ref="novoArquivoInput" type="file" accept="application/pdf" hidden @change="onNovoArquivo" />
          </label>
          <button v-if="novo.arquivo" type="button" class="file-remove" @click="limparNovoArquivo">
            Remover arquivo selecionado
          </button>
        </div>
        <button type="button" class="btn-add" :disabled="salvando" @click="cadastrar">
          <v-icon size="14">mdi-plus</v-icon>
          {{ salvando ? 'Publicando...' : 'Publicar' }}
        </button>
      </div>

      <transition name="feedback-fade">
        <p v-if="feedback" class="feedback-msg" :class="feedback.tipo === 'sucesso' ? 'feedback-sucesso' : 'feedback-erro'">
          <v-icon size="14">{{ feedback.tipo === 'sucesso' ? 'mdi-check-circle' : 'mdi-alert-circle' }}</v-icon>
          {{ feedback.mensagem }}
        </p>
      </transition>
    </div>

    <p v-if="loading" class="status-msg">Carregando conteúdos...</p>
    <p v-else-if="error" class="status-msg status-erro">{{ error }}</p>

    <div v-else class="lista">
      <div v-for="(item, idx) in itens" :key="item.id" class="card card-item">
        <div class="item-row">
          <span class="item-titulo">{{ item.titulo }}</span>

          <div class="ordem-btns">
            <button type="button" class="icon-btn" :disabled="idx === 0" title="Mover para cima" @click="mudarOrdem(idx, 'cima')">
              <v-icon size="15">mdi-chevron-up</v-icon>
            </button>
            <button type="button" class="icon-btn" :disabled="idx === itens.length - 1" title="Mover para baixo" @click="mudarOrdem(idx, 'baixo')">
              <v-icon size="15">mdi-chevron-down</v-icon>
            </button>
          </div>

          <button type="button" class="icon-btn icon-btn-danger" title="Excluir" @click="excluir(item)">
            <v-icon size="15">mdi-trash-can-outline</v-icon>
          </button>
          <button type="button" class="btn-editar" @click="editandoId === item.id ? fecharEdicao() : abrirEdicao(item)">
            <v-icon size="12">mdi-pencil-outline</v-icon>
            Editar
          </button>
        </div>

        <template v-if="editandoId !== item.id">
          <p class="item-preview">{{ item.conteudo }}</p>
          <a
            v-if="item.arquivo_pdf"
            class="pdf-link"
            :href="item.arquivo_pdf"
            target="_blank"
            rel="noopener noreferrer"
          >
            <v-icon size="15">mdi-file-pdf-box</v-icon>
            Baixar PDF
          </a>
        </template>

        <div v-else class="item-edit">
          <input v-model="rascunho.titulo" class="field-input" maxlength="255" />
          <textarea v-model="rascunho.conteudo" rows="4" maxlength="5000" class="field-input field-textarea" />

          <div class="pdf-area">
            <a
              v-if="item.arquivo_pdf && !rascunho.remover"
              class="pdf-link"
              :href="item.arquivo_pdf"
              target="_blank"
              rel="noopener noreferrer"
            >
              <v-icon size="15">mdi-file-pdf-box</v-icon>
              PDF atual
            </a>
            <button
              v-if="item.arquivo_pdf && !rascunho.remover"
              type="button"
              class="file-remove"
              @click="rascunho.remover = true"
            >
              Remover PDF
            </button>
            <button
              v-else-if="item.arquivo_pdf && rascunho.remover"
              type="button"
              class="file-remove"
              @click="rascunho.remover = false"
            >
              Manter PDF
            </button>

            <label class="upload-box">
              <v-icon size="16">mdi-file-pdf-box</v-icon>
              <span>{{ rascunho.arquivo ? rascunho.arquivo.name : (rascunho.remover ? 'Enviar novo PDF' : 'Substituir PDF') }}</span>
              <input ref="rascunhoArquivoInput" type="file" accept="application/pdf" hidden @change="onRascunhoArquivo" />
            </label>
            <button
              v-if="rascunho.arquivo"
              type="button"
              class="file-remove"
              @click="limparRascunhoArquivo"
            >
              Remover arquivo selecionado
            </button>
          </div>

          <div class="item-edit-actions">
            <button type="button" class="btn-cancelar" @click="fecharEdicao">Cancelar</button>
            <button type="button" class="btn-salvar" @click="salvarEdicao(item)">Salvar</button>
          </div>
        </div>
      </div>

      <p v-if="!itens.length" class="status-msg status-vazio">Nenhum conteúdo publicado ainda.</p>
    </div>
  </div>
</template>

<style scoped>
.conteudos-manager {
  display: flex;
  flex-direction: column;
  gap: 24px;
  font-family: var(--font-body, 'DM Sans', sans-serif);
}

.card {
  background: var(--color-surface, #ffffff);
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: var(--radius-card, 16px);
  padding: 24px;
}

.card-title {
  font-weight: 700;
  color: var(--color-navy, #0f2038);
  font-size: 14px;
  margin: 0 0 16px;
}

.form-stack {
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-width: 560px;
}

.field-label {
  display: block;
  font-size: 12px;
  font-weight: 500;
  color: var(--color-text-secondary, #6b7c93);
  margin-bottom: 4px;
}

.field-input {
  width: 100%;
  border: 1px solid rgba(13, 31, 60, 0.15);
  border-radius: 12px;
  padding: 8px 12px;
  font-size: 14px;
  background: var(--color-surface-alt, #eef1f6);
  color: var(--color-navy, #0f2038);
  box-sizing: border-box;
  font-family: inherit;
}

.field-textarea {
  resize: vertical;
  line-height: 1.6;
}

.field-input:focus {
  outline: none;
  border-color: var(--color-navy-soft, #16509b);
}

.btn-add {
  display: flex;
  align-items: center;
  gap: 8px;
  width: fit-content;
  border: none;
  background: var(--color-navy-soft, #16509b);
  color: #ffffff;
  padding: 10px 20px;
  border-radius: var(--radius-pill, 999px);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

.btn-add:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.feedback-msg {
  display: flex;
  align-items: center;
  gap: 6px;
  margin: 12px 0 0;
  padding: 8px 14px;
  border-radius: 10px;
  font-size: 12.5px;
  font-weight: 500;
  width: fit-content;
}
.feedback-sucesso { background: #dcfce7; color: #15803d; }
.feedback-erro { background: #fef2f2; color: #dc2626; }
.feedback-fade-enter-active, .feedback-fade-leave-active { transition: opacity 0.2s ease; }
.feedback-fade-enter-from, .feedback-fade-leave-to { opacity: 0; }

.status-msg { color: var(--color-text-secondary, #6b7c93); font-size: 14px; padding: 8px 0; }
.status-erro { color: #dc2626; }
.status-vazio { color: var(--color-text-muted, #8a90a8); }

.lista {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.card-item {
  padding: 16px 20px;
}

.item-row {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}

.item-titulo {
  flex: 1;
  min-width: 140px;
  font-weight: 700;
  font-size: 14px;
  color: var(--color-navy, #0f2038);
  overflow-wrap: anywhere;
}

.item-preview {
  margin: 10px 0 0;
  font-size: 13px;
  line-height: 1.6;
  color: var(--color-text-secondary, #6b7c93);
  white-space: pre-line;
  /* Quebra palavras longas sem espaço (ex.: URLs ou texto colado). */
  overflow-wrap: anywhere;
}

.pdf-link {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin-top: 10px;
  font-size: 12.5px;
  font-weight: 600;
  color: var(--color-navy-soft, #16509b);
  text-decoration: none;
}

.pdf-link:hover {
  text-decoration: underline;
}

.item-edit .pdf-link {
  margin-top: 0;
}

.pdf-area {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px;
}

.upload-box {
  display: flex;
  align-items: center;
  gap: 8px;
  border: 1px dashed rgba(13, 31, 60, 0.25);
  border-radius: 12px;
  padding: 10px 12px;
  background: var(--color-surface-alt, #eef1f6);
  color: #5a6a85;
  font-size: 13px;
  cursor: pointer;
}

.upload-box:hover {
  border-color: rgba(22, 80, 155, 0.45);
  color: var(--color-navy-soft, #16509b);
}

.file-remove {
  display: block;
  margin-top: 6px;
  padding: 0;
  border: none;
  background: transparent;
  color: #dc2626;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  text-decoration: underline;
}

.pdf-area .file-remove {
  margin-top: 0;
}

.ordem-btns { display: flex; gap: 2px; }

.icon-btn {
  border: none;
  background: transparent;
  padding: 6px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}
.icon-btn:disabled { opacity: 0.3; cursor: not-allowed; }
.icon-btn-danger { color: #f87171; }
.icon-btn-danger:hover { background: #fef2f2; color: #dc2626; }

.btn-editar {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 8px;
  background: var(--color-surface-alt, #eef1f6);
  color: var(--color-navy-soft, #16509b);
  font-size: 12px;
  font-weight: 500;
  border: none;
  cursor: pointer;
}

.item-edit {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 12px;
}

.item-edit-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.btn-cancelar,
.btn-salvar {
  padding: 7px 16px;
  border-radius: var(--radius-pill, 999px);
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  border: none;
}
.btn-cancelar {
  background: transparent;
  color: var(--color-text-secondary, #6b7c93);
  border: 1px solid rgba(13, 31, 60, 0.15);
}
.btn-salvar {
  background: var(--color-navy-soft, #16509b);
  color: #ffffff;
}

@media (max-width: 480px) {
  .card { padding: 16px; }
  .ordem-btns { order: 3; }
}
</style>