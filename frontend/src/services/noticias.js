import { createResourceService } from './resource'

const base = createResourceService('/noticias')

function formatarData(iso) {
  if (!iso) return ''
  return String(iso).slice(0, 10)
}

// O backend guarda o corpo da notícia em 'descricao'; os componentes
// (NoticiaCard, NoticiaFormModal, NoticiaDetalhe) usam 'texto'.
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
  async create(dados) {
    return fromApi(await base.create(toApi(dados)))
  },
  async update(id, dados) {
    return fromApi(await base.update(id, toApi(dados)))
  },
  async remove(id) {
    return base.remove(id)
  },
}