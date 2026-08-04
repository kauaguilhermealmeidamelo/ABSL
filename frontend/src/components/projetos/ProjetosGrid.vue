<script setup>
import AdminBanner from '@/components/common/AdminBanner.vue'
import ProjetoCard from './ProjetoCard.vue'

defineProps({
  projetos: { type: Array, required: true },
  isAdmin: { type: Boolean, default: false },
})

defineEmits(['novo', 'editar', 'excluir'])
</script>

<template>
  <div class="grid-wrapper">
    <div v-if="isAdmin" class="admin-row">
      <AdminBanner class="admin-row-banner" />
      <button type="button" class="btn-novo" @click="$emit('novo')">
        <v-icon size="14">mdi-plus</v-icon>
        Novo Projeto
      </button>
    </div>

    <div class="proj-grid">
      <ProjetoCard
        v-for="projeto in projetos"
        :key="projeto.id"
        :projeto="projeto"
        :is-admin="isAdmin"
        @editar="$emit('editar', $event)"
        @excluir="$emit('excluir', $event)"
      />
    </div>
  </div>
</template>

<style scoped>
.grid-wrapper {
  font-family: 'DM Sans', sans-serif;
}

.admin-row {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 4px;
}

.admin-row-banner {
  flex: 1 1 260px;
  margin-bottom: 0;
}

.btn-novo {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 18px;
  border-radius: 999px;
  border: none;
  background: #1a3f8f;
  color: #ffffff;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  white-space: nowrap;
  transition: background-color 0.15s ease;
}

.btn-novo:hover {
  background: #0d1f3c;
}

.proj-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
  margin-top: 20px;
}

@media (min-width: 640px) {
  .proj-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>