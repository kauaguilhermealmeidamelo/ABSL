<script setup>
import { onMounted, ref } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import OuvintesFormulario from '@/components/ouvintes/OuvintesFormulario.vue'
import OuvintesLista from '@/components/ouvintes/OuvintesLista.vue'
import { useAdmin } from '@/composables/useAdmin'
import { ouvintesService } from '@/services/ouvintes'

const { isAdmin } = useAdmin()

const mensagens = ref([])
const loading = ref(false)
const error = ref('')

async function carregar() {
  // GET /ouvintes (listagem completa) exige token de admin — não faz
  // sentido chamar se o visitante não estiver logado como admin.
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

onMounted(carregar)

async function adicionarMensagem(payload) {
  try {
    await ouvintesService.create(payload)
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

    <div class="form-wrapper">
      <OuvintesFormulario @enviar="adicionarMensagem" />
    </div>

    <template v-if="isAdmin">
      <p v-if="loading" class="status-msg">Carregando mensagens...</p>
      <p v-else-if="error" class="status-msg status-erro">{{ error }}</p>
      <OuvintesLista
        v-else
        :mensagens="mensagens"
        @excluir="excluirMensagem"
        @responder="responderMensagem"
      />
    </template>
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