<template>
  <div class="lista-wrapper">
    <AdminBanner />

    <div class="lista-header">
      <h2 class="lista-title">Mensagens recebidas</h2>
      <span class="lista-count">{{ mensagens.length }} mensagens</span>
    </div>

    <div class="lista-itens">
      <OuvintesMensagem
        v-for="mensagem in mensagens"
        :key="mensagem.id"
        :mensagem="mensagem"
        @excluir="$emit('excluir', $event)"
        @responder="$emit('responder', $event)"
      />
    </div>
  </div>
</template>

<script setup>
import AdminBanner from '@/components/common/AdminBanner.vue'
import OuvintesMensagem from './OuvintesMensagem.vue'

defineProps({
  mensagens: { type: Array, required: true },
})

defineEmits(['excluir', 'responder'])
</script>

<style scoped>
.lista-wrapper {
  font-family: 'DM Sans', sans-serif;
}

.lista-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
}

.lista-title {
  color: #0d1f3c;
  font-weight: 700;
  font-size: 16px;
  margin: 0;
}

.lista-count {
  font-size: 12px;
  color: #5a6a85;
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.1);
  padding: 4px 8px;
  border-radius: 999px;
  white-space: nowrap;
}

.lista-itens {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
</style>