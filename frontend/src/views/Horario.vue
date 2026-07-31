<!-- <script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import HorarioFiltros from '@/components/horario/HorarioFiltros.vue'
import HorarioTabela from '@/components/horario/HorarioTabela.vue'

/**
 * Se este projeto já possui um estado global de autenticação/admin
 * (Pinia, Vuex, composable, etc.), substitua esta prop/ref por ele.
 */
const props = withDefaults(defineProps<{ isAdmin?: boolean }>(), {
  isAdmin: false,
})
const isAdmin = computed(() => props.isAdmin)

// ─── Constantes ──────────────────────────────────────────────────────────────
const SUBJECTS = [
  'Matemática', 'Português', 'Física', 'Química', 'Biologia', 'História',
  'Geografia', 'Arte', 'Ed. Física', 'Filosofia', 'Sociologia', 'Literatura',
  'Inglês', 'Redação',
]

const DAYS = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta']

interface Slot { time: string; isBreak: boolean }

const MAT_SLOTS: Slot[] = [
  { time: '07:00–07:45', isBreak: false },
  { time: '07:45–08:30', isBreak: false },
  { time: '08:30–08:40', isBreak: true },
  { time: '08:40–09:25', isBreak: false },
  { time: '09:25–10:10', isBreak: false },
  { time: '10:10–10:30', isBreak: true },
  { time: '10:30–11:15', isBreak: false },
  { time: '11:15–12:00', isBreak: false },
]

const VES_SLOTS: Slot[] = [
  { time: '13:00–13:45', isBreak: false },
  { time: '13:45–14:30', isBreak: false },
  { time: '14:30–14:50', isBreak: true },
  { time: '14:50–15:35', isBreak: false },
  { time: '15:35–16:20', isBreak: false },
  { time: '16:20–16:30', isBreak: true },
  { time: '16:30–17:15', isBreak: false },
  { time: '17:15–18:00', isBreak: false },
]

// Gera as letras de turma: letters('2', 'A', 'H') -> ['2A', '2B', ..., '2H']
function letters(prefix: string, from: string, to: string): string[] {
  const a = from.charCodeAt(0)
  const b = to.charCodeAt(0)
  return Array.from({ length: b - a + 1 }, (_, i) => prefix + String.fromCharCode(a + i))
}

const TURNO_ANOS: Record<'matutino' | 'vespertino', string[]> = {
  matutino: ['2º ano', '3º ano'],
  vespertino: ['1º ano', '2º ano'],
}

const TURNO_TURMAS: Record<'matutino' | 'vespertino', Record<string, string[]>> = {
  matutino: {
    '2º ano': letters('2', 'A', 'H'),
    '3º ano': letters('3', 'A', 'O'),
  },
  vespertino: {
    '1º ano': letters('1', 'A', 'P'),
    '2º ano': letters('2', 'I', 'P'),
  },
}

// Gera uma grade determinística (pseudo-aleatória) a partir do nome da turma.
// Substitua por uma chamada real à API/serviço quando o backend estiver disponível.
function genSchedule(turma: string): Record<string, Record<string, string>> {
  const seed = turma.charCodeAt(0) * 31 + turma.charCodeAt(1)
  const result: Record<string, Record<string, string>> = {}
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

// ─── Estado ──────────────────────────────────────────────────────────────────
const turno = ref<'matutino' | 'vespertino'>('matutino')
const ano = ref<string>(TURNO_ANOS.matutino[0])
const turma = ref<string>(TURNO_TURMAS.matutino[TURNO_ANOS.matutino[0]][0])

// cache de horários por turma: { turma: { day: { time: materia } } }
const scheduleCache = reactive<Record<string, Record<string, Record<string, string>>>>({})

// ─── Computeds ───────────────────────────────────────────────────────────────
const anos = computed(() => TURNO_ANOS[turno.value])
const turmasDisponiveis = computed(() => TURNO_TURMAS[turno.value][ano.value] ?? [])
const slots = computed(() => (turno.value === 'matutino' ? MAT_SLOTS : VES_SLOTS))
const turnoHours = computed(() => (turno.value === 'matutino' ? '07:00 – 12:00' : '13:00 – 18:00'))

const currentSchedule = computed(() => {
  if (!scheduleCache[turma.value]) {
    scheduleCache[turma.value] = genSchedule(turma.value)
  }
  return scheduleCache[turma.value]
})

// ─── Métodos ─────────────────────────────────────────────────────────────────
function selectTurno(t: 'matutino' | 'vespertino') {
  turno.value = t
  const novoAno = TURNO_ANOS[t][0]
  ano.value = novoAno
  turma.value = TURNO_TURMAS[t][novoAno][0]
}

function selectAno(a: string) {
  ano.value = a
  turma.value = TURNO_TURMAS[turno.value][a][0]
}

function onUpdateCell({ day, time, value }: { day: string; time: string; value: string }) {
  if (!scheduleCache[turma.value]) {
    scheduleCache[turma.value] = genSchedule(turma.value)
  }
  if (!scheduleCache[turma.value][day]) {
    scheduleCache[turma.value][day] = {}
  }
  scheduleCache[turma.value][day][time] = value

  // TODO: persistir a alteração via serviço/API, ex.:
  // horarioService.atualizarCelula({ turma: turma.value, turno: turno.value, day, time, materia: value })
}
</script> -->

<template>
  <div class="horario-page">
    <PageHeader
      label="ABSL"
      title="Horário das Aulas"
      subtitle="Selecione o turno, o ano e a turma para visualizar a grade semanal."
    />

    <AdminBanner v-if="isAdmin" message="Modo administrador ativo — você pode editar os horários desta página." />

    <HorarioFiltros
      :turno="turno"
      :ano="ano"
      :turma="turma"
      :anos="anos"
      :turmas="turmasDisponiveis"
      :turno-hours="turnoHours"
      @select-turno="selectTurno"
      @select-ano="selectAno"
      @update:turma="turma = $event"
    />

    <HorarioTabela
      :days="DAYS"
      :slots="slots"
      :schedule="currentSchedule"
      :subjects="SUBJECTS"
      :is-admin="isAdmin"
      @update-cell="onUpdateCell"
    />
  </div>
</template>


<style scoped>
.horario-page {
  font-family: 'DM Sans', sans-serif;
  color: #0d1f3c;
}
</style>