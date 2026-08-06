<script setup>
defineProps({
  noticia: { type: Object, required: true },
  isAdmin: { type: Boolean, default: false },
})

defineEmits(['abrir', 'editar', 'excluir'])
</script>

<template>
  <article class="noticia-card" @click="$emit('abrir', noticia)">
    <div class="noticia-imagem">
      <div class="noticia-imagem-textura" />
    </div>

    <div class="noticia-corpo">
      <p class="noticia-data">{{ noticia.data_publicacao }}</p>
      <h3 class="noticia-titulo">{{ noticia.titulo }}</h3>
      <p class="noticia-resumo">{{ noticia.texto }}</p>

      <div v-if="isAdmin" class="noticia-acoes" @click.stop>
        <button type="button" class="btn-editar" @click="$emit('editar', noticia)">
          <v-icon size="13">mdi-pencil-outline</v-icon>
          Editar
        </button>
        <button type="button" class="btn-excluir" @click="$emit('excluir', noticia.id)">
          <v-icon size="13">mdi-trash-can-outline</v-icon>
          Excluir
        </button>
      </div>
    </div>
  </article>
</template>

<style scoped>
.noticia-card {
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 16px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  cursor: pointer;
  transition: box-shadow 0.15s ease;
  font-family: 'DM Sans', sans-serif;
}

.noticia-card:hover {
  box-shadow: 0 6px 16px rgba(13, 31, 60, 0.1);
}

.noticia-imagem {
  height: 150px;
  background: linear-gradient(135deg, #1a3f8f, #16509b);
  position: relative;
  overflow: hidden;
}

.noticia-imagem-textura {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(rgba(255, 255, 255, 0.18) 1.5px, transparent 1.5px);
  background-size: 16px 16px;
}

.noticia-corpo {
  padding: 16px 20px 20px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  flex: 1;
}

.noticia-data {
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  color: #5a6a85;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin: 0;
}

.noticia-titulo {
  color: #0d1f3c;
  font-weight: 700;
  font-size: 15px;
  line-height: 1.35;
  margin: 0;
  transition: color 0.15s ease;
}

.noticia-card:hover .noticia-titulo {
  color: #1a3f8f;
}

.noticia-resumo {
  color: #5a6a85;
  font-size: 13px;
  line-height: 1.55;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.noticia-acoes {
  display: flex;
  gap: 8px;
  margin-top: 10px;
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