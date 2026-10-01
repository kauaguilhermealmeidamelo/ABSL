<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import FiltrosHorario from '@/components/horario/FiltrosHorario.vue'
import TabelaHorario from '@/components/horario/TabelaHorario.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import { useAdmin } from '@/composables/useAdmin'
import { user, readTurmaVisitante, saveTurmaVisitante } from '@/stores/auth'
import {
  TURNO_ANOS, turmasState, DAYS, MAT_SLOTS, VES_SLOTS, SUBJECTS,
  getSchedule, setHorarioOverride,
} from '@/stores/appData'

const { isAdmin } = useAdmin()
const turno = ref('matutino')
const ano = ref('2º ano')
const turma = ref('2A')
const salvando = ref(false)
const error = ref('')

function turmaEfetiva() {
  if (user.value?.turma) return user.value.turma
  return readTurmaVisitante() ?? ''
}
function aplicarTurmaEfetiva() {
  const codigo = turmaEfetiva()
  if (!codigo) return
  for (const [t, anos] of Object.entries(turmasState)) {
    for (const [a, codigos] of Object.entries(anos as Record<string, string[]>)) {
      if ((codigos as string[]).includes(codigo)) {
        turno.value = t
        ano.value = a
        turma.value = codigo
        return
      }
    }
  }
}
watch(() => user.value?.turma, () => aplicarTurmaEfetiva())
onMounted(() => aplicarTurmaEfetiva())

const anos = computed(() => TURNO_ANOS[turno.value] ?? [])
const turmas = computed(() => turmasState[turno.value]?.[ano.value] ?? [])
const slots = computed(() => (turno.value === 'matutino' ? MAT_SLOTS : VES_SLOTS))
const schedule = computed(() => getSchedule(turma.value))

function onTurnoChange(value: string) {
  turno.value = value
  const newAno: string = TURNO_ANOS[value]?.[0] ?? ano.value
  ano.value = newAno
  turma.value = turmasState[value]?.[newAno]?.[0] ?? ''
  if (!user.value) saveTurmaVisitante(turma.value || null)
}
function onAnoChange(value: string) {
  ano.value = value
  turma.value = turmasState[turno.value]?.[value]?.[0] ?? ''
  if (!user.value) saveTurmaVisitante(turma.value || null)
}
function onTurmaChange(value: string) {
  turma.value = value
  if (!user.value) saveTurmaVisitante(value || null)
}
async function onEditarAula({ day, time, subject }: { day: string; time: string; subject: string }) {
  if (!isAdmin.value || salvando.value) return
  salvando.value = true
  error.value = ''
  try {
    await setHorarioOverride(turma.value, day, time, subject)
  } catch (err: any) {
    error.value = err?.response?.data?.message || 'Não foi possível salvar a aula.'
  } finally {
    salvando.value = false
  }
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
    <p v-if="error" class="state-error" role="alert">{{ error }}</p>
    <p v-if="salvando" class="state-saving" aria-live="polite">Salvando alteração...</p>

    <FiltrosHorario
      :turno="turno"
      :ano="ano"
      :turma="turma"
      :anos="anos"
      :turmas="turmas"
      @update:turno="onTurnoChange"
      @update:ano="onAnoChange"
      @update:turma="onTurmaChange"
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
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400;1,9..40,700&display=swap');
.page{padding:32px 40px 64px;max-width:1180px;margin:0 auto;font-family:'DM Sans',sans-serif}.page-header{margin-bottom:24px}.eyebrow{display:block;font-family:'DM Mono',monospace;font-size:11px;letter-spacing:.15em;text-transform:uppercase;color:#1a3f8f;margin-bottom:6px}.page-header h1{font-family:'Playfair Display',serif;font-size:34px;font-weight:700;color:#0d1f3c;margin:0 0 8px}.subtitle{color:#5a6a85;font-size:14.5px;margin:0}.state-error{color:#b91c1c;font-size:13px;margin:0 0 8px}.state-saving{color:#5a6a85;font-size:13px;margin:0 0 8px}@media (max-width:720px){.page{padding:20px}.page-header h1{font-size:26px}}
</style>