import { ref } from 'vue'

export const token = ref(localStorage.getItem('token') || '')
let initialUser = null
try {
  const raw = localStorage.getItem('usuario')
  initialUser = raw ? JSON.parse(raw) : null
} catch {
  initialUser = null
}
export const user = ref(initialUser)

export function setSession(data) {
  if (data?.access_token) {
    token.value = data.access_token
    localStorage.setItem('token', token.value)
  }
  if (data?.user) {
    user.value = data.user
    localStorage.setItem('usuario', JSON.stringify(user.value))
  }
}

export function clearSession() {
  token.value = ''
  user.value = null
  localStorage.removeItem('token')
  localStorage.removeItem('usuario')
}

export function initSession() {
  token.value = localStorage.getItem('token') || ''
  try {
    const raw = localStorage.getItem('usuario')
    user.value = raw ? JSON.parse(raw) : null
  } catch {
    user.value = null
  }
}

// initialize from localStorage
initSession()
