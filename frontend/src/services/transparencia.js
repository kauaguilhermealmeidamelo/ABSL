import { createResourceService } from './resource'

const base = createResourceService('/transparencia')

// O backend guarda o texto principal em 'titulo'; o TransparenciaFormModal
// usa 'referencia'. 'categoria' vem da aba ativa (atas | contas), não do
// formulário.
function fromApi(t) {
  return { ...t, referencia: t.titulo }
}

// O formulário atual não tem seletor de data (só texto livre em
// 'referencia'), mas 'data_documento' é obrigatório no backend. Enquanto
// isso não muda, gravamos a data de hoje como melhor esforço.
function toApi({ referencia, arquivo, ...rest }, categoria) {
  return {
    ...rest,
    titulo: referencia,
    arquivo_url: arquivo || null,
    categoria,
    tipo_documento: categoria === 'atas' ? 'ata' : 'prestacao_contas',
    data_documento: new Date().toISOString().slice(0, 10),
  }
}

export const transparenciaService = {
  async list() {
    return (await base.list()).map(fromApi)
  },
  async create(dados, categoria) {
    return fromApi(await base.create(toApi(dados, categoria)))
  },
  async remove(id) {
    return base.remove(id)
  },
}
