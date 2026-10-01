<script setup>
import { onMounted, ref } from 'vue'
import OuvintesLista from '@/components/ouvintes/OuvintesLista.vue'
import OuvintesRespondidasLista from '@/components/ouvintes/OuvintesRespondidasLista.vue'
import { ouvintesService } from '@/services/ouvintes'

const pendentes = ref([])
const respondidas = ref([])
const loading = ref(false)
const error = ref('')

async function carregar() {
  loading.value = true
  error.value = ''
  try {
    const [pendentesData, respondidasData] = await Promise.all([
      ouvintesService.list(),
      ouvintesService.listRespondidas(),
    ])
    pendentes.value = pendentesData
    respondidas.value = respondidasData
  } catch {
    error.value = 'Não foi possível carregar a ouvidoria.'
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

    <p v-if="loading" class="state">Carregando...</p>
    <p v-else-if="error" class="state error">{{ error }}</p>

    <template v-else>
      <section>
        <h3>Pendentes</h3>
        <OuvintesLista :mensagens="pendentes" />
      </section>

      <section>
        <h3>Respondidas</h3>
        <OuvintesRespondidasLista :mensagens="respondidas" :loading="false" :error="''" />
      </section>
    </template>
  </div>
</template>

<style scoped>
.manager{display:flex;flex-direction:column;gap:22px;font-family:'DM Sans',sans-serif}.manager h2{margin:0;color:#0d1f3c}.manager p{margin:5px 0 0;color:#5a6a85;font-size:13px}.manager h3{margin:0 0 10px;color:#0d1f3c;font-size:15px}.state{padding:28px 0;color:#5a6a85}.error{color:#dc2626}
</style>
