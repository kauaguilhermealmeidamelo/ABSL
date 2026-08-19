import { createResourceService } from './resource'
import api from './api'

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
  // Consulta pública, usada por usuários comuns para ver a resposta da
  // própria mensagem via protocolo (o id retornado ao enviar).
  async consultarProtocolo(id) {
    const { data } = await api.get(`/ouvintes/protocolo/${id}`)
    return { ...data, data_envio: formatarData(data.data_envio) }
  },
  // Lista pública de mensagens já respondidas — a "central de suporte"
  // que qualquer visitante pode consultar, sem precisar ter enviado nada.
  async listRespondidas() {
    const { data } = await api.get('/ouvintes/respondidas')
    return data.map((m) => ({ ...m, data_envio: formatarData(m.data_envio) }))
  },
}