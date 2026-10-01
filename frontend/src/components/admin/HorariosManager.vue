<script setup lang="ts">
import { computed, ref } from 'vue'
import FiltrosHorario from '@/components/horario/FiltrosHorario.vue'
import TabelaHorario from '@/components/horario/TabelaHorario.vue'
import { TURNO_ANOS, turmasState, DAYS, MAT_SLOTS, VES_SLOTS, SUBJECTS, getSchedule, setHorarioOverride } from '@/stores/appData'
const turno=ref('matutino'), ano=ref('2º ano'), turma=ref('2A')
const anos=computed(()=>TURNO_ANOS[turno.value]??[]), turmas=computed(()=>turmasState[turno.value]?.[ano.value]??[]), slots=computed(()=>turno.value==='matutino'?MAT_SLOTS:VES_SLOTS), schedule=computed(()=>getSchedule(turma.value))
function turnoChange(v:string){turno.value=v;ano.value=TURNO_ANOS[v]?.[0]??ano.value;turma.value=turmasState[v]?.[ano.value]?.[0]??''}
function anoChange(v:string){ano.value=v;turma.value=turmasState[turno.value]?.[v]?.[0]??''}
function aula(e:{day:string;time:string;subject:string}){setHorarioOverride(turma.value,e.day,e.time,e.subject)}
</script>
<template>
<div class="manager"><div><h2>Horários</h2><p>Edite a grade diretamente pelo painel administrativo.</p></div><FiltrosHorario :turno="turno" :ano="ano" :turma="turma" :anos="anos" :turmas="turmas" @update:turno="turnoChange" @update:ano="anoChange" @update:turma="turma=$event"/><TabelaHorario :days="DAYS" :slots="slots" :schedule="schedule" :is-admin="true" :subjects="SUBJECTS" @editar-aula="aula"/></div>
</template>
<style scoped>.manager{display:flex;flex-direction:column;gap:18px;font-family:'DM Sans',sans-serif}.manager h2{margin:0;color:#0d1f3c}.manager p{margin:5px 0 0;color:#5a6a85;font-size:13px}</style>