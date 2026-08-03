<template>
  <div class="admin-banner">
    <v-icon size="14" color="#92400e">mdi-pencil-outline</v-icon>
    <span>{{ message }}</span>
  </div>
</template>

<script setup>
defineProps({
  message: {
    type: String,
    default: 'Modo administrador ativo — você pode criar, editar e excluir conteúdo nesta página.',
  },
})
</script>

<style scoped>
.admin-banner {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: #fffbeb;
  border: 1px solid #fde68a;
  border-radius: 12px;
  margin-bottom: 24px;
  color: #92400e;
  font-size: 14px;
  font-weight: 500;
  font-family: 'DM Sans', sans-serif;
}
</style>