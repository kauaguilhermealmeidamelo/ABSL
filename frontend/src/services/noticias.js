import api from './api'
import { createResourceService } from './resource'

const base = createResourceService('/noticias')

function formatarData(iso) {
  if (!iso) return ''
  const match = /^(\d{4})-(\d{2})-(\d{2})/.exec(iso)
  if (!match) return ''
  const [, year, month, day] = match
  return `${day}/${month}/${year}`
}

function fromApi(n) {
  return { ...n, texto: n.descricao, data_publicacao: formatarData(n.data_publicacao) }
}

function toApi({ texto, imagem, ...rest }) {
  return { ...rest, descricao: texto, imagem }
}

function buildFormData(payload) {
  const fd = new FormData()
  Object.entries(payload).forEach(([key, value]) => {
    if (value === undefined || value === null) return
    // FormData.append() converte qualquer valor não-Blob para string via
    // toString(). Um booleano true vira a string "true", que a regra
    // 'boolean' do Laravel rejeita (só aceita "1"/"0"/1/0/true/false).
    // Convertendo aqui pra '1'/'0' explicitamente, evita esse descompasso.
    if (typeof value === 'boolean') {
      fd.append(key, value ? '1' : '0')
      return
    }
    fd.append(key, value)
  })
  return fd
}

export const noticiasService = {
  async list() {
    return (await base.list()).map(fromApi)
  },
  async get(id) {
    return fromApi(await base.get(id))
  },
  async create(dados) {
    const { imagem, ...payload } = toApi(dados)
    if (imagem instanceof File) {
      const { data } = await api.post('/noticias', buildFormData({ ...payload, imagem }), {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      return fromApi(data)
    }
    return fromApi(await base.create(payload))
  },
  async update(id, dados) {
    const { imagem, ...payload } = toApi(dados)
    if (imagem instanceof File) {
      const fd = buildFormData({ ...payload, imagem })
      fd.append('_method', 'PUT')
      const { data } = await api.post(`/noticias/${id}`, fd, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      return fromApi(data)
    }
    return fromApi(await base.update(id, payload))
  },
  async remove(id) {
    return base.remove(id)
  },
  async curtir(id) {
    const { data } = await api.post(`/noticias/${id}/curtir`)
    return data
  },
  async listarComentarios(id) {
    const { data } = await api.get(`/noticias/${id}/comentarios`)
    return data
  },
  async comentar(id, texto) {
    const { data } = await api.post(`/noticias/${id}/comentarios`, { texto })
    return data
  },
}