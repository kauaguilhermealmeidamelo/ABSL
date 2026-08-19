import api from './api'

export const visitasService = {
  async registrar() {
    try {
      await api.post('/visitas', { path: window.location.pathname })
    } catch {
      // não bloqueia o carregamento do app se a chamada falhar
    }
  },
  async estatisticas() {
    const { data } = await api.get('/visitas/estatisticas')
    return data
  },
}   