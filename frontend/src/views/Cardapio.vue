<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import AdminBanner from '@/components/common/AdminBanner.vue'
import CardapioSemana from '@/components/cardapio/CardapioSemana.vue'
import CardapioTabela from '@/components/cardapio/CardapioTabela.vue'
import { useAdmin } from '@/composables/useAdmin'
import { CARDAPIO_DIAS, cardapioDias, setCardapioDia } from '@/stores/appData'

const { isAdmin } = useAdmin()
const DIAS_SEMANA = CARDAPIO_DIAS
async function updateDia({ dia, valor }) {
  if (!isAdmin.value || salvando.value) return
  salvando.value = true
  error.value = ''
  try {
    await setCardapioDia(dia, valor)
  } catch (err) {
    error.value = err?.response?.data?.message || 'Não foi possível salvar o item do cardápio.'
  } finally {
    salvando.value = false
  }
}
</script>

<template>
  <div class="cardapio-page">
    <PageHeader label="ABSL" title="Cardápio Semanal" subtitle="Lanche servido na cantina escolar." />
    <AdminBanner
      v-if="isAdmin"
      message="Modo administrador ativo — você pode editar a semana e os itens do cardápio."
    />
    <p v-if="error" class="state-error" role="alert">{{ error }}</p>
    <p v-if="salvando" class="state-saving" aria-live="polite">Salvando alteração...</p>
        <CardapioTabela
      :dias="DIAS_SEMANA"
      :cardapio="cardapioDias"
      :is-admin="isAdmin"
      @update-dia="updateDia"
    />
  </div>
</template>

<style scoped>
.cardapio-page{font-family:'DM Sans',sans-serif;color:#0d1f3c;max-width:1024px;margin:0 auto;padding:32px 40px 64px}.state-error{color:#b91c1c;font-size:13px;margin:0 0 8px}.state-saving{color:#5a6a85;font-size:13px;margin:0 0 8px}@media (max-width:720px){.cardapio-page{padding:20px}}
</style>