import { createResourceService } from './resource'

const base = createResourceService('/ouvintes')

function formatarData(iso) {
  if (!iso) return ''
  return new Date(iso).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' })
}

// O backend guarda a mensagem em 'mensagem'; o OuvintesFormulario/
// OuvintesMensagem usam 'texto'. 'data_envio' é derivado de 'created_at'.
function fromApi(o) {
  return { ...o, texto: o.mensagem, data_envio: formatarData(o.created_at) }
}
function toApi({ texto, ...rest }) {
  return { ...rest, mensagem: texto }
}

export const ouvintesService = {
  async list() {
    return (await base.list()).map(fromApi)
  },
  async create(dados) {
    return fromApi(await base.create(toApi(dados)))
  },
  async update(id, dados) {
    return fromApi(await base.update(id, dados))
  },
  async remove(id) {
    return base.remove(id)
  },
}
