import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  withCredentials: true,
  withXSRFToken: true,
})

/**
 * Registra um interceptor que desloga automaticamente o usuário sempre que
 * o backend responder 401 (sessão expirada/não autenticado) ou 419 (token
 * CSRF expirado). Chamado a partir do main.ts para evitar import circular
 * entre api.ts e o store de auth.
 */
export function setupAuthInterceptor(onUnauthorized: () => void): void {
  api.interceptors.response.use(
    (response) => response,
    (error) => {
      const status = error?.response?.status
      if (status === 401 || status === 419) {
        onUnauthorized()
      }
      return Promise.reject(error)
    }
  )
}

export default api