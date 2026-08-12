// src/stores/appData.js
//
// Estado compartilhado do app (ainda sem backend). Centraliza os dados que a
// tela de Gerenciamento edita e que as telas públicas (Início, Horário,
// Cardápio, Gabarito) exibem — uma alteração feita pelo admin em /usuarios
// aparece na hora nas outras telas.
//
// TODO: substituir por chamadas à API quando os endpoints existirem.
import { reactive, ref } from 'vue'
import api from '@/services/api'

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

export async function addTurma(turno, ano, letra) {
  if (!letra) return false
  const codigo = `${ano.charAt(0)}${letra.toUpperCase()}`
  try {
    const res = await api.post('/turmas', { turno, ano, codigo })
    // push into local state if created
    const atual = turmasState[turno]?.[ano] ?? []
    turmasState[turno] = { ...turmasState[turno], [ano]: [...atual, codigo].sort() }
    return true
  } catch (err) {
    console.error('addTurma failed', err)
    return false
  }
}

export async function removeTurma(turno, ano, codigo) {
  try {
    await api.delete(`/turmas/${codigo}`)
    turmasState[turno] = {
      ...turmasState[turno],
      [ano]: (turmasState[turno]?.[ano] ?? []).filter((t) => t !== codigo),
    }
  } catch (err) {
    console.error('removeTurma failed', err)
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

export async function setHorarioOverride(turma, day, time, subject) {
  const key = scheduleKey(turma, day, time)
  try {
    // store as a Horario entry in the backend
    await api.post('/horario', {
      turma,
      dia_semana: day,
      horario_inicio: time.split('–')[0].trim(),
      horario_fim: time.split('–')[1]?.trim() ?? time,
      disciplina: subject,
      ativo: true,
    })
    horarioOverrides[key] = subject
  } catch (err) {
    console.error('setHorarioOverride failed', err)
  }
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

export async function addDiretoria({ name, diretorGeral, primeiro, segundo }) {
  if (!name?.trim()) return
  const members = [{ cargo: 'Diretor(a)-Geral', nome: diretorGeral }]
  if (primeiro) members.push({ cargo: '1º(ª) Diretor(a)', nome: primeiro })
  if (segundo) members.push({ cargo: '2º(ª) Diretor(a)', nome: segundo })
  try {
    const res = await api.post('/diretorias', { name, members, icon: 'mdi-domain' })
    team.push(res.data)
  } catch (err) {
    console.error('addDiretoria failed', err)
  }
}

export async function removeDiretoria(index) {
  const dir = team[index]
  if (!dir) return
  try {
    if (dir.id) {
      await api.delete(`/diretorias/${dir.id}`)
    }
    team.splice(index, 1)
  } catch (err) {
    console.error('removeDiretoria failed', err)
  }
}

export async function saveDiretoriaMembers(index, members) {
  const dir = team[index]
  if (!dir) return
  const payload = { members: members.filter((m) => m.cargo || m.nome) }
  try {
    let res
    if (dir.id) {
      res = await api.put(`/diretorias/${dir.id}`, payload)
    } else {
      res = await api.post('/diretorias', {
        name: dir.name,
        icon: dir.icon ?? 'mdi-domain',
        members: payload.members,
      })
    }
    team[index] = res.data
  } catch (err) {
    console.error('saveDiretoriaMembers failed', err)
  }
}

// ── Mídia da tela inicial ───────────────────────────────────────────────────
export const inicioMedia = reactive({
  file: null,
  fileName: '',
  videoUrl: '',
})

export function setInicioMedia(file) {
  if (!file) return
  // upload to backend and use returned URL
  const fd = new FormData()
  fd.append('file', file)
  api.post('/inicio-media', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
    .then((res) => {
      inicioMedia.file = null
      inicioMedia.fileName = res.data.file_name || file.name
      inicioMedia.videoUrl = res.data.url
    })
    .catch((err) => {
      console.error('upload inicio media failed', err)
    })
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
export const CARDAPIO_DIAS = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo']

export const cardapioSemana = ref('')

export const cardapioDias = reactive({})
for (const dia of CARDAPIO_DIAS) {
  cardapioDias[dia] = ''
}

// helper map: dia_semana -> cardapio record (id + metadata)
const cardapioMap = {}

export async function setCardapioSemana(valor) {
  cardapioSemana.value = valor
}

export async function setCardapioDia(dia, valor) {
  try {
    const existing = cardapioMap[dia]
    if (existing && existing.id) {
      const res = await api.put(`/cardapio/${existing.id}`, {
        descricao: valor,
      })
      cardapioDias[dia] = res.data.descricao
      cardapioMap[dia] = res.data
    } else {
      const today = new Date().toISOString().slice(0, 10)
      const res = await api.post('/cardapio', {
        data: today,
        dia_semana: dia,
        refeicao: 'Lanche',
        descricao: valor,
        ativo: true,
      })
      cardapioDias[dia] = res.data.descricao
      cardapioMap[dia] = res.data
    }
  } catch (err) {
    console.error('setCardapioDia failed', err)
  }
}

export async function initAppData() {
  try {
    const [turmasRes, dirsRes, mediaRes] = await Promise.all([
      api.get('/turmas').catch(() => ({ data: [] })),
      api.get('/diretorias').catch(() => ({ data: [] })),
      api.get('/inicio-media').catch(() => ({ data: null })),
    ])

    // populate turmasState from server (merge by turno/ano)
    for (const t of turmasRes.data ?? []) {
      if (!turmasState[t.turno]) turmasState[t.turno] = {}
      if (!turmasState[t.turno][t.ano]) turmasState[t.turno][t.ano] = []
      if (!turmasState[t.turno][t.ano].includes(t.codigo)) {
        turmasState[t.turno][t.ano].push(t.codigo)
        turmasState[t.turno][t.ano].sort()
      }
    }

    // populate team
    if (Array.isArray(dirsRes.data)) {
      team.splice(0, team.length, ...dirsRes.data)
    }

    // inicio media
    if (mediaRes.data) {
      inicioMedia.file = null
      inicioMedia.fileName = mediaRes.data.file_name ?? ''
      inicioMedia.videoUrl = mediaRes.data.url ?? ''
    }
    // populate cardapio
    const cardRes = await api.get('/cardapio').catch(() => ({ data: [] }))
    if (Array.isArray(cardRes.data)) {
      for (const item of cardRes.data) {
        cardapioDias[item.dia_semana] = item.descricao
        cardapioMap[item.dia_semana] = item
      }
      if (cardRes.data.length) {
        const first = cardRes.data[0].data
        const last = cardRes.data[cardRes.data.length - 1].data
        cardapioSemana.value = `${first} → ${last}`
      }
    }

    // populate horarios from backend for each turma
    const turmaList = []
    for (const turnoKey of Object.keys(turmasState)) {
      for (const anoKey of Object.keys(turmasState[turnoKey] || {})) {
        turmaList.push(...(turmasState[turnoKey][anoKey] || []))
      }
    }
    await Promise.all(turmaList.map(async (t) => {
      const res = await api.get(`/horario/${t}`).catch(() => ({ data: [] }))
      for (const h of res.data ?? []) {
        const key = scheduleKey(h.turma, h.dia_semana, `${h.horario_inicio}–${h.horario_fim}`)
        horarioOverrides[key] = h.disciplina
      }
    }))
  } catch (err) {
    console.error('initAppData failed', err)
  }
}