import api from './api'

/**
 * Autentica com e-mail + senha reais (backend). Salva token e usuário
 * no localStorage — os mesmos que useAdmin.js e api.ts já leem.
 */
export async function login(email, password) {
  const { data } = await api.post('/login', { email, password })
  persistSession(data)
  return data.user
}

/**
 * Cria uma nova conta (sempre role 'user' — nunca admin via registro
 * público). Não há tela de cadastro no frontend ainda; a função existe
 * para quando essa tela for construída.
 */
export async function register({ name, email, password, password_confirmation }) {
  const { data } = await api.post('/register', { name, email, password, password_confirmation })
  persistSession(data)
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

function persistSession(data) {
  localStorage.setItem('token', data.access_token)
  localStorage.setItem('usuario', JSON.stringify(data.user))
}

function clearSession() {
  localStorage.removeItem('token')
  localStorage.removeItem('usuario')
}
