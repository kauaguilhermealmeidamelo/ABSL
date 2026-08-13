import api from './api'

function toFormData(dados) {
  const fd = new FormData()
  Object.entries(dados).forEach(([key, value]) => {
    if (value === undefined || value === null) return
    fd.append(key, value)
  })
  return fd
}

export const gabaritoService = {
  async list() {
    const { data } = await api.get('/gabarito')
    return data.data ?? data
  },

  // dados = { grupo_turma, tipo_prova, tipo_documento: 'gabarito'|'prova', arquivo: File }
  async create(dados) {
    const { data } = await api.post('/gabarito', toFormData(dados), {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return data.data ?? data
  },

  // Substituir só o PDF, mantendo grupo/tipo: gabaritoService.substituirArquivo(id, file)
  async substituirArquivo(id, file) {
    const fd = new FormData()
    fd.append('arquivo', file)
    fd.append('_method', 'PUT') // Laravel: multipart não suporta PUT nativo
    const { data } = await api.post(`/gabarito/${id}`, fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return data.data ?? data
  },

  async remove(id) {
    await api.delete(`/gabarito/${id}`)
  },
}