import api from './api'
import { createResourceService } from './resource'

const base = createResourceService('/noticias')

// Formata sem passar por new Date(string), que interpreta "aaaa-mm-dd" como
// UTC meia-noite e pode voltar um dia ao converter pro fuso local (ex:
// Brasília, UTC-3). Extrai os componentes direto da string, evitando
// qualquer conversão de fuso.
function formatarData(iso) {
  if (!iso) return ''
  const match = /^(\d{4})-(\d{2})-(\d{2})/.exec(iso)
  if (!match) return ''
  const [, year, month, day] = match
  return `${day}/${month}/${year}`
}

// O backend guarda o corpo da notícia em 'descricao'; os componentes
// (NoticiaCard, NoticiaFormModal, NoticiaDetalhe) usam 'texto'.
// 'data_publicacao' também é reformatada aqui (de ISO para dd/mm/aaaa) —
// assim os componentes recebem a data já pronta pra exibir, sem duplicar
// lógica de formatação em cada tela.
function fromApi(n) {
  return { ...n, texto: n.descricao, data_publicacao: formatarData(n.data_publicacao) }
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