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

function toApi({ texto, ...rest }) {
  return { ...rest, descricao: texto }
}

export const noticiasService = {
  async list() {
    return (await base.list()).map(fromApi)
  },
  async get(id) {
    return fromApi(await base.get(id))
  },
  // Cria só os dados de texto — mídias são adicionadas depois, assim que
  // a notícia já tem um id (ver adicionarMidias).
  async create(dados) {
    return fromApi(await base.create(toApi(dados)))
  },
  async update(id, dados) {
    return fromApi(await base.update(id, toApi(dados)))
  },
  async remove(id) {
    return base.remove(id)
  },
  async adicionarMidias(id, arquivos) {
    const fd = new FormData()
    arquivos.forEach((file) => fd.append('midias[]', file))
    const { data } = await api.post(`/noticias/${id}/midias`, fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return fromApi(data)
  },
  async removerMidia(id, midiaId) {
    await api.delete(`/noticias/${id}/midias/${midiaId}`)
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
  async removerComentario(id, comentarioId) {
    await api.delete(`/noticias/${id}/comentarios/${comentarioId}`)
  },
}