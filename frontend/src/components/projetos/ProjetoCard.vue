<script setup>
defineProps({
  projeto: { type: Object, required: true },
  isAdmin: { type: Boolean, default: false },
})

defineEmits(['editar', 'excluir'])
</script>

<template>
  <div class="proj-card">
    <div v-if="projeto.imagem_url" class="proj-cover">
      <img :src="projeto.imagem_url" :alt="projeto.titulo" />
    </div>

    <div class="proj-body">
      <div class="proj-top">
        <span class="proj-categoria">{{ projeto.categoria }}</span>
        <span class="badge" :class="projeto.status === 'concluido' ? 'badge-concluido' : 'badge-andamento'">
          {{ projeto.status === 'concluido' ? 'CONCLUÍDO' : 'EM ANDAMENTO' }}
        </span>
      </div>

      <h3 class="proj-titulo">{{ projeto.titulo }}</h3>
      <p class="proj-descricao">{{ projeto.descricao }}</p>

      <div v-if="isAdmin" class="proj-actions">
        <button type="button" class="btn-editar" @click="$emit('editar', projeto)">
          <v-icon size="12">mdi-pencil-outline</v-icon>
          Editar
        </button>
        <button type="button" class="btn-excluir" @click="$emit('excluir', projeto.id)">
          <v-icon size="12">mdi-trash-can-outline</v-icon>
          Excluir
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.proj-card {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid rgba(13, 31, 60, 0.08);
  overflow: hidden;
  font-family: 'DM Sans', sans-serif;
  transition: box-shadow 0.15s ease;
}

.proj-card:hover {
  box-shadow: 0 4px 10px rgba(13, 31, 60, 0.1);
}

.proj-cover {
  height: 144px;
  overflow: hidden;
}

.proj-cover img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.proj-body {
  padding: 20px;
}

.proj-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 8px;
  margin-bottom: 12px;
}

.proj-categoria {
  font-family: 'DM Mono', monospace;
  font-size: 10px;
  font-weight: 500;
  color: #1a3f8f;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.badge {
  flex-shrink: 0;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  padding: 2px 8px;
  border-radius: 999px;
  white-space: nowrap;
}

.badge-concluido {
  background: #dcfce7;
  color: #15803d;
}

.badge-andamento {
  background: #dbeafe;
  color: #1a3f8f;
}

.proj-titulo {
  color: #0d1f3c;
  font-weight: 700;
  font-size: 14px;
  margin: 0 0 8px;
}

.proj-descricao {
  color: #5a6a85;
  font-size: 12px;
  line-height: 1.6;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.proj-actions {
  display: flex;
  gap: 8px;
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1px solid rgba(13, 31, 60, 0.08);
}

.btn-editar,
.btn-excluir {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 5px 10px;
  border-radius: 8px;
  font-size: 11px;
  cursor: pointer;
  background: transparent;
  transition: background-color 0.15s ease;
}

.btn-editar {
  border: 1px solid #fbbf24;
  color: #b45309;
}
.btn-editar:hover {
  background: #fffbeb;
}

.btn-excluir {
  border: 1px solid #fca5a5;
  color: #dc2626;
}
.btn-excluir:hover {
  background: #fef2f2;
}
</style>