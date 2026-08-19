<script setup>
import { onMounted, ref, computed } from 'vue'
import { visitasService } from '@/services/visitas'

const stats = ref(null)
const loading = ref(false)
const error = ref('')

async function carregar() {
  loading.value = true
  error.value = ''
  try {
    stats.value = await visitasService.estatisticas()
  } catch {
    error.value = 'Não foi possível carregar as estatísticas de visitas.'
  } finally {
    loading.value = false
  }
}

onMounted(carregar)

const cards = computed(() => {
  if (!stats.value) return []
  return [
    { label: 'Total de visitas', valor: stats.value.total, icon: 'mdi-chart-line' },
    { label: 'Hoje', valor: stats.value.hoje, icon: 'mdi-calendar-today' },
    { label: 'Esta semana', valor: stats.value.semana, icon: 'mdi-calendar-week' },
    { label: 'Este mês', valor: stats.value.mes, icon: 'mdi-calendar-month' },
    { label: 'Visitantes únicos hoje', valor: stats.value.visitantes_unicos_hoje, icon: 'mdi-account-multiple-outline' },
  ]
})

const maxDia = computed(() => {
  if (!stats.value?.ultimos_7_dias?.length) return 1
  return Math.max(...stats.value.ultimos_7_dias.map((d) => d.total), 1)
})

function formatarDia(dia) {
  const [, mes, diaNum] = dia.split('-')
  return `${diaNum}/${mes}`
}
</script>

<template>
  <div class="dashboard">
    <p v-if="loading" class="status-msg">Carregando estatísticas...</p>
    <p v-else-if="error" class="status-msg status-erro">{{ error }}</p>

    <template v-else>
      <div class="cards-grid">
        <div v-for="card in cards" :key="card.label" class="stat-card">
          <div class="stat-icon">
            <v-icon size="20" color="#1a3f8f">{{ card.icon }}</v-icon>
          </div>
          <div>
            <p class="stat-valor">{{ card.valor }}</p>
            <p class="stat-label">{{ card.label }}</p>
          </div>
        </div>
      </div>

      <div class="chart-card">
        <p class="chart-title">Últimos 7 dias</p>
        <div class="chart-bars">
          <div
            v-for="d in stats.ultimos_7_dias"
            :key="d.dia"
            class="chart-bar-col"
          >
            <div class="chart-bar" :style="{ height: `${(d.total / maxDia) * 100}%` }" />
            <span class="chart-bar-valor">{{ d.total }}</span>
            <span class="chart-bar-label">{{ formatarDia(d.dia) }}</span>
          </div>
          <p v-if="!stats.ultimos_7_dias.length" class="chart-vazio">Sem visitas registradas nesse período.</p>
        </div>
      </div>
    </template>
  </div>
</template>

<style scoped>
.dashboard {
  font-family: 'DM Sans', sans-serif;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.status-msg {
  color: #5a6a85;
  font-size: 14px;
  padding: 12px 0;
}
.status-erro {
  color: #dc2626;
}

.cards-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

@media (min-width: 640px) {
  .cards-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 960px) {
  .cards-grid {
    grid-template-columns: repeat(5, 1fr);
  }
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 16px;
  padding: 16px;
}

.stat-icon {
  width: 40px;
  height: 40px;
  flex-shrink: 0;
  border-radius: 12px;
  background: #eef3fb;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-valor {
  font-family: 'Playfair Display', serif;
  font-weight: 700;
  font-size: 22px;
  color: #0d1f3c;
  margin: 0;
  line-height: 1.2;
}

.stat-label {
  font-size: 11px;
  color: #5a6a85;
  margin: 2px 0 0;
}

.chart-card {
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 16px;
  padding: 20px;
}

.chart-title {
  font-size: 12px;
  font-weight: 700;
  color: #0d1f3c;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin: 0 0 16px;
}

.chart-bars {
  display: flex;
  align-items: flex-end;
  gap: 10px;
  height: 140px;
}

.chart-bar-col {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-end;
  height: 100%;
  gap: 4px;
}

.chart-bar {
  width: 100%;
  max-width: 32px;
  min-height: 4px;
  border-radius: 6px 6px 0 0;
  background: linear-gradient(180deg, #1a3f8f, #16509b);
}

.chart-bar-valor {
  font-size: 11px;
  font-weight: 600;
  color: #0d1f3c;
}

.chart-bar-label {
  font-size: 10px;
  color: #5a6a85;
}

.chart-vazio {
  color: #94a3b8;
  font-size: 13px;
}

@media (max-width: 480px) {
  .cards-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .chart-bars {
    height: 110px;
  }
}
</style>