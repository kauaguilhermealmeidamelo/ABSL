<script setup>
import { onMounted, ref } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import OuvintesTabs from '@/components/ouvintes/OuvintesTabs.vue'
import OuvintesFormulario from '@/components/ouvintes/OuvintesFormulario.vue'
import OuvintesMeusProtocolos from '@/components/ouvintes/OuvintesMeusProtocolos.vue'
import OuvintesRespondidasLista from '@/components/ouvintes/OuvintesRespondidasLista.vue'
import OuvintesLista from '@/components/ouvintes/OuvintesLista.vue'
import { useAdmin } from '@/composables/useAdmin'
import { ouvintesService } from '@/services/ouvintes'

const { isAdmin } = useAdmin()

const abaAtiva = ref('enviar') // 'enviar' | 'respondidas' | 'gerenciar'

const meusProtocolosRef = ref(null)
const STORAGE_KEY = 'absl_meus_protocolos'

function salvarProtocoloLocal(id) {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    const ids = raw ? JSON.parse(raw) : []
    const lista = Array.isArray(ids) ? ids : []
    if (!lista.includes(id)) lista.push(id)
    localStorage.setItem(STORAGE_KEY, JSON.stringify(lista.slice(-20)))
  } catch {
    // localStorage indisponível — segue sem persistir
  }
}

// ── Aba pública: mensagens respondidas (central de suporte) ───────────────
const respondidas = ref([])
const loadingRespondidas = ref(false)
const errorRespondidas = ref('')
const respondidasCarregadas = ref(false)

async function carregarRespondidas(force = false) {
  if (respondidasCarregadas.value && !force) return
  loadingRespondidas.value = true
  errorRespondidas.value = ''
  try {
    respondidas.value = await ouvintesService.listRespondidas()
    respondidasCarregadas.value = true
  } catch {
    errorRespondidas.value = 'Não foi possível carregar as mensagens respondidas.'
  } finally {
    loadingRespondidas.value = false
  }
}

// ── Aba admin: gerenciar todas as mensagens ────────────────────────────────
const mensagens = ref([])
const loading = ref(false)
const error = ref('')

async function carregar() {
  if (!isAdmin.value) return
  loading.value = true
  error.value = ''
  try {
    mensagens.value = await ouvintesService.list()
  } catch {
    error.value = 'Não foi possível carregar as mensagens.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  carregarRespondidas()
  carregar()
})

function onMudarAba(aba) {
  abaAtiva.value = aba
  if (aba === 'respondidas') carregarRespondidas()
}

async function adicionarMensagem(payload) {
  try {
    const criada = await ouvintesService.create(payload)
    if (criada?.id) {
      salvarProtocoloLocal(criada.id)
      meusProtocolosRef.value?.recarregar()
    }
    if (isAdmin.value) await carregar()
  } catch {
    error.value = 'Não foi possível enviar a mensagem.'
  }
}

async function excluirMensagem(id) {
  try {
    await ouvintesService.remove(id)
    mensagens.value = mensagens.value.filter((m) => m.id !== id)
  } catch {
    error.value = 'Não foi possível excluir a mensagem.'
  }
}

async function responderMensagem({ id, resposta }) {
  try {
    const atualizada = await ouvintesService.update(id, { resposta })
    mensagens.value = mensagens.value.map((m) => (m.id === id ? atualizada : m))
    // A resposta acabou de ser publicada — força a atualização da aba
    // pública na próxima vez que ela for aberta, para não ficar
    // desatualizada.
    respondidasCarregadas.value = false
  } catch {
    error.value = 'Não foi possível salvar a resposta.'
  }
}
</script>

<template>
  <div class="ouvintes-page">
    <PageHeader
      label="ABSL"
      title="Ouvidoria"
      subtitle="Ouvidoria do Grêmio Athos Bulcão — envie sugestões, críticas ou opiniões. Sua voz importa."
    />

    <OuvintesTabs :model-value="abaAtiva" :mostrar-gerenciar="isAdmin" @update:model-value="onMudarAba" />

    <div v-show="abaAtiva === 'enviar'" class="aba-conteudo">
      <div class="form-wrapper">
        <OuvintesFormulario @enviar="adicionarMensagem" />
      </div>

      <OuvintesMeusProtocolos ref="meusProtocolosRef" />
    </div>

    <div v-show="abaAtiva === 'respondidas'" class="aba-conteudo">
      <OuvintesRespondidasLista
        :mensagens="respondidas"
        :loading="loadingRespondidas"
        :error="errorRespondidas"
      />
    </div>

    <div v-if="isAdmin" v-show="abaAtiva === 'gerenciar'" class="aba-conteudo">
      <p v-if="loading" class="status-msg">Carregando mensagens...</p>
      <p v-else-if="error" class="status-msg status-erro">{{ error }}</p>
      <OuvintesLista
        v-else
        :mensagens="mensagens"
        @excluir="excluirMensagem"
        @responder="responderMensagem"
      />
    </div>
  </div>
</template>

<style scoped>
.ouvintes-page {
  font-family: 'DM Sans', sans-serif;
  padding: 24px;
}

@media (max-width: 480px) {
  .ouvintes-page {
    padding: 16px;
  }
}

.aba-conteudo {
  max-width: 720px;
}

.form-wrapper {
  max-width: 560px;
  margin: 0 auto 32px;
}

.status-msg {
  color: #5a6a85;
  font-size: 14px;
  padding: 12px 0;
}

.status-erro {
  color: #dc2626;
}
</style>