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
const moeda = (centavos) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(centavos || 0) / 100)
function centavos(valor) {
  const texto = String(valor).trim().replace(/\./g, '').replace(',', '.')
  if (!/^\d+(\.\d{1,2})?$/.test(texto)) throw new Error('Informe um valor válido, como 10,50.')
  return Math.round(Number(texto) * 100)
}
async function carregar() {
  const { data } = await api.get('/admin/financeiro/gestoes')
  gestoes.value = data
  if (!atual.value && data.length) atual.value = data[0].id
  if (atual.value) await selecionar(atual.value)
}
async function selecionar(id) {
  atual.value = Number(id)
  const [resumo, lista] = await Promise.all([
    api.get(`/admin/financeiro/gestoes/${id}/resumo`),
    api.get(`/admin/financeiro/gestoes/${id}/movimentacoes`),
  ])
  totais.value = resumo.data.totais
  movimentacoes.value = lista.data.data || []
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
      saldo_inicial_centavos: centavos(novaGestao.value.saldo),
    })
    atual.value = null
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
    <section class="bloco">
      <h2>Gestões</h2>
      <label for="gestao">Selecionar gestão</label>
      <select id="gestao" :value="atual || ''" @change="selecionar($event.target.value)">
        <option value="" disabled>Selecione</option>
        <option v-for="g in gestoes" :key="g.id" :value="g.id">{{ g.nome }} — {{ g.status }}</option>
      </select>
      <form v-if="!gestoes.some(g => g.status === 'ativa')" @submit.prevent="criarGestao" class="formulario">
        <h3>Nova gestão</h3>
        <input v-model="novaGestao.nome" required maxlength="255" placeholder="Nome da gestão" aria-label="Nome da gestão" />
        <input v-model="novaGestao.inicio" required type="date" aria-label="Início da gestão" />
        <input v-model="novaGestao.saldo" required inputmode="decimal" placeholder="Saldo inicial em reais" aria-label="Saldo inicial em reais" />
        <button :disabled="ocupado">Criar gestão</button>
      </form>
    </section>
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
          <input v-model="nova.valor" required inputmode="decimal" placeholder="Valor em reais" aria-label="Valor em reais" />
          <button :disabled="ocupado">Registrar</button>
        </form>
      </section>
      <section class="bloco">
        <h2>Movimentações recentes</h2>
        <div class="tabela-scroll">
          <table>
            <thead><tr><th>Data</th><th>Descrição</th><th>Tipo</th><th>Valor</th><th>Situação</th><th>Ação</th></tr></thead>
            <tbody>
              <tr v-for="m in movimentacoes" :key="m.id">
                <td>{{ m.data }}</td><td>{{ m.descricao }}</td><td>{{ m.tipo }}</td>
                <td>{{ moeda(m.valor_centavos) }}</td><td>{{ m.status }}</td>
                <td><button v-if="m.status === 'pendente' && gestaoSelecionada?.status === 'ativa'" :disabled="ocupado" @click="confirmar(m.id)">Confirmar</button></td>
              </tr>
              <tr v-if="!movimentacoes.length"><td colspan="6">Nenhuma movimentação cadastrada.</td></tr>
            </tbody>
          </table>
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
