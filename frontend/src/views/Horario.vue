<template>
  <div class="page">
    <header class="page-header">
      <span class="eyebrow">ABSL</span>
      <h1>Horário das Aulas</h1>
      <p class="subtitle">Selecione o turno, o ano e a turma para visualizar a grade semanal.</p>
    </header>

    <FiltrosHorario
      :turno="turno"
      :ano="ano"
      :turma="turma"
      :anos="anos"
      :turmas="turmas"
      @update:turno="onTurnoChange"
      @update:ano="onAnoChange"
      @update:turma="(v) => (turma = v)"
    />

    <TabelaHorario :days="DAYS" :slots="slots" :schedule="schedule" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import FiltrosHorario from '@/components/horario/FiltrosHorario.vue'
import TabelaHorario from '@/components/horario/TabelaHorario.vue'

const SUBJECTS = [
  'Matemática', 'Português', 'Física', 'Química', 'Biologia', 'História',
  'Geografia', 'Arte', 'Ed. Física', 'Filosofia', 'Sociologia', 'Literatura',
  'Inglês', 'Redação',
]
const DAYS = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta']

const MAT_SLOTS = [
  { time: '07:00–07:45', isBreak: false },
  { time: '07:45–08:30', isBreak: false },
  { time: '08:30–08:40', isBreak: true },
  { time: '08:40–09:25', isBreak: false },
  { time: '09:25–10:10', isBreak: false },
  { time: '10:10–10:30', isBreak: true },
  { time: '10:30–11:15', isBreak: false },
  { time: '11:15–12:00', isBreak: false },
]

const VES_SLOTS = [
  { time: '13:00–13:45', isBreak: false },
  { time: '13:45–14:30', isBreak: false },
  { time: '14:30–14:50', isBreak: true },
  { time: '14:50–15:35', isBreak: false },
  { time: '15:35–16:20', isBreak: false },
  { time: '16:20–16:30', isBreak: true },
  { time: '16:30–17:15', isBreak: false },
  { time: '17:15–18:00', isBreak: false },
]

function letters(prefix, from, to) {
  const a = from.charCodeAt(0)
  const b = to.charCodeAt(0)
  return Array.from({ length: b - a + 1 }, (_, i) => prefix + String.fromCharCode(a + i))
}

const TURNO_ANOS = {
  matutino: ['2º ano', '3º ano'],
  vespertino: ['1º ano', '2º ano'],
}

const TURNO_TURMAS = {
  matutino: {
    '2º ano': letters('2', 'A', 'H'),
    '3º ano': letters('3', 'A', 'O'),
  },
  vespertino: {
    '1º ano': letters('1', 'A', 'P'),
    '2º ano': letters('2', 'I', 'P'),
  },
}

// Gera uma grade determinística (mesma turma sempre cai nas mesmas matérias)
function genSchedule(turma) {
  const seed = turma.charCodeAt(0) * 31 + turma.charCodeAt(1)
  const result = {}
  let idx = seed
  for (const day of DAYS) {
    result[day] = {}
    for (const slot of [...MAT_SLOTS, ...VES_SLOTS]) {
      if (!slot.isBreak) {
        result[day][slot.time] = SUBJECTS[Math.abs(idx) % SUBJECTS.length]
        idx = (idx * 7919 + 137) % 9973
      }
    }
  }
  return result
}

const turno = ref('matutino')
const ano = ref('2º ano')
const turma = ref('2A')

const anos = computed(() => TURNO_ANOS[turno.value])
const turmas = computed(() => TURNO_TURMAS[turno.value][ano.value] ?? [])
const slots = computed(() => (turno.value === 'matutino' ? MAT_SLOTS : VES_SLOTS))
const schedule = computed(() => genSchedule(turma.value))

function onTurnoChange(value) {
  turno.value = value
  const newAno = TURNO_ANOS[value][0]
  ano.value = newAno
  turma.value = TURNO_TURMAS[value][newAno][0]
}

function onAnoChange(value) {
  ano.value = value
  turma.value = TURNO_TURMAS[turno.value][value][0]
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700;1,900&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400;1,9..40,700&display=swap');

.page {
  padding: 32px 40px 64px;
  max-width: 1180px;
  margin: 0 auto;
  font-family: 'DM Sans', sans-serif;
}

.page-header {
  margin-bottom: 24px;
}
.eyebrow {
  display: block;
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: #1a3f8f;
  margin-bottom: 6px;
}
.page-header h1 {
  font-family: 'Playfair Display', serif;
  font-size: 34px;
  font-weight: 700;
  color: #0d1f3c;
  margin: 0 0 8px;
}
.subtitle {
  color: #5a6a85;
  font-size: 14.5px;
  margin: 0;
}

@media (max-width: 720px) {
  .page {
    padding: 20px;
  }
  .page-header h1 {
    font-size: 26px;
  }
}
</style>