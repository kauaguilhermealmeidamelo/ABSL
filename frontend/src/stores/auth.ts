import { ref } from 'vue'

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

function readStoredUser(): AuthUser | null {
  try {
    const raw = localStorage.getItem('usuario')
    return raw ? (JSON.parse(raw) as AuthUser) : null
  } catch {
    return null
  }
}

export const user = ref<AuthUser | null>(readStoredUser())

export function setSession(data: LoginResponse): void {
  if (data?.user) {
    user.value = data.user
    localStorage.setItem('usuario', JSON.stringify(user.value))
  }
}

export function clearSession(): void {
  user.value = null
  localStorage.removeItem('usuario')
}

export function initSession(): void {
  user.value = readStoredUser()
}

initSession()