import { useState, useCallback, useEffect } from "react"
import logoImg from "../imports/image-2.png"
import azulejoImg from "../imports/image-4.png"
import {
  Home, Clock, Newspaper, FolderKanban, FileText,
  Download, X, Edit2, Trash2, Plus, Play, ArrowRight,
  LogIn, LogOut, Check, ChevronDown, Shield, Menu,
  MessageSquare, Map, Send, User, EyeOff, Trash,
  UtensilsCrossed, ImageIcon, Reply,
} from "lucide-react"

// ─── Types ───────────────────────────────────────────────────────────────────
type Page = "inicio" | "horario" | "noticias" | "projetos" | "gabarito" | "transparencia" | "ouvidoria" | "mapa" | "cardapio"

// Tipos espelhando o banco de dados
interface NewsItem {
  id: number
  titulo: string
  chave: string            // slug
  imagem_url?: string
  data_publicacao: string
  texto: string
}
interface ProjectItem {
  id: number
  titulo: string
  categoria: string
  status: "em_andamento" | "concluido"
  descricao: string
  imagem_url?: string
  data_conclusao?: string
}
interface AtaItem { id: number; data_referencia: string; descricao: string }
interface ContasItem { id: number; periodo: string; descricao: string }
interface Member { cargo: string; nome: string; foto_url?: string; ordem?: number }
interface Diretoria { emoji: string; name: string; members: Member[] }

// ─── Initial Data ─────────────────────────────────────────────────────────────
const INIT_NEWS: NewsItem[] = [
  { id: 1, data_publicacao: "14 de março de 2026", titulo: "Resultado da eleição: chapa Movimento assume o grêmio", chave: "eleicao-2026", texto: "Com 71% dos votos e alta participação, a nova gestão foi eleita em votação aberta no pátio da escola." },
  { id: 2, data_publicacao: "03 de abril de 2026", titulo: "Feira Cultural ABSL reúne 20 turmas no ginásio", chave: "feira-cultural-2026", texto: "Apresentações de teatro, dança e exposição de artes marcaram a primeira edição do ano, organizada pela Diretoria de Cultura." },
  { id: 3, data_publicacao: "12 de abril de 2026", titulo: "Torneio Interclasses abre inscrições para o 1º semestre", chave: "torneio-interclasses", texto: "Futebol, vôlei e handebol. Inscrições até o dia 20, feitas com o representante de cada turma." },
  { id: 4, data_publicacao: "28 de maio de 2026", titulo: "Campanha do Agasalho: escola arrecada mais de 400 peças", chave: "campanha-agasalho-2026", texto: "Em parceria com a Diretoria Social, a campanha bateu recorde e as doações foram entregues à ONG Recomeçar." },
  { id: 5, data_publicacao: "10 de junho de 2026", titulo: "Grêmio leva pauta de reforma do refeitório à direção", chave: "reforma-refeitorio", texto: "Após 340 assinaturas de alunos, a presidência entregou documento formal e aguarda resposta até o fim do semestre." },
  { id: 6, data_publicacao: "18 de junho de 2026", titulo: "Semana da Saúde Mental: atividades gratuitas para todos", chave: "semana-saude-mental", texto: "Rodas de conversa, meditação guiada e atendimento com profissionais durante os intervalos da semana." },
]

const INIT_PROJECTS: ProjectItem[] = [
  { id: 1, categoria: "Diretoria Social", status: "concluido", titulo: "Campanha do Agasalho 2026", descricao: "Arrecadação de roupas e calçados em todas as turmas, com entrega a instituições da cidade.", data_conclusao: "Maio 2026" },
  { id: 2, categoria: "Esporte e Lazer", status: "em_andamento", titulo: "Torneio Interclasses", descricao: "Competição entre turmas de futsal, vôlei e handebol, com tabela publicada e arbitragem voluntária." },
  { id: 3, categoria: "Cultura", status: "em_andamento", titulo: "Feira Cultural ABSL", descricao: "Mostra de música, teatro e artes visuais produzidos pelos alunos, com palco montado no ginásio." },
  { id: 4, categoria: "Presidência", status: "em_andamento", titulo: "Reforma do Espaço de Convivência", descricao: "Bancos, sombrite e torneiras no pátio central, negociadas junto à direção a partir de pauta do estudantil." },
  { id: 5, categoria: "Saúde e Meio Ambiente", status: "em_andamento", titulo: "Semana da Saúde Mental", descricao: "Rodas de conversa e atividades com profissionais convidados durante os intervalos." },
  { id: 6, categoria: "Tecnologia e Inovação", status: "em_andamento", titulo: "Portal do Estudante", descricao: "Canal digital com horários, gabaritos e notícias do grêmio reunidos em um só lugar." },
]

const INIT_ATAS: AtaItem[] = [
  { id: 1, data_referencia: "10 de março de 2026", descricao: "Posse da nova diretoria e eleição interna de cargos complementares" },
  { id: 2, data_referencia: "02 de abril de 2026", descricao: "Planejamento do Torneio Interclasses e Feira Cultural ABSL" },
  { id: 3, data_referencia: "15 de maio de 2026", descricao: "Prestação de contas do 1º trimestre e aprovação do orçamento da Campanha do Agasalho" },
  { id: 4, data_referencia: "18 de junho de 2026", descricao: "Avaliação dos projetos em andamento e pauta enviada à direção da escola" },
]

const INIT_CONTAS: ContasItem[] = [
  { id: 1, periodo: "1º Trimestre 2026", descricao: "Receitas, despesas e saldo referentes ao período de janeiro a março de 2026." },
  { id: 2, periodo: "Relatório Campanha do Agasalho", descricao: "Prestação de contas da campanha solidária com totais arrecadados e destino das doações." },
]

const INIT_TEAM: Diretoria[] = [
  { emoji: "👑", name: "Presidência", members: [{ cargo: "Presidente", nome: "Samuel" }, { cargo: "Vice-Presidente", nome: "Bárbara" }, { cargo: "1º Vice-Presidente", nome: "Jósue" }] },
  { emoji: "📋", name: "Secretaria", members: [{ cargo: "Secretário-Geral", nome: "Marcus Paulo" }, { cargo: "1ª Secretária", nome: "Giovanna" }, { cargo: "2ª Secretária", nome: "Brenda" }] },
  { emoji: "🔥", name: "Tesouraria", members: [{ cargo: "Tesoureira-Geral", nome: "Andressa" }, { cargo: "1º Tesoureiro", nome: "Rafael" }, { cargo: "2º Tesoureiro", nome: "Vitor" }] },
  { emoji: "🌐", name: "Esporte e Lazer", members: [{ cargo: "Diretor-Geral", nome: "Igor" }, { cargo: "1ª Diretora", nome: "Rafaela" }, { cargo: "2º Diretor", nome: "Artur Araripe" }] },
  { emoji: "🎭", name: "Cultura", members: [{ cargo: "Diretora-Geral", nome: "Jennyfer" }, { cargo: "1ª Diretora", nome: "Ludmila" }, { cargo: "2º Diretor", nome: "Thiago" }] },
  { emoji: "📚", name: "Políticas Educacionais", members: [{ cargo: "Diretor-Geral", nome: "Enzo" }, { cargo: "1ª Diretora", nome: "Lorena" }, { cargo: "2ª Diretora", nome: "Larissa" }] },
  { emoji: "🌱", name: "Saúde e Meio Ambiente", members: [{ cargo: "Diretora-Geral", nome: "Ayla" }, { cargo: "1ª Diretora", nome: "Maryane" }, { cargo: "2ª Diretora", nome: "Joana" }] },
  { emoji: "🤝", name: "Diretoria Social", members: [{ cargo: "Diretor-Geral", nome: "David" }, { cargo: "1ª Diretora", nome: "Mariana" }, { cargo: "2º Diretor", nome: "Matheus" }] },
  { emoji: "📢", name: "Imprensa e Comunicação", members: [{ cargo: "Diretora-Geral", nome: "Yara" }, { cargo: "1ª Diretora", nome: "Giulia" }, { cargo: "2ª Diretora", nome: "Ana Júlia" }] },
  { emoji: "💻", name: "Tecnologia e Inovação", members: [{ cargo: "Diretor-Geral", nome: "Kauan Guilherme" }, { cargo: "1º Diretor", nome: "Pedro Lucas" }, { cargo: "2º Diretor", nome: "Maria Eduarda" }] },
]

const SUBJECTS = ["Matemática", "Português", "Física", "Química", "Biologia", "História", "Geografia", "Arte", "Ed. Física", "Filosofia", "Sociologia", "Literatura", "Inglês", "Redação"]
const DAYS = ["Segunda", "Terça", "Quarta", "Quinta", "Sexta"]

const MAT_SLOTS = [
  { time: "07:00–07:45", isBreak: false },
  { time: "07:45–08:30", isBreak: false },
  { time: "08:30–08:40", isBreak: true },
  { time: "08:40–09:25", isBreak: false },
  { time: "09:25–10:10", isBreak: false },
  { time: "10:10–10:30", isBreak: true },
  { time: "10:30–11:15", isBreak: false },
  { time: "11:15–12:00", isBreak: false },
]

const VES_SLOTS = [
  { time: "13:00–13:45", isBreak: false },
  { time: "13:45–14:30", isBreak: false },
  { time: "14:30–14:50", isBreak: true },
  { time: "14:50–15:35", isBreak: false },
  { time: "15:35–16:20", isBreak: false },
  { time: "16:20–16:30", isBreak: true },
  { time: "16:30–17:15", isBreak: false },
  { time: "17:15–18:00", isBreak: false },
]

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

// ─── Logo ─────────────────────────────────────────────────────────────────────
function LogoStar({ size = 40 }: { size?: number }) {
  return (
    <img
      src={logoImg}
      alt="ABSL Logo"
      width={size}
      height={size}
      style={{ width: size, height: size, objectFit: "contain" }}
    />
  )
}

// ─── Geometric Background for Hero ───────────────────────────────────────────
function GeoBg() {
  const shapes = [
    { type: "circle", x: 8, y: 12, r: 60, opacity: 0.06 },
    { type: "circle", x: 82, y: 70, r: 80, opacity: 0.05 },
    { type: "circle", x: 55, y: 5, r: 30, opacity: 0.07 },
    { type: "square", x: 70, y: 20, size: 40, rot: 15, opacity: 0.05 },
    { type: "square", x: 15, y: 60, size: 25, rot: 30, opacity: 0.06 },
    { type: "diamond", x: 85, y: 40, size: 35, opacity: 0.07 },
    { type: "diamond", x: 40, y: 85, size: 20, opacity: 0.06 },
    { type: "semicircle", x: 25, y: 25, r: 45, opacity: 0.05 },
    { type: "semicircle", x: 90, y: 88, r: 55, opacity: 0.04 },
    { type: "circle", x: 60, y: 50, r: 15, opacity: 0.09 },
    { type: "square", x: 45, y: 15, size: 15, rot: 45, opacity: 0.08 },
    { type: "diamond", x: 10, y: 85, size: 25, opacity: 0.06 },
  ]

  return (
    <svg className="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
      {shapes.map((s, i) => {
        if (s.type === "circle") {
          return <circle key={i} cx={`${s.x}%`} cy={`${s.y}%`} r={s.r} fill="white" fillOpacity={s.opacity} />
        }
        if (s.type === "square") {
          const half = (s.size ?? 20) / 2
          return (
            <rect key={i} x={`${s.x}%`} y={`${s.y}%`} width={s.size} height={s.size}
              fill="white" fillOpacity={s.opacity}
              transform={`translate(-${half},-${half}) rotate(${s.rot ?? 0}, ${half}, ${half})`}
            />
          )
        }
        if (s.type === "diamond") {
          const half = (s.size ?? 20) / 2
          return (
            <rect key={i} x={`${s.x}%`} y={`${s.y}%`} width={s.size} height={s.size}
              fill="white" fillOpacity={s.opacity}
              transform={`translate(-${half},-${half}) rotate(45, ${half}, ${half})`}
            />
          )
        }
        if (s.type === "semicircle") {
          return (
            <path key={i}
              d={`M -${s.r ?? 40} 0 A ${s.r ?? 40} ${s.r ?? 40} 0 0 1 ${s.r ?? 40} 0 Z`}
              fill="white" fillOpacity={s.opacity}
              transform={`translate(${s.x}%, ${s.y}%)`}
            />
          )
        }
        return null
      })}
    </svg>
  )
}

// ─── Decorative Strip ─────────────────────────────────────────────────────────
function DecorativeStrip() {
  const items = Array.from({ length: 30 }, (_, i) => i % 4)
  return (
    <div className="w-full flex items-center gap-1.5 py-1.5 px-4 overflow-hidden bg-[#0D1F3C]/5 border-b border-[#0D1F3C]/8">
      {items.map((type, i) => (
        <span key={i} className="inline-block flex-shrink-0" style={{ opacity: 0.4 + (i % 3) * 0.15 }}>
          {type === 0 && <svg width="8" height="8" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4" fill="#1A3F8F" /></svg>}
          {type === 1 && <svg width="8" height="8" viewBox="0 0 8 8"><rect width="8" height="8" fill="#2563EB" /></svg>}
          {type === 2 && <svg width="8" height="8" viewBox="0 0 8 8"><rect width="6" height="6" x="1" y="1" fill="#F5C518" transform="rotate(45 4 4)" /></svg>}
          {type === 3 && <svg width="8" height="8" viewBox="0 0 8 8"><path d="M0 8 A8 8 0 0 1 8 8 Z" fill="#38BDF8" /></svg>}
        </span>
      ))}
    </div>
  )
}

// ─── Admin Banner ─────────────────────────────────────────────────────────────
function AdminBanner() {
  return (
    <div className="flex items-center gap-2 px-4 py-2.5 bg-amber-50 border border-amber-200 rounded-xl mb-6 text-amber-800 text-sm font-medium">
      <Edit2 size={14} />
      <span>Modo administrador ativo — você pode criar, editar e excluir conteúdo nesta página.</span>
    </div>
  )
}

// ─── Page Header ──────────────────────────────────────────────────────────────
function PageHeader({ label, title, subtitle }: { label: string; title: string; subtitle?: string }) {
  return (
    <div className="mb-8">
      <span className="text-xs font-mono font-medium tracking-widest text-[#1A3F8F] uppercase">{label}</span>
      <h1 className="text-4xl font-bold text-[#0D1F3C] mt-1 mb-2" style={{ fontFamily: "'Playfair Display', serif" }}>{title}</h1>
      {subtitle && <p className="text-[#5A6A85] text-base">{subtitle}</p>}
    </div>
  )
}

// ─── Sidebar ──────────────────────────────────────────────────────────────────
const NAV_ITEMS: { id: Page; label: string; icon: React.ReactNode }[] = [
  { id: "inicio", label: "Início", icon: <Home size={17} /> },
  { id: "horario", label: "Horário das Aulas", icon: <Clock size={17} /> },
  { id: "noticias", label: "Notícias", icon: <Newspaper size={17} /> },
  { id: "projetos", label: "Projetos", icon: <FolderKanban size={17} /> },
  { id: "gabarito", label: "Gabarito", icon: <FileText size={17} /> },
  { id: "transparencia", label: "Transparência", icon: <Shield size={17} /> },
  { id: "cardapio", label: "Cardápio Semanal", icon: <UtensilsCrossed size={17} /> },
  { id: "ouvidoria", label: "Os Ouvintes", icon: <MessageSquare size={17} /> },
  { id: "mapa", label: "Mapa da Escola", icon: <Map size={17} /> },
]

function Sidebar({ current, onChange, isAdmin, onToggleAdmin, open, onClose }: {
  current: Page; onChange: (p: Page) => void; isAdmin: boolean; onToggleAdmin: () => void
  open: boolean; onClose: () => void
}) {
  // Close on route change (mobile)
  const handleNav = (p: Page) => { onChange(p); onClose() }

  const inner = (
    <aside className="h-full w-56 flex flex-col" style={{ background: "#0D1F3C" }}>
      {/* Logo area */}
      <div className="px-5 pt-6 pb-4 border-b border-white/10">
        <div className="flex items-center justify-between mb-2">
          <div className="flex items-center gap-3">
            <LogoStar size={40} />
            <div>
              <div className="text-white font-bold text-sm leading-tight" style={{ fontFamily: "'DM Sans', sans-serif" }}>ABSL</div>
              <div className="text-white/60 text-[10px] leading-tight">Grêmio Athos Bulcão</div>
            </div>
          </div>
          {/* Close button — mobile only */}
          <button onClick={onClose} className="md:hidden text-white/50 hover:text-white p-1">
            <X size={18} />
          </button>
        </div>
        {isAdmin && (
          <div className="mt-2 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-amber-400/20 border border-amber-400/40">
            <div className="w-1.5 h-1.5 rounded-full bg-amber-400" />
            <span className="text-amber-300 text-[10px] font-medium">Admin ativo</span>
          </div>
        )}
      </div>

      {/* Nav */}
      <nav className="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
        {NAV_ITEMS.map((item) => {
          const active = current === item.id
          return (
            <button
              key={item.id}
              onClick={() => handleNav(item.id)}
              className={`w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 ${
                active ? "bg-[#1A3F8F] text-white" : "text-white/60 hover:text-white hover:bg-white/8"
              }`}
              style={{ fontFamily: "'DM Sans', sans-serif" }}
            >
              <span className={active ? "text-[#F5C518]" : ""}>{item.icon}</span>
              {item.label}
            </button>
          )
        })}
      </nav>

      {/* Footer */}
      <div className="px-3 pb-4 pt-2 border-t border-white/10 space-y-2">
        <button
          onClick={onToggleAdmin}
          className={`w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-all ${
            isAdmin
              ? "bg-red-500/20 text-red-300 hover:bg-red-500/30"
              : "bg-[#F5C518]/15 text-[#F5C518] hover:bg-[#F5C518]/25"
          }`}
          style={{ fontFamily: "'DM Sans', sans-serif" }}
        >
          {isAdmin ? <><LogOut size={15} /> Sair da administração</> : <><LogIn size={15} /> Login administrativo</>}
        </button>
        <p className="text-white/30 text-[9px] text-center leading-tight px-1">
          Grêmio Estudantil Athos Bulcão · ABSL — Brasília, DF
        </p>
      </div>
    </aside>
  )

  return (
    <>
      {/* Desktop: fixed sidebar */}
      <div className="hidden md:flex fixed left-0 top-0 h-full w-56 z-40 flex-col">
        {inner}
      </div>

      {/* Mobile: overlay drawer */}
      {open && (
        <div className="md:hidden fixed inset-0 z-50 flex">
          <div className="w-56 flex flex-col h-full shadow-2xl">{inner}</div>
          <div className="flex-1 bg-black/50" onClick={onClose} />
        </div>
      )}
    </>
  )
}

// ─── Page: INÍCIO ─────────────────────────────────────────────────────────────
function PageInicio({ isAdmin, team, setTeam }: { isAdmin: boolean; team: Diretoria[]; setTeam: (t: Diretoria[]) => void }) {
  const [editingDiretoria, setEditingDiretoria] = useState<number | null>(null)
  const [addingMemberTo, setAddingMemberTo] = useState<number | null>(null)
  const [newMember, setNewMember] = useState({ cargo: "", nome: "" })
  const [editTeam, setEditTeam] = useState<Diretoria[]>(team)
  const [showNewDiretoria, setShowNewDiretoria] = useState(false)
  const [newDir, setNewDir] = useState({ emoji: "⭐", name: "", members: [{ cargo: "", nome: "" }] })

  const saveEdit = (idx: number) => {
    setTeam(editTeam)
    setEditingDiretoria(null)
  }

  const addMember = (idx: number) => {
    const updated = team.map((d, i) => i === idx ? { ...d, members: [...d.members, { ...newMember }] } : d)
    setTeam(updated)
    setEditTeam(updated)
    setAddingMemberTo(null)
    setNewMember({ cargo: "", nome: "" })
  }

  const addDiretoria = () => {
    const updated = [...team, { ...newDir }]
    setTeam(updated)
    setEditTeam(updated)
    setShowNewDiretoria(false)
    setNewDir({ emoji: "⭐", name: "", members: [{ cargo: "", nome: "" }] })
  }

  return (
    <div style={{ fontFamily: "'DM Sans', sans-serif" }}>
      {/* Hero */}
      <div className="relative rounded-2xl overflow-hidden mb-10" style={{ minHeight: 360 }}>
        {/* Azulejo background */}
        <img src={azulejoImg} alt="" aria-hidden className="absolute inset-0 w-full h-full object-cover" />
        {/* Dark overlay so text stays readable */}
        <div className="absolute inset-0" style={{ background: "linear-gradient(135deg, rgba(13,31,60,0.82) 0%, rgba(26,63,143,0.72) 60%, rgba(37,99,235,0.60) 100%)" }} />
        <div className="relative z-10 p-10 flex flex-col justify-center min-h-[360px]">
          <p className="text-[#F5C518] text-xs font-mono tracking-widest uppercase mb-4">GESTÃO 2026 · ABSL</p>
          <h1 className="text-4xl md:text-6xl font-black leading-none text-white mb-1" style={{ fontFamily: "'Playfair Display', serif" }}>
            GRÊMIO
          </h1>
          <h1 className="text-4xl md:text-6xl font-black leading-none text-[#F5C518] italic mb-1" style={{ fontFamily: "'Playfair Display', serif" }}>
            ATHOS
          </h1>
          <h1 className="text-3xl md:text-5xl font-black leading-none text-white italic mb-8" style={{ fontFamily: "'Playfair Display', serif" }}>
            BULCÃO ✦
          </h1>

          <div className="flex flex-col gap-5 max-w-sm md:max-w-md">
            <div className="flex gap-3">
              <div className="w-1 rounded-full flex-shrink-0" style={{ background: "#F5C518" }} />
              <p className="text-white/85 text-sm leading-relaxed">
                Não é apenas representação estudantil. É a <strong className="text-white">voz</strong> de cada aluno virando decisão, projeto e mudança real dentro da escola.
              </p>
            </div>
            <div className="flex flex-wrap items-center gap-3">
              <button className="px-5 py-2.5 rounded-full bg-white text-[#0D1F3C] text-sm font-bold uppercase tracking-wide hover:bg-[#F5C518] transition-colors flex items-center gap-2">
                CONHEÇA O GRÊMIO <ArrowRight size={14} />
              </button>
              <button className="flex items-center gap-2 text-white/80 text-sm hover:text-white transition-colors">
                <div className="w-8 h-8 rounded-full border border-white/40 flex items-center justify-center">
                  <Play size={12} className="ml-0.5" />
                </div>
                Ver Vídeo
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* Institutional */}
      <section className="mb-10">
        <h2 className="text-2xl font-bold text-[#0D1F3C] mb-3" style={{ fontFamily: "'Playfair Display', serif" }}>Grêmio Athos Bulcão</h2>
        <p className="text-[#5A6A85] leading-relaxed max-w-2xl">
          O Grêmio Estudantil Athos Bulcão — ABSL — é a entidade representativa dos estudantes do Colégio Athos Bulcão, em Brasília. Eleito democraticamente, o grêmio atua como canal oficial entre o corpo discente e a direção da escola, levando demandas, propondo projetos e garantindo que a voz de cada aluno seja ouvida. Qualquer estudante pode propor pauta, participar de reuniões e contribuir com a gestão.
        </p>
      </section>

      {/* Deveres */}
      <section className="mb-10">
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
          {[
            { title: "Representar", desc: "Levamos as demandas dos estudantes à direção e acompanhamos as respostas até o fim." },
            { title: "Cuidar", desc: "Campanhas solidárias, acolhimento e projetos de bem-estar dentro da escola." },
            { title: "Movimentar", desc: "Eventos, torneios, feiras culturais e tudo que faz a escola ter vida." },
          ].map((d) => (
            <div key={d.title} className="bg-white rounded-xl p-6 border border-[#0D1F3C]/8 hover:shadow-md transition-shadow">
              <div className="w-6 h-0.5 bg-[#F5C518] mb-3 rounded" />
              <h3 className="text-[#0D1F3C] font-bold text-base mb-2">{d.title}</h3>
              <p className="text-[#5A6A85] text-sm leading-relaxed">{d.desc}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Equipe */}
      <section>
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-2xl font-bold text-[#0D1F3C]" style={{ fontFamily: "'Playfair Display', serif" }}>Equipe</h2>
          {isAdmin && (
            <button
              onClick={() => setShowNewDiretoria(true)}
              className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#1A3F8F] text-white text-xs font-medium hover:bg-[#0D1F3C] transition-colors"
            >
              <Plus size={13} /> Nova Diretoria
            </button>
          )}
        </div>
        {isAdmin && <AdminBanner />}

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {(isAdmin ? editTeam : team).map((dir, idx) => (
            <div key={idx} className="bg-white rounded-xl p-5 border border-[#0D1F3C]/8">
              <div className="flex items-start justify-between mb-3">
                <div className="flex items-center gap-2">
                  <span className="text-xl">{dir.emoji}</span>
                  <span className="text-[#1A3F8F] font-bold text-xs uppercase tracking-wide">{dir.name}</span>
                </div>
                {isAdmin && (
                  <div className="flex gap-1">
                    <button
                      onClick={() => setEditingDiretoria(editingDiretoria === idx ? null : idx)}
                      className="p-1 rounded text-[#5A6A85] hover:text-[#1A3F8F] hover:bg-blue-50 transition-colors"
                    >
                      <Edit2 size={13} />
                    </button>
                  </div>
                )}
              </div>
              <ul className="space-y-1">
                {dir.members.map((m, mi) =>
                  isAdmin && editingDiretoria === idx ? (
                    <li key={mi} className="flex gap-2">
                      <input
                        className="flex-1 text-xs border border-[#0D1F3C]/15 rounded px-2 py-1 bg-[#EEF3FB]"
                        value={editTeam[idx]?.members[mi]?.cargo ?? m.cargo}
                        onChange={(e) => {
                          const t = editTeam.map((d, di) => di !== idx ? d : {
                            ...d, members: d.members.map((mm, mmi) => mmi !== mi ? mm : { ...mm, cargo: e.target.value })
                          })
                          setEditTeam(t)
                        }}
                      />
                      <input
                        className="flex-1 text-xs border border-[#0D1F3C]/15 rounded px-2 py-1 bg-[#EEF3FB]"
                        value={editTeam[idx]?.members[mi]?.nome ?? m.nome}
                        onChange={(e) => {
                          const t = editTeam.map((d, di) => di !== idx ? d : {
                            ...d, members: d.members.map((mm, mmi) => mmi !== mi ? mm : { ...mm, nome: e.target.value })
                          })
                          setEditTeam(t)
                        }}
                      />
                    </li>
                  ) : (
                    <li key={mi} className="text-xs text-[#5A6A85]">
                      <span className="text-[#0D1F3C] font-medium">{m.cargo}:</span> {m.nome}
                    </li>
                  )
                )}
              </ul>
              {isAdmin && editingDiretoria === idx && (
                <button onClick={() => saveEdit(idx)} className="mt-2 text-xs px-3 py-1 rounded bg-[#1A3F8F] text-white hover:bg-[#0D1F3C]">
                  Salvar
                </button>
              )}
              {isAdmin && (
                <button
                  onClick={() => setAddingMemberTo(addingMemberTo === idx ? null : idx)}
                  className="mt-3 flex items-center gap-1 text-xs text-[#1A3F8F] hover:underline"
                >
                  <Plus size={12} /> Adicionar Integrante
                </button>
              )}
              {isAdmin && addingMemberTo === idx && (
                <div className="mt-2 flex gap-2">
                  <input
                    placeholder="Cargo"
                    className="flex-1 text-xs border border-[#0D1F3C]/15 rounded px-2 py-1 bg-[#EEF3FB]"
                    value={newMember.cargo}
                    onChange={(e) => setNewMember({ ...newMember, cargo: e.target.value })}
                  />
                  <input
                    placeholder="Nome"
                    className="flex-1 text-xs border border-[#0D1F3C]/15 rounded px-2 py-1 bg-[#EEF3FB]"
                    value={newMember.nome}
                    onChange={(e) => setNewMember({ ...newMember, nome: e.target.value })}
                  />
                  <button onClick={() => addMember(idx)} className="px-2 py-1 rounded bg-green-500 text-white text-xs">
                    <Check size={12} />
                  </button>
                </div>
              )}
            </div>
          ))}
        </div>

        {/* New Diretoria Modal */}
        {showNewDiretoria && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div className="bg-white rounded-2xl p-6 w-full max-w-sm shadow-2xl mx-4">
              <div className="flex items-center justify-between mb-4">
                <h3 className="font-bold text-[#0D1F3C]">Nova Diretoria</h3>
                <button onClick={() => setShowNewDiretoria(false)}><X size={18} /></button>
              </div>
              <div className="space-y-3">
                <input placeholder="Emoji" className="w-full border border-[#0D1F3C]/15 rounded-lg px-3 py-2 text-sm bg-[#EEF3FB]" value={newDir.emoji} onChange={e => setNewDir({ ...newDir, emoji: e.target.value })} />
                <input placeholder="Nome da diretoria" className="w-full border border-[#0D1F3C]/15 rounded-lg px-3 py-2 text-sm bg-[#EEF3FB]" value={newDir.name} onChange={e => setNewDir({ ...newDir, name: e.target.value })} />
                <div className="flex gap-2">
                  <button onClick={() => setShowNewDiretoria(false)} className="flex-1 py-2 rounded-lg border border-[#0D1F3C]/15 text-sm text-[#5A6A85] hover:bg-gray-50">Cancelar</button>
                  <button onClick={addDiretoria} className="flex-1 py-2 rounded-lg bg-[#1A3F8F] text-white text-sm font-medium hover:bg-[#0D1F3C]">Salvar</button>
                </div>
              </div>
            </div>
          </div>
        )}
      </section>
    </div>
  )
}

// ─── Page: HORÁRIO ────────────────────────────────────────────────────────────
const letters = (prefix: string, from: string, to: string) => {
  const a = from.charCodeAt(0), b = to.charCodeAt(0)
  return Array.from({ length: b - a + 1 }, (_, i) => prefix + String.fromCharCode(a + i))
}

const TURNO_ANOS: Record<string, string[]> = {
  matutino: ["2º ano", "3º ano"],
  vespertino: ["1º ano", "2º ano"],
}

const TURNO_TURMAS: Record<string, Record<string, string[]>> = {
  matutino: {
    "2º ano": letters("2", "A", "H"),
    "3º ano": letters("3", "A", "O"),
  },
  vespertino: {
    "1º ano": letters("1", "A", "P"),
    "2º ano": letters("2", "I", "P"),
  },
}

function PageHorario({ isAdmin }: { isAdmin: boolean }) {
  const [turno, setTurno] = useState<"matutino" | "vespertino">("matutino")
  const [ano, setAno] = useState("2º ano")
  const [turma, setTurma] = useState("2A")
  const [schedule, setSchedule] = useState<Record<string, Record<string, Record<string, Record<string, string>>>>>({})
  const [editingCell, setEditingCell] = useState<{ day: string; time: string } | null>(null)
  const [editValue, setEditValue] = useState("")

  const anos = TURNO_ANOS[turno]
  const turmasMap = TURNO_TURMAS[turno]

  const getSchedule = (t: string) => {
    if (!schedule[t]) {
      const gen = genSchedule(t)
      setSchedule(prev => ({ ...prev, [t]: { mat: gen, ves: gen } }))
      return gen
    }
    return schedule[t][turno === "matutino" ? "mat" : "ves"] ?? genSchedule(t)
  }

  const currentSched = getSchedule(turma)
  const slots = turno === "matutino" ? MAT_SLOTS : VES_SLOTS

  const updateCell = (day: string, time: string, val: string) => {
    setSchedule(prev => ({
      ...prev,
      [turma]: {
        ...(prev[turma] ?? {}),
        [turno === "matutino" ? "mat" : "ves"]: {
          ...(currentSched),
          [day]: { ...(currentSched[day] ?? {}), [time]: val }
        }
      }
    }))
    setEditingCell(null)
  }

  return (
    <div style={{ fontFamily: "'DM Sans', sans-serif" }}>
      <PageHeader label="ABSL" title="Horário das Aulas" subtitle="Selecione o turno, o ano e a turma para visualizar a grade semanal." />
      {isAdmin && <AdminBanner />}

      {/* Controls */}
      <div className="flex flex-wrap items-start gap-3 mb-6">
        {/* Turno toggle */}
        <div className="flex bg-white border border-[#0D1F3C]/12 rounded-full p-0.5">
          {(["matutino", "vespertino"] as const).map((t) => (
            <button
              key={t}
              onClick={() => {
                const newAnos = TURNO_ANOS[t]
                const newAno = newAnos[0]
                setTurno(t)
                setAno(newAno)
                setTurma(TURNO_TURMAS[t][newAno][0])
              }}
              className={`px-4 py-1.5 rounded-full text-sm font-medium transition-all ${turno === t ? "bg-[#1A3F8F] text-white" : "text-[#5A6A85] hover:text-[#0D1F3C]"}`}
            >
              {t === "matutino" ? "Matutino" : "Vespertino"}
            </button>
          ))}
        </div>

        {/* Ano tabs */}
        <div className="flex bg-white border border-[#0D1F3C]/12 rounded-full p-0.5">
          {anos.map((a) => (
            <button
              key={a}
              onClick={() => { setAno(a); setTurma(turmasMap[a][0]) }}
              className={`px-4 py-1.5 rounded-full text-sm font-medium transition-all ${ano === a ? "bg-[#1A3F8F] text-white" : "text-[#5A6A85] hover:text-[#0D1F3C]"}`}
            >
              {a}
            </button>
          ))}
        </div>

        {/* Turma select */}
        <div className="relative">
          <select
            value={turma}
            onChange={(e) => setTurma(e.target.value)}
            className="appearance-none bg-white border border-[#0D1F3C]/12 rounded-full px-4 py-1.5 pr-8 text-sm font-medium text-[#0D1F3C] cursor-pointer"
          >
            {(turmasMap[ano] ?? []).map((t) => <option key={t}>{t}</option>)}
          </select>
          <ChevronDown size={14} className="absolute right-2.5 top-1/2 -translate-y-1/2 text-[#5A6A85] pointer-events-none" />
        </div>

        <span className="text-xs font-mono text-[#5A6A85] bg-white border border-[#0D1F3C]/12 px-3 py-1.5 rounded-full">
          {turno === "matutino" ? "07:00 – 12:00" : "13:00 – 18:00"}
        </span>
      </div>

      {/* Table */}
      <div className="bg-white rounded-2xl border border-[#0D1F3C]/10 overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-[#0D1F3C]/8">
                <th className="text-left px-4 py-3 text-[#0D1F3C] font-semibold text-xs uppercase tracking-wide w-32">Horário</th>
                {DAYS.map(d => (
                  <th key={d} className="text-center px-3 py-3 text-[#0D1F3C] font-semibold text-xs uppercase tracking-wide">{d}</th>
                ))}
              </tr>
            </thead>
            <tbody>
              {slots.map((slot, si) => (
                <tr key={si} className={`border-b border-[#0D1F3C]/6 ${slot.isBreak ? "bg-[#EEF3FB]/60" : si % 2 === 0 ? "bg-white" : "bg-[#EEF3FB]/30"}`}>
                  <td className="px-4 py-2.5 font-mono text-xs text-[#5A6A85] whitespace-nowrap">{slot.time}</td>
                  {slot.isBreak ? (
                    <td colSpan={5} className="text-center text-xs text-[#5A6A85] font-medium py-2 tracking-widest uppercase">
                      INTERVALO
                    </td>
                  ) : (
                    DAYS.map(day => {
                      const val = currentSched[day]?.[slot.time] ?? "—"
                      const isEditing = isAdmin && editingCell?.day === day && editingCell?.time === slot.time
                      return (
                        <td key={day} className="text-center px-3 py-2.5 relative group">
                          {isEditing ? (
                            <div className="flex gap-1 items-center">
                              <select
                                autoFocus
                                value={editValue}
                                onChange={e => setEditValue(e.target.value)}
                                className="text-xs border border-[#1A3F8F] rounded px-1 py-0.5 w-full"
                              >
                                {SUBJECTS.map(s => <option key={s}>{s}</option>)}
                              </select>
                              <button onClick={() => updateCell(day, slot.time, editValue)} className="text-green-600 hover:text-green-700">
                                <Check size={12} />
                              </button>
                            </div>
                          ) : (
                            <div className="flex items-center justify-center gap-1">
                              <span className="text-xs text-[#0D1F3C]">{val}</span>
                              {isAdmin && (
                                <button
                                  onClick={() => { setEditingCell({ day, time: slot.time }); setEditValue(val) }}
                                  className="opacity-0 group-hover:opacity-100 p-0.5 text-[#5A6A85] hover:text-[#1A3F8F] transition-opacity"
                                >
                                  <Edit2 size={11} />
                                </button>
                              )}
                            </div>
                          )}
                        </td>
                      )
                    })
                  )}
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  )
}

// ─── Image Upload Helper ──────────────────────────────────────────────────────
function useImageUpload(initial?: string) {
  const [preview, setPreview] = useState<string | undefined>(initial)
  const handle = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0]
    if (!file) return
    const reader = new FileReader()
    reader.onload = ev => setPreview(ev.target?.result as string)
    reader.readAsDataURL(file)
  }
  return { preview, setPreview, handle }
}

function ImageUploadField({ value, onChange, label = "Imagem de capa" }: {
  value?: string; onChange: (v: string | undefined) => void; label?: string
}) {
  const { preview, setPreview, handle } = useImageUpload(value)
  useEffect(() => { onChange(preview) }, [preview])
  return (
    <div>
      <label className="text-xs font-medium text-[#5A6A85] mb-1 block">{label}</label>
      {preview ? (
        <div className="relative rounded-xl overflow-hidden mb-2" style={{ height: 140 }}>
          <img src={preview} alt="preview" className="w-full h-full object-cover" />
          <button
            onClick={() => { setPreview(undefined); onChange(undefined) }}
            className="absolute top-2 right-2 bg-black/50 text-white rounded-full p-1 hover:bg-black/70"
          ><X size={12} /></button>
        </div>
      ) : (
        <label className="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-[#0D1F3C]/15 rounded-xl py-6 cursor-pointer hover:border-[#1A3F8F]/40 hover:bg-[#EEF3FB]/60 transition-colors mb-2">
          <ImageIcon size={22} className="text-[#5A6A85]" />
          <span className="text-xs text-[#5A6A85]">Clique para enviar imagem</span>
          <input type="file" accept="image/*" className="hidden" onChange={handle} />
        </label>
      )}
    </div>
  )
}

// ─── Page: NOTÍCIAS ───────────────────────────────────────────────────────────
function PageNoticias({ isAdmin }: { isAdmin: boolean }) {
  const [news, setNews] = useState<NewsItem[]>(INIT_NEWS)
  const [modal, setModal] = useState<{ open: boolean; item: NewsItem | null }>({ open: false, item: null })
  const [form, setForm] = useState<Partial<NewsItem>>({})

  const openNew = () => { setForm({ data_publicacao: "", titulo: "", chave: "", texto: "", imagem_url: undefined }); setModal({ open: true, item: null }) }
  const openEdit = (item: NewsItem) => { setForm({ ...item }); setModal({ open: true, item }) }
  const save = () => {
    if (modal.item) {
      setNews(news.map(n => n.id === modal.item!.id ? { ...n, ...form } as NewsItem : n))
    } else {
      setNews([...news, { id: Date.now(), data_publicacao: form.data_publicacao ?? "", titulo: form.titulo ?? "", chave: form.chave ?? "", texto: form.texto ?? "", imagem_url: form.imagem_url }])
    }
    setModal({ open: false, item: null })
  }
  const del = (id: number) => setNews(news.filter(n => n.id !== id))

  return (
    <div style={{ fontFamily: "'DM Sans', sans-serif" }}>
      <PageHeader label="ABSL" title="Notícias" subtitle="Tudo que acontece no grêmio e na escola, direto da Diretoria de Imprensa e Comunicação." />
      {isAdmin && (
        <div className="mb-5 flex flex-wrap items-start justify-between gap-3">
          <AdminBanner />
          <button onClick={openNew} className="flex items-center gap-2 px-4 py-2 rounded-full bg-[#1A3F8F] text-white text-sm font-medium hover:bg-[#0D1F3C] transition-colors ml-4 whitespace-nowrap">
            <Plus size={14} /> Nova Notícia
          </button>
        </div>
      )}

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
        {news.map((n) => (
          <div key={n.id} className="bg-white rounded-xl border border-[#0D1F3C]/8 overflow-hidden hover:shadow-md transition-shadow">
            <div className="h-40 flex items-center justify-center relative overflow-hidden bg-[#1A3F8F]">
              {n.imagem_url
                ? <img src={n.imagem_url} alt={n.titulo} className="w-full h-full object-cover" />
                : <><GeoBg /><span className="relative z-10 text-white/20 text-5xl font-black" style={{ fontFamily: "'Playfair Display', serif" }}>ABSL</span></>
              }
            </div>
            <div className="p-5">
              <p className="text-[#5A6A85] text-xs font-mono uppercase tracking-wider mb-2">{n.data_publicacao}</p>
              <h3 className="text-[#0D1F3C] font-bold text-sm leading-snug mb-2">{n.titulo}</h3>
              <p className="text-[#5A6A85] text-xs leading-relaxed line-clamp-2">{n.texto}</p>
              {isAdmin && (
                <div className="flex gap-2 mt-3 pt-3 border-t border-[#0D1F3C]/8">
                  <button onClick={() => openEdit(n)} className="px-3 py-1 rounded-lg border border-amber-400 text-amber-700 text-xs hover:bg-amber-50">Editar</button>
                  <button onClick={() => del(n.id)} className="px-3 py-1 rounded-lg border border-red-300 text-red-600 text-xs hover:bg-red-50">Excluir</button>
                </div>
              )}
            </div>
          </div>
        ))}
      </div>

      {modal.open && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
          <div className="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto mx-4">
            <div className="flex items-center justify-between mb-5">
              <h3 className="font-bold text-[#0D1F3C] text-lg">{modal.item ? "Editar Notícia" : "Nova Notícia"}</h3>
              <button onClick={() => setModal({ open: false, item: null })}><X size={20} /></button>
            </div>
            <div className="space-y-3">
              <ImageUploadField value={form.imagem_url} onChange={v => setForm(f => ({ ...f, imagem_url: v }))} />
              <div>
                <label className="text-xs font-medium text-[#5A6A85] mb-1 block">Data de publicação</label>
                <input className="w-full border border-[#0D1F3C]/15 rounded-xl px-3 py-2 text-sm bg-[#EEF3FB]" value={form.data_publicacao ?? ""} onChange={e => setForm({ ...form, data_publicacao: e.target.value })} placeholder="Ex: 29 de julho de 2026" />
              </div>
              <div>
                <label className="text-xs font-medium text-[#5A6A85] mb-1 block">Slug (chave)</label>
                <input className="w-full border border-[#0D1F3C]/15 rounded-xl px-3 py-2 text-sm bg-[#EEF3FB]" value={form.chave ?? ""} onChange={e => setForm({ ...form, chave: e.target.value })} placeholder="Ex: eleicao-2026" />
              </div>
              <div>
                <label className="text-xs font-medium text-[#5A6A85] mb-1 block">Título</label>
                <input className="w-full border border-[#0D1F3C]/15 rounded-xl px-3 py-2 text-sm bg-[#EEF3FB]" value={form.titulo ?? ""} onChange={e => setForm({ ...form, titulo: e.target.value })} placeholder="Título da notícia" />
              </div>
              <div>
                <label className="text-xs font-medium text-[#5A6A85] mb-1 block">Texto / Resumo</label>
                <textarea className="w-full border border-[#0D1F3C]/15 rounded-xl px-3 py-2 text-sm bg-[#EEF3FB] resize-none" rows={3} value={form.texto ?? ""} onChange={e => setForm({ ...form, texto: e.target.value })} placeholder="Descrição breve da notícia" />
              </div>
              <div className="flex gap-3 pt-2">
                <button onClick={() => setModal({ open: false, item: null })} className="flex-1 py-2.5 rounded-xl border border-[#0D1F3C]/15 text-sm text-[#5A6A85] hover:bg-gray-50">Cancelar</button>
                <button onClick={save} className="flex-1 py-2.5 rounded-xl bg-[#1A3F8F] text-white text-sm font-medium hover:bg-[#0D1F3C]">Salvar</button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}

// ─── Page: PROJETOS ───────────────────────────────────────────────────────────
const PROJECT_CATS = ["Presidência", "Secretaria", "Tesouraria", "Esporte e Lazer", "Cultura", "Políticas Educacionais", "Saúde e Meio Ambiente", "Diretoria Social", "Imprensa e Comunicação", "Tecnologia e Inovação"]

function PageProjetos({ isAdmin }: { isAdmin: boolean }) {
  const [projects, setProjects] = useState<ProjectItem[]>(INIT_PROJECTS)
  const [modal, setModal] = useState<{ open: boolean; item: ProjectItem | null }>({ open: false, item: null })
  const [form, setForm] = useState<Partial<ProjectItem>>({})

  const openNew = () => { setForm({ categoria: PROJECT_CATS[0], status: "em_andamento", titulo: "", descricao: "", imagem_url: undefined }); setModal({ open: true, item: null }) }
  const openEdit = (item: ProjectItem) => { setForm({ ...item }); setModal({ open: true, item }) }
  const save = () => {
    if (modal.item) {
      setProjects(projects.map(p => p.id === modal.item!.id ? { ...p, ...form } as ProjectItem : p))
    } else {
      setProjects([...projects, { id: Date.now(), categoria: form.categoria ?? "", status: form.status ?? "em_andamento", titulo: form.titulo ?? "", descricao: form.descricao ?? "", imagem_url: form.imagem_url }])
    }
    setModal({ open: false, item: null })
  }
  const del = (id: number) => setProjects(projects.filter(p => p.id !== id))

  return (
    <div style={{ fontFamily: "'DM Sans', sans-serif" }}>
      <PageHeader label="ABSL" title="Projetos" subtitle="Ideias que saíram do papel — e as que estão em construção agora." />
      {isAdmin && (
        <div className="mb-5 flex flex-wrap items-start justify-between gap-3">
          <AdminBanner />
          <button onClick={openNew} className="flex items-center gap-2 px-4 py-2 rounded-full bg-[#1A3F8F] text-white text-sm font-medium hover:bg-[#0D1F3C] transition-colors ml-4 whitespace-nowrap">
            <Plus size={14} /> Novo Projeto
          </button>
        </div>
      )}

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
        {projects.map((p) => (
          <div key={p.id} className="bg-white rounded-xl border border-[#0D1F3C]/8 overflow-hidden hover:shadow-md transition-shadow">
            {p.imagem_url && (
              <div className="h-36 overflow-hidden">
                <img src={p.imagem_url} alt={p.titulo} className="w-full h-full object-cover" />
              </div>
            )}
            <div className="p-5">
              <div className="flex items-start justify-between mb-3">
                <span className="text-[#1A3F8F] text-[10px] font-mono font-medium uppercase tracking-wider">{p.categoria}</span>
                <span className={`text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-full ${p.status === "concluido" ? "bg-green-100 text-green-700" : "bg-blue-100 text-[#1A3F8F]"}`}>
                  {p.status === "concluido" ? "CONCLUÍDO" : "EM ANDAMENTO"}
                </span>
              </div>
              <h3 className="text-[#0D1F3C] font-bold text-sm mb-2">{p.titulo}</h3>
              <p className="text-[#5A6A85] text-xs leading-relaxed line-clamp-2">{p.descricao}</p>
              {isAdmin && (
                <div className="flex gap-2 mt-3 pt-3 border-t border-[#0D1F3C]/8">
                  <button onClick={() => openEdit(p)} className="px-3 py-1 rounded-lg border border-amber-400 text-amber-700 text-xs hover:bg-amber-50">Editar</button>
                  <button onClick={() => del(p.id)} className="px-3 py-1 rounded-lg border border-red-300 text-red-600 text-xs hover:bg-red-50">Excluir</button>
                </div>
              )}
            </div>
          </div>
        ))}
      </div>

      {modal.open && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
          <div className="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl mx-4">
            <div className="flex items-center justify-between mb-5">
              <h3 className="font-bold text-[#0D1F3C] text-lg">{modal.item ? "Editar Projeto" : "Novo Projeto"}</h3>
              <button onClick={() => setModal({ open: false, item: null })}><X size={20} /></button>
            </div>
            <div className="space-y-3">
              <ImageUploadField value={form.imagem_url} onChange={v => setForm(f => ({ ...f, imagem_url: v }))} label="Imagem do projeto (opcional)" />
              <div>
                <label className="text-xs font-medium text-[#5A6A85] mb-1 block">Título</label>
                <input className="w-full border border-[#0D1F3C]/15 rounded-xl px-3 py-2 text-sm bg-[#EEF3FB]" value={form.titulo ?? ""} onChange={e => setForm({ ...form, titulo: e.target.value })} />
              </div>
              <div>
                <label className="text-xs font-medium text-[#5A6A85] mb-1 block">Diretoria / Categoria</label>
                <select className="w-full border border-[#0D1F3C]/15 rounded-xl px-3 py-2 text-sm bg-[#EEF3FB]" value={form.categoria ?? ""} onChange={e => setForm({ ...form, categoria: e.target.value })}>
                  {PROJECT_CATS.map(c => <option key={c}>{c}</option>)}
                </select>
              </div>
              <div>
                <label className="text-xs font-medium text-[#5A6A85] mb-1 block">Status</label>
                <select className="w-full border border-[#0D1F3C]/15 rounded-xl px-3 py-2 text-sm bg-[#EEF3FB]" value={form.status ?? "em_andamento"} onChange={e => setForm({ ...form, status: e.target.value as "concluido" | "em_andamento" })}>
                  <option value="em_andamento">Em andamento</option>
                  <option value="concluido">Concluído</option>
                </select>
              </div>
              <div>
                <label className="text-xs font-medium text-[#5A6A85] mb-1 block">Descrição</label>
                <textarea className="w-full border border-[#0D1F3C]/15 rounded-xl px-3 py-2 text-sm bg-[#EEF3FB] resize-none" rows={3} value={form.descricao ?? ""} onChange={e => setForm({ ...form, descricao: e.target.value })} />
              </div>
              <div className="flex gap-3 pt-2">
                <button onClick={() => setModal({ open: false, item: null })} className="flex-1 py-2.5 rounded-xl border border-[#0D1F3C]/15 text-sm text-[#5A6A85]">Cancelar</button>
                <button onClick={save} className="flex-1 py-2.5 rounded-xl bg-[#1A3F8F] text-white text-sm font-medium">Salvar</button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}

// ─── Page: GABARITO ───────────────────────────────────────────────────────────
function PageGabarito({ isAdmin }: { isAdmin: boolean }) {
  const matGroups = ["2A até 2D", "2E até 2H", "3A até 3H", "3I até 3O"]
  const vesGroups = ["1A até 1H", "1I até 1P"]

  const DocRow = ({ name, type }: { name: string; type: "gabarito" | "prova" }) => (
    <div className="flex items-center gap-3 py-2.5 border-b border-[#0D1F3C]/6 last:border-0">
      <div className={`w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 ${type === "gabarito" ? "bg-[#EEF3FB]" : "bg-amber-50"}`}>
        <FileText size={15} className={type === "gabarito" ? "text-[#1A3F8F]" : "text-amber-600"} />
      </div>
      <div className="flex-1 min-w-0">
        <p className="text-[#0D1F3C] text-xs font-medium truncate">{name}</p>
        <p className="text-[#5A6A85] text-[10px]">{type === "gabarito" ? "Gabarito" : "Prova para consulta"}</p>
      </div>
      <button className="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-[#EEF3FB] text-[#1A3F8F] text-xs hover:bg-[#D6E4FF] transition-colors flex-shrink-0">
        <Download size={12} /> PDF
      </button>
      {isAdmin && (
        <button className="text-[10px] px-2 py-1.5 rounded-lg border border-amber-400 text-amber-700 hover:bg-amber-50 flex-shrink-0">
          Substituir
        </button>
      )}
    </div>
  )

  const GabCard = ({ label }: { label: string }) => (
    <div className="bg-white rounded-xl border border-[#0D1F3C]/8 p-4 hover:shadow-md transition-shadow">
      <p className="text-xs font-mono font-bold text-[#1A3F8F] uppercase tracking-wider mb-3">Turmas {label}</p>
      <DocRow name={`Gabarito Provão – ${label}.pdf`} type="gabarito" />
      <DocRow name={`Prova Provão – ${label}.pdf`} type="prova" />
    </div>
  )

  return (
    <div style={{ fontFamily: "'DM Sans', sans-serif" }}>
      <PageHeader label="ABSL" title="Gabarito" subtitle="Gabaritos divididos por turno e por grupo de turmas." />
      {isAdmin && <AdminBanner />}

      <section className="mb-8">
        <div className="flex items-center gap-3 mb-4">
          <span className="text-xs font-mono font-bold text-[#1A3F8F] uppercase tracking-widest">Matutino</span>
          <div className="h-px flex-1 bg-[#0D1F3C]/10" />
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {matGroups.map(g => <GabCard key={g} label={g} />)}
        </div>
      </section>

      <section>
        <div className="flex items-center gap-3 mb-4">
          <span className="text-xs font-mono font-bold text-[#1A3F8F] uppercase tracking-widest">Vespertino</span>
          <div className="h-px flex-1 bg-[#0D1F3C]/10" />
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {vesGroups.map(g => <GabCard key={g} label={g} />)}
        </div>
      </section>
    </div>
  )
}

// ─── Page: TRANSPARÊNCIA ──────────────────────────────────────────────────────
function PageTransparencia({ isAdmin }: { isAdmin: boolean }) {
  const [tab, setTab] = useState<"atas" | "contas">("atas")
  const [atas, setAtas] = useState<AtaItem[]>(INIT_ATAS)
  const [contas, setContas] = useState<ContasItem[]>(INIT_CONTAS)
  const [modal, setModal] = useState(false)
  const [form, setForm] = useState({ data_referencia: "", descricao: "", periodo: "" })

  const saveAta = () => {
    setAtas([...atas, { id: Date.now(), data_referencia: form.data_referencia, descricao: form.descricao }])
    setModal(false)
    setForm({ data_referencia: "", descricao: "", periodo: "" })
  }

  const saveConta = () => {
    setContas([...contas, { id: Date.now(), periodo: form.periodo, descricao: form.descricao }])
    setModal(false)
    setForm({ data_referencia: "", descricao: "", periodo: "" })
  }

  return (
    <div style={{ fontFamily: "'DM Sans', sans-serif" }}>
      <PageHeader label="ABSL" title="Portal de Transparência" subtitle="Atas de reuniões e prestações de contas do Grêmio Athos Bulcão." />
      {isAdmin && (
        <div className="mb-5 flex flex-wrap items-start justify-between gap-3">
          <AdminBanner />
          <button onClick={() => setModal(true)} className="flex items-center gap-2 px-4 py-2 rounded-full bg-[#1A3F8F] text-white text-sm font-medium hover:bg-[#0D1F3C] transition-colors ml-4 whitespace-nowrap">
            <Plus size={14} /> {tab === "atas" ? "Nova Ata" : "Nova Prestação de Contas"}
          </button>
        </div>
      )}

      {/* Tabs */}
      <div className="flex gap-1 p-1 bg-white border border-[#0D1F3C]/10 rounded-xl mb-6 w-fit">
        {(["atas", "contas"] as const).map((t) => (
          <button
            key={t}
            onClick={() => setTab(t)}
            className={`px-5 py-2 rounded-lg text-sm font-medium transition-all ${tab === t ? "bg-[#1A3F8F] text-white" : "text-[#5A6A85] hover:text-[#0D1F3C]"}`}
          >
            {t === "atas" ? "Atas das Reuniões" : "Prestação de Contas"}
          </button>
        ))}
      </div>

      <div className="space-y-2">
        {tab === "atas" ? atas.map((a) => (
          <div key={a.id} className="bg-white rounded-xl border border-[#0D1F3C]/8 px-5 py-4 flex items-center gap-4 hover:shadow-sm transition-shadow">
            <div className="w-10 h-10 rounded-xl bg-[#EEF3FB] flex items-center justify-center flex-shrink-0">
              <FileText size={18} className="text-[#1A3F8F]" />
            </div>
            <div className="flex-1 min-w-0">
              <p className="text-[#0D1F3C] font-semibold text-sm">{a.data_referencia}</p>
              <p className="text-[#5A6A85] text-xs mt-0.5 truncate">{a.descricao}</p>
            </div>
            <div className="flex items-center gap-2 flex-shrink-0">
              <button className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#EEF3FB] text-[#1A3F8F] text-xs font-medium hover:bg-[#D6E4FF] transition-colors">
                <Download size={13} /> PDF
              </button>
              {isAdmin && (
                <button onClick={() => setAtas(atas.filter(i => i.id !== a.id))} className="p-1.5 rounded-lg text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                  <X size={15} />
                </button>
              )}
            </div>
          </div>
        )) : contas.map((c) => (
          <div key={c.id} className="bg-white rounded-xl border border-[#0D1F3C]/8 px-5 py-4 flex items-center gap-4 hover:shadow-sm transition-shadow">
            <div className="w-10 h-10 rounded-xl bg-[#EEF3FB] flex items-center justify-center flex-shrink-0">
              <FileText size={18} className="text-[#1A3F8F]" />
            </div>
            <div className="flex-1 min-w-0">
              <p className="text-[#0D1F3C] font-semibold text-sm">{c.periodo}</p>
              <p className="text-[#5A6A85] text-xs mt-0.5 truncate">{c.descricao}</p>
            </div>
            <div className="flex items-center gap-2 flex-shrink-0">
              <button className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#EEF3FB] text-[#1A3F8F] text-xs font-medium hover:bg-[#D6E4FF] transition-colors">
                <Download size={13} /> PDF
              </button>
              {isAdmin && (
                <button onClick={() => setContas(contas.filter(i => i.id !== c.id))} className="p-1.5 rounded-lg text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                  <X size={15} />
                </button>
              )}
            </div>
          </div>
        ))}
      </div>

      {modal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
          <div className="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl mx-4">
            <div className="flex items-center justify-between mb-5">
              <h3 className="font-bold text-[#0D1F3C] text-lg">{tab === "atas" ? "Nova Ata" : "Nova Prestação de Contas"}</h3>
              <button onClick={() => setModal(false)}><X size={20} /></button>
            </div>
            <div className="space-y-3">
              {tab === "atas" ? (
                <div>
                  <label className="text-xs font-medium text-[#5A6A85] mb-1 block">Data da reunião</label>
                  <input className="w-full border border-[#0D1F3C]/15 rounded-xl px-3 py-2 text-sm bg-[#EEF3FB]" value={form.data_referencia} onChange={e => setForm({ ...form, data_referencia: e.target.value })} placeholder="Ex: 29 de julho de 2026" />
                </div>
              ) : (
                <div>
                  <label className="text-xs font-medium text-[#5A6A85] mb-1 block">Período de referência</label>
                  <input className="w-full border border-[#0D1F3C]/15 rounded-xl px-3 py-2 text-sm bg-[#EEF3FB]" value={form.periodo} onChange={e => setForm({ ...form, periodo: e.target.value })} placeholder="Ex: 2º Trimestre 2026" />
                </div>
              )}
              <div>
                <label className="text-xs font-medium text-[#5A6A85] mb-1 block">Descrição / Pauta</label>
                <textarea className="w-full border border-[#0D1F3C]/15 rounded-xl px-3 py-2 text-sm bg-[#EEF3FB] resize-none" rows={3} value={form.descricao} onChange={e => setForm({ ...form, descricao: e.target.value })} />
              </div>
              <div className="border border-dashed border-[#0D1F3C]/20 rounded-xl p-4 text-center text-sm text-[#5A6A85]">
                Clique para fazer upload do PDF
              </div>
              <div className="flex gap-3 pt-2">
                <button onClick={() => setModal(false)} className="flex-1 py-2.5 rounded-xl border border-[#0D1F3C]/15 text-sm text-[#5A6A85]">Cancelar</button>
                <button onClick={tab === "atas" ? saveAta : saveConta} className="flex-1 py-2.5 rounded-xl bg-[#1A3F8F] text-white text-sm font-medium">Salvar</button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}

// ─── Page: OS OUVINTES ───────────────────────────────────────────────────────
interface OuvidoriaMsg {
  id: number
  nome?: string
  email?: string
  anonimo: boolean
  texto: string
  status: "pendente" | "respondida" | "arquivada"
  resposta?: string
  data_envio: string
}

function PageOuvidoria({ isAdmin }: { isAdmin: boolean }) {
  const [modo, setModo] = useState<"identificada" | "anonima">("identificada")
  const [form, setForm] = useState({ nome: "", email: "", texto: "" })
  const [msgs, setMsgs] = useState<OuvidoriaMsg[]>([
    { id: 1, anonimo: false, nome: "Ana Souza", email: "ana@email.com", texto: "Gostaria de sugerir que a biblioteca fique aberta no contraturno para estudos.", status: "pendente", data_envio: "12 jun 2026" },
    { id: 2, anonimo: true, texto: "O refeitório precisa de mais opções vegetarianas no cardápio.", status: "pendente", data_envio: "18 jun 2026" },
    { id: 3, anonimo: false, nome: "Carlos Lima", email: "carlos@email.com", texto: "Seria ótimo ter mais tomadas na sala de informática para carregar os notebooks.", status: "respondida", resposta: "Estamos levando essa demanda à direção da escola.", data_envio: "25 jun 2026" },
  ])
  const [sent, setSent] = useState(false)
  const [replyingTo, setReplyingTo] = useState<number | null>(null)
  const [replyText, setReplyText] = useState("")

  const saveReply = (id: number) => {
    setMsgs(msgs.map(m => m.id === id ? { ...m, resposta: replyText } : m))
    setReplyingTo(null)
    setReplyText("")
  }

  const handleSubmit = () => {
    if (!form.texto.trim()) return
    const nova: OuvidoriaMsg = {
      id: Date.now(),
      anonimo: modo === "anonima",
      nome: modo === "identificada" ? form.nome : undefined,
      email: modo === "identificada" ? form.email : undefined,
      texto: form.texto,
      status: "pendente",
      data_envio: new Date().toLocaleDateString("pt-BR", { day: "2-digit", month: "short", year: "numeric" }),
    }
    setMsgs([...msgs, nova])
    setForm({ nome: "", email: "", texto: "" })
    setSent(true)
    setTimeout(() => setSent(false), 3500)
  }

  return (
    <div style={{ fontFamily: "'DM Sans', sans-serif" }}>
      <PageHeader
        label="ABSL"
        title="Os Ouvintes"
        subtitle="Ouvidoria do Grêmio Athos Bulcão — envie sugestões, críticas ou opiniões. Sua voz importa."
      />

      {/* Form card */}
      <div className="bg-white rounded-2xl border border-[#0D1F3C]/8 p-6 mb-8 shadow-sm">
        <h2 className="text-[#0D1F3C] font-bold text-base mb-4">Enviar mensagem</h2>

        {/* Modo toggle */}
        <div className="flex bg-[#EEF3FB] rounded-xl p-1 mb-5 w-full sm:w-fit gap-1">
          <button
            onClick={() => setModo("identificada")}
            className={`flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all flex-1 sm:flex-none justify-center ${
              modo === "identificada" ? "bg-[#1A3F8F] text-white shadow-sm" : "text-[#5A6A85] hover:text-[#0D1F3C]"
            }`}
          >
            <User size={14} /> Com identificação
          </button>
          <button
            onClick={() => setModo("anonima")}
            className={`flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all flex-1 sm:flex-none justify-center ${
              modo === "anonima" ? "bg-[#1A3F8F] text-white shadow-sm" : "text-[#5A6A85] hover:text-[#0D1F3C]"
            }`}
          >
            <EyeOff size={14} /> Anônima
          </button>
        </div>

        {/* Info banner */}
        <div className={`flex items-start gap-3 px-4 py-3 rounded-xl mb-4 text-xs ${
          modo === "anonima"
            ? "bg-[#0D1F3C]/5 text-[#5A6A85]"
            : "bg-blue-50 text-[#1A3F8F]"
        }`}>
          {modo === "anonima" ? <EyeOff size={14} className="mt-0.5 flex-shrink-0" /> : <User size={14} className="mt-0.5 flex-shrink-0" />}
          <span>
            {modo === "anonima"
              ? "Sua identidade não será revelada. Nenhum dado pessoal será coletado ou armazenado."
              : "Seus dados serão visíveis apenas para a diretoria do grêmio e tratados com sigilo."}
          </span>
        </div>

        {/* Identified fields */}
        {modo === "identificada" && (
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
            <div>
              <label className="text-xs font-medium text-[#5A6A85] mb-1 block">Nome completo</label>
              <input
                className="w-full border border-[#0D1F3C]/15 rounded-xl px-3 py-2 text-sm bg-[#EEF3FB]"
                placeholder="Seu nome"
                value={form.nome}
                onChange={e => setForm({ ...form, nome: e.target.value })}
              />
            </div>
            <div>
              <label className="text-xs font-medium text-[#5A6A85] mb-1 block">E-mail (opcional)</label>
              <input
                className="w-full border border-[#0D1F3C]/15 rounded-xl px-3 py-2 text-sm bg-[#EEF3FB]"
                placeholder="seu@email.com"
                type="email"
                value={form.email}
                onChange={e => setForm({ ...form, email: e.target.value })}
              />
            </div>
          </div>
        )}

        {/* Message */}
        <div className="mb-4">
          <label className="text-xs font-medium text-[#5A6A85] mb-1 block">Sua mensagem — sugestão, crítica ou opinião</label>
          <textarea
            className="w-full border border-[#0D1F3C]/15 rounded-xl px-3 py-3 text-sm bg-[#EEF3FB] resize-none leading-relaxed"
            rows={4}
            placeholder="Escreva aqui sua sugestão, crítica ou opinião..."
            value={form.texto}
            onChange={e => setForm({ ...form, texto: e.target.value })}
          />
        </div>

        <div className="flex items-center gap-4">
          <button
            onClick={handleSubmit}
            disabled={!form.texto.trim()}
            className="flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#1A3F8F] text-white text-sm font-medium hover:bg-[#0D1F3C] transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
          >
            <Send size={14} /> Enviar mensagem
          </button>
          {sent && (
            <span className="flex items-center gap-1.5 text-green-600 text-sm font-medium">
              <Check size={15} /> Mensagem enviada!
            </span>
          )}
        </div>
      </div>

      {/* Admin: message list */}
      {isAdmin && (
        <div>
          <AdminBanner />
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-[#0D1F3C] font-bold text-base">Mensagens recebidas</h2>
            <span className="text-xs text-[#5A6A85] bg-white border border-[#0D1F3C]/10 px-2 py-1 rounded-full">{msgs.length} mensagens</span>
          </div>
          <div className="space-y-3">
            {msgs.map((m) => (
              <div key={m.id} className="bg-white rounded-xl border border-[#0D1F3C]/8 px-5 py-4">
                <div className="flex items-start justify-between gap-3 mb-2">
                  <div className="flex items-center gap-2">
                    <span className={`inline-flex items-center gap-1 text-[10px] font-medium px-2 py-0.5 rounded-full ${
                      m.anonimo ? "bg-[#0D1F3C]/8 text-[#5A6A85]" : "bg-blue-100 text-[#1A3F8F]"
                    }`}>
                      {m.anonimo ? <><EyeOff size={10} /> Anônima</> : <><User size={10} /> Identificada</>}
                    </span>
                    {m.nome && <span className="text-xs font-medium text-[#0D1F3C]">{m.nome}</span>}
                    {m.email && <span className="text-xs text-[#5A6A85]">· {m.email}</span>}
                    <span className={`text-[10px] font-medium px-2 py-0.5 rounded-full ${
                      m.status === "respondida" ? "bg-green-100 text-green-700" : m.status === "arquivada" ? "bg-gray-100 text-gray-500" : "bg-amber-100 text-amber-700"
                    }`}>{m.status}</span>
                  </div>
                  <div className="flex items-center gap-2 flex-shrink-0">
                    <span className="text-xs text-[#5A6A85] font-mono">{m.data_envio}</span>
                    <button
                      onClick={() => setMsgs(msgs.filter(x => x.id !== m.id))}
                      className="p-1 rounded text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors"
                    >
                      <Trash size={13} />
                    </button>
                  </div>
                </div>
                <p className="text-sm text-[#0D1F3C] leading-relaxed mb-2">{m.texto}</p>

                {/* Resposta existente */}
                {m.resposta && (
                  <div className="mt-2 flex gap-2 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2">
                    <Reply size={13} className="text-[#1A3F8F] flex-shrink-0 mt-0.5" />
                    <div>
                      <p className="text-[10px] font-semibold text-[#1A3F8F] mb-0.5">Resposta do Grêmio</p>
                      <p className="text-xs text-[#0D1F3C]">{m.resposta}</p>
                    </div>
                  </div>
                )}

                {/* Reply form */}
                {replyingTo === m.id ? (
                  <div className="mt-2 flex gap-2">
                    <textarea
                      autoFocus
                      className="flex-1 border border-[#0D1F3C]/15 rounded-lg px-3 py-2 text-xs bg-[#EEF3FB] resize-none"
                      rows={2}
                      placeholder="Escreva uma resposta..."
                      value={replyText}
                      onChange={e => setReplyText(e.target.value)}
                    />
                    <div className="flex flex-col gap-1">
                      <button onClick={() => saveReply(m.id)} className="px-3 py-1.5 rounded-lg bg-[#1A3F8F] text-white text-xs">Enviar</button>
                      <button onClick={() => setReplyingTo(null)} className="px-3 py-1.5 rounded-lg border border-[#0D1F3C]/15 text-xs text-[#5A6A85]">Cancelar</button>
                    </div>
                  </div>
                ) : (
                  <button
                    onClick={() => { setReplyingTo(m.id); setReplyText(m.resposta ?? "") }}
                    className="mt-1 flex items-center gap-1 text-xs text-[#1A3F8F] hover:underline"
                  >
                    <Reply size={12} /> {m.resposta ? "Editar resposta" : "Responder"}
                  </button>
                )}
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  )
}

// ─── Page: MAPA DA ESCOLA ────────────────────────────────────────────────────
const LOCAIS = [
  { id: "entrada", label: "Entrada Principal", cor: "#1A3F8F", desc: "Portão de acesso e recepção" },
  { id: "admin", label: "Administração", cor: "#2563EB", desc: "Direção, secretaria e coordenação" },
  { id: "salas-a", label: "Bloco A — Salas de Aula", cor: "#3B82F6", desc: "Salas de 1º e 2º ano (térreo e 1º andar)" },
  { id: "salas-b", label: "Bloco B — Salas de Aula", cor: "#60A5FA", desc: "Salas de 3º ano (térreo e 1º andar)" },
  { id: "biblioteca", label: "Biblioteca", cor: "#F5C518", desc: "Acervo, leitura e sala de estudo" },
  { id: "laboratorio", label: "Laboratório de Ciências", cor: "#F59E0B", desc: "Lab. de Física, Química e Biologia" },
  { id: "informatica", label: "Lab. de Informática", cor: "#FBBF24", desc: "40 computadores, impressão e Wi-Fi" },
  { id: "refeitorio", label: "Refeitório", cor: "#10B981", desc: "Refeições e cantina escolar" },
  { id: "patio", label: "Pátio Central", cor: "#6EE7B7", desc: "Convivência, eventos e intervalo" },
  { id: "quadra", label: "Quadra Poliesportiva", cor: "#EF4444", desc: "Futebol, vôlei, basquete e handebol" },
  { id: "banheiros", label: "Banheiros", cor: "#94A3B8", desc: "Feminino, masculino e acessível" },
  { id: "gremio", label: "Sala do Grêmio (ABSL)", cor: "#0D1F3C", desc: "Sede do Grêmio Estudantil Athos Bulcão" },
]

function MapaEscola() {
  const [hovered, setHovered] = useState<string | null>(null)

  const getOpacity = (id: string) => hovered === null ? 1 : hovered === id ? 1 : 0.45

  return (
    <svg viewBox="0 0 800 560" className="w-full rounded-2xl border border-[#0D1F3C]/10" style={{ background: "#EEF3FB", maxHeight: 520 }}>
      {/* Ground/campus outline */}
      <rect x="20" y="20" width="760" height="520" rx="12" fill="#DDE6F5" stroke="#B8CCE8" strokeWidth="1.5" />

      {/* Road / entrance path */}
      <rect x="355" y="490" width="90" height="50" rx="4" fill="#C5D5E8" />
      <rect x="355" y="20" width="90" height="30" rx="0" fill="#C5D5E8" />

      {/* Pátio central */}
      <rect
        x="250" y="200" width="300" height="200"
        rx="8" fill={hovered === "patio" ? "#6EE7B7" : "#A7F3D0"}
        stroke="#6EE7B7" strokeWidth="1.5"
        style={{ cursor: "pointer", opacity: getOpacity("patio"), transition: "all 0.15s" }}
        onMouseEnter={() => setHovered("patio")} onMouseLeave={() => setHovered(null)}
      />
      <text x="400" y="300" textAnchor="middle" fill="#065F46" fontSize="11" fontWeight="600" style={{ pointerEvents: "none" }}>PÁTIO CENTRAL</text>

      {/* Entrada principal */}
      <rect
        x="355" y="480" width="90" height="42"
        rx="6" fill={hovered === "entrada" ? "#1A3F8F" : "#2563EB"}
        stroke="#1A3F8F" strokeWidth="1.5"
        style={{ cursor: "pointer", opacity: getOpacity("entrada"), transition: "all 0.15s" }}
        onMouseEnter={() => setHovered("entrada")} onMouseLeave={() => setHovered(null)}
      />
      <text x="400" y="499" textAnchor="middle" fill="white" fontSize="8.5" fontWeight="700" style={{ pointerEvents: "none" }}>ENTRADA</text>
      <text x="400" y="511" textAnchor="middle" fill="white" fontSize="8.5" fontWeight="700" style={{ pointerEvents: "none" }}>PRINCIPAL</text>

      {/* Administração */}
      <rect
        x="355" y="30" width="90" height="60"
        rx="6" fill={hovered === "admin" ? "#1A3F8F" : "#2563EB"}
        stroke="#1A3F8F" strokeWidth="1.5"
        style={{ cursor: "pointer", opacity: getOpacity("admin"), transition: "all 0.15s" }}
        onMouseEnter={() => setHovered("admin")} onMouseLeave={() => setHovered(null)}
      />
      <text x="400" y="56" textAnchor="middle" fill="white" fontSize="8.5" fontWeight="700" style={{ pointerEvents: "none" }}>ADMINIS-</text>
      <text x="400" y="68" textAnchor="middle" fill="white" fontSize="8.5" fontWeight="700" style={{ pointerEvents: "none" }}>TRAÇÃO</text>

      {/* Bloco A — esquerda */}
      <rect
        x="30" y="100" width="200" height="330"
        rx="8" fill={hovered === "salas-a" ? "#3B82F6" : "#BFDBFE"}
        stroke="#3B82F6" strokeWidth="1.5"
        style={{ cursor: "pointer", opacity: getOpacity("salas-a"), transition: "all 0.15s" }}
        onMouseEnter={() => setHovered("salas-a")} onMouseLeave={() => setHovered(null)}
      />
      {/* Sala lines A */}
      {[0,1,2,3,4].map(i => (
        <line key={i} x1="30" y1={100 + (i+1)*55} x2="230" y2={100 + (i+1)*55} stroke="#93C5FD" strokeWidth="0.8" style={{ pointerEvents: "none" }} />
      ))}
      <text x="130" y="265" textAnchor="middle" fill="#1E40AF" fontSize="11" fontWeight="700" style={{ pointerEvents: "none" }}>BLOCO A</text>
      <text x="130" y="279" textAnchor="middle" fill="#1E40AF" fontSize="9" style={{ pointerEvents: "none" }}>1º e 2º ano</text>

      {/* Bloco B — direita */}
      <rect
        x="570" y="100" width="200" height="330"
        rx="8" fill={hovered === "salas-b" ? "#60A5FA" : "#DBEAFE"}
        stroke="#60A5FA" strokeWidth="1.5"
        style={{ cursor: "pointer", opacity: getOpacity("salas-b"), transition: "all 0.15s" }}
        onMouseEnter={() => setHovered("salas-b")} onMouseLeave={() => setHovered(null)}
      />
      {[0,1,2,3,4].map(i => (
        <line key={i} x1="570" y1={100 + (i+1)*55} x2="770" y2={100 + (i+1)*55} stroke="#BFDBFE" strokeWidth="0.8" style={{ pointerEvents: "none" }} />
      ))}
      <text x="670" y="265" textAnchor="middle" fill="#1E40AF" fontSize="11" fontWeight="700" style={{ pointerEvents: "none" }}>BLOCO B</text>
      <text x="670" y="279" textAnchor="middle" fill="#1E40AF" fontSize="9" style={{ pointerEvents: "none" }}>3º ano</text>

      {/* Biblioteca */}
      <rect
        x="30" y="440" width="120" height="80"
        rx="6" fill={hovered === "biblioteca" ? "#F5C518" : "#FEF08A"}
        stroke="#F5C518" strokeWidth="1.5"
        style={{ cursor: "pointer", opacity: getOpacity("biblioteca"), transition: "all 0.15s" }}
        onMouseEnter={() => setHovered("biblioteca")} onMouseLeave={() => setHovered(null)}
      />
      <text x="90" y="477" textAnchor="middle" fill="#78350F" fontSize="10" fontWeight="700" style={{ pointerEvents: "none" }}>BIBLIO-</text>
      <text x="90" y="490" textAnchor="middle" fill="#78350F" fontSize="10" fontWeight="700" style={{ pointerEvents: "none" }}>TECA</text>

      {/* Refeitório */}
      <rect
        x="160" y="440" width="80" height="80"
        rx="6" fill={hovered === "refeitorio" ? "#10B981" : "#A7F3D0"}
        stroke="#10B981" strokeWidth="1.5"
        style={{ cursor: "pointer", opacity: getOpacity("refeitorio"), transition: "all 0.15s" }}
        onMouseEnter={() => setHovered("refeitorio")} onMouseLeave={() => setHovered(null)}
      />
      <text x="200" y="477" textAnchor="middle" fill="#065F46" fontSize="9" fontWeight="700" style={{ pointerEvents: "none" }}>REFEI-</text>
      <text x="200" y="490" textAnchor="middle" fill="#065F46" fontSize="9" fontWeight="700" style={{ pointerEvents: "none" }}>TÓRIO</text>

      {/* Lab Ciências */}
      <rect
        x="560" y="440" width="110" height="80"
        rx="6" fill={hovered === "laboratorio" ? "#F59E0B" : "#FDE68A"}
        stroke="#F59E0B" strokeWidth="1.5"
        style={{ cursor: "pointer", opacity: getOpacity("laboratorio"), transition: "all 0.15s" }}
        onMouseEnter={() => setHovered("laboratorio")} onMouseLeave={() => setHovered(null)}
      />
      <text x="615" y="474" textAnchor="middle" fill="#78350F" fontSize="9" fontWeight="700" style={{ pointerEvents: "none" }}>LAB.</text>
      <text x="615" y="486" textAnchor="middle" fill="#78350F" fontSize="9" fontWeight="700" style={{ pointerEvents: "none" }}>CIÊNCIAS</text>

      {/* Informática */}
      <rect
        x="680" y="440" width="90" height="80"
        rx="6" fill={hovered === "informatica" ? "#FBBF24" : "#FEF3C7"}
        stroke="#FBBF24" strokeWidth="1.5"
        style={{ cursor: "pointer", opacity: getOpacity("informatica"), transition: "all 0.15s" }}
        onMouseEnter={() => setHovered("informatica")} onMouseLeave={() => setHovered(null)}
      />
      <text x="725" y="474" textAnchor="middle" fill="#78350F" fontSize="9" fontWeight="700" style={{ pointerEvents: "none" }}>LAB.</text>
      <text x="725" y="486" textAnchor="middle" fill="#78350F" fontSize="9" fontWeight="700" style={{ pointerEvents: "none" }}>INFO.</text>

      {/* Quadra */}
      <rect
        x="250" y="30" width="100" height="155"
        rx="6" fill={hovered === "quadra" ? "#EF4444" : "#FECACA"}
        stroke="#EF4444" strokeWidth="1.5"
        style={{ cursor: "pointer", opacity: getOpacity("quadra"), transition: "all 0.15s" }}
        onMouseEnter={() => setHovered("quadra")} onMouseLeave={() => setHovered(null)}
      />
      <ellipse cx="300" cy="107" rx="32" ry="50" fill="none" stroke="#FCA5A5" strokeWidth="0.8" style={{ pointerEvents: "none" }} />
      <text x="300" y="104" textAnchor="middle" fill="#7F1D1D" fontSize="9" fontWeight="700" style={{ pointerEvents: "none" }}>QUADRA</text>
      <text x="300" y="116" textAnchor="middle" fill="#7F1D1D" fontSize="8" style={{ pointerEvents: "none" }}>POLIES.</text>

      {/* Sala do Grêmio */}
      <rect
        x="450" y="30" width="110" height="60"
        rx="6" fill={hovered === "gremio" ? "#0D1F3C" : "#1A3F8F"}
        stroke="#0D1F3C" strokeWidth="1.5"
        style={{ cursor: "pointer", opacity: getOpacity("gremio"), transition: "all 0.15s" }}
        onMouseEnter={() => setHovered("gremio")} onMouseLeave={() => setHovered(null)}
      />
      <text x="505" y="54" textAnchor="middle" fill="white" fontSize="9" fontWeight="700" style={{ pointerEvents: "none" }}>SALA DO</text>
      <text x="505" y="66" textAnchor="middle" fill="#F5C518" fontSize="9" fontWeight="700" style={{ pointerEvents: "none" }}>GRÊMIO (ABSL)</text>

      {/* Banheiros */}
      <rect
        x="450" y="100" width="110" height="90"
        rx="6" fill={hovered === "banheiros" ? "#94A3B8" : "#E2E8F0"}
        stroke="#94A3B8" strokeWidth="1.5"
        style={{ cursor: "pointer", opacity: getOpacity("banheiros"), transition: "all 0.15s" }}
        onMouseEnter={() => setHovered("banheiros")} onMouseLeave={() => setHovered(null)}
      />
      <line x1="505" y1="100" x2="505" y2="190" stroke="#CBD5E1" strokeWidth="1" style={{ pointerEvents: "none" }} />
      <text x="480" y="145" textAnchor="middle" fill="#475569" fontSize="9" fontWeight="600" style={{ pointerEvents: "none" }}>WC ♀</text>
      <text x="530" y="145" textAnchor="middle" fill="#475569" fontSize="9" fontWeight="600" style={{ pointerEvents: "none" }}>WC ♂</text>

      {/* Corridors */}
      <rect x="230" y="100" width="20" height="340" fill="#C8D8EE" opacity="0.7" />
      <rect x="550" y="100" width="20" height="340" fill="#C8D8EE" opacity="0.7" />
      <rect x="230" y="195" width="320" height="15" fill="#C8D8EE" opacity="0.7" />
      <rect x="230" y="390" width="320" height="15" fill="#C8D8EE" opacity="0.7" />

      {/* North arrow */}
      <g transform="translate(748, 48)">
        <circle cx="0" cy="0" r="14" fill="white" stroke="#B8CCE8" strokeWidth="1" />
        <text x="0" y="-3" textAnchor="middle" fill="#0D1F3C" fontSize="9" fontWeight="800">N</text>
        <path d="M0 3 L-3 9 L0 7 L3 9 Z" fill="#1A3F8F" />
      </g>

      {/* Hover tooltip */}
      {hovered && (() => {
        const local = LOCAIS.find(l => l.id === hovered)
        if (!local) return null
        return (
          <g>
            <rect x="20" y="530" width="760" height="22" rx="4" fill={local.cor} opacity="0.9" />
            <text x="400" y="545" textAnchor="middle" fill="white" fontSize="10" fontWeight="600">{local.label} — {local.desc}</text>
          </g>
        )
      })()}
    </svg>
  )
}

function PageMapa() {
  const [selectedLocal, setSelectedLocal] = useState<string | null>(null)
  const selected = LOCAIS.find(l => l.id === selectedLocal)

  return (
    <div style={{ fontFamily: "'DM Sans', sans-serif" }}>
      <PageHeader
        label="ABSL"
        title="Mapa da Escola"
        subtitle="Centro de Ensino Médio Athos Bulcão — Brasília, DF. Passe o mouse sobre os ambientes para identificá-los."
      />

      {/* School info cards */}
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        {[
          { label: "Turmas", value: "~60" },
          { label: "Alunos", value: "~1.800" },
          { label: "Professores", value: "~90" },
          { label: "Turnos", value: "2" },
        ].map(({ label, value }) => (
          <div key={label} className="bg-white rounded-xl border border-[#0D1F3C]/8 px-4 py-3 text-center">
            <div className="text-2xl font-black text-[#1A3F8F]" style={{ fontFamily: "'Playfair Display', serif" }}>{value}</div>
            <div className="text-xs text-[#5A6A85] mt-0.5">{label}</div>
          </div>
        ))}
      </div>

      {/* Map */}
      <div className="mb-6">
        <MapaEscola />
      </div>

      {/* Legend */}
      <div className="bg-white rounded-2xl border border-[#0D1F3C]/8 p-5">
        <h3 className="text-sm font-bold text-[#0D1F3C] mb-3">Legenda</h3>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
          {LOCAIS.map((l) => (
            <button
              key={l.id}
              onClick={() => setSelectedLocal(selectedLocal === l.id ? null : l.id)}
              className={`flex items-center gap-3 px-3 py-2 rounded-lg text-left transition-all ${
                selectedLocal === l.id ? "bg-[#EEF3FB] ring-1 ring-[#1A3F8F]/30" : "hover:bg-[#EEF3FB]/60"
              }`}
            >
              <span className="w-3 h-3 rounded-sm flex-shrink-0" style={{ background: l.cor }} />
              <div>
                <span className="text-xs font-medium text-[#0D1F3C]">{l.label}</span>
                {selectedLocal === l.id && (
                  <p className="text-[10px] text-[#5A6A85] mt-0.5">{l.desc}</p>
                )}
              </div>
            </button>
          ))}
        </div>
      </div>
    </div>
  )
}

// ─── Page: CARDÁPIO SEMANAL ──────────────────────────────────────────────────
const DIAS_SEMANA = ["Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira"]

const CARDAPIO_INIT: Record<string, string> = {
  "Segunda-feira": "Galinhada",
  "Terça-feira":   "Strogonoff de frango",
  "Quarta-feira":  "Macarrão ao sugo com almôndegas",
  "Quinta-feira":  "Peixe assado com limão e alho",
  "Sexta-feira":   "Frango grelhado ao molho de ervas",
}

function PageCardapio({ isAdmin }: { isAdmin: boolean }) {
  const [cardapio, setCardapio] = useState<Record<string, string>>(CARDAPIO_INIT)
  const [semana, setSemana] = useState("28 jul – 01 ago 2026")
  const [editingSemana, setEditingSemana] = useState(false)
  const [editingDia, setEditingDia] = useState<string | null>(null)
  const [editVal, setEditVal] = useState("")

  const startEdit = (dia: string) => { setEditingDia(dia); setEditVal(cardapio[dia] ?? "") }
  const saveEdit = () => {
    if (!editingDia) return
    setCardapio(prev => ({ ...prev, [editingDia]: editVal }))
    setEditingDia(null)
  }

  return (
    <div style={{ fontFamily: "'DM Sans', sans-serif" }}>
      <PageHeader label="ABSL" title="Cardápio Semanal" subtitle="Lanche servido na cantina escolar." />
      {isAdmin && <AdminBanner />}

      {/* Semana */}
      <div className="flex items-center gap-3 mb-5">
        <div className="w-1 h-5 rounded bg-[#F5C518]" />
        {isAdmin && editingSemana ? (
          <input
            autoFocus
            className="border border-[#0D1F3C]/15 rounded-lg px-3 py-1 text-sm bg-[#EEF3FB]"
            value={semana}
            onChange={e => setSemana(e.target.value)}
            onBlur={() => setEditingSemana(false)}
            onKeyDown={e => e.key === "Enter" && setEditingSemana(false)}
          />
        ) : (
          <button
            onClick={() => isAdmin && setEditingSemana(true)}
            className={`text-sm font-semibold text-[#0D1F3C] flex items-center gap-1.5 ${isAdmin ? "hover:text-[#1A3F8F]" : "cursor-default"}`}
          >
            Semana: {semana}
            {isAdmin && <Edit2 size={11} className="text-[#5A6A85]" />}
          </button>
        )}
      </div>

      {/* Table */}
      <div className="bg-white rounded-2xl border border-[#0D1F3C]/10 overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr style={{ background: "#0D1F3C" }}>
              <th className="text-left px-6 py-3 text-white/70 text-xs font-semibold uppercase tracking-wide w-48">Dia</th>
              <th className="text-left px-6 py-3 text-white text-xs font-bold uppercase tracking-wide">🥪 Lanche</th>
            </tr>
          </thead>
          <tbody>
            {DIAS_SEMANA.map((dia, i) => {
              const isEditing = isAdmin && editingDia === dia
              return (
                <tr key={dia} className={`border-b border-[#0D1F3C]/6 ${i % 2 === 0 ? "bg-white" : "bg-[#EEF3FB]/40"} group`}>
                  <td className="px-6 py-4 font-semibold text-sm text-[#0D1F3C] border-r border-[#0D1F3C]/8 whitespace-nowrap">
                    {dia}
                  </td>
                  <td className="px-6 py-4">
                    {isEditing ? (
                      <div className="flex items-center gap-2">
                        <input
                          autoFocus
                          value={editVal}
                          onChange={e => setEditVal(e.target.value)}
                          onKeyDown={e => e.key === "Enter" && saveEdit()}
                          className="flex-1 text-sm border border-[#1A3F8F] rounded-lg px-3 py-1.5 bg-white"
                        />
                        <button onClick={saveEdit} className="text-green-600 hover:text-green-700">
                          <Check size={16} />
                        </button>
                        <button onClick={() => setEditingDia(null)} className="text-[#5A6A85] hover:text-[#0D1F3C]">
                          <X size={16} />
                        </button>
                      </div>
                    ) : (
                      <div className="flex items-center gap-2">
                        <span className="text-sm text-[#0D1F3C]">{cardapio[dia] || "—"}</span>
                        {isAdmin && (
                          <button
                            onClick={() => startEdit(dia)}
                            className="opacity-0 group-hover:opacity-100 p-1 text-[#5A6A85] hover:text-[#1A3F8F] transition-opacity"
                          >
                            <Edit2 size={13} />
                          </button>
                        )}
                      </div>
                    )}
                  </td>
                </tr>
              )
            })}
          </tbody>
        </table>
      </div>
    </div>
  )
}

// ─── Login Modal ──────────────────────────────────────────────────────────────
function LoginModal({ onClose, onLogin }: { onClose: () => void; onLogin: () => void }) {
  const [pw, setPw] = useState("")
  const [error, setError] = useState(false)

  const handleLogin = () => {
    if (pw === "admin123") { onLogin() }
    else { setError(true) }
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div className="bg-white rounded-2xl p-7 w-full max-w-sm shadow-2xl mx-4">
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center gap-3">
            <LogoStar size={32} />
            <div>
              <p className="font-bold text-[#0D1F3C] text-sm">Login Administrativo</p>
              <p className="text-[#5A6A85] text-xs">ABSL — Grêmio Athos Bulcão</p>
            </div>
          </div>
          <button onClick={onClose}><X size={18} className="text-[#5A6A85]" /></button>
        </div>
        <div className="space-y-4">
          <div>
            <label className="text-xs font-medium text-[#5A6A85] mb-1 block">Senha de administrador</label>
            <input
              type="password"
              className={`w-full border rounded-xl px-3 py-2.5 text-sm bg-[#EEF3FB] ${error ? "border-red-400" : "border-[#0D1F3C]/15"}`}
              value={pw}
              onChange={e => { setPw(e.target.value); setError(false) }}
              onKeyDown={e => e.key === "Enter" && handleLogin()}
              placeholder="Digite a senha"
            />
            {error && <p className="text-red-500 text-xs mt-1">Senha incorreta. Tente &quot;admin123&quot; para o protótipo.</p>}
          </div>
          <button onClick={handleLogin} className="w-full py-2.5 rounded-xl bg-[#1A3F8F] text-white text-sm font-medium hover:bg-[#0D1F3C] transition-colors">
            Entrar
          </button>
          <p className="text-center text-xs text-[#5A6A85]">Use <code className="bg-[#EEF3FB] px-1 rounded">admin123</code> para o protótipo.</p>
        </div>
      </div>
    </div>
  )
}

// ─── Main App ─────────────────────────────────────────────────────────────────
export default function App() {
  const [page, setPage] = useState<Page>("inicio")
  const [isAdmin, setIsAdmin] = useState(false)
  const [showLogin, setShowLogin] = useState(false)
  const [team, setTeam] = useState<Diretoria[]>(INIT_TEAM)
  const [sidebarOpen, setSidebarOpen] = useState(false)

  // Close sidebar on wider screens
  useEffect(() => {
    const mq = window.matchMedia("(min-width: 768px)")
    const handler = (e: MediaQueryListEvent) => { if (e.matches) setSidebarOpen(false) }
    mq.addEventListener("change", handler)
    return () => mq.removeEventListener("change", handler)
  }, [])

  const handleToggleAdmin = useCallback(() => {
    if (isAdmin) setIsAdmin(false)
    else setShowLogin(true)
  }, [isAdmin])

  return (
    <div className="min-h-screen flex" style={{ fontFamily: "'DM Sans', sans-serif", background: "#EEF3FB" }}>
      <Sidebar
        current={page}
        onChange={setPage}
        isAdmin={isAdmin}
        onToggleAdmin={handleToggleAdmin}
        open={sidebarOpen}
        onClose={() => setSidebarOpen(false)}
      />

      {/* Main */}
      <main className="md:ml-56 flex-1 flex flex-col min-h-screen w-full min-w-0">
        {/* Mobile top bar */}
        <div className="md:hidden flex items-center gap-3 px-4 py-3 border-b border-[#0D1F3C]/10 bg-white sticky top-0 z-30">
          <button
            onClick={() => setSidebarOpen(true)}
            className="p-2 rounded-lg text-[#0D1F3C] hover:bg-[#EEF3FB] transition-colors"
          >
            <Menu size={20} />
          </button>
          <LogoStar size={28} />
          <span className="font-bold text-sm text-[#0D1F3C]">ABSL</span>
          {isAdmin && (
            <span className="ml-auto text-[10px] font-medium text-amber-700 bg-amber-100 border border-amber-200 px-2 py-0.5 rounded-full">
              Admin ativo
            </span>
          )}
        </div>

        <DecorativeStrip />
        <div className="flex-1 px-4 py-6 md:px-8 md:py-8 max-w-5xl w-full mx-auto">
          {page === "inicio" && <PageInicio isAdmin={isAdmin} team={team} setTeam={setTeam} />}
          {page === "horario" && <PageHorario isAdmin={isAdmin} />}
          {page === "noticias" && <PageNoticias isAdmin={isAdmin} />}
          {page === "projetos" && <PageProjetos isAdmin={isAdmin} />}
          {page === "gabarito" && <PageGabarito isAdmin={isAdmin} />}
          {page === "transparencia" && <PageTransparencia isAdmin={isAdmin} />}
          {page === "cardapio" && <PageCardapio isAdmin={isAdmin} />}
          {page === "ouvidoria" && <PageOuvidoria isAdmin={isAdmin} />}
          {page === "mapa" && <PageMapa />}
        </div>
      </main>

      {showLogin && (
        <LoginModal
          onClose={() => setShowLogin(false)}
          onLogin={() => { setIsAdmin(true); setShowLogin(false) }}
        />
      )}
    </div>
  )
}
