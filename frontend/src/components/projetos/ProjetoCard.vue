<script setup>
defineProps({
  projeto: { type: Object, required: true },
  isAdmin: { type: Boolean, default: false },
})

defineEmits(['editar', 'excluir', 'abrir'])
</script>

<template>
  <article class="projeto-card" @click="$emit('abrir', projeto)">
    <div v-if="projeto.imagem_url" class="projeto-imagem">
      <img :src="projeto.imagem_url" :alt="projeto.titulo" />
    </div>

    <div class="projeto-corpo">
      <div class="projeto-topo">
        <span class="projeto-categoria">{{ projeto.categoria }}</span>
        <span class="projeto-status" :class="projeto.status === 'concluido' ? 'status-concluido' : 'status-andamento'">
          {{ projeto.status === 'concluido' ? 'CONCLUÍDO' : 'EM ANDAMENTO' }}
        </span>
      </div>

      <h3 class="projeto-titulo">{{ projeto.titulo }}</h3>
      <p class="projeto-descricao">{{ projeto.descricao }}</p>
      <p v-if="projeto.data_conclusao && projeto.status === 'concluido'" class="projeto-conclusao">
        Concluído em: {{ projeto.data_conclusao }}
      </p>

      <div v-if="isAdmin" class="projeto-acoes" @click.stop>
        <button type="button" class="btn-editar" @click="$emit('editar', projeto)">
          <v-icon size="13">mdi-pencil-outline</v-icon>
          Editar
        </button>
        <button type="button" class="btn-excluir" @click="$emit('excluir', projeto.id)">
          <v-icon size="13">mdi-trash-can-outline</v-icon>
          Excluir
        </button>
      </div>
    </div>
  </article>
</template>

<style scoped>
.projeto-card {
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 16px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  transition: box-shadow 0.15s ease;
}

.projeto-card:hover {
  box-shadow: 0 6px 16px rgba(13, 31, 60, 0.1);
}

.projeto-imagem {
  height: 140px;
  overflow: hidden;
}

.projeto-imagem img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.projeto-card:hover .projeto-imagem img {
  transform: scale(1.05);
}

.projeto-corpo {
  padding: 20px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.projeto-topo {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  margin-bottom: 10px;
}

.projeto-categoria {
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  font-weight: 700;
  color: #1a3f8f;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.projeto-status {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  padding: 3px 10px;
  border-radius: 999px;
  white-space: nowrap;
}

.status-concluido {
  background: #dcfce7;
  color: #15803d;
}

.status-andamento {
  background: #dbeafe;
  color: #1a3f8f;
}

.projeto-titulo {
  color: #0d1f3c;
  font-weight: 700;
  font-size: 15px;
  margin: 0 0 6px;
  transition: color 0.15s ease;
}

.projeto-card:hover .projeto-titulo {
  color: #1a3f8f;
}

.projeto-descricao {
  color: #5a6a85;
  font-size: 13px;
  line-height: 1.55;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.projeto-conclusao {
  font-size: 11px;
  color: #16a34a;
  margin: 8px 0 0;
}

.projeto-acoes {
  display: flex;
  gap: 8px;
  margin-top: 14px;
  padding-top: 14px;
  border-top: 1px solid rgba(13, 31, 60, 0.08);
}

.btn-editar,
.btn-excluir {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 500;
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
