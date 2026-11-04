<script setup>
import { computed, onMounted, ref } from 'vue'
import api from '@/services/api'
import PageHeader from '@/components/common/PageHeader.vue'

const relatorios = ref([])
const carregando = ref(true)
const erro = ref('')
const ano = ref('todos')
const moeda = (centavos) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(centavos || 0) / 100)
const anos = computed(() => [...new Set(relatorios.value.map((r) => r.ano))].sort((a, b) => b - a))
const exibidos = computed(() => relatorios.value.filter((r) => ano.value === 'todos' || r.ano === Number(ano.value)))
onMounted(async () => {
  try {
    const { data } = await api.get('/financeiro/relatorios')
    relatorios.value = Array.isArray(data) ? data : data.data || []
  } catch {
    erro.value = 'Não foi possível carregar os relatórios.'
  } finally {
    carregando.value = false
  }
})
</script>

<template>
  <main class="financeiro-publico">
    <PageHeader label="ABSL" title="Prestação de Contas" subtitle="Relatórios financeiros mensais do Grêmio Athos Bulcão." />
    <router-link to="/transparencia" class="voltar">← Voltar à transparência</router-link>
    <div class="filtros">
      <label for="ano-financeiro">Ano</label>
      <select id="ano-financeiro" v-model="ano">
        <option value="todos">Todos os anos</option>
        <option v-for="item in anos" :key="item" :value="String(item)">{{ item }}</option>
      </select>
    </div>
    <p v-if="carregando" role="status">Carregando relatórios...</p>
    <p v-else-if="erro" role="alert">{{ erro }}</p>
    <p v-else-if="!exibidos.length">Nenhum relatório financeiro publicado para o período.</p>
    <div v-else class="relatorios">
      <article v-for="r in exibidos" :key="r.id" class="relatorio">
        <h2>{{ String(r.mes).padStart(2, '0') }}/{{ r.ano }}</h2>
        <dl>
          <div><dt>Saldo inicial</dt><dd>{{ moeda(r.saldo_inicial_centavos) }}</dd></div>
          <div><dt>Receitas</dt><dd>{{ moeda(r.receitas_centavos) }}</dd></div>
          <div><dt>Despesas</dt><dd>{{ moeda(r.despesas_centavos) }}</dd></div>
          <div><dt>Saldo final</dt><dd>{{ moeda(r.saldo_final_centavos) }}</dd></div>
        </dl>
        <small>Publicado em {{ new Date(r.publicado_em).toLocaleDateString('pt-BR') }}</small>
      </article>
    </div>
  </main>
</template>

<style scoped>
.financeiro-publico { padding: 24px; max-width: 1100px; margin: auto; color: #0b132b; }
.voltar { display: inline-block; margin: 12px 0 24px; color: #16509b; }
.filtros { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
.filtros select { border: 1px solid #aab9d1; border-radius: 10px; padding: 8px 12px; }
.relatorios { display: grid; grid-template-columns: repeat(auto-fit,minmax(min(100%,280px),1fr)); gap: 16px; }
.relatorio { border: 1px solid #dbe3ef; border-radius: 16px; padding: 20px; background: #fff; }
.relatorio h2 { margin: 0 0 16px; font-size: 20px; }
.relatorio dl { margin: 0 0 16px; }
.relatorio dl div { display: flex; justify-content: space-between; gap: 12px; padding: 8px 0; border-bottom: 1px solid #eef2f7; }
.relatorio dt { color: #53647c; }
.relatorio dd { margin: 0; font-weight: 700; text-align: right; }
.relatorio small { color: #53647c; }
@media(max-width:480px) { .financeiro-publico { padding: 16px; } }
</style>
