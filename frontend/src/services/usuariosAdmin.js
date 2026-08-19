import api from './api'

export const usuariosAdminService = {
  async list() {
    const { data } = await api.get('/admin/usuarios')
    return data
  },
  async create(dados) {
    const { data } = await api.post('/admin/usuarios', dados)
    return data
  },
  async updatePassword(id, password) {
    const { data } = await api.put(`/admin/usuarios/${id}/senha`, { password })
    return data
  },
  async remove(id) {
    await api.delete(`/admin/usuarios/${id}`)
  },
}