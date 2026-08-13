<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import GabaritoSecao from '@/components/gabarito/Gabaritosecao.vue'
import { useAdmin } from '@/composables/useAdmin'
import { gabaritoService } from '@/services/gabarito'

const { isAdmin } = useAdmin()

const documentos = ref([])
const loading = ref(false)
const error = ref('')

async function carregar() {
  loading.value = true
  error.value = ''
  try {
    documentos.value = await gabaritoService.list()
  } catch {
    error.value = 'Não foi possível carregar os gabaritos.'
  } finally {
    loading.value = false
  }
}

onMounted(carregar)

// Agrupa os documentos pela "série" cadastrada (ex: "2A até 2D"), já que o
// backend não guarda turno (matutino/vespertino) — cada grupo vira um card,
// igual ao layout anterior, só que com dados reais.
const grupos = computed(() => {
  const mapa = new Map()
  for (const doc of documentos.value) {
    const chave = doc.serie?.trim() || 'Sem série informada'
    if (!mapa.has(chave)) mapa.set(chave, [])
    mapa.get(chave).push(doc)
  }
  return [...mapa.entries()]
    .sort(([a], [b]) => a.localeCompare(b, 'pt-BR'))
    .map(([label, docs]) => ({ label, documentos: docs }))
})

// ── Substituir PDF de um documento existente ──────────────────────────────
async function onSubstituir(id, file, done) {
  try {
    const atualizado = await gabaritoService.substituirArquivo(id, file)
    documentos.value = documentos.value.map((d) => (d.id === atualizado.id ? atualizado : d))
  } catch {
    error.value = 'Não foi possível substituir o arquivo.'
  } finally {
    done?.()
  }
}

// ── Cadastro de um novo gabarito/prova (admin) ────────────────────────────
const formAberto = ref(false)
const salvando = ref(false)
const novo = reactive({
  titulo: '',
  disciplina: '',
  serie: '',
  tipo_prova: '',
  data_prova: '',
  arquivo: null,
})

function resetForm() {
  novo.titulo = ''
  novo.disciplina = ''
  novo.serie = ''
  novo.tipo_prova = ''
  novo.data_prova = ''
  novo.arquivo = null
}

function onArquivoChange(event) {
  novo.arquivo = event.target.files?.[0] ?? null
}

async function salvarNovo() {
  if (!novo.titulo.trim() || !novo.disciplina.trim() || !novo.tipo_prova.trim() || !novo.data_prova || !novo.arquivo) {
    error.value = 'Preencha título, disciplina, tipo de prova, data e selecione o PDF.'
    return
  }

  salvando.value = true
  error.value = ''
  try {
    const criado = await gabaritoService.create({ ...novo })
    documentos.value = [criado, ...documentos.value]
    resetForm()
    formAberto.value = false
  } catch {
    error.value = 'Não foi possível salvar o gabarito.'
  } finally {
    salvando.value = false
  }
}
</script>

<template>
  <div class="gabarito-page">
    <PageHeader
      label="ABSL"
      title="Gabarito"
      subtitle="Gabaritos e provas para consulta, organizados por turma."
    />

    <AdminBanner v-if="isAdmin" message="Modo administrador ativo — você pode substituir o PDF de qualquer documento." />

    <div v-if="isAdmin" class="admin-add">
      <button type="button" class="btn-novo" @click="formAberto = !formAberto">
        <v-icon size="14">mdi-plus</v-icon>
        {{ formAberto ? 'Cancelar' : 'Novo gabarito' }}
      </button>

      <form v-if="formAberto" class="novo-form" @submit.prevent="salvarNovo">
        <input v-model="novo.titulo" class="field-input" placeholder="Título (ex: Gabarito Provão – 2A até 2D)" />
        <input v-model="novo.disciplina" class="field-input" placeholder="Disciplina" />
        <input v-model="novo.serie" class="field-input" placeholder="Grupo/série (ex: 2A até 2D)" />
        <input v-model="novo.tipo_prova" class="field-input" placeholder="Tipo (ex: Provão, 2ª Chamada)" />
        <input v-model="novo.data_prova" type="date" class="field-input" />
        <label class="upload-box">
          <v-icon size="16" color="#5a6a85">mdi-file-upload-outline</v-icon>
          <span>{{ novo.arquivo ? novo.arquivo.name : 'Selecionar PDF' }}</span>
          <input type="file" accept="application/pdf" hidden @change="onArquivoChange" />
        </label>
        <button type="submit" class="btn-salvar" :disabled="salvando">
          {{ salvando ? 'Salvando...' : 'Salvar' }}
        </button>
      </form>
    </div>

    <p v-if="loading" class="status-msg">Carregando gabaritos...</p>
    <p v-else-if="error" class="status-msg status-erro">{{ error }}</p>

    <template v-else>
      <GabaritoSecao title="Gabaritos" :grupos="grupos" :is-admin="isAdmin" @substituir="onSubstituir" />
      <p v-if="!grupos.length" class="status-msg">Nenhum gabarito publicado ainda.</p>
    </template>
  </div>
</template>

<style scoped>
.gabarito-page {
  font-family: 'DM Sans', sans-serif;
  padding: 24px;
}

.admin-add {
  margin-bottom: 24px;
}

.btn-novo {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 9px 18px;
  border-radius: 999px;
  border: none;
  background: #1a3f8f;
  color: #ffffff;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.15s ease;
}
.btn-novo:hover {
  background: #0d1f3c;
}

.novo-form {
  display: grid;
  grid-template-columns: 1fr;
  gap: 10px;
  margin-top: 12px;
  padding: 16px;
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 16px;
  max-width: 420px;
}

@media (min-width: 640px) {
  .novo-form {
    grid-template-columns: repeat(2, 1fr);
  }
}

.field-input {
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
.field-input:focus {
  outline: none;
  border-color: #1a3f8f;
}

.upload-box {
  display: flex;
  align-items: center;
  gap: 8px;
  border: 1px dashed rgba(13, 31, 60, 0.25);
  border-radius: 12px;
  padding: 10px 12px;
  background: #eef3fb;
  color: #5a6a85;
  font-size: 13px;
  cursor: pointer;
  grid-column: 1 / -1;
}

.btn-salvar {
  grid-column: 1 / -1;
  border: none;
  background: #1a3f8f;
  color: #ffffff;
  padding: 10px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}
.btn-salvar:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.btn-salvar:hover:not(:disabled) {
  background: #0d1f3c;
}

.status-msg {
  color: #5a6a85;
  font-size: 14px;
  padding: 12px 0;
}
.status-erro {
  color: #dc2626;
}

@media (max-width: 480px) {
  .gabarito-page {
    padding: 16px;
  }
}
</style>