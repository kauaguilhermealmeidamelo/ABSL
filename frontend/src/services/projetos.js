import { createResourceService } from './resource'

const base = createResourceService('/projetos')

// O backend guarda o título do projeto em 'nome'; os componentes
// (ProjetoCard, ProjetoFormModal, ProjetoDetalheDialog) usam 'titulo'.
function fromApi(p) {
  return { ...p, titulo: p.nome }
}
function toApi({ titulo, ...rest }) {
  return { ...rest, nome: titulo }
}

export const projetosService = {
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
