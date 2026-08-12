import api from './api'
import { setSession, clearSession } from '@/stores/auth'

async function ensureCsrfCookie() {
  await api.get('/sanctum/csrf-cookie', { baseURL: '/' })
}

export async function login(email, password) {
  await ensureCsrfCookie()
  const { data } = await api.post('/login', { email, password })
  setSession(data)
  return data.user
}

export async function register({ name, email, password, password_confirmation }) {
  await ensureCsrfCookie()
  const { data } = await api.post('/register', { name, email, password, password_confirmation })
  setSession(data)
  return data.user
}

export async function logout() {
  try {
    await api.post('/logout')
  } finally {
    clearSession()
  }
}