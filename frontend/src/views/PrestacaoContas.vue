<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import api from '@/services/api'
import PageHeader from '@/components/common/PageHeader.vue'
import BackButton from '@/components/common/BackButton.vue'

const gestoes = ref([])
const gestaoId = ref('')
const relatorios = ref([])
const versoes = ref({})
const esclarecimentos = ref([])
const inatividade = ref(null)
const carregando = ref(true)
const carregandoGestao = ref(false)
const erro = ref('')
const ano = ref('todos')
const moeda = (centavos) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(centavos || 0) / 100)
const dataBR = (data) => data ? new Date(String(data).slice(0, 10) + 'T12:00:00').toLocaleDateString('pt-BR') : '—'
const gestao = computed(() => gestoes.value.find((item) => String(item.id) === String(gestaoId.value)))
const anos = computed(() => [...new Set(relatorios.value.map((r) => r.ano))].sort((a, b) => b - a))
const exibidos = computed(() => relatorios.value.filter((r) => ano.value === 'todos' || r.ano === Number(ano.value)))
const resumo = computed(() => {
  const lista = relatorios.value
  return {
    receitas: lista.reduce((total, r) => total + Number(r.receitas_centavos || 0), 0),
    despesas: lista.reduce((total, r) => total + Number(r.despesas_centavos || 0), 0),
    saldo: lista.length ? lista[lista.length - 1].saldo_final_centavos : null,
  }
})
const pdfRelatorio = (id, versao = null) => versao === null
  ? `/api/financeiro/relatorios/${id}/pdf`
  : `/api/financeiro/relatorios/${id}/versoes/${versao}/pdf`

async function carregarGestao(id) {
  if (!id) return
  carregandoGestao.value = true
  erro.value = ''
  relatorios.value = []
  versoes.value = {}
  esclarecimentos.value = []
  inatividade.value = null
  ano.value = 'todos'
  try {
    const [resRelatorios, resNotas, resInatividade] = await Promise.all([
      api.get(`/financeiro/gestoes/${id}/relatorios`),
      api.get(`/financeiro/gestoes/${id}/esclarecimentos`),
      api.get(`/financeiro/gestoes/${id}/inatividade`).catch((e) => {
        if (e.response?.status === 404) return { data: null }
        throw e
      }),
    ])
    // A API publica os relatórios em ordem cronológica.
    relatorios.value = Array.isArray(resRelatorios.data) ? resRelatorios.data : []
    esclarecimentos.value = Array.isArray(resNotas.data) ? resNotas.data : []
    inatividade.value = resInatividade.data
  } catch {
    erro.value = 'Não foi possível consultar esta gestão. Tente novamente.'
  } finally {
    carregandoGestao.value = false
  }
}
async function consultarVersoes(relatorio) {
  if (versoes.value[relatorio.id]) {
    const copia = { ...versoes.value }
    delete copia[relatorio.id]
    versoes.value = copia
    return
  }
  try {
    const { data } = await api.get(`/financeiro/relatorios/${relatorio.id}/versoes`)
    versoes.value = { ...versoes.value, [relatorio.id]: Array.isArray(data) ? data : [] }
  } catch {
    erro.value = 'Não foi possível consultar o histórico de versões.'
  }
}
watch(gestaoId, (id) => carregarGestao(id))
onMounted(async () => {
  try {
    const { data } = await api.get('/financeiro/gestoes')
    gestoes.value = Array.isArray(data) ? data : []
    if (gestoes.value.length) gestaoId.value = String(gestoes.value[0].id)
  } catch {
    erro.value = 'Não foi possível carregar as gestões financeiras.'
  } finally {
    carregando.value = false
  }
})
</script>

<template>
  <main class="financeiro-publico">
    <PageHeader label="ABSL" title="Prestação de Contas" subtitle="Consulte as informações financeiras publicadas pelo Grêmio Athos Bulcão." />
    <BackButton to="/transparencia" label="Voltar à transparência" />

    <p v-if="carregando" role="status">Carregando gestões financeiras...</p>
    <p v-else-if="erro" role="alert" class="erro">{{ erro }}</p>
    <p v-if="!carregando && !gestoes.length && !erro">Ainda não há gestões financeiras disponíveis para consulta.</p>

    <template v-if="gestoes.length">
      <div class="filtros">
        <label for="gestao-financeira">Gestão</label>
        <select id="gestao-financeira" v-model="gestaoId">
          <option v-for="item in gestoes" :key="item.id" :value="String(item.id)">{{ item.nome }}</option>
        </select>
        <label for="ano-financeiro">Ano</label>
        <select id="ano-financeiro" v-model="ano">
          <option value="todos">Todos</option>
          <option v-for="item in anos" :key="item" :value="String(item)">{{ item }}</option>
        </select>
      </div>

      <section v-if="gestao" class="bloco" aria-label="Informações da gestão">
        <h2>{{ gestao.nome }}</h2>
        <p>{{ dataBR(gestao.inicio) }} a {{ dataBR(gestao.fim_previsto) }} · {{ gestao.status === 'encerrada' ? 'Encerrada' : 'Ativa' }}</p>
        <div class="indicadores">
          <div><span>Saldo inicial</span><strong>{{ moeda(gestao.saldo_inicial_centavos) }}</strong></div>
          <div><span>Receitas publicadas</span><strong>{{ moeda(resumo.receitas) }}</strong></div>
          <div><span>Despesas publicadas</span><strong>{{ moeda(resumo.despesas) }}</strong></div>
          <div><span>Último saldo publicado</span><strong>{{ resumo.saldo === null ? 'Sem relatório' : moeda(resumo.saldo) }}</strong></div>
        </div>
        <p class="observacao">Os valores resumem somente relatórios publicados. O saldo contábil pode diferir do saldo real conciliado.</p>
        <p v-if="gestao.status === 'encerrada' && gestao.saldo_real_final_centavos !== null">Saldo real conciliado no encerramento: <strong>{{ moeda(gestao.saldo_real_final_centavos) }}</strong></p>
      </section>

      <p v-if="carregandoGestao" role="status">Consultando relatórios e documentos...</p>
      <template v-else>
        <section class="bloco">
          <h2>Relatórios mensais</h2>
          <p v-if="!exibidos.length">Nenhum relatório publicado para o período selecionado.</p>
          <div v-else class="relatorios">
            <article v-for="r in exibidos" :key="r.id" class="relatorio">
              <h3>{{ String(r.mes).padStart(2, '0') }}/{{ r.ano }}</h3>
              <dl>
                <div><dt>Saldo inicial</dt><dd>{{ moeda(r.saldo_inicial_centavos) }}</dd></div>
                <div><dt>Receitas</dt><dd>{{ moeda(r.receitas_centavos) }}</dd></div>
                <div><dt>Despesas</dt><dd>{{ moeda(r.despesas_centavos) }}</dd></div>
                <div><dt>Saldo final</dt><dd>{{ moeda(r.saldo_final_centavos) }}</dd></div>
              </dl>
              <p class="observacao">Publicado em {{ dataBR(r.publicado_em) }} · Versão {{ r.versao }}</p>
              <div class="acoes">
                <a :href="pdfRelatorio(r.id)" target="_blank" rel="noopener noreferrer">Abrir PDF</a>
                <button type="button" :aria-expanded="Boolean(versoes[r.id])" @click="consultarVersoes(r)">Histórico de versões</button>
              </div>
              <ul v-if="versoes[r.id]" class="versoes">
                <li v-for="v in versoes[r.id]" :key="v.versao">
                  <a :href="pdfRelatorio(r.id, v.versao)" target="_blank" rel="noopener noreferrer">Versão {{ v.versao }} (PDF)</a>
                  <span v-if="v.justificativa"> — {{ v.justificativa }}</span>
                </li>
                <li v-if="!versoes[r.id].length">Nenhuma versão disponível.</li>
              </ul>
            </article>
          </div>
        </section>
        <section v-if="inatividade" class="bloco">
          <h2>Declaração de inatividade</h2>
          <p>Esta gestão possui uma declaração de inatividade publicada.</p>
          <a :href="`/api/financeiro/gestoes/${gestaoId}/inatividade/pdf`" target="_blank" rel="noopener noreferrer">Consultar declaração (PDF)</a>
        </section>
        <section v-if="esclarecimentos.length" class="bloco">
          <h2>Notas de esclarecimento</h2>
          <article v-for="nota in esclarecimentos" :key="nota.id" class="nota">
            <p>{{ nota.texto }}</p>
            <small>Publicado em {{ dataBR(nota.publicado_em) }}</small>
          </article>
        </section>
      </template>
    </template>
  </main>
</template>

<style scoped>
.financeiro-publico { padding: 24px; max-width: 1100px; margin: auto; color: #0b132b; }
.voltar { display: inline-block; margin: 12px 0 24px; color: #16509b; }
.filtros { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px; }
select { border: 1px solid #aab9d1; border-radius: 10px; padding: 9px 12px; background: #fff; max-width: 100%; }
.bloco { border: 1px solid #dbe3ef; border-radius: 16px; padding: 20px; margin-bottom: 20px; background: #fff; }
.bloco h2 { font-size: 21px; margin: 0 0 12px; }
.indicadores { display: grid; grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); gap: 12px; margin: 18px 0; }
.indicadores div { background: #f4f7fc; border-radius: 12px; padding: 14px; display: grid; gap: 8px; }
.indicadores span, .observacao, small { color: #53647c; font-size: 13px; }
.indicadores strong { font-size: 19px; }
.relatorios { display: grid; grid-template-columns: repeat(auto-fit,minmax(min(100%,280px),1fr)); gap: 16px; }
.relatorio { border: 1px solid #dbe3ef; border-radius: 14px; padding: 18px; }
.relatorio h3 { margin: 0 0 12px; font-size: 19px; }
.relatorio dl { margin: 0 0 12px; }
.relatorio dl div { display: flex; justify-content: space-between; gap: 12px; padding: 8px 0; border-bottom: 1px solid #eef2f7; }
.relatorio dt { color: #53647c; }
.relatorio dd { margin: 0; font-weight: 700; text-align: right; }
.acoes { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; }
.acoes a, .bloco > a, .versoes a { color: #16509b; font-weight: 600; }
.acoes button { background: transparent; border: 1px solid #aab9d1; border-radius: 8px; padding: 7px 10px; cursor: pointer; color: #16509b; }
.versoes { padding-left: 20px; overflow-wrap: anywhere; }
.versoes li { margin: 8px 0; }
.nota { border-top: 1px solid #dbe3ef; padding: 12px 0; }
.nota p { white-space: pre-wrap; overflow-wrap: anywhere; }
.erro { color: #a52020; }
@media(max-width:480px) { .financeiro-publico { padding: 16px; } .bloco { padding: 15px; } }
</style>
