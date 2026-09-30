import api from './api'
import { setSession, clearSession } from '@/stores/auth'

async function ensureCsrfCookie() {
  await api.get('/sanctum/csrf-cookie', { baseURL: '/' })
}

function toUserMessage(err, fallback) {
  const status = err?.response?.status
  const data = err?.response?.data
  if (status === 422 && data?.errors) {
    const first = Object.values(data.errors)[0]
    if (Array.isArray(first) && first[0]) return first[0]
  }
  const msg = data?.message
  if (typeof msg === 'string' && msg) {
    if (msg.includes('already been taken') && JSON.stringify(data).includes('email'))
      return 'Este e-mail já está cadastrado.'
    if (msg.includes('already been taken') && JSON.stringify(data).includes('username'))
      return 'Este @ já está em uso.'
    return msg
  }
  if (status === 419) return 'Sua sessão expirou. Tente novamente.'
  if (status === 429) return 'Muitas tentativas. Aguarde um minuto e tente de novo.'
  return fallback
}

export async function login(email, password, remember = false) {
  await ensureCsrfCookie()
  try {
    const { data } = await api.post('/login', { email, password, remember })
    setSession(data)
    return data.user
  } catch (err) {
    throw new Error(toUserMessage(err, 'Credenciais inválidas.'))
  }
}

export async function register({ name, username, email, password, password_confirmation, turma }) {
  await ensureCsrfCookie()
  try {
    const { data } = await api.post('/register', {
      name,
      username,
      email,
      password,
      password_confirmation,
      ...(turma ? { turma } : {}),
    })
    setSession(data)
    return data.user
  } catch (err) {
    throw new Error(toUserMessage(err, 'Não foi possível criar a conta.'))
  }
}

export async function logout() {
  try {
    await api.post('/logout')
  } finally {
    clearSession()
  }
}

export async function updateProfile({ name, username, email, turma }) {
  try {
    const { data } = await api.put('/me', {
      name,
      username,
      email,
      turma: turma || null,
    })
    setSession(data)
    return data
  } catch (err) {
    throw new Error(toUserMessage(err, 'Não foi possível salvar o perfil.'))
  }
}

export async function resendVerification() {
  const { data } = await api.post('/email/verification-notification')
  return data
}