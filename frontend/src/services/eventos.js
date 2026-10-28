import api from './api'
import { createResourceService } from './resource'

const base = createResourceService('/eventos')

// Multipart com _method=PUT: o PHP não lê arquivos em PUT puro.
function toFormData({ imagem, removerImagem, ...campos }) {
  const fd = new FormData()
  Object.entries(campos).forEach(([k, v]) => {
    if (v !== null && v !== undefined) fd.append(k, v)
  })
  if (imagem instanceof File) fd.append('imagem', imagem)
  if (removerImagem) fd.append('remover_imagem', '1')
  return fd
}

const headers = { 'Content-Type': 'multipart/form-data' }

export const eventosService = {
  list: () => base.list(),
  get: (id) => base.get(id),
  async create(dados) {
    const { data } = await api.post('/eventos', toFormData(dados), { headers })
    return data
  },
  async update(id, dados) {
    const fd = toFormData(dados)
    fd.append('_method', 'PUT')
    const { data } = await api.post(`/eventos/${id}`, fd, { headers })
    return data
  },
  remove: (id) => base.remove(id),
}
