<script setup>
import { ref, onMounted, computed } from 'vue'
import { logsService, acaoLabel } from '@/services/logs'

const logs = ref([])
const acoesDisponiveis = ref([])
const loading = ref(false)
const error = ref('')

const busca = ref('')
const acaoSelecionada = ref('')
const dataSelecionada = ref('') // yyyy-mm-dd — filtro por dia

const page = ref(1)
const lastPage = ref(1)
const total = ref(0)

const temFiltroAtivo = computed(() => !!busca.value || !!acaoSelecionada.value || !!dataSelecionada.value)

async function carregar() {
  loading.value = true
  error.value = ''
  try {
    const data = await logsService.list({
      page: page.value,
      busca: busca.value,
      acao: acaoSelecionada.value,
      data: dataSelecionada.value,
    })
    logs.value = data.data
    lastPage.value = data.last_page
    total.value = data.total
  } catch {
    error.value = 'Não foi possível carregar os logs.'
  } finally {
    loading.value = false
  }
}

async function carregarAcoes() {
  try {
    acoesDisponiveis.value = await logsService.acoesDisponiveis()
  } catch {
    acoesDisponiveis.value = []
  }
}

onMounted(() => {
  carregar()
  carregarAcoes()
})

function aplicarFiltros() {
  page.value = 1
  carregar()
}

function limparFiltros() {
  busca.value = ''
  acaoSelecionada.value = ''
  dataSelecionada.value = ''
  page.value = 1
  carregar()
}

// Atalhos de data — "hoje" e "ontem" preenchem o campo de data e já
// aplicam o filtro, evitando o usuário ter que abrir o seletor de datas
// para os casos mais comuns de auditoria.
function filtrarHoje() {
  dataSelecionada.value = new Date().toISOString().slice(0, 10)
  aplicarFiltros()
}

function filtrarOntem() {
  const ontem = new Date()
  ontem.setDate(ontem.getDate() - 1)
  dataSelecionada.value = ontem.toISOString().slice(0, 10)
  aplicarFiltros()
}

function anterior() {
  if (page.value > 1) {
    page.value -= 1
    carregar()
  }
}

function proxima() {
  if (page.value < lastPage.value) {
    page.value += 1
    carregar()
  }
}

function formatarData(iso) {
  if (!iso) return ''
  const dt = new Date(iso)
  if (Number.isNaN(dt.getTime())) return ''
  return dt.toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

function formatarDataFiltro(iso) {
  if (!iso) return ''
  const [ano, mes, dia] = iso.split('-')
  return `${dia}/${mes}/${ano}`
}
</script>

<template>
  <div class="logs-viewer">
    <div class="filtros">
      <div class="filtro-busca">
        <v-icon size="16" color="#5a6a85">mdi-magnify</v-icon>
        <input
          v-model="busca"
          type="text"
          class="search-input"
          placeholder="Buscar por ação, usuário ou descrição..."
          @keydown.enter="aplicarFiltros"
        />
      </div>

      <select v-model="acaoSelecionada" class="filtro-select" @change="aplicarFiltros">
        <option value="">Todas as ações</option>
        <option v-for="a in acoesDisponiveis" :key="a" :value="a">{{ acaoLabel(a) }}</option>
      </select>

      <div class="filtro-data">
        <input
          v-model="dataSelecionada"
          type="date"
          class="filtro-data-input"
          @change="aplicarFiltros"
        />
      </div>

      <div class="filtro-atalhos">
        <button type="button" class="btn-atalho" @click="filtrarHoje">Hoje</button>
        <button type="button" class="btn-atalho" @click="filtrarOntem">Ontem</button>
      </div>

      <button type="button" class="btn-buscar" @click="aplicarFiltros">Filtrar</button>
      <button v-if="temFiltroAtivo" type="button" class="btn-limpar" @click="limparFiltros">
        <v-icon size="13">mdi-close</v-icon>
        Limpar filtros
      </button>
    </div>

    <div class="logs-header">
      <span v-if="dataSelecionada" class="filtro-ativo-badge">
        <v-icon size="12">mdi-calendar</v-icon>
        Modificações de {{ formatarDataFiltro(dataSelecionada) }}
      </span>
      <span class="logs-total">{{ total }} registro{{ total !== 1 ? 's' : '' }}</span>
    </div>

    <p v-if="loading" class="status-msg">Carregando logs...</p>
    <p v-else-if="error" class="status-msg status-erro">{{ error }}</p>

    <template v-else>
      <div v-if="logs.length" class="logs-lista">
        <div v-for="log in logs" :key="log.id" class="log-card">
          <div class="log-topo">
            <span class="log-acao">{{ acaoLabel(log.acao) }}</span>
            <span class="log-data">{{ formatarData(log.created_at) }}</span>
          </div>
          <p v-if="log.descricao" class="log-descricao">{{ log.descricao }}</p>
          <div class="log-rodape">
            <span v-if="log.user_nome" class="log-usuario">
              <v-icon size="11">mdi-account-outline</v-icon>
              {{ log.user_nome }}
            </span>
            <span v-if="log.ip" class="log-ip">{{ log.ip }}</span>
          </div>
        </div>
      </div>
      <p v-else class="status-msg status-vazio">
        {{ temFiltroAtivo ? 'Nenhum log encontrado para esse filtro.' : 'Nenhum log encontrado.' }}
      </p>

      <div v-if="logs.length" class="paginacao">
        <button type="button" class="btn-pagina" :disabled="page <= 1" @click="anterior">
          <v-icon size="14">mdi-chevron-left</v-icon>
          Anterior
        </button>
        <span class="pagina-atual">Página {{ page }} de {{ lastPage }}</span>
        <button type="button" class="btn-pagina" :disabled="page >= lastPage" @click="proxima">
          Próxima
          <v-icon size="14">mdi-chevron-right</v-icon>
        </button>
      </div>
    </template>
  </div>
</template>

<style scoped>
.logs-viewer {
  font-family: 'DM Sans', sans-serif;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.filtros {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px;
}

.filtro-busca {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.12);
  border-radius: 999px;
  padding: 8px 14px;
  flex: 1 1 220px;
}

.search-input {
  border: none;
  outline: none;
  background: transparent;
  font-size: 13px;
  color: #0d1f3c;
  flex: 1;
  min-width: 0;
  font-family: inherit;
}

.filtro-select {
  border: 1px solid rgba(13, 31, 60, 0.12);
  border-radius: 999px;
  padding: 8px 14px;
  font-size: 13px;
  background: #ffffff;
  color: #0d1f3c;
  cursor: pointer;
  font-family: inherit;
  max-width: 200px;
}

.filtro-data {
  display: flex;
  align-items: center;
}

.filtro-data-input {
  border: 1px solid rgba(13, 31, 60, 0.12);
  border-radius: 999px;
  padding: 7px 14px;
  font-size: 13px;
  background: #ffffff;
  color: #0d1f3c;
  font-family: inherit;
  cursor: pointer;
}

.filtro-atalhos {
  display: flex;
  gap: 6px;
}

.btn-atalho {
  border: 1px solid rgba(13, 31, 60, 0.12);
  background: #eef3fb;
  color: #1a3f8f;
  font-size: 12px;
  font-weight: 600;
  padding: 7px 12px;
  border-radius: 999px;
  cursor: pointer;
  white-space: nowrap;
  transition: background-color 0.15s ease;
}
.btn-atalho:hover {
  background: #d6e4ff;
}

.btn-buscar {
  border: none;
  background: #1a3f8f;
  color: #ffffff;
  font-size: 12px;
  font-weight: 600;
  padding: 8px 16px;
  border-radius: 999px;
  cursor: pointer;
  white-space: nowrap;
  transition: background-color 0.15s ease;
}
.btn-buscar:hover {
  background: #0d1f3c;
}

.btn-limpar {
  display: flex;
  align-items: center;
  gap: 4px;
  border: none;
  background: transparent;
  color: #dc2626;
  font-size: 12px;
  font-weight: 600;
  padding: 8px 4px;
  cursor: pointer;
  white-space: nowrap;
}
.btn-limpar:hover {
  text-decoration: underline;
}

.logs-header {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: space-between;
}

.filtro-ativo-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  background: #eef3fb;
  color: #1a3f8f;
  font-size: 12px;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 999px;
}

.logs-total {
  font-size: 12px;
  color: #5a6a85;
  white-space: nowrap;
}

.status-msg {
  color: #5a6a85;
  font-size: 14px;
  padding: 12px 0;
}
.status-erro {
  color: #dc2626;
}
.status-vazio {
  color: #94a3b8;
}

.logs-lista {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.log-card {
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 12px;
  padding: 14px 16px;
}

.log-topo {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 6px;
}

.log-acao {
  font-size: 12px;
  font-weight: 700;
  color: #1a3f8f;
  background: #eef3fb;
  padding: 3px 10px;
  border-radius: 999px;
  text-transform: capitalize;
}

.log-data {
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  color: #5a6a85;
}

.log-descricao {
  font-size: 13px;
  color: #0d1f3c;
  line-height: 1.5;
  margin: 0 0 8px;
}

.log-rodape {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.log-usuario {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 11.5px;
  color: #5a6a85;
  font-weight: 500;
}

.log-ip {
  font-family: 'DM Mono', monospace;
  font-size: 10.5px;
  color: #94a3b8;
}

.paginacao {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding-top: 4px;
}

.btn-pagina {
  display: flex;
  align-items: center;
  gap: 4px;
  border: 1px solid rgba(13, 31, 60, 0.15);
  background: #ffffff;
  color: #0d1f3c;
  padding: 7px 14px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.15s ease;
}
.btn-pagina:hover:not(:disabled) {
  background: #eef3fb;
}
.btn-pagina:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.pagina-atual {
  font-size: 12px;
  color: #5a6a85;
}

@media (max-width: 640px) {
  .filtros {
    flex-direction: column;
    align-items: stretch;
  }
  .filtro-busca,
  .filtro-select,
  .filtro-data,
  .filtro-data-input {
    width: 100%;
    max-width: none;
  }
  .filtro-atalhos {
    width: 100%;
  }
  .btn-atalho {
    flex: 1;
    text-align: center;
  }
  .btn-buscar,
  .btn-limpar {
    width: 100%;
    text-align: center;
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .paginacao {
    flex-wrap: wrap;
  }
}
</style>