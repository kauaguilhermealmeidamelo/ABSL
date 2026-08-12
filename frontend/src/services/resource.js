import api from './api'

/**
 * Cria um serviço CRUD padrão para um endpoint REST.
 * Usado como base pelos services de cada entidade (noticias, projetos...).
 */
export function createResourceService(basePath) {
  return {
    async list() {
      const { data } = await api.get(basePath)
      return data
    },
    async get(id) {
      const { data } = await api.get(`${basePath}/${id}`)
      return data
    },
    async create(payload) {
      const { data } = await api.post(basePath, payload)
      return data
    },
    async update(id, payload) {
      const { data } = await api.put(`${basePath}/${id}`, payload)
      return data
    },
    async remove(id) {
      await api.delete(`${basePath}/${id}`)
    },
  }
}
