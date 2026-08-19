<script setup>
import { onMounted, ref } from 'vue'
import { ouvintesService } from '@/services/ouvintes'

const STORAGE_KEY = 'absl_meus_protocolos'

const protocolos = ref([]) // [{ id, resultado, carregando, erro }]

function lerProtocolosSalvos() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    const ids = raw ? JSON.parse(raw) : []
    return Array.isArray(ids) ? ids : []
  } catch {
    return []
  }
}

async function carregarTodos() {
  const ids = lerProtocolosSalvos()
  protocolos.value = ids.map((id) => ({ id, resultado: null, carregando: true, erro: '' }))

  await Promise.all(
    protocolos.value.map(async (item) => {
      try {
        item.resultado = await ouvintesService.consultarProtocolo(item.id)
      } catch {
        item.erro = 'Não foi possível carregar esta mensagem.'
      } finally {
        item.carregando = false
      }
    })
  )
}

function statusLabel(status) {
  return { pendente: 'Pendente', respondido: 'Respondida', fechado: 'Encerrada' }[status] || status
}

onMounted(carregarTodos)

defineExpose({ recarregar: carregarTodos })
</script>

<template>
  <div v-if="protocolos.length" class="meus-protocolos">
    <h3 class="meus-protocolos-titulo">Suas mensagens enviadas</h3>

    <div v-for="item in protocolos" :key="item.id" class="protocolo-card">
      <p v-if="item.carregando" class="protocolo-status">Carregando...</p>
      <p v-else-if="item.erro" class="protocolo-status protocolo-erro">{{ item.erro }}</p>

      <template v-else-if="item.resultado">
        <div class="protocolo-header">
          <span class="protocolo-numero">Protocolo #{{ item.resultado.id }}</span>
          <span class="protocolo-badge" :class="`badge-${item.resultado.status}`">
            {{ statusLabel(item.resultado.status) }}
          </span>
        </div>
        <p class="protocolo-texto">{{ item.resultado.texto }}</p>

        <div v-if="item.resultado.resposta" class="protocolo-resposta">
          <v-icon size="13" color="#1a3f8f">mdi-reply</v-icon>
          <div>
            <p class="protocolo-resposta-titulo">Resposta do Grêmio</p>
            <p class="protocolo-resposta-texto">{{ item.resultado.resposta }}</p>
          </div>
        </div>
        <p v-else class="protocolo-aguardando">Ainda não há resposta para esta mensagem.</p>
      </template>
    </div>
  </div>
</template>

<style scoped>
.meus-protocolos {
  display: flex;
  flex-direction: column;
  gap: 12px;
  font-family: 'DM Sans', sans-serif;
  max-width: 560px;
  margin: 0 auto 32px;
}

.meus-protocolos-titulo {
  font-size: 14px;
  font-weight: 700;
  color: #0d1f3c;
  margin: 0;
}

.protocolo-card {
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 12px;
  padding: 14px 16px;
}

.protocolo-status {
  font-size: 12px;
  color: #5a6a85;
  margin: 0;
}
.protocolo-erro {
  color: #dc2626;
}

.protocolo-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  margin-bottom: 6px;
}

.protocolo-numero {
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  color: #5a6a85;
}

.protocolo-badge {
  font-size: 10px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 999px;
}
.badge-pendente {
  background: #fef3c7;
  color: #b45309;
}
.badge-respondido {
  background: #dcfce7;
  color: #15803d;
}
.badge-fechado {
  background: #f3f4f6;
  color: #6b7280;
}

.protocolo-texto {
  font-size: 13px;
  color: #0d1f3c;
  line-height: 1.6;
  margin: 0 0 8px;
}

.protocolo-resposta {
  display: flex;
  gap: 8px;
  background: #eff6ff;
  border: 1px solid #dbeafe;
  border-radius: 8px;
  padding: 8px 12px;
}

.protocolo-resposta-titulo {
  font-size: 10px;
  font-weight: 600;
  color: #1a3f8f;
  margin: 0 0 2px;
}

.protocolo-resposta-texto {
  font-size: 12px;
  color: #0d1f3c;
  margin: 0;
}

.protocolo-aguardando {
  font-size: 12px;
  color: #94a3b8;
  margin: 0;
}

@media (max-width: 480px) {
  .protocolo-card {
    padding: 12px;
  }
}
</style>