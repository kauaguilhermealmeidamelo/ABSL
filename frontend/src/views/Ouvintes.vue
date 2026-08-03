<template>
  <div class="ouvintes-page">
    <PageHeader
      label="ABSL"
      title="Os Ouvintes"
      subtitle="Ouvidoria do Grêmio Athos Bulcão — envie sugestões, críticas ou opiniões. Sua voz importa."
    />

    <div class="form-wrapper">
      <OuvintesFormulario @enviar="adicionarMensagem" />
    </div>

    <OuvintesLista
      v-if="isAdmin"
      :mensagens="mensagens"
      @excluir="excluirMensagem"
      @responder="responderMensagem"
    />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import OuvintesFormulario from '@/components/ouvintes/OuvintesFormulario.vue'
import OuvintesLista from '@/components/ouvintes/OuvintesLista.vue'

// Mesmo critério usado em AdminCard.vue / menuLateral.vue / Gabarito.vue para detectar admin
const isAdmin = computed(() => {
  const raw = localStorage.getItem('usuario')
  if (!raw) return false

  try {
    const parsed = JSON.parse(raw)
    if (parsed && typeof parsed === 'object') {
      const role = String(
        parsed.role || parsed.tipo || parsed.perfil || parsed.is_admin || parsed.administrador || ''
      ).toLowerCase()
      return (
        role === 'admin' ||
        role === 'administrator' ||
        role === 'administrador' ||
        role === 'super_admin' ||
        role === 'super-admin' ||
        parsed.is_admin === true ||
        parsed.administrador === true
      )
    }
  } catch {
    // fallback para valores simples armazenados como texto
  }

  return String(raw).toLowerCase().includes('admin')
})

// TODO: substituir por chamadas à API (não há endpoint de ouvidoria no backend ainda)
const mensagens = ref([
  {
    id: 1,
    anonimo: false,
    nome: 'Ana Souza',
    email: 'ana@email.com',
    texto: 'Gostaria de sugerir que a biblioteca fique aberta no contraturno para estudos.',
    status: 'pendente',
    data_envio: '12 jun 2026',
  },
  {
    id: 2,
    anonimo: true,
    texto: 'O refeitório precisa de mais opções vegetarianas no cardápio.',
    status: 'pendente',
    data_envio: '18 jun 2026',
  },
  {
    id: 3,
    anonimo: false,
    nome: 'Carlos Lima',
    email: 'carlos@email.com',
    texto: 'Seria ótimo ter mais tomadas na sala de informática para carregar os notebooks.',
    status: 'respondida',
    resposta: 'Estamos levando essa demanda à direção da escola.',
    data_envio: '25 jun 2026',
  },
])

function adicionarMensagem(payload) {
  mensagens.value.push({
    id: Date.now(),
    status: 'pendente',
    data_envio: new Date().toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' }),
    ...payload,
  })
}

function excluirMensagem(id) {
  mensagens.value = mensagens.value.filter((m) => m.id !== id)
}

function responderMensagem({ id, resposta }) {
  mensagens.value = mensagens.value.map((m) => (m.id === id ? { ...m, resposta, status: 'respondida' } : m))
}
</script>

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
</style>