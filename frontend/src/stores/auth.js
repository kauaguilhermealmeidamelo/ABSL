import { ref } from 'vue'


let initialUser = null
try {
  const raw = localStorage.getItem('usuario')
  initialUser = raw ? JSON.parse(raw) : null
} catch {
  initialUser = null
}
export const user = ref(initialUser)

export function setSession(data) {
  if (data?.user) {
    user.value = data.user
    localStorage.setItem('usuario', JSON.stringify(user.value))
  }
}

export function clearSession() {
  user.value = null
  localStorage.removeItem('usuario')
}

export function initSession() {
  try {
    const raw = localStorage.getItem('usuario')
    user.value = raw ? JSON.parse(raw) : null
  } catch {
    user.value = null
  }
}

initSession()