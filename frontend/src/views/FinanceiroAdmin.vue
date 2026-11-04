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
  catch (e) { erro.value = e.response?.data?.message || e.message || 'Não foi possível concluir a operação.' }
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
  }, 'Movimentação registrada como pendente.')
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
    <PageHeader label="Administração" title="Controle Financeiro" subtitle="Gestões, receitas, despesas e prestação de contas." />
    <router-link to="/transparencia">← Transparência</router-link>
    <p v-if="erro" role="alert" class="erro">{{ erro }}</p>
    <p v-if="aviso" role="status" class="aviso">{{ aviso }}</p>
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
      <section class="bloco">
        <h2>Resumo financeiro</h2>
        <div class="indicadores">
          <p>Saldo inicial <strong>{{ moeda(totais.saldo_inicial_centavos) }}</strong></p>
          <p>Receitas <strong>{{ moeda(totais.receitas_centavos) }}</strong></p>
          <p>Despesas <strong>{{ moeda(totais.despesas_centavos) }}</strong></p>
          <p>Saldo calculado <strong>{{ moeda(totais.saldo_final_centavos) }}</strong></p>
        </div>
      </section>
      <section v-if="gestaoSelecionada?.status === 'ativa'" class="bloco">
        <h2>Nova movimentação</h2>
        <form class="formulario" @submit.prevent="adicionar">
          <input v-model="nova.data" type="date" required aria-label="Data da movimentação" />
          <select v-model="nova.tipo" aria-label="Tipo"><option value="receita">Receita</option><option value="despesa">Despesa</option></select>
          <input v-model="nova.categoria" required maxlength="100" placeholder="Categoria" aria-label="Categoria" />
          <input v-model="nova.descricao" required maxlength="255" placeholder="Descrição" aria-label="Descrição" />
          <input :value="nova.valor" required inputmode="numeric" @input="formatarReais($event, 'movimentacao')" placeholder="R$ 0,00" aria-label="Valor em reais" />
          <button :disabled="ocupado">Registrar</button>
        </form>
      </section>
      <section class="bloco">
        <h2>Movimentações recentes</h2>
        <div class="tabela-scroll">
          <table>
            <thead><tr><th>Data</th><th>Descrição</th><th>Tipo</th><th>Valor</th><th>Situação</th><th>Ação</th><th>Comprovantes</th></tr></thead>
            <tbody>
              <tr v-for="m in movimentacoes" :key="m.id">
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
              <tr v-if="!movimentacoes.length"><td colspan="7">Nenhuma movimentação cadastrada.</td></tr>
            </tbody>
          </table>
        </div>
        <div v-if="ultimaPagina > 1" class="paginacao">
          <button type="button" class="btn-secundario" :disabled="pagina <= 1" @click="selecionar(atual, pagina - 1)">Anterior</button>
          <span>Página {{ pagina }} de {{ ultimaPagina }}</span>
          <button type="button" class="btn-secundario" :disabled="pagina >= ultimaPagina" @click="selecionar(atual, pagina + 1)">Próxima</button>
        </div>
      </section>
      <section v-if="gestaoSelecionada?.status === 'ativa'" class="bloco">
        <h2>Publicar relatório mensal</h2>
        <form class="formulario" @submit.prevent="publicar">
          <input v-model.number="fechamento.ano" type="number" min="2000" max="2100" required aria-label="Ano do relatório" />
          <input v-model.number="fechamento.mes" type="number" min="1" max="12" required aria-label="Mês do relatório" />
          <button :disabled="ocupado">Publicar relatório</button>
        </form>
        <h2>Encerrar gestão</h2>
        <form class="formulario" @submit.prevent="encerrar">
          <textarea v-model="observacao" required minlength="10" maxlength="2000" placeholder="Justificativa do encerramento" aria-label="Justificativa do encerramento" />
          <button class="perigo" :disabled="ocupado">Encerrar gestão</button>
        </form>
      </section>
    </template>
  </main>
</template>

<style scoped>
.painel-financeiro { font-family: 'DM Sans', sans-serif; }
.barra-gestao { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; padding: 18px 22px; background: #eef3fb; border: 1px solid rgba(13,31,60,.1); border-radius: 16px; margin-top: 20px; }
.gestao-atual { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.rotulo { font-size: 12px; color: #5a6a85; }
.gestao-atual strong { color: #0d1f3c; font-size: 16px; }
.estado { border-radius: 999px; padding: 4px 10px; font-size: 12px; background: #e4e8ef; text-transform: capitalize; }
.estado.ativa { background: #dcfce7; color: #166534; }
.btn-gestoes { display: inline-flex; align-items: center; gap: 8px; }
.gestao-dialog { border-radius: 18px !important; font-family: 'DM Sans', sans-serif; padding: 8px; }
.dialog-titulo { color: #0d1f3c; font-weight: 700; padding: 18px 20px 4px; }
.dialog-conteudo { display: flex; flex-direction: column; gap: 10px; padding: 14px 20px; }
.dialog-conteudo label { color: #5a6a85; font-size: 12px; font-weight: 600; }
.dialog-conteudo input, .dialog-conteudo select { width: 100%; background: #eef3fb; box-sizing: border-box; }
.modal-form { display: flex; flex-direction: column; gap: 10px; border-top: 1px solid #e1e7f0; padding-top: 14px; }
.modal-form h3 { margin: 0; font-size: 16px; }
.ajuda { color: #5a6a85; font-size: 13px; margin: 0; line-height: 1.6; }
.btn-secundario { background: #eef3fb; color: #0d1f3c; border: 1px solid #d3deee; }
.paginacao { display: flex; align-items: center; justify-content: center; gap: 16px; margin-top: 16px; font-size: 13px; }
@media (max-width: 600px) { .barra-gestao { align-items: stretch; } .btn-gestoes { justify-content: center; } }

.painel-financeiro { max-width: 1100px; margin: auto; padding: 24px; color: #0b132b; }
.bloco { padding: 20px; margin: 20px 0; border: 1px solid #dce4f0; border-radius: 16px; }
.formulario { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 16px; }
input,select,textarea { padding: 10px; border: 1px solid #b4c0d3; border-radius: 8px; max-width: 100%; }
button { background: #16509b; color: white; padding: 10px 16px; border: 0; border-radius: 8px; cursor: pointer; }
button:disabled { opacity: .5; cursor: not-allowed; }
.perigo { background: #9c2020; }
.indicadores { display: grid; grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); gap: 12px; }
.indicadores p { background: #f1f5fa; padding: 16px; border-radius: 10px; }
.indicadores strong { display: block; margin-top: 8px; }
.tabela-scroll { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th,td { text-align: left; padding: 10px; border-bottom: 1px solid #dce4f0; }
.erro { color: #a11; }.aviso { color: #176c39; }
@media(max-width:480px) { .painel-financeiro { padding: 16px; } }
</style>
