<script setup>
import { ref, computed } from 'vue'
import FiltrosHorario from '@/components/horario/FiltrosHorario.vue'
import TabelaHorario from '@/components/horario/TabelaHorario.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import { useAdmin } from '@/composables/useAdmin'
import {
  TURNO_ANOS, turmasState, DAYS, MAT_SLOTS, VES_SLOTS, SUBJECTS,
  getSchedule, setHorarioOverride,
} from '@/stores/appData'

const { isAdmin } = useAdmin()

const turno = ref('matutino')
const ano = ref('2º ano')
const turma = ref('2A')

const anos = computed(() => TURNO_ANOS[turno.value])
const turmas = computed(() => turmasState[turno.value]?.[ano.value] ?? [])
const slots = computed(() => (turno.value === 'matutino' ? MAT_SLOTS : VES_SLOTS))
const schedule = computed(() => getSchedule(turma.value))

function onTurnoChange(value) {
  turno.value = value
  const newAno = TURNO_ANOS[value][0]
  ano.value = newAno
  turma.value = turmasState[value]?.[newAno]?.[0] ?? ''
}

function onAnoChange(value) {
  ano.value = value
  turma.value = turmasState[turno.value]?.[value]?.[0] ?? ''
}

function onEditarAula({ day, time, subject }) {
  setHorarioOverride(turma.value, day, time, subject)
}
</script>

<template>
  <div class="page">
    <header class="page-header">
      <span class="eyebrow">ABSL</span>
      <h1>Horário das Aulas</h1>
      <p class="subtitle">Selecione o turno, o ano e a turma para visualizar a grade semanal.</p>
    </header>

    <AdminBanner
      v-if="isAdmin"
      message="Modo administrador ativo — clique em uma matéria para editá-la."
    />

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

    <TabelaHorario
      :days="DAYS"
      :slots="slots"
      :schedule="schedule"
      :is-admin="isAdmin"
      :subjects="SUBJECTS"
      @editar-aula="onEditarAula"
    />
  </div>
</template>

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