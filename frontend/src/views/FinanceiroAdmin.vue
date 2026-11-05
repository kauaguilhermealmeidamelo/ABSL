<script setup>
import { computed, onMounted, ref } from 'vue'
import api from '@/services/api'
import PageHeader from '@/components/common/PageHeader.vue'

const gestoes = ref([])
const atual = ref(null)
const movimentacoes = ref([])
const totais = ref(null)
const erro = ref('')
const aviso = ref('')
const ocupado = ref(false)
const novaGestao = ref({ nome: '', inicio: '', fim_previsto: '', saldo: '0,00' })
const nova = ref({ data: '', tipo: 'receita', categoria: '', descricao: '', valor: '' })
const fechamento = ref({ ano: new Date().getFullYear(), mes: new Date().getMonth() + 1 })
const observacao = ref('')
const vinculos = ref([])
const novoVinculo = ref({ user_id: '', cargo: 'tesoureiro_geral', interino: false, fim_previsto_em: '', justificativa: '' })
const ativacao = ref({ saldo: '0,00', justificativa: '' })
const conciliacao = ref({ saldo: '0,00', justificativa: '' })
const comprovantes = ref({})
const transicoes = ref([])
const transicaoAtual = ref(null)
const movimentosTransicao = ref([])
const novaTransicao = ref({ inicio: '', fim_previsto: '', motivo: '' })
const novaMovTransicao = ref({ data: '', tipo: 'despesa', categoria: '', descricao: '', justificativa_essencialidade: '', valor: '' })
const encerramentoTransicao = ref({ fim_real: '', saldo: '0,00', justificativa_conciliacao: '' })
const nota = ref({ texto: '', relatorio_financeiro_id: '' })
const modalRetificacao = ref(false)
const retificacao = ref({ id: null, descricao: '', valor: '', justificativa: '' })
const modalGestao = ref(false)
const modalMovimentacao = ref(false)
const aba = ref('movimentacoes')
const filtroTipo = ref('todas')
const filtradas = computed(() => movimentacoes.value.filter(m => filtroTipo.value === 'todas' || m.tipo === filtroTipo.value))
const pagina = ref(1)
const ultimaPagina = ref(1)
const inicialManual = computed(() => gestoes.value.length === 0 || (gestoes.value.length === 1 && gestoes.value[0]?.id === atual.value))
const formatadorBRL = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' })
const moeda = (centavos) => formatadorBRL.format(Number(centavos || 0) / 100)
function formatarReais(event, campo) {
  const digitos = event.target.value.replace(/\D/g, '').slice(0, 13)
  const valor = formatadorBRL.format(Number(digitos || 0) / 100)
  if (campo === 'gestao') ativacao.value.saldo = valor
  else nova.value.valor = valor
}
function centavos(valor) {
  const texto = String(valor).trim().replace(/R\$\s?/g, '').replace(/\./g, '').replace(',', '.')
  if (!/^\d+(\.\d{1,2})?$/.test(texto)) throw new Error('Informe um valor válido, como 10,50.')
  return Math.round(Number(texto) * 100)
}
async function carregarTransicoes() {
  const { data } = await api.get('/admin/financeiro/transicoes')
  transicoes.value = data
  if (!transicaoAtual.value && data.length) transicaoAtual.value = data[0].id
  if (transicaoAtual.value && !data.some(t => t.id === transicaoAtual.value)) transicaoAtual.value = data[0]?.id || null
  if (transicaoAtual.value) {
    const resposta = await api.get(`/admin/financeiro/transicoes/${transicaoAtual.value}/movimentacoes`)
    movimentosTransicao.value = resposta.data.data || []
  } else movimentosTransicao.value = []
}
const transicaoSelecionada = computed(() => transicoes.value.find(t => t.id === transicaoAtual.value))
function iniciarTransicao() {
  executar(async () => {
    const { data } = await api.post('/admin/financeiro/transicoes', novaTransicao.value)
    transicaoAtual.value = data.id
    novaTransicao.value = { inicio: '', fim_previsto: '', motivo: '' }
    await carregarTransicoes()
  }, 'Transição financeira iniciada.')
}
function criarMovimentoTransicao() {
  executar(async () => {
    await api.post(`/admin/financeiro/transicoes/${transicaoAtual.value}/movimentacoes`, {
      ...novaMovTransicao.value, valor_centavos: centavos(novaMovTransicao.value.valor),
    })
    novaMovTransicao.value = { data: '', tipo: 'despesa', categoria: '', descricao: '', justificativa_essencialidade: '', valor: '' }
    await carregarTransicoes()
  }, 'Movimentação da transição registrada como pendente.')
}
function resolverMovimentoTransicao(m, acao) {
  let justificativa = ''
  if (acao === 'cancelar') {
    const resposta = window.prompt('Justificativa do cancelamento (mínimo 10 caracteres):')
    if (resposta === null) return
    justificativa = resposta.trim()
    if (justificativa.length < 10) { erro.value = 'Informe ao menos 10 caracteres.'; return }
  }
  executar(async () => {
    await api.post(`/admin/financeiro/transicoes/${transicaoAtual.value}/movimentacoes/${m.id}/resolver`, { acao, ...(justificativa ? { justificativa } : {}) })
    await carregarTransicoes()
  }, acao === 'confirmar' ? 'Movimentação confirmada.' : 'Movimentação cancelada.')
}
function encerrarTransicao() {
  if (!window.confirm('Encerrar a transição e publicar o relatório definitivo?')) return
  executar(async () => {
    await api.post(`/admin/financeiro/transicoes/${transicaoAtual.value}/encerrar`, {
      fim_real: encerramentoTransicao.value.fim_real,
      saldo_real_final_centavos: centavos(encerramentoTransicao.value.saldo),
      justificativa_conciliacao: encerramentoTransicao.value.justificativa_conciliacao,
    })
    await carregarTransicoes()
  }, 'Transição encerrada e relatório publicado.')
}
function publicarNota() {
  executar(() => api.post(`/admin/financeiro/gestoes/${atual.value}/esclarecimentos`, {
    texto: nota.value.texto,
    ...(nota.value.relatorio_financeiro_id ? { relatorio_financeiro_id: Number(nota.value.relatorio_financeiro_id) } : {}),
  }).then(() => { nota.value = { texto: '', relatorio_financeiro_id: '' } }), 'Nota de esclarecimento publicada.')
}
async function carregar() {
  const { data } = await api.get('/admin/financeiro/gestoes')
  gestoes.value = data
  if (!atual.value && data.length) atual.value = data[0].id
  if (atual.value && !data.some(g => g.id === atual.value)) atual.value = data[0]?.id || null
  if (atual.value) await selecionar(atual.value, pagina.value)
  await carregarTransicoes()
}
async function selecionar(id, numeroPagina = 1) {
  pagina.value = numeroPagina
  atual.value = Number(id)
  const [resumo, lista, responsaveis] = await Promise.all([
    api.get(`/admin/financeiro/gestoes/${id}/resumo`),
    api.get(`/admin/financeiro/gestoes/${id}/movimentacoes`, { params: { page: numeroPagina } }),
    api.get(`/admin/financeiro/gestoes/${id}/vinculos`),
  ])
  vinculos.value = Array.isArray(responsaveis.data) ? responsaveis.data : []
  totais.value = resumo.data.totais
  movimentacoes.value = lista.data.data || []
  ultimaPagina.value = lista.data.last_page || 1
}
const gestaoSelecionada = computed(() => gestoes.value.find(g => g.id === atual.value))
async function executar(acao, sucesso) {
  ocupado.value = true; erro.value = ''; aviso.value = ''
  try { await acao(); aviso.value = sucesso; await carregar() }
  catch (e) { erro.value = Object.values(e.response?.data?.errors || {}).flat().join(' ') || e.response?.data?.message || e.message || 'Não foi possível concluir a operação.' }
  finally { ocupado.value = false }
}
function criarGestao() {
  executar(async () => {
    await api.post('/admin/financeiro/gestoes', {
      nome: novaGestao.value.nome, inicio: novaGestao.value.inicio, fim_previsto: novaGestao.value.fim_previsto,
    })
    atual.value = null
    modalGestao.value = false
  }, 'Gestão cadastrada.')
}
function adicionar() {
  executar(async () => {
    await api.post(`/admin/financeiro/gestoes/${atual.value}/movimentacoes`, {
      ...nova.value, valor_centavos: centavos(nova.value.valor),
    })
    nova.value = { data: '', tipo: 'receita', categoria: '', descricao: '', valor: '' }
    modalMovimentacao.value = false
  }, 'Movimentação registrada como pendente.')
}
function abrirMovimentacao(tipo) {
  nova.value = { data: new Date().toISOString().slice(0, 10), tipo, categoria: '', descricao: '', valor: '' }
  modalMovimentacao.value = true
}
function cancelarMovimentacao(m) {
  const justificativa = window.prompt(`Justifique o cancelamento da movimentação #${m.id} (mínimo 10 caracteres):`)
  if (justificativa === null) return
  if (justificativa.trim().length < 10) { erro.value = 'A justificativa deve ter pelo menos 10 caracteres.'; return }
  if (!window.confirm(`Cancelar a movimentação #${m.id}? Esta ação ficará registrada.`)) return
  executar(() => api.post(`/admin/financeiro/movimentacoes/${m.id}/cancelar`, { justificativa: justificativa.trim() }), 'Movimentação pendente cancelada.')
}
function abrirRetificacao(m) {
  retificacao.value = { id: m.id, descricao: m.descricao, valor: (Number(m.valor_centavos) / 100).toFixed(2).replace('.', ','), justificativa: '' }
  modalRetificacao.value = true
}
function salvarRetificacao() {
  executar(async () => {
    await api.post(`/admin/financeiro/movimentacoes/${retificacao.value.id}/retificar`, {
      descricao: retificacao.value.descricao,
      valor_centavos: centavos(retificacao.value.valor),
      justificativa: retificacao.value.justificativa,
    })
    modalRetificacao.value = false
  }, 'Retificação registrada. As versões dos relatórios afetados foram atualizadas.')
}
function confirmar(id) {
  executar(() => api.post(`/admin/financeiro/movimentacoes/${id}/confirmar`), 'Movimentação confirmada.')
}
async function anexarComprovante(id, event) {
  const arquivo = event.target.files?.[0]
  if (!arquivo) return
  await executar(async () => {
    const dados = new FormData()
    dados.append('arquivo', arquivo)
    await api.post(`/admin/financeiro/movimentacoes/${id}/comprovantes`, dados)
  }, 'Comprovante privado anexado.')
  event.target.value = ''
}
async function listarComprovantes(id) {
  try {
    const { data } = await api.get(`/admin/financeiro/movimentacoes/${id}/comprovantes`)
    comprovantes.value = { ...comprovantes.value, [id]: data }
  } catch { erro.value = 'Não foi possível consultar os comprovantes.' }
}
function baixarComprovante(id, nome) {
  api.get(`/admin/financeiro/comprovantes/${id}/download`, { responseType: 'blob' })
    .then(({ data }) => {
      const url = URL.createObjectURL(data)
      const link = document.createElement('a')
      link.href = url
      link.download = nome
      link.click()
      URL.revokeObjectURL(url)
    }).catch(() => { erro.value = 'Não foi possível baixar o comprovante.' })
}
function ativarGestao() {
  executar(() => api.post(`/admin/financeiro/gestoes/${atual.value}/ativar`, {
    ...(inicialManual.value ? { saldo_inicial_centavos: centavos(ativacao.value.saldo), justificativa_saldo_inicial: ativacao.value.justificativa } : {}),
  }), 'Gestão ativada.')
}
function nomearResponsavel() {
  executar(() => api.post(`/admin/financeiro/gestoes/${atual.value}/vinculos`, {
    user_id: Number(novoVinculo.value.user_id), cargo: novoVinculo.value.cargo,
    interino: novoVinculo.value.interino,
    ...(novoVinculo.value.interino ? { fim_previsto_em: novoVinculo.value.fim_previsto_em } : {}),
    justificativa: novoVinculo.value.justificativa,
  }), 'Responsável financeiro nomeado.')
}
function encerrarVinculo(id) {
  const justificativa = window.prompt('Justificativa do encerramento do vínculo (mínimo 10 caracteres):')
  if (!justificativa) return
  executar(() => api.post(`/admin/financeiro/gestoes/${atual.value}/vinculos/${id}/encerrar`, { justificativa }), 'Vínculo encerrado.')
}
function conciliar() {
  executar(() => api.post(`/admin/financeiro/gestoes/${atual.value}/conciliacao`, {
    saldo_real_final_centavos: centavos(conciliacao.value.saldo), justificativa: conciliacao.value.justificativa,
  }), 'Saldo real conciliado.')
}
function fecharMes() {
  executar(() => api.post(`/admin/financeiro/gestoes/${atual.value}/relatorios/fechar`, fechamento.value), 'Mês fechado. Agora você pode publicar o relatório.')
}
function publicar() {
  executar(() => api.post(`/admin/financeiro/gestoes/${atual.value}/relatorios`, fechamento.value), 'Relatório mensal publicado.')
}
function encerrar() {
  if (!window.confirm('Encerrar esta gestão? A operação bloqueia novos lançamentos.')) return
  executar(() => api.post(`/admin/financeiro/gestoes/${atual.value}/encerrar`, { observacao: observacao.value }), 'Gestão encerrada.')
}
onMounted(() => { carregar().catch(() => { erro.value = 'Falha ao carregar os dados financeiros.' }) })
</script>

<template>
  <main class="painel-financeiro">
    <PageHeader label="ADMIN" title="Prestação de Contas" subtitle="Controle financeiro do Grêmio Estudantil Athos Bulcão" />
    <p v-if="erro" class="mensagem erro" role="alert">{{ erro }}</p>
    <p v-if="aviso" class="mensagem aviso" role="status">{{ aviso }}</p>
    <nav class="abas" aria-label="Seções financeiras">
      <button v-for="item in [{ id: 'dashboard', nome: 'Dashboard', icone: 'mdi-chart-bar' }, { id: 'movimentacoes', nome: 'Movimentações', icone: 'mdi-wallet-outline' }, { id: 'relatorios', nome: 'Gerar PDF', icone: 'mdi-printer-outline' }, { id: 'historico', nome: 'Histórico', icone: 'mdi-history' }, { id: 'transicoes', nome: 'Transições', icone: 'mdi-swap-horizontal' }]" :key="item.id" type="button" :class="{ selecionada: aba === item.id }" @click="aba = item.id">
        <v-icon size="16">{{ item.icone }}</v-icon> {{ item.nome }}
      </button>
    </nav>
    <div class="barra-gestao">
      <div class="gestao-atual">
        <span class="rotulo">Gestão selecionada</span>
        <strong>{{ gestaoSelecionada?.nome || 'Nenhuma gestão cadastrada' }}</strong>
        <span v-if="gestaoSelecionada" class="estado" :class="gestaoSelecionada.status">{{ gestaoSelecionada.status }}</span>
      </div>
      <button type="button" class="btn-gestoes" @click="modalGestao = true"><v-icon size="18">mdi-office-building-cog-outline</v-icon> Gerenciar gestões</button>
    </div>
    <v-dialog v-model="modalGestao" max-width="520">
      <v-card class="gestao-dialog">
        <v-card-title class="dialog-titulo">Gerenciar gestões</v-card-title>
        <v-card-text class="dialog-conteudo">
          <p class="ajuda">Selecione uma gestão para consultar seu histórico financeiro.</p>
          <label for="gestao-modal">Gestão</label>
          <select id="gestao-modal" :value="atual || ''" @change="selecionar($event.target.value); modalGestao = false">
            <option value="" disabled>Selecione uma gestão</option>
            <option v-for="g in gestoes" :key="g.id" :value="g.id">{{ g.nome }} — {{ g.status }}</option>
          </select>
          <form v-if="!gestoes.some(g => g.status === 'ativa')" @submit.prevent="criarGestao" class="formulario modal-form">
            <h3>Iniciar nova gestão</h3>
            <label for="nome-gestao">Nome da gestão</label>
            <input id="nome-gestao" v-model="novaGestao.nome" required maxlength="255" placeholder="Ex.: Gestão 2026" />
            <label for="inicio-gestao">Data de início</label>
            <input id="inicio-gestao" v-model="novaGestao.inicio" required type="date" />
            <label for="fim-gestao">Data prevista de término</label>
            <input id="fim-gestao" v-model="novaGestao.fim_previsto" required type="date" :min="novaGestao.inicio" />
            <p class="ajuda">O saldo inicial é definido na ativação e herdado automaticamente quando existir gestão ou transição anterior.</p>
            <button :disabled="ocupado" type="submit">Criar gestão</button>
          </form>
          <p v-else class="ajuda">Encerre a gestão ativa antes de cadastrar outra.</p>
        </v-card-text>
        <v-card-actions><v-spacer /><button type="button" class="btn-secundario" @click="modalGestao = false">Fechar</button></v-card-actions>
      </v-card>
    </v-dialog>
    <section v-if="aba === 'transicoes'" class="bloco">
      <h2>Transições financeiras entre gestões</h2>
      <p class="ajuda">Uma transição só pode começar quando não existir gestão ativa ou outra transição ativa. O saldo inicial é herdado automaticamente.</p>
      <form v-if="!transicoes.some(t => t.status === 'ativa') && !gestoes.some(g => g.status === 'ativa')" class="formulario" @submit.prevent="iniciarTransicao">
        <h3>Iniciar transição</h3>
        <label for="transicao-inicio">Início</label><input id="transicao-inicio" v-model="novaTransicao.inicio" type="date" required />
        <label for="transicao-fim">Fim previsto</label><input id="transicao-fim" v-model="novaTransicao.fim_previsto" type="date" :min="novaTransicao.inicio" required />
        <label for="transicao-motivo">Motivo</label><textarea id="transicao-motivo" v-model.trim="novaTransicao.motivo" required minlength="10" maxlength="3000" />
        <button type="submit" :disabled="ocupado">Iniciar transição</button>
      </form>
      <template v-if="transicoes.length">
        <label for="transicao-selecao">Consultar transição</label>
        <select id="transicao-selecao" v-model.number="transicaoAtual" @change="carregarTransicoes">
          <option v-for="t in transicoes" :key="t.id" :value="t.id">#{{ t.id }} — {{ t.inicio }} a {{ t.fim_previsto }} ({{ t.status }})</option>
        </select>
        <div v-if="transicaoSelecionada" class="indicadores">
          <p>Saldo inicial <strong>{{ moeda(transicaoSelecionada.saldo_inicial_centavos) }}</strong></p>
          <p>Status <strong>{{ transicaoSelecionada.status }}</strong></p>
        </div>
        <div class="tabela-scroll">
          <table>
            <thead><tr><th>Data</th><th>Descrição</th><th>Tipo</th><th>Valor</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
              <tr v-for="m in movimentosTransicao" :key="m.id">
                <td>{{ m.data }}</td><td>{{ m.descricao }}</td><td>{{ m.tipo }}</td><td>{{ moeda(m.valor_centavos) }}</td><td>{{ m.status }}</td>
                <td class="acoes-linha">
                  <template v-if="m.status === 'pendente' && transicaoSelecionada?.status === 'ativa'">
                    <button type="button" :disabled="ocupado" @click="resolverMovimentoTransicao(m, 'confirmar')">Confirmar</button>
                    <button type="button" class="btn-perigo" :disabled="ocupado" @click="resolverMovimentoTransicao(m, 'cancelar')">Cancelar</button>
                  </template>
                </td>
              </tr>
              <tr v-if="!movimentosTransicao.length"><td colspan="6">Nenhuma movimentação nesta transição.</td></tr>
            </tbody>
          </table>
        </div>
        <p class="ajuda">A tabela exibe as 25 movimentações mais recentes.</p>
        <form v-if="transicaoSelecionada?.status === 'ativa'" class="formulario" @submit.prevent="criarMovimentoTransicao">
          <h3>Movimentação essencial da transição</h3>
          <label for="tm-data">Data</label><input id="tm-data" v-model="novaMovTransicao.data" type="date" :min="transicaoSelecionada.inicio" :max="transicaoSelecionada.fim_previsto" required />
          <label for="tm-tipo">Tipo</label><select id="tm-tipo" v-model="novaMovTransicao.tipo"><option value="receita">Receita</option><option value="despesa">Despesa</option></select>
          <label for="tm-categoria">Categoria</label><input id="tm-categoria" v-model.trim="novaMovTransicao.categoria" maxlength="100" required />
          <label for="tm-descricao">Descrição</label><input id="tm-descricao" v-model.trim="novaMovTransicao.descricao" maxlength="255" required />
          <label for="tm-essencial">Justificativa da essencialidade</label><textarea id="tm-essencial" v-model.trim="novaMovTransicao.justificativa_essencialidade" minlength="10" maxlength="3000" required />
          <label for="tm-valor">Valor em reais</label><input id="tm-valor" v-model="novaMovTransicao.valor" inputmode="decimal" placeholder="10,50" required />
          <button type="submit" :disabled="ocupado">Registrar movimentação</button>
        </form>
        <form v-if="transicaoSelecionada?.status === 'ativa'" class="formulario" @submit.prevent="encerrarTransicao">
          <h3>Encerrar transição e publicar relatório</h3>
          <p class="ajuda">Resolva todas as movimentações pendentes antes do encerramento.</p>
          <label for="tm-fim-real">Data de encerramento</label><input id="tm-fim-real" v-model="encerramentoTransicao.fim_real" type="date" :min="transicaoSelecionada.inicio" :max="transicaoSelecionada.fim_previsto" required />
          <label for="tm-saldo">Saldo real final (R$)</label><input id="tm-saldo" v-model="encerramentoTransicao.saldo" required />
          <label for="tm-conciliacao">Justificativa de eventual diferença</label><textarea id="tm-conciliacao" v-model.trim="encerramentoTransicao.justificativa_conciliacao" maxlength="3000" />
          <button type="submit" :disabled="ocupado">Encerrar e publicar relatório</button>
        </form>
        <a v-if="transicaoSelecionada?.status === 'encerrada'" :href="`/transparencia/prestacao-de-contas`">Consultar relatório no portal público</a>
      </template>
    </section>
    <template v-if="atual && totais">
      <section v-if="aba === 'dashboard'" class="bloco painel-resumo">
        <h2>Visão geral</h2>
        <div class="indicadores">
          <p>Saldo inicial <strong>{{ moeda(totais.saldo_inicial_centavos) }}</strong></p>
          <p>Receitas <strong class="texto-receita">{{ moeda(totais.receitas_centavos) }}</strong></p>
          <p>Despesas <strong class="texto-despesa">{{ moeda(totais.despesas_centavos) }}</strong></p>
          <p>Saldo calculado <strong>{{ moeda(totais.saldo_final_centavos) }}</strong></p>
        </div>
        <p class="ajuda">O saldo calculado considera apenas movimentações confirmadas e não representa necessariamente o saldo bancário.</p>
      </section>
      <section v-if="aba === 'movimentacoes'" class="bloco lista-movimentacoes">
        <h2 class="sr-only">Movimentações</h2>
        <div class="tabela-scroll">
          <table>
            <thead><tr><th>Data</th><th>Descrição</th><th>Tipo</th><th>Valor</th><th>Situação</th><th>Ação</th><th>Comprovantes</th></tr></thead>
            <tbody>
              <tr v-for="m in filtradas" :key="m.id">
                <td>{{ m.data }}</td><td>{{ m.descricao }}</td><td>{{ m.tipo }}</td>
                <td>{{ moeda(m.valor_centavos) }}</td><td>{{ m.status }}</td>
                <td class="acoes-linha">
                  <template v-if="m.status === 'pendente' && gestaoSelecionada?.status === 'ativa'">
                    <button type="button" :disabled="ocupado" @click="confirmar(m.id)">Confirmar</button>
                    <button type="button" class="btn-perigo" :disabled="ocupado" @click="cancelarMovimentacao(m)">Cancelar</button>
                  </template>
                  <button v-if="m.status === 'confirmada'" type="button" :disabled="ocupado" @click="abrirRetificacao(m)">Retificar</button>
                </td>
                <td>
                  <label v-if="gestaoSelecionada?.status === 'ativa'">Anexar
                    <input type="file" accept=".pdf,.jpg,.jpeg,.png" @change="anexarComprovante(m.id, $event)" />
                  </label>
                  <button type="button" @click="listarComprovantes(m.id)">Ver arquivos</button>
                  <ul v-if="comprovantes[m.id]">
                    <li v-for="c in comprovantes[m.id]" :key="c.id">
                      <button type="button" @click="baixarComprovante(c.id, c.nome_original)">{{ c.nome_original }}</button>
                    </li>
                  </ul>
                </td>
              </tr>
              <tr v-if="!filtradas.length"><td colspan="7">Nenhuma movimentação cadastrada.</td></tr>
            </tbody>
          </table>
        </div>
        <div v-if="ultimaPagina > 1" class="paginacao">
          <button type="button" class="btn-secundario" :disabled="pagina <= 1" @click="selecionar(atual, pagina - 1)">Anterior</button>
          <span>Página {{ pagina }} de {{ ultimaPagina }}</span>
          <button type="button" class="btn-secundario" :disabled="pagina >= ultimaPagina" @click="selecionar(atual, pagina + 1)">Próxima</button>
        </div>
      </section>
      <div v-if="aba === 'movimentacoes' && gestaoSelecionada?.status === 'ativa'" class="barra-acoes">
        <div class="botoes-lancamento">
          <button class="botao-receita" type="button" @click="abrirMovimentacao('receita')"><v-icon size="17">mdi-plus</v-icon> Nova receita</button>
          <button class="botao-despesa" type="button" @click="abrirMovimentacao('despesa')"><v-icon size="17">mdi-plus</v-icon> Nova despesa</button>
        </div>
        <label class="filtro">Filtrar <select v-model="filtroTipo"><option value="todas">Todas</option><option value="receita">Receitas</option><option value="despesa">Despesas</option></select></label>
      </div>
      <v-dialog v-model="modalMovimentacao" max-width="520">
        <v-card class="gestao-dialog">
          <v-card-title class="dialog-titulo">{{ nova.tipo === 'receita' ? 'Nova receita' : 'Nova despesa' }}</v-card-title>
          <v-card-text class="dialog-conteudo">
            <form id="nova-movimentacao" class="modal-form" @submit.prevent="adicionar">
              <label for="mov-data">Data</label><input id="mov-data" v-model="nova.data" type="date" required />
              <label for="mov-tipo">Tipo</label><select id="mov-tipo" v-model="nova.tipo"><option value="receita">Receita</option><option value="despesa">Despesa</option></select>
              <label for="mov-categoria">Categoria</label><input id="mov-categoria" v-model.trim="nova.categoria" required maxlength="100" placeholder="Categoria" />
              <label for="mov-descricao">Descrição</label><textarea id="mov-descricao" v-model.trim="nova.descricao" required maxlength="255" rows="3" placeholder="Descreva a movimentação" />
              <label for="mov-valor">Valor em reais</label><input id="mov-valor" :value="nova.valor" required inputmode="numeric" @input="formatarReais($event, 'movimentacao')" placeholder="R$ 0,00" />
              <div class="acoes-modal"><button class="btn-secundario" type="button" @click="modalMovimentacao = false">Cancelar</button><button type="submit" :disabled="ocupado">Registrar</button></div>
            </form>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="modalRetificacao" max-width="540">
        <v-card class="gestao-dialog">
          <v-card-title class="dialog-titulo">Retificar movimentação #{{ retificacao.id }}</v-card-title>
          <v-card-text class="dialog-conteudo">
            <p class="ajuda">Disponível somente para movimentações confirmadas de meses publicados. A retificação preserva as versões anteriores dos relatórios e atualiza os meses afetados.</p>
            <form class="modal-form" @submit.prevent="salvarRetificacao">
              <label for="retificacao-descricao">Descrição corrigida</label>
              <input id="retificacao-descricao" v-model.trim="retificacao.descricao" required maxlength="255" />
              <label for="retificacao-valor">Valor corrigido (R$)</label>
              <input id="retificacao-valor" v-model="retificacao.valor" required inputmode="decimal" placeholder="10,50" />
              <label for="retificacao-justificativa">Justificativa da retificação</label>
              <textarea id="retificacao-justificativa" v-model.trim="retificacao.justificativa" required minlength="10" maxlength="3000" rows="4" />
              <div class="acoes-modal">
                <button type="button" class="btn-secundario" @click="modalRetificacao = false">Voltar</button>
                <button type="submit" :disabled="ocupado">Salvar retificação</button>
              </div>
            </form>
          </v-card-text>
        </v-card>
      </v-dialog>
      <section v-if="aba === 'dashboard'" class="bloco">
        <h2>Responsáveis e ativação</h2>
        <p v-if="gestaoSelecionada?.status === 'planejada'">Esta gestão está planejada. Nomeie exatamente um Tesoureiro-Geral titular antes de ativá-la.</p>
        <div v-if="gestaoSelecionada?.status !== 'encerrada'" class="formulario">
          <h3>Nomear responsável financeiro</h3>
          <form class="formulario" @submit.prevent="nomearResponsavel">
            <label for="vinculo-usuario">ID do usuário cadastrado</label><input id="vinculo-usuario" v-model="novoVinculo.user_id" type="number" min="1" required />
            <label for="vinculo-cargo">Cargo</label><select id="vinculo-cargo" v-model="novoVinculo.cargo"><option value="tesoureiro_geral">Tesoureiro-Geral</option><option value="tesoureiro">Tesoureiro</option></select>
            <label><input v-model="novoVinculo.interino" type="checkbox" :disabled="novoVinculo.cargo !== 'tesoureiro_geral'" /> Nomeação interina</label>
            <template v-if="novoVinculo.interino"><label for="vinculo-prazo">Fim da interinidade</label><input id="vinculo-prazo" v-model="novoVinculo.fim_previsto_em" type="datetime-local" required /></template>
            <label for="vinculo-justificativa">Justificativa</label><textarea id="vinculo-justificativa" v-model="novoVinculo.justificativa" minlength="10" maxlength="2000" required />
            <button type="submit" :disabled="ocupado">Nomear</button>
          </form>
        </div>
        <ul><li v-for="v in vinculos" :key="v.id">Usuário #{{ v.user_id }} — {{ v.cargo }}{{ v.interino ? ' (interino)' : '' }} — {{ v.fim_em ? 'encerrado' : 'vigente' }} <button v-if="!v.fim_em && gestaoSelecionada?.status !== 'encerrada'" :disabled="ocupado" type="button" @click="encerrarVinculo(v.id)">Encerrar vínculo</button></li></ul>
        <form v-if="gestaoSelecionada?.status === 'planejada'" class="formulario" @submit.prevent="ativarGestao">
          <h3>Ativar gestão</h3>
          <template v-if="inicialManual"><label for="saldo-ativacao">Saldo inicial excepcional</label><input id="saldo-ativacao" v-model="ativacao.saldo" required /><label for="justificativa-ativacao">Justificativa, se houver saldo inicial</label><textarea id="justificativa-ativacao" v-model="ativacao.justificativa" /></template>
          <button type="submit" :disabled="ocupado">Ativar gestão</button>
        </form>
      </section>
      <section v-if="aba === 'dashboard' && gestaoSelecionada?.status === 'ativa'" class="bloco">
        <h2>Conciliação do saldo real</h2>
        <form class="formulario" @submit.prevent="conciliar"><label for="saldo-real">Saldo real em reais</label><input id="saldo-real" v-model="conciliacao.saldo" required /><label for="justificativa-conciliacao">Justificativa para eventual diferença</label><textarea id="justificativa-conciliacao" v-model="conciliacao.justificativa" maxlength="2000" /><button type="submit" :disabled="ocupado">Registrar conciliação</button></form>
      </section>
      <section v-if="aba === 'relatorios'" class="bloco">
        <h2>Relatórios mensais</h2>
        <p class="ajuda">O mês deve ser fechado antes da publicação do relatório.</p>
        <form class="formulario" @submit.prevent="publicar">
          <label for="relatorio-ano">Ano</label>
          <input id="relatorio-ano" v-model.number="fechamento.ano" type="number" min="2000" max="2100" required />
          <label for="relatorio-mes">Mês</label>
          <input id="relatorio-mes" v-model.number="fechamento.mes" type="number" min="1" max="12" required />
          <div class="barra-acoes"><button type="button" :disabled="ocupado" @click="fecharMes">1. Fechar mês</button><button type="submit" :disabled="ocupado">2. Publicar relatório fechado</button></div>
        </form>
      </section>
      <section v-if="aba === 'historico'" class="bloco">
        <h2>Histórico da gestão</h2>
        <p>Consulte o histórico e os documentos publicados no portal de transparência.</p>
        <router-link to="/transparencia/prestacao-de-contas">Consultar prestação de contas</router-link>
        <form v-if="gestaoSelecionada?.status === 'encerrada'" class="formulario" @submit.prevent="publicarNota">
          <h3>Publicar nota de esclarecimento</h3>
          <p class="ajuda">Notas são públicas e exclusivas de gestões encerradas.</p>
          <label for="nota-relatorio">ID do relatório relacionado (opcional)</label>
          <input id="nota-relatorio" v-model="nota.relatorio_financeiro_id" type="number" min="1" />
          <label for="nota-texto">Esclarecimento</label>
          <textarea id="nota-texto" v-model.trim="nota.texto" required minlength="10" maxlength="10000" rows="5" />
          <button type="submit" :disabled="ocupado">Publicar esclarecimento</button>
        </form>
        <template v-if="gestaoSelecionada?.status === 'ativa'">
          <label for="observacao-encerramento">Observação do encerramento</label>
          <textarea id="observacao-encerramento" v-model="observacao" rows="3" />
          <button type="button" :disabled="ocupado" @click="encerrar">Solicitar encerramento</button>
        </template>
      </section>
    </template>
  </main>
</template>

<style scoped>
.painel-financeiro { max-width: 1200px; margin: auto; padding: 24px; color: #0b132b; }
.abas, .barra-gestao, .barra-acoes, .botoes-lancamento { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; margin: 16px 0; }
.abas button, button, .btn-gestoes { cursor: pointer; border: 1px solid #cbd5e1; border-radius: 10px; padding: 9px 14px; background: white; color: #0b132b; }
.abas .selecionada { background: #16509b; color: white; }
button:disabled { opacity: .55; cursor: not-allowed; }
.bloco { background: white; border: 1px solid #dbe3ef; border-radius: 16px; padding: 20px; margin: 16px 0; }
.indicadores { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; }
.indicadores p { background: #f4f7fc; padding: 16px; border-radius: 12px; }
.indicadores strong { display: block; font-size: 20px; margin-top: 8px; }
.tabela-scroll { overflow-x: auto; }
.acoes-linha { white-space: nowrap; }
.acoes-linha button + button { margin-left: 6px; }
.btn-perigo { color: #9f1239; border-color: #fecdd3; }
.acoes-modal { display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-end; }
table { width: 100%; border-collapse: collapse; min-width: 720px; }
td, th { padding: 10px; border-bottom: 1px solid #dbe3ef; text-align: left; }
.mensagem { padding: 12px; border-radius: 8px; }
.erro { color: #a52020; background: #fff0f0; }
.aviso { color: #166534; background: #f0fdf4; }
.modal-form, .formulario { display: grid; gap: 10px; }
input, select, textarea { border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px; max-width: 100%; }
.dialog-conteudo { display: grid; gap: 12px; }
.ajuda, .rotulo { color: #53647c; }
.paginacao { display: flex; align-items: center; gap: 12px; margin: 14px 0; }
@media (max-width: 600px) { .painel-financeiro { padding: 16px; } }
</style>
