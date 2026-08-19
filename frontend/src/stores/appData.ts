// src/stores/appData.ts
//
// Estado compartilhado do app, sincronizado com a API Laravel. Centraliza
// os dados que a tela de Gerenciamento edita e que as telas públicas
// (Início, Horário, Cardápio, Equipe) exibem — uma alteração feita pelo
// admin em /usuarios aparece na hora nas outras telas, porque tudo aqui é
// reatividade do Vue sobre o mesmo estado em memória.
//
// Tudo neste arquivo reflete dados reais vindos do backend. Não há mais
// geração local/fake de conteúdo — isso existia como placeholder antes do
// backend ficar pronto e foi removido para o deploy em produção.
import { reactive, ref } from 'vue'
import api from '@/services/api'

// ── Tipos ────────────────────────────────────────────────────────────────
export interface Member {
  cargo: string
  nome: string
}

export interface Diretoria {
  id?: number
  name: string
  icon?: string
  members: Member[]
}

interface TurmaApiItem {
  turno: string
  ano: string
  codigo: string
}

interface HorarioApiItem {
  id: number
  turma: string
  dia_semana: string
  horario_inicio: string
  horario_fim: string
  disciplina: string
}

interface CardapioApiItem {
  id: number
  data: string
  dia_semana: string
  descricao: string
}

interface InicioMediaApiItem {
  file_name: string
  url: string
}

// ── Turmas ──────────────────────────────────────────────────────────────
// Anos possíveis por turno — é configuração fixa do colégio, não dado de
// turma cadastrada, então continua hardcoded (não é "mock").
export const TURNO_ANOS: Record<string, string[]> = {
  matutino: ['2º ano', '3º ano'],
  vespertino: ['1º ano', '2º ano'],
}

// Começa vazio de propósito — as turmas reais só chegam via initAppData().
// Antes havia uma lista de turmas fixas (2A–2H, 3A–3O, etc.) pré-populada
// aqui, que aparecia pro público mesmo se o banco estivesse vazio ou a API
// fora do ar. Removido: em produção é melhor mostrar "nenhuma turma
// cadastrada" do que turmas fictícias.
export const turmasState = reactive<Record<string, Record<string, string[]>>>({
  matutino: {},
  vespertino: {},
})

export async function addTurma(turno: string, ano: string, letra: string): Promise<boolean> {
  if (!letra) return false
  const codigo = `${ano.charAt(0)}${letra.toUpperCase()}`
  try {
    await api.post('/turmas', { turno, ano, codigo })
    const atual = turmasState[turno]?.[ano] ?? []
    turmasState[turno] = { ...turmasState[turno], [ano]: [...atual, codigo].sort() }
    return true
  } catch (err) {
    console.error('addTurma failed', err)
    return false
  }
}

export async function removeTurma(turno: string, ano: string, codigo: string): Promise<void> {
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
// Lista de matérias disponíveis para o admin escolher ao editar uma aula —
// é opção de formulário, não dado exibido como se fosse real.
export const SUBJECTS = [
  'Matemática', 'Português', 'Física', 'Química', 'Biologia', 'História',
  'Geografia', 'Arte', 'Ed. Física', 'Filosofia', 'Sociologia', 'Trilhas',
  'Inglês', 'Espanhol', 'Redação',
]
export const DAYS = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta']

export interface HorarioSlot {
  time: string
  isBreak: boolean
}

export const MAT_SLOTS: HorarioSlot[] = [
  { time: '07:00–07:45', isBreak: false },
  { time: '07:45–08:30', isBreak: false },
  { time: '08:30–08:40', isBreak: true },
  { time: '08:40–09:25', isBreak: false },
  { time: '09:25–10:10', isBreak: false },
  { time: '10:10–10:30', isBreak: true },
  { time: '10:30–11:15', isBreak: false },
  { time: '11:15–12:00', isBreak: false },
]

export const VES_SLOTS: HorarioSlot[] = [
  { time: '13:00–13:45', isBreak: false },
  { time: '13:45–14:30', isBreak: false },
  { time: '14:30–14:50', isBreak: true },
  { time: '14:50–15:35', isBreak: false },
  { time: '15:35–16:20', isBreak: false },
  { time: '16:20–16:30', isBreak: true },
  { time: '16:30–17:15', isBreak: false },
  { time: '17:15–18:00', isBreak: false },
]

// Aulas cadastradas via API: chave "turma|dia|horario" -> matéria.
// Não existe mais gerador de matérias "aleatórias" por turma (baseSchedule
// com seed pseudo-aleatório) — se uma célula não tem registro no backend,
// a UI mostra vazio ("—"), como já faz TabelaHorario.vue.
export const horarioOverrides = reactive<Record<string, string>>({})

function scheduleKey(turma: string, day: string, time: string): string {
  return `${turma}|${day}|${time}`
}

// Colunas TIME do MySQL voltam do Eloquent como "07:00:00" (com segundos),
// mas os slots do frontend usam "07:00" (sem segundos). Sem normalizar, a
// chave montada aqui nunca bateria com a chave usada por getSchedule() ao
// consultar slot.time — a aula cadastrada pelo admin nunca apareceria na
// tabela pública depois de um reload.
function toHHMM(time: string): string {
  return time.length > 5 ? time.slice(0, 5) : time
}

export function getSchedule(turma: string): Record<string, Record<string, string>> {
  const merged: Record<string, Record<string, string>> = {}
  for (const day of DAYS) merged[day] = {}

  for (const key in horarioOverrides) {
    const [t, day, time] = key.split('|')
    if (t !== turma || !day || !time) continue
    if (!merged[day]) merged[day] = {}

    const value = horarioOverrides[key]
    if (value === undefined) continue
    merged[day][time] = value
  }

  return merged
}

export async function setHorarioOverride(turma: string, day: string, time: string, subject: string): Promise<void> {
  const key = scheduleKey(turma, day, time)
  const [inicio, fim] = time.split('–').map((s) => s.trim())
  try {
    await api.post('/horario', {
      turma,
      dia_semana: day,
      horario_inicio: inicio,
      horario_fim: fim ?? inicio,
      disciplina: subject,
      ativo: true,
    })
    horarioOverrides[key] = subject
  } catch (err) {
    console.error('setHorarioOverride failed', err)
  }
}

// ── Equipe / Diretorias ─────────────────────────────────────────────────────
// Fallback exibido só enquanto GET /diretorias ainda não respondeu (ou
// falhou) — evita a tela "Equipe" aparecer vazia no primeiro load. Assim
// que a API devolver dados reais, essa lista é substituída (ver
// initAppData). Se preferir, isso pode virar um seeder no backend em vez de
// ficar hardcoded no frontend — fica a critério de vocês.
const TEAM_FALLBACK: Diretoria[] = [
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
]

export const team = reactive<Diretoria[]>([...TEAM_FALLBACK])

export async function addDiretoria(payload: { name: string; diretorGeral: string; primeiro?: string; segundo?: string }): Promise<void> {
  const { name, diretorGeral, primeiro, segundo } = payload
  if (!name?.trim()) return
  const members: Member[] = [{ cargo: 'Diretor(a)-Geral', nome: diretorGeral }]
  if (primeiro) members.push({ cargo: '1º(ª) Diretor(a)', nome: primeiro })
  if (segundo) members.push({ cargo: '2º(ª) Diretor(a)', nome: segundo })
  try {
    const res = await api.post<Diretoria>('/diretorias', { name, members, icon: 'mdi-domain' })
    team.push(res.data)
  } catch (err) {
    console.error('addDiretoria failed', err)
  }
}

export async function removeDiretoria(index: number): Promise<void> {
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

export async function saveDiretoriaMembers(index: number, members: Member[]): Promise<void> {
  const dir = team[index]
  if (!dir) return
  const payload = { members: members.filter((m) => m.cargo || m.nome) }
  try {
    const res = dir.id
      ? await api.put<Diretoria>(`/diretorias/${dir.id}`, payload)
      : await api.post<Diretoria>('/diretorias', {
          name: dir.name,
          icon: dir.icon ?? 'mdi-domain',
          members: payload.members,
        })
    team[index] = res.data
  } catch (err) {
    console.error('saveDiretoriaMembers failed', err)
  }
}

export async function moveDiretoria(index: number, direcao: 'cima' | 'baixo'): Promise<void> {
  const alvoIndex = direcao === 'cima' ? index - 1 : index + 1
  const atual = team[index]
  const alvo = team[alvoIndex]
  if (!atual?.id || !alvo?.id) return

  try {
    const res = await api.post<Diretoria[]>('/diretorias/reorder', {
      id_a: atual.id,
      id_b: alvo.id,
    })
    team.splice(0, team.length, ...res.data)
  } catch (err) {
    console.error('moveDiretoria failed', err)
  }
}

// ── Mídia da tela inicial ───────────────────────────────────────────────────
export const inicioMedia = reactive({
  file: null as File | null,
  fileName: '',
  videoUrl: '',
})

export function setInicioMedia(file: File | null): void {
  if (!file) return
  const fd = new FormData()
  fd.append('file', file)
  api
    .post<InicioMediaApiItem>('/inicio-media', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
    .then((res) => {
      inicioMedia.file = null
      inicioMedia.fileName = res.data.file_name || file.name
      inicioMedia.videoUrl = res.data.url
    })
    .catch((err) => {
      console.error('upload inicio media failed', err)
    })
}

export function clearInicioMedia(): void {
  inicioMedia.file = null
  inicioMedia.fileName = ''
  inicioMedia.videoUrl = ''
}

// ── Cardápio ─────────────────────────────────────────────────────────────
export const CARDAPIO_DIAS = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado']

export const cardapioSemana = ref('')

export const cardapioDias = reactive<Record<string, string>>({})
for (const dia of CARDAPIO_DIAS) {
  cardapioDias[dia] = ''
}

// Mapa auxiliar dia_semana -> registro completo (com id), pra saber se
// setCardapioDia deve criar (POST) ou atualizar (PUT).
const cardapioMap: Record<string, CardapioApiItem> = {}

export async function setCardapioSemana(valor: string): Promise<void> {
  cardapioSemana.value = valor
}

export async function setCardapioDia(dia: string, valor: string): Promise<void> {
  try {
    const existing = cardapioMap[dia]
    if (existing?.id) {
      const res = await api.put<CardapioApiItem>(`/cardapio/${existing.id}`, { descricao: valor })
      cardapioDias[dia] = res.data.descricao
      cardapioMap[dia] = res.data
    } else {
      const today = new Date().toISOString().slice(0, 10)
      const res = await api.post<CardapioApiItem>('/cardapio', {
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

// ── Bootstrap ────────────────────────────────────────────────────────────
export async function initAppData(): Promise<void> {
  try {
    const [turmasRes, dirsRes, mediaRes] = await Promise.all([
      api.get<TurmaApiItem[]>('/turmas').catch(() => ({ data: [] as TurmaApiItem[] })),
      api.get<Diretoria[]>('/diretorias').catch(() => ({ data: [] as Diretoria[] })),
      api.get<InicioMediaApiItem | null>('/inicio-media').catch(() => ({ data: null })),
    ])

   for (const t of turmasRes.data ?? []) {
      if (!turmasState[t.turno]) turmasState[t.turno] = {}
      const anoList = turmasState[t.turno]!
      if (!anoList[t.ano]) anoList[t.ano] = []
      const codigos = anoList[t.ano]!
      if (!codigos.includes(t.codigo)) {
        codigos.push(t.codigo)
        codigos.sort()
      }
    }

    // Só substitui o fallback se a API realmente devolveu diretorias — uma
    // resposta vazia não deve apagar a lista e deixar "Equipe" em branco.
    if (Array.isArray(dirsRes.data) && dirsRes.data.length > 0) {
      team.splice(0, team.length, ...dirsRes.data)
    }

    if (mediaRes.data) {
      inicioMedia.file = null
      inicioMedia.fileName = mediaRes.data.file_name ?? ''
      inicioMedia.videoUrl = mediaRes.data.url ?? ''
    }

    const cardRes = await api.get<CardapioApiItem[]>('/cardapio').catch(() => ({ data: [] as CardapioApiItem[] }))
    if (Array.isArray(cardRes.data)) {
      for (const item of cardRes.data) {
        cardapioDias[item.dia_semana] = item.descricao
        cardapioMap[item.dia_semana] = item
      }
      if (cardRes.data.length) {
        const first = cardRes.data[0]!.data
        const last = cardRes.data[cardRes.data.length - 1]!.data
        cardapioSemana.value = `${first} → ${last}`
      }
    }

    const horarioRes = await api.get<HorarioApiItem[]>('/horario').catch(() => ({ data: [] as HorarioApiItem[] }))
    for (const h of horarioRes.data ?? []) {
      const key = scheduleKey(h.turma, h.dia_semana, `${toHHMM(h.horario_inicio)}–${toHHMM(h.horario_fim)}`)
      horarioOverrides[key] = h.disciplina
    }
      
  } catch (err) {
    console.error('initAppData failed', err)
  }
}