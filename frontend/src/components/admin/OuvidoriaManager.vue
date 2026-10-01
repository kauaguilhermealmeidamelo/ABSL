<script setup>
import { computed, onMounted, ref } from 'vue'
import OuvintesLista from '@/components/ouvintes/OuvintesLista.vue'
import OuvintesRespondidasLista from '@/components/ouvintes/OuvintesRespondidasLista.vue'
import { ouvintesService } from '@/services/ouvintes'
import GerenciamentoTabs from '@/components/gerenciamento/GerenciamentoTabs.vue'

const pendentes = ref([])
const respondidas = ref([])
const loading = ref(false)
const error = ref('')
const categoria = ref('pendentes')

const tabs = computed(() => [
  { id: 'pendentes', label: `Pendentes (${pendentes.value.length})`, icon: 'mdi-clock-outline' },
  { id: 'respondidas', label: `Respondidas (${respondidas.value.length})`, icon: 'mdi-check-circle-outline' },
])

async function responderMensagem({ id, resposta }) {
  const texto = String(resposta || '').trim()
  if (!texto) {
    error.value = 'Digite uma resposta antes de enviar.'
    return
  }

  loading.value = true
  error.value = ''
  try {
    await ouvintesService.update(id, { resposta: texto })
    await carregar()
    categoria.value = 'respondidas'
  } catch (err) {
    error.value = err?.response?.data?.message || 'Não foi possível salvar a resposta.'
    loading.value = false
  }
}

async function excluirMensagem(id) {
  try {
    await ouvintesService.remove(id)
    await carregar()
  } catch (err) {
    error.value = err?.response?.data?.message || 'Não foi possível excluir a mensagem.'
  }
}

async function carregar() {
  loading.value = true
  error.value = ''
  try {
    const [mensagensData, respondidasData] = await Promise.all([
      ouvintesService.list(),
      ouvintesService.listRespondidas(),
    ])
    pendentes.value = mensagensData.filter((mensagem) => mensagem.status === 'pendente')
    respondidas.value = respondidasData
  } catch (err) {
    error.value = err?.response?.data?.message || 'Não foi possível carregar a ouvidoria.'
  } finally {
    loading.value = false
  }
}

onMounted(carregar)
</script>

<template>
  <div class="manager">
    <div>
      <h2>Ouvidoria</h2>
      <p>Consulte e acompanhe as manifestações recebidas.</p>
    </div>

    <GerenciamentoTabs v-model="categoria" :tabs="tabs" />

    <p v-if="loading" class="state">Carregando...</p>
    <p v-else-if="error" class="state error">{{ error }}</p>

    <template v-else>
      <section v-if="categoria === 'pendentes'">
        <h3>Manifestações pendentes</h3>
        <OuvintesLista :mensagens="pendentes" @responder="responderMensagem" @excluir="excluirMensagem" />
      </section>

      <section v-else>
        <h3>Manifestações respondidas</h3>
        <OuvintesRespondidasLista :mensagens="respondidas" :loading="false" :error="''" />
      </section>
    </template>
  </div>
</template>

<style scoped>
.manager{display:flex;flex-direction:column;gap:14px;font-family:'DM Sans',sans-serif}.manager h2{margin:0;color:#0d1f3c}.manager p{margin:5px 0 0;color:#5a6a85;font-size:13px}.manager h3{margin:0 0 10px;color:#0d1f3c;font-size:15px}.state{padding:28px 0;color:#5a6a85}.error{color:#dc2626}
</style>
