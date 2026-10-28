<script setup>
import { formatarDataEvento } from './eventoFormat'

defineProps({
  evento: { type: Object, required: true },
  isAdmin: { type: Boolean, default: false },
})
defineEmits(['editar', 'excluir'])
</script>

<template>
  <article class="evento-card">
    <div v-if="isAdmin" class="admin-acoes">
      <button type="button" class="admin-btn" aria-label="Editar evento" @click="$emit('editar', evento)">
        <v-icon size="14">mdi-pencil-outline</v-icon>
      </button>
      <button type="button" class="admin-btn admin-btn-excluir" aria-label="Excluir evento" @click="$emit('excluir', evento.id)">
        <v-icon size="14">mdi-trash-can-outline</v-icon>
      </button>
    </div>

    <div class="evento-imagem">
      <img v-if="evento.imagem_url" :src="evento.imagem_url" :alt="evento.titulo" loading="lazy" />
      <v-icon v-else size="48" color="#9aa8c2">mdi-calendar-star</v-icon>
    </div>

    <div class="evento-corpo">
      <h2 class="evento-titulo">{{ evento.titulo }}</h2>
      <p class="evento-meta"><v-icon size="16">mdi-calendar-clock</v-icon>{{ formatarDataEvento(evento.data_evento) }}</p>
      <p v-if="evento.local" class="evento-meta"><v-icon size="16">mdi-map-marker-outline</v-icon>{{ evento.local }}</p>
      <p v-if="evento.descricao" class="evento-descricao">{{ evento.descricao }}</p>

      <a v-if="evento.link_ingresso" class="btn-ingresso" :href="evento.link_ingresso" target="_blank" rel="noopener noreferrer">
        <v-icon size="18">mdi-ticket-confirmation-outline</v-icon>Comprar ingresso
      </a>
    </div>
  </article>
</template>

<style scoped>
.evento-card{position:relative;display:flex;flex-direction:column;background:#fff;border:1px solid rgba(13,31,60,.08);border-radius:16px;overflow:hidden;font-family:'DM Sans',sans-serif}
.admin-acoes{position:absolute;top:10px;right:10px;z-index:5;display:flex;gap:6px}
.admin-btn{display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:999px;border:0;background:rgba(13,31,60,.55);color:#fff;cursor:pointer}
.admin-btn:hover{background:rgba(13,31,60,.75)}
.admin-btn-excluir:hover{background:#dc2626}
.evento-imagem{display:flex;align-items:center;justify-content:center;aspect-ratio:16/9;background:#eef3fb}
.evento-imagem img{width:100%;height:100%;object-fit:cover}
.evento-corpo{display:flex;flex-direction:column;gap:6px;padding:16px}
.evento-titulo{font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:#0d1f3c;margin:0 0 4px;overflow-wrap:anywhere}
.evento-meta{display:flex;align-items:center;gap:6px;margin:0;font-size:13px;color:#5a6a85}
.evento-descricao{margin:6px 0 0;font-size:14px;line-height:1.6;color:#0d1f3c;white-space:pre-line;overflow-wrap:anywhere;display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden}
.btn-ingresso{align-self:flex-start;display:inline-flex;align-items:center;gap:6px;margin-top:10px;padding:10px 20px;border-radius:999px;background:#f5c518;color:#0d1f3c;font-size:13px;font-weight:700;text-decoration:none}
.btn-ingresso:hover{background:#e0b300}
@media (max-width:560px){.btn-ingresso{align-self:stretch;justify-content:center}}
</style>
