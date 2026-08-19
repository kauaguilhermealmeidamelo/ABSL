<script setup>
import { ref, computed } from 'vue'
import { TURNO_ANOS, turmasState, addTurma, removeTurma } from '@/stores/appData'

const novoTurno = ref('matutino')
const novoAno = ref(TURNO_ANOS.matutino[0])
const novaLetra = ref('')

const anosDisponiveis = computed(() => TURNO_ANOS[novoTurno.value])

// Feedback de sucesso/erro ao cadastrar ou remover turma.
const feedback = ref(null) // { tipo: 'sucesso' | 'erro', mensagem: string }
let feedbackTimeoutId = null

function mostrarFeedback(tipo, mensagem) {
  feedback.value = { tipo, mensagem }
  clearTimeout(feedbackTimeoutId)
  feedbackTimeoutId = setTimeout(() => {
    feedback.value = null
  }, 3500)
}

function onTurnoChange(event) {
  novoTurno.value = event.target.value
  novoAno.value = TURNO_ANOS[novoTurno.value][0]
}

async function cadastrar() {
  if (!novaLetra.value.trim()) {
    mostrarFeedback('erro', 'Informe a letra da turma.')
    return
  }

  const sucesso = await addTurma(novoTurno.value, novoAno.value, novaLetra.value)

  if (sucesso) {
    mostrarFeedback('sucesso', 'Turma adicionada.')
    novaLetra.value = ''
  } else {
    mostrarFeedback('erro', 'Erro ao adicionar turma.')
  }
}

async function excluir(turno, ano, codigo) {
  try {
    await removeTurma(turno, ano, codigo)
    mostrarFeedback('sucesso', 'Turma removida.')
  } catch {
    mostrarFeedback('erro', 'Erro ao remover turma.')
  }
}
</script>

<template>
  <div class="turmas-manager">
    <div class="card">
      <h3 class="card-title">Cadastrar nova turma</h3>
      <div class="form-grid">
        <div>
          <label class="field-label">Turno</label>
          <select class="field-select" :value="novoTurno" @change="onTurnoChange">
            <option value="matutino">Matutino</option>
            <option value="vespertino">Vespertino</option>
          </select>
        </div>
        <div>
          <label class="field-label">Ano</label>
          <select class="field-select" v-model="novoAno">
            <option v-for="a in anosDisponiveis" :key="a" :value="a">{{ a }}</option>
          </select>
        </div>
        <div>
          <label class="field-label">Letra</label>
          <input
            class="field-input letra-input"
            maxlength="1"
            v-model="novaLetra"
            placeholder="Ex: A"
            @input="novaLetra = novaLetra.toUpperCase()"
            @keydown.enter="cadastrar"
          />
        </div>
      </div>
      <button type="button" class="btn-add" @click="cadastrar">
        <v-icon size="14">mdi-plus</v-icon>
        Adicionar turma
      </button>

      <transition name="feedback-fade">
        <p
          v-if="feedback"
          class="feedback-msg"
          :class="feedback.tipo === 'sucesso' ? 'feedback-sucesso' : 'feedback-erro'"
        >
          <v-icon size="14">{{ feedback.tipo === 'sucesso' ? 'mdi-check-circle' : 'mdi-alert-circle' }}</v-icon>
          {{ feedback.mensagem }}
        </p>
      </transition>
    </div>

    <div v-for="turno in ['matutino', 'vespertino']" :key="turno" class="card">
      <h3 class="card-title uppercase">{{ turno === 'matutino' ? 'Matutino' : 'Vespertino' }}</h3>
      <div v-for="ano in TURNO_ANOS[turno]" :key="ano" class="ano-block">
        <p class="ano-label">{{ ano }}</p>
        <div class="turma-chips">
          <span v-for="t in turmasState[turno]?.[ano] ?? []" :key="t" class="chip">
            {{ t }}
            <button type="button" class="chip-remove" @click="excluir(turno, ano, t)">
              <v-icon size="11">mdi-close</v-icon>
            </button>
          </span>
          <span v-if="!(turmasState[turno]?.[ano] ?? []).length" class="chip-empty">
            Nenhuma turma cadastrada
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.turmas-manager {
  display: flex;
  flex-direction: column;
  gap: 24px;
  font-family: 'DM Sans', sans-serif;
}
.card {
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 16px;
  padding: 24px;
}
.card-title {
  font-weight: 700;
  color: #0d1f3c;
  font-size: 14px;
  margin: 0 0 16px;
}
.uppercase {
  text-transform: uppercase;
  letter-spacing: 0.04em;
  font-size: 12px;
}
.form-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 12px;
  margin-bottom: 16px;
}
@media (min-width: 560px) {
  .form-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
.field-label {
  display: block;
  font-size: 12px;
  font-weight: 500;
  color: #5a6a85;
  margin-bottom: 4px;
}
.field-input,
.field-select {
  width: 100%;
  border: 1px solid rgba(13, 31, 60, 0.15);
  border-radius: 12px;
  padding: 8px 12px;
  font-size: 14px;
  background: #eef3fb;
  color: #0d1f3c;
  box-sizing: border-box;
  font-family: inherit;
}
.letra-input {
  text-transform: uppercase;
}
.field-input:focus,
.field-select:focus {
  outline: none;
  border-color: #1a3f8f;
}
.btn-add {
  display: flex;
  align-items: center;
  gap: 8px;
  border: none;
  background: #1a3f8f;
  color: #ffffff;
  padding: 10px 20px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.15s ease;
}
.btn-add:hover {
  background: #0d1f3c;
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
.feedback-sucesso {
  background: #dcfce7;
  color: #15803d;
}
.feedback-erro {
  background: #fef2f2;
  color: #dc2626;
}

.feedback-fade-enter-active,
.feedback-fade-leave-active {
  transition: opacity 0.2s ease;
}
.feedback-fade-enter-from,
.feedback-fade-leave-to {
  opacity: 0;
}

.ano-block {
  margin-bottom: 16px;
}
.ano-block:last-child {
  margin-bottom: 0;
}
.ano-label {
  font-size: 12px;
  font-weight: 500;
  color: #5a6a85;
  margin: 0 0 8px;
}
.turma-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
.chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 10px;
  border-radius: 999px;
  background: #eef3fb;
  border: 1px solid rgba(13, 31, 60, 0.1);
  color: #0d1f3c;
  font-size: 12px;
  font-family: 'DM Mono', monospace;
  font-weight: 500;
}
.chip-remove {
  display: flex;
  border: none;
  background: transparent;
  color: #f87171;
  cursor: pointer;
  padding: 0;
}
.chip-remove:hover {
  color: #dc2626;
}
.chip-empty {
  font-size: 12px;
  color: #94a3b8;
}
</style>