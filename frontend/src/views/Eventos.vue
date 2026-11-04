<script setup>
import { ref, onMounted } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import NovoItemButton from '@/components/common/NovoItemButton.vue'
import EventoCard from '@/components/eventos/EventoCard.vue'
import EventoFormModal from '@/components/eventos/EventoFormModal.vue'
import { useEventos } from '@/composables/useEventos'
import { useAdmin } from '@/composables/useAdmin'

const { isAdmin } = useAdmin()
const { eventos, loading, error, fetchEventos, adicionar, atualizar, remover } = useEventos()

const showModal = ref(false)
const editing = ref(null)
const salvando = ref(false)
const erroServidor = ref('')

onMounted(() => fetchEventos(true))

function onAdd() {
  editing.value = null
  erroServidor.value = ''
  showModal.value = true
}

function onEdit(e) {
  editing.value = { ...e }
  erroServidor.value = ''
  showModal.value = true
}

async function onSalvar({ id, payload }) {
  erroServidor.value = ''
  salvando.value = true
  try {
    if (id) await atualizar(id, payload)
    else await adicionar(payload)
    showModal.value = false
  } catch (err) {
    const erros = err?.response?.data?.errors
    erroServidor.value = (erros && Object.values(erros)[0]?.[0]) || err?.response?.data?.message || 'Erro ao salvar evento.'
  } finally {
    salvando.value = false
  }
}

async function onDelete(id) {
  if (!confirm('Confirma exclusão do evento?')) return
  await remover(id)
}
</script>

<template>
  <div class="eventos-page">
    <PageHeader label="ABSL" title="Eventos" subtitle="Próximos eventos do Grêmio e da Escola" />

    <div v-if="isAdmin" class="controls">
      <NovoItemButton label="Novo Evento" @click="onAdd" />
    </div>

    <p v-if="loading">Carregando eventos...</p>
    <p v-else-if="error" class="status-erro">{{ error }}</p>

    <div v-else class="grid">
      <EventoCard v-for="e in eventos" :key="e.id" :evento="e" :is-admin="isAdmin" @editar="onEdit" @excluir="onDelete" />
      <p v-if="!eventos.length" class="empty">Nenhum evento por enquanto.</p>
    </div>

    <EventoFormModal v-model="showModal" :evento="editing" :erro-servidor="erroServidor" :salvando="salvando" @salvar="onSalvar" />
  </div>
</template>

<style scoped>
.eventos-page{max-width:960px;margin:0 auto;padding:24px 16px 48px;font-family:'DM Sans',sans-serif}
.controls{display:flex;justify-content:flex-end;margin-bottom:16px}
.status-erro{color:#dc2626}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px}
.empty{grid-column:1/-1;color:#5a6a85;text-align:center;padding:32px 0}
@media (max-width:560px){.controls{display:block}}
</style>
