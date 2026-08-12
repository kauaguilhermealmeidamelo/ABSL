import api from './api'
import { createResourceService } from './resource'

const base = createResourceService('/transparencia')

// O backend guarda o texto principal em 'titulo'; o TransparenciaFormModal
// usa 'referencia'. 'categoria' vem da aba ativa (atas | contas), não do
// formulário.
function fromApi(t) {
  return { ...t, referencia: t.titulo }
}

function toApi({ arquivo, data_documento, ...rest }, categoria) {
  const payload = {
    ...rest,
    titulo: data_documento,
    categoria,
    tipo_documento: categoria === 'atas' ? 'ata' : 'prestacao_contas',
    data_documento,
  }

  if (arquivo instanceof File) {
    const formData = new FormData()
    Object.entries(payload).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        formData.append(key, value)
      }
    })
    formData.append('file', arquivo)
    return formData
  }

  return payload
}

export const transparenciaService = {
  async list() {
    return (await base.list()).map(fromApi)
  },
  async create(dados, categoria) {
    const payload = toApi(dados, categoria)
    if (payload instanceof FormData) {
      const { data } = await api.post('/transparencia', payload, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      return fromApi(data)
    }

    return fromApi(await base.create(payload))
  },
  async remove(id) {
    return base.remove(id)
  },
}
