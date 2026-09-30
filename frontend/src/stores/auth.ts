import { ref } from 'vue'
import api from '@/services/api'

export interface AuthUser {
  id: number
  name: string
  username?: string | null
  email: string
  email_verified_at?: string | null
  is_admin?: boolean
  role?: string
  turma?: string | null
}

interface SessionResponse {
  user?: AuthUser
}

// Chave do fallback de turma para visitantes (sem login).
// Para usuário logado, a fonte de verdade é SEMPRE user.turma (perfil).
export const TURMA_VISITANTE_KEY = 'turma_visitante'

const USER_KEY = 'usuario'

function readStoredUser(): AuthUser | null {
  try {
    const raw = localStorage.getItem(USER_KEY)
    return raw ? (JSON.parse(raw) as AuthUser) : null
  } catch {
    return null
  }
}

/**
 * Turma efetiva: perfil do usuário logado tem prioridade; visitante usa
 * o fallback de localStorage. localStorage NUNCA sobrescreve o perfil.
 */
export function readTurmaVisitante(): string | null {
  try {
    return localStorage.getItem(TURMA_VISITANTE_KEY)
  } catch {
    return null
  }
}

export function saveTurmaVisitante(codigo: string | null): void {
  try {
    if (!codigo) localStorage.removeItem(TURMA_VISITANTE_KEY)
    else localStorage.setItem(TURMA_VISITANTE_KEY, codigo)
  } catch {
    // localStorage indisponível — segue sem persistir
  }
}

export const user = ref<AuthUser | null>(null)

export function setSession(data: SessionResponse): void {
  if (data?.user) {
    user.value = data.user
    localStorage.setItem(USER_KEY, JSON.stringify(user.value))
  }
}

export function clearSession(): void {
  user.value = null
  localStorage.removeItem(USER_KEY)
}

export function initSession(): void {
  user.value = readStoredUser()
}

/**
 * Fonte de verdade é o backend: pergunta se a sessão/cookie ainda é válida
 * e sincroniza o estado local. Não há mais expiração artificial por
 * cronômetro no frontend (o "Lembrar de mim" / remember_token do Laravel
 * continua válido mesmo após SESSION_LIFETIME de inatividade).
 */
export async function checkSession(): Promise<void> {
  if (!user.value) return

  try {
    const { data } = await api.get<AuthUser>('/user')
    if (data) setSession({ user: data })
  } catch (err: any) {
    const status = err?.response?.status
    if (status === 401 || status === 419) {
      clearSession()
    }
  }
}

initSession()