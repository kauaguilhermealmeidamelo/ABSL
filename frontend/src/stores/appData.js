// src/stores/appData.js
//
// Estado compartilhado do app (ainda sem backend). Centraliza os dados que a
// tela de Gerenciamento edita e que as telas públicas (Início, Horário,
// Cardápio, Gabarito) exibem — uma alteração feita pelo admin em /usuarios
// aparece na hora nas outras telas.
//
// TODO: substituir por chamadas à API quando os endpoints existirem.
import { reactive, ref } from 'vue'

// ── Turmas ──────────────────────────────────────────────────────────────
export const TURNO_ANOS = {
  matutino: ['2º ano', '3º ano'],
  vespertino: ['1º ano', '2º ano'],
}

function letters(prefix, from, to) {
  const a = from.charCodeAt(0)
  const b = to.charCodeAt(0)
  return Array.from({ length: b - a + 1 }, (_, i) => prefix + String.fromCharCode(a + i))
}

export const turmasState = reactive({
  matutino: {
    '2º ano': letters('2', 'A', 'H'),
    '3º ano': letters('3', 'A', 'O'),
  },
  vespertino: {
    '1º ano': letters('1', 'A', 'P'),
    '2º ano': letters('2', 'I', 'P'),
  },
})

export function addTurma(turno, ano, letra) {
  if (!letra) return false
  const codigo = `${ano.charAt(0)}${letra.toUpperCase()}`
  const atual = turmasState[turno]?.[ano] ?? []
  if (atual.includes(codigo)) return false
  turmasState[turno] = { ...turmasState[turno], [ano]: [...atual, codigo].sort() }
  return true
}

export function removeTurma(turno, ano, codigo) {
  turmasState[turno] = {
    ...turmasState[turno],
    [ano]: (turmasState[turno]?.[ano] ?? []).filter((t) => t !== codigo),
  }
}

// ── Horário ──────────────────────────────────────────────────────────────
export const SUBJECTS = [
  'Matemática', 'Português', 'Física', 'Química', 'Biologia', 'História',
  'Geografia', 'Arte', 'Ed. Física', 'Filosofia', 'Sociologia', 'Literatura',
  'Inglês', 'Redação',
]
export const DAYS = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta']

export const MAT_SLOTS = [
  { time: '07:00–07:45', isBreak: false },
  { time: '07:45–08:30', isBreak: false },
  { time: '08:30–08:40', isBreak: true },
  { time: '08:40–09:25', isBreak: false },
  { time: '09:25–10:10', isBreak: false },
  { time: '10:10–10:30', isBreak: true },
  { time: '10:30–11:15', isBreak: false },
  { time: '11:15–12:00', isBreak: false },
]

export const VES_SLOTS = [
  { time: '13:00–13:45', isBreak: false },
  { time: '13:45–14:30', isBreak: false },
  { time: '14:30–14:50', isBreak: true },
  { time: '14:50–15:35', isBreak: false },
  { time: '15:35–16:20', isBreak: false },
  { time: '16:20–16:30', isBreak: true },
  { time: '16:30–17:15', isBreak: false },
  { time: '17:15–18:00', isBreak: false },
]

// Grade base determinística (mesma turma sempre cai nas mesmas matérias)
function baseSchedule(turma) {
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

// Edições feitas pelo admin: { "turma|dia|horario": "Matéria" }
export const horarioOverrides = reactive({})

function scheduleKey(turma, day, time) {
  return `${turma}|${day}|${time}`
}

export function getSchedule(turma) {
  const base = baseSchedule(turma)
  const merged = {}
  for (const day of DAYS) {
    merged[day] = {}
    for (const time in base[day]) {
      const key = scheduleKey(turma, day, time)
      merged[day][time] = horarioOverrides[key] ?? base[day][time]
    }
  }
  return merged
}

export function setHorarioOverride(turma, day, time, subject) {
  horarioOverrides[scheduleKey(turma, day, time)] = subject
}

// ── Gabarito (rótulos dos grupos de turmas) ────────────────────────────────
export const gabMatGroups = reactive(['2A até 2D', '2E até 2H', '3A até 3H', '3I até 3O'])
export const gabVesGroups = reactive(['1A até 1H', '1I até 1P'])

// ── Equipe / Diretorias ─────────────────────────────────────────────────────
export const team = reactive([
  { icon: 'mdi-crown', name: 'Presidência', members: [{ cargo: 'Presidente', nome: 'Samuel' }, { cargo: 'Vice-Presidente', nome: 'Bárbara' }, { cargo: '1º Vice-Presidente', nome: 'Jósue' }] },
  { icon: 'mdi-clipboard-text-outline', name: 'Secretaria', members: [{ cargo: 'Secretário-Geral', nome: 'Marcus Paulo' }, { cargo: '1ª Secretária', nome: 'Giovanna' }, { cargo: '2ª Secretária', nome: 'Brenda' }] },
  { icon: 'mdi-cash', name: 'Tesouraria', members: [{ cargo: 'Tesoureira-Geral', nome: 'Andressa' }, { cargo: '1º Tesoureiro', nome: 'Rafael' }, { cargo: '2º Tesoureiro', nome: 'Vitor' }] },
  { icon: 'mdi-run-fast', name: 'Esporte e Lazer', members: [{ cargo: 'Diretor-Geral', nome: 'Igor' }, { cargo: '1ª Diretora', nome: 'Rafaela' }, { cargo: '2º Diretor', nome: 'Artur Araripe' }] },
  { icon: 'mdi-theater', name: 'Cultura', members: [{ cargo: 'Diretora-Geral', nome: 'Jennyfer' }, { cargo: '1ª Diretora', nome: 'Ludmila' }, { cargo: '2º Diretor', nome: 'Thiago' }] },
  { icon: 'mdi-school-outline', name: 'Políticas Educacionais', members: [{ cargo: 'Diretor-Geral', nome: 'Enzo' }, { cargo: '1ª Diretora', nome: 'Lorena' }, { cargo: '2ª Diretora', nome: 'Larissa' }] },
  { icon: 'mdi-leaf', name: 'Saúde e Meio Ambiente', members: [{ cargo: 'Diretora-Geral', nome: 'Ayla' }, { cargo: '1ª Diretora', nome: 'Maryane' }, { cargo: '2ª Diretora', nome: 'Joana' }] },
  { icon: 'mdi-handshake-outline', name: 'Diretoria Social', members: [{ cargo: 'Diretor-Geral', nome: 'David' }, { cargo: '1ª Diretora', nome: 'Mariana' }, { cargo: '2º Diretor', nome: 'Matheus' }] },
  { icon: 'mdi-bullhorn-variant-outline', name: 'Imprensa e Comunicação', members: [{ cargo: 'Diretora-Geral', nome: 'Yara' }, { cargo: '1ª Diretora', nome: 'Giulia' }, { cargo: '2ª Diretora', nome: 'Ana Júlia' }] },
  { icon: 'mdi-laptop', name: 'Tecnologia e Inovação', members: [{ cargo: 'Diretor-Geral', nome: 'Kauan Guilherme' }, { cargo: '1º Diretor', nome: 'Pedro Lucas' }, { cargo: '2º Diretor', nome: 'Maria Eduarda' }] },
])

export function addDiretoria({ name, diretorGeral, primeiro, segundo }) {
  if (!name?.trim()) return
  const members = [{ cargo: 'Diretor(a)-Geral', nome: diretorGeral }]
  if (primeiro) members.push({ cargo: '1º(ª) Diretor(a)', nome: primeiro })
  if (segundo) members.push({ cargo: '2º(ª) Diretor(a)', nome: segundo })
  team.push({ icon: 'mdi-domain', name, members })
}

export function removeDiretoria(index) {
  team.splice(index, 1)
}

export function saveDiretoriaMembers(index, members) {
  team[index].members = members.filter((m) => m.cargo || m.nome)
}

// ── Mídia da tela inicial ───────────────────────────────────────────────────
export const inicioMedia = reactive({
  file: null,
  fileName: '',
  videoUrl: '',
})

export function setInicioMedia(file) {
  if (!file) return
  inicioMedia.file = file
  inicioMedia.fileName = file.name
  inicioMedia.videoUrl = URL.createObjectURL(file)
}

export function clearInicioMedia() {
  if (inicioMedia.videoUrl) {
    URL.revokeObjectURL(inicioMedia.videoUrl)
  }
  inicioMedia.file = null
  inicioMedia.fileName = ''
  inicioMedia.videoUrl = ''
}

// ── Cardápio ─────────────────────────────────────────────────────────────
export const cardapioSemana = ref('28 jul – 01 ago 2026')

export const cardapioDias = reactive({
  'Segunda-feira': 'Galinhada',
  'Terça-feira': 'Strogonoff de frango',
  'Quarta-feira': 'Macarrão ao sugo com almôndegas',
  'Quinta-feira': 'Peixe assado com limão e alho',
  'Sexta-feira': 'Frango grelhado ao molho de ervas',
})