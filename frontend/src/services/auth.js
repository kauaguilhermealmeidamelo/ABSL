import api from './api'
import { setSession, clearSession } from '@/stores/auth'

/**
 * Autentica com e-mail + senha reais (backend). Salva token e usuário
 * no localStorage — os mesmos que useAdmin.js e api.ts já leem.
 */
export async function login(email, password) {
  const { data } = await api.post('/login', { email, password })
  setSession(data)
  return data.user
}

/**
 * Cria uma nova conta (sempre role 'user' — nunca admin via registro
 * público). Não há tela de cadastro no frontend ainda; a função existe
 * para quando essa tela for construída.
 */
export async function register({ name, email, password, password_confirmation }) {
  const { data } = await api.post('/register', { name, email, password, password_confirmation })
  setSession(data)
  return data.user
}

/**
 * Revoga o token atual no backend e limpa a sessão local.
 */
export async function logout() {
  try {
    await api.post('/logout')
  } finally {
    clearSession()
  }
}

// persist/clear are handled by auth store
