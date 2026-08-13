import api from './api'
import { createResourceService } from './resource'

const base = createResourceService('/noticias')

// O backend guarda o corpo da notícia em 'descricao'; os componentes
// (NoticiaCard, NoticiaFormModal, NoticiaDetalhe) usam 'texto'.
function fromApi(n) {
  return { ...n, texto: n.descricao }
}

// 'imagem' é um File (upload novo). Quando presente, manda multipart e o
// backend salva o arquivo de verdade. Quando ausente, manda JSON normal e
// mantém a 'imagem_url' que já existia (edição sem trocar a foto).
function toApi({ texto, imagem, ...rest }) {
  return { ...rest, descricao: texto, imagem }
}

function buildFormData(payload) {
  const fd = new FormData()
  Object.entries(payload).forEach(([key, value]) => {
    if (value === undefined || value === null) return
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
      fd.append('_method', 'PUT') // Laravel: multipart não suporta PUT nativo
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
}