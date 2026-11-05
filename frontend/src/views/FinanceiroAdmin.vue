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
const novaGestao = ref({ nome: '', inicio: '', saldo: '0,00' })
const nova = ref({ data: '', tipo: 'receita', categoria: '', descricao: '', valor: '' })
const fechamento = ref({ ano: new Date().getFullYear(), mes: new Date().getMonth() + 1 })
const observacao = ref('')
const comprovantes = ref({})
const modalGestao = ref(false)
const modalMovimentacao = ref(false)
const aba = ref('movimentacoes')
const filtroTipo = ref('todas')
const filtradas = computed(() => movimentacoes.value.filter(m => filtroTipo.value === 'todas' || m.tipo === filtroTipo.value))
const pagina = ref(1)
const ultimaPagina = ref(1)
const inicialManual = computed(() => gestoes.value.length === 0)
const formatadorBRL = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' })
const moeda = (centavos) => formatadorBRL.format(Number(centavos || 0) / 100)
function formatarReais(event, campo) {
  const digitos = event.target.value.replace(/\D/g, '').slice(0, 13)
  const valor = formatadorBRL.format(Number(digitos || 0) / 100)
  if (campo === 'gestao') novaGestao.value.saldo = valor
  else nova.value.valor = valor
}
function centavos(valor) {
  const texto = String(valor).trim().replace(/R\$\s?/g, '').replace(/\./g, '').replace(',', '.')
  if (!/^\d+(\.\d{1,2})?$/.test(texto)) throw new Error('Informe um valor válido, como 10,50.')
  return Math.round(Number(texto) * 100)
}
async function carregar() {
  const { data } = await api.get('/admin/financeiro/gestoes')
  gestoes.value = data
  if (!atual.value && data.length) atual.value = data[0].id
  if (atual.value) await selecionar(atual.value, pagina.value)
}
async function selecionar(id, numeroPagina = 1) {
  pagina.value = numeroPagina
  atual.value = Number(id)
  const [resumo, lista] = await Promise.all([
    api.get(`/admin/financeiro/gestoes/${id}/resumo`),
    api.get(`/admin/financeiro/gestoes/${id}/movimentacoes`, { params: { page: numeroPagina } }),
  ])
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
      nome: novaGestao.value.nome, inicio: novaGestao.value.inicio,
      ...(inicialManual.value ? { saldo_inicial_centavos: centavos(novaGestao.value.saldo) } : {}),
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
      <button v-for="item in [{ id: 'dashboard', nome: 'Dashboard', icone: 'mdi-chart-bar' }, { id: 'movimentacoes', nome: 'Movimentações', icone: 'mdi-wallet-outline' }, { id: 'relatorios', nome: 'Gerar PDF', icone: 'mdi-printer-outline' }, { id: 'historico', nome: 'Histórico', icone: 'mdi-history' }]" :key="item.id" type="button" :class="{ selecionada: aba === item.id }" @click="aba = item.id">
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
            <template v-if="inicialManual">
              <label for="saldo-abertura">Saldo inicial da primeira gestão</label>
              <input id="saldo-abertura" :value="novaGestao.saldo" inputmode="numeric" @input="formatarReais($event, 'gestao')" required />
            </template>
            <p v-else class="ajuda">O saldo inicial será transferido automaticamente da gestão anterior encerrada.</p>
            <button :disabled="ocupado" type="submit">Criar gestão</button>
          </form>
          <p v-else class="ajuda">Para criar outra gestão, primeiro encerre a gestão ativa.</p>
        </v-card-text>
        <v-card-actions><v-spacer /><button type="button" class="btn-secundario" @click="modalGestao = false">Fechar</button></v-card-actions>
      </v-card>
    </v-dialog>
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
                <td><button v-if="m.status === 'pendente' && gestaoSelecionada?.status === 'ativa'" :disabled="ocupado" @click="confirmar(m.id)">Confirmar</button></td>
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

      <section v-if="aba === 'relatorios'" class="bloco">
        <h2>Relatórios mensais</h2>
        <p class="ajuda">O mês deve ser fechado antes da publicação do relatório.</p>
        <form class="formulario" @submit.prevent="publicar">
          <label for="relatorio-ano">Ano</label>
          <input id="relatorio-ano" v-model.number="fechamento.ano" type="number" min="2000" max="2100" required />
          <label for="relatorio-mes">Mês</label>
          <input id="relatorio-mes" v-model.number="fechamento.mes" type="number" min="1" max="12" required />
          <button type="submit" :disabled="ocupado">Publicar relatório fechado</button>
        </form>
      </section>
      <section v-if="aba === 'historico'" class="bloco">
        <h2>Histórico da gestão</h2>
        <p>Consulte o histórico e os documentos publicados no portal de transparência.</p>
        <router-link to="/transparencia/prestacao-de-contas">Consultar prestação de contas</router-link>
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
