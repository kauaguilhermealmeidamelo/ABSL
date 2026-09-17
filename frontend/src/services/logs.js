import api from './api'

const ACAO_LABELS = {
  login: 'Login',
  logout: 'Logout',
  criou_usuario: 'Criou usuário',
  atualizou_senha_usuario: 'Atualizou senha de usuário',
  excluiu_usuario: 'Excluiu usuário',
  criou_noticia: 'Criou notícia',
  editou_noticia: 'Editou notícia',
  excluiu_noticia: 'Excluiu notícia',
  criou_projeto: 'Criou projeto',
  editou_projeto: 'Editou projeto',
  excluiu_projeto: 'Excluiu projeto',
  criou_cardapio: 'Cadastrou cardápio',
  editou_cardapio: 'Editou cardápio',
  excluiu_cardapio: 'Excluiu cardápio',
  criou_gabarito: 'Cadastrou gabarito/prova',
  editou_gabarito: 'Editou gabarito/prova',
  substituiu_arquivo_gabarito: 'Substituiu arquivo de gabarito/prova',
  excluiu_gabarito: 'Excluiu gabarito/prova',
  criou_transparencia: 'Publicou documento de transparência',
  editou_transparencia: 'Editou documento de transparência',
  excluiu_transparencia: 'Excluiu documento de transparência',
  criou_aula: 'Criou aula no horário',
  editou_aula: 'Editou aula no horário',
  excluiu_aula: 'Excluiu aula do horário',
  criou_turma: 'Criou turma',
  excluiu_turma: 'Excluiu turma',
  criou_diretoria: 'Criou diretoria',
  editou_diretoria: 'Editou diretoria',
  reordenou_diretorias: 'Reordenou diretorias',
  excluiu_diretoria: 'Excluiu diretoria',
  atualizou_video_inicio: 'Atualizou vídeo da tela inicial',
  respondeu_ouvidoria: 'Respondeu mensagem da ouvidoria',
  atualizou_status_ouvidoria: 'Atualizou status de mensagem da ouvidoria',
  excluiu_ouvidoria: 'Excluiu mensagem da ouvidoria',
}

export function acaoLabel(acao) {
  return ACAO_LABELS[acao] || String(acao).replace(/_/g, ' ')
}

export const logsService = {
  // 'data' aqui é uma data única no formato yyyy-mm-dd (input type="date"),
  // usada para filtrar as modificações de um dia específico.
  async list({ page = 1, acao = '', busca = '', data = '' } = {}) {
    const { data: resposta } = await api.get('/logs', { params: { page, acao, busca, data } })
    return resposta
  },
  async acoesDisponiveis() {
    const { data } = await api.get('/logs/acoes')
    return data
  },
}