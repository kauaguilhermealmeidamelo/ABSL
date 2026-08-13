import { ref } from 'vue'
import api from '@/services/api'

export interface AuthUser {
  id: number
  name: string
  email: string
  is_admin?: boolean
  role?: string
  turma?: string | null
}

interface LoginResponse {
  user?: AuthUser
}

// Tempo máximo (ms) que consideramos uma sessão de admin válida no frontend.
// Deve ficar igual (ou menor) que SESSION_LIFETIME do backend (em minutos,
// configurado em backend/.env). Depois desse tempo, o admin é deslogado
// automaticamente e volta a navegar como usuário comum.
const SESSION_LIFETIME_MS = 120 * 60 * 1000 // 120 minutos

const USER_KEY = 'usuario'
const LOGIN_AT_KEY = 'usuario_login_at'

function readStoredUser(): AuthUser | null {
  try {
    const raw = localStorage.getItem(USER_KEY)
    return raw ? (JSON.parse(raw) as AuthUser) : null
  } catch {
    return null
  }
}

function readLoginAt(): number | null {
  const raw = localStorage.getItem(LOGIN_AT_KEY)
  const parsed = raw ? Number(raw) : null
  return parsed && !Number.isNaN(parsed) ? parsed : null
}

export const user = ref<AuthUser | null>(null)
let loginAt: number | null = null

export function setSession(data: LoginResponse): void {
  if (data?.user) {
    user.value = data.user
    loginAt = Date.now()
    localStorage.setItem(USER_KEY, JSON.stringify(user.value))
    localStorage.setItem(LOGIN_AT_KEY, String(loginAt))
  }
}

export function clearSession(): void {
  user.value = null
  loginAt = null
  localStorage.removeItem(USER_KEY)
  localStorage.removeItem(LOGIN_AT_KEY)
}

function isExpiredLocally(): boolean {
  return !!loginAt && Date.now() - loginAt > SESSION_LIFETIME_MS
}

export function initSession(): void {
  user.value = readStoredUser()
  loginAt = readLoginAt()

  // Se já passou do tempo limite desde o último login (ex: aba ficou aberta
  // de um dia pro outro), desloga na hora, sem esperar uma chamada à API.
  if (user.value && isExpiredLocally()) {
    clearSession()
  }
}

/**
 * Confirma junto ao backend se a sessão ainda é válida. Cobre o caso do
 * cookie de sessão do Laravel ter expirado (SESSION_LIFETIME) mesmo dentro
 * da janela local — o backend é sempre a fonte da verdade final.
 */
export async function checkSession(): Promise<void> {
  if (!user.value) return

  if (isExpiredLocally()) {
    clearSession()
    return
  }

  try {
    await api.get('/user')
  } catch (err: any) {
    const status = err?.response?.status
    if (status === 401 || status === 419) {
      clearSession()
    }
  }
}

initSession()