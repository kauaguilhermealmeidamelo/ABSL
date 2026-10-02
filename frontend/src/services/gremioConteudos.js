import api from './api'
import { createResourceService } from './resource'

const base = createResourceService('/gremio-conteudos')

// Com arquivo binário o corpo precisa ser multipart; como o axios não envia
// PUT em multipart, usamos POST + _method=PUT (mesmo padrão de gabarito.js).
// Booleans viram '1'/'0' porque a regra boolean do Laravel não aceita "true".
function toFormData(dados) {
  const fd = new FormData()
  Object.entries(dados).forEach(([key, value]) => {
    if (value === undefined || value === null) return
    fd.append(key, typeof value === 'boolean' ? (value ? '1' : '0') : value)
  })
  return fd
}

function precisaMultipart(dados) {
  return dados?.arquivo_pdf instanceof File || dados?.remover_arquivo_pdf === true
}

export const gremioConteudosService = {
  list: () => base.list(),

  // dados = { titulo, conteudo, arquivo_pdf?: File }
  async create(dados) {
    if (dados?.arquivo_pdf instanceof File) {
      const { data } = await api.post('/gremio-conteudos', toFormData(dados), {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      return data
    }
    return base.create(dados)
  },

  // dados = { titulo?, conteudo?, arquivo_pdf?: File, remover_arquivo_pdf?: boolean }
  async update(id, dados) {
    if (precisaMultipart(dados)) {
      const { data } = await api.post(
        `/gremio-conteudos/${id}`,
        toFormData({ ...dados, _method: 'PUT' }),
        { headers: { 'Content-Type': 'multipart/form-data' } },
      )
      return data
    }
    return base.update(id, dados)
  },

  remove: (id) => base.remove(id),

  async reorder(idA, idB) {
    const { data } = await api.post('/gremio-conteudos/reorder', { id_a: idA, id_b: idB })
    return data
  },
}