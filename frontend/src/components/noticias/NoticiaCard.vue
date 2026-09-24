<script setup>
import { ref } from 'vue'
import NoticiaCarrossel from './NoticiaCarrossel.vue'
import NoticiaAcoes from './NoticiaAcoes.vue'
import NoticiaLegenda from './NoticiaLegenda.vue'
import NoticiaComentariosDialog from './NoticiaComentariosDialog.vue'
import { noticiasService } from '@/services/noticias'
const props=defineProps({noticia:{type:Object,required:true},isAdmin:{type:Boolean,default:false}})
const emit=defineEmits(['editar','excluir'])
const curtido=ref(props.noticia.curtido), curtidasCount=ref(props.noticia.curtidas_count??0), comentariosCount=ref(props.noticia.comentarios_count??0), comentariosAbertos=ref(false)
async function alternarCurtida(){const a=curtido.value,c=curtidasCount.value;curtido.value=!curtido.value;curtidasCount.value+=curtido.value?1:-1;try{const r=await noticiasService.curtir(props.noticia.id);curtido.value=r.curtido;curtidasCount.value=r.curtidas_count}catch{curtido.value=a;curtidasCount.value=c}}
function curtirViaImagem(){if(!curtido.value)alternarCurtida()}
function onComentarioAdicionado(){comentariosCount.value+=1}
async function compartilhar(){const url=window.location.origin+'/noticias';if(navigator.share){try{await navigator.share({title:props.noticia.titulo,url})}catch{}}else{await navigator.clipboard.writeText(url)}}
</script>
<template>
<article class="noticia-card">
<div v-if="isAdmin" class="admin-acoes" @click.stop>
<button type="button" class="admin-btn" @click="emit('editar',noticia)"><v-icon size="14">mdi-pencil-outline</v-icon></button>
<button type="button" class="admin-btn admin-btn-excluir" @click="emit('excluir',noticia.id)"><v-icon size="14">mdi-trash-can-outline</v-icon></button>
</div>
<NoticiaCarrossel :midias="noticia.midias" :titulo="noticia.titulo" :curtido="curtido" @curtir="curtirViaImagem"/>
<NoticiaAcoes :curtido="curtido" :curtidas-count="curtidasCount" @curtir="alternarCurtida" @comentar="comentariosAbertos=true" @compartilhar="compartilhar"/>
<NoticiaLegenda :titulo="noticia.titulo" :descricao="noticia.texto" :comentarios-count="comentariosCount" :data-publicacao="noticia.data_publicacao" @ver-comentarios="comentariosAbertos=true"/>
<NoticiaComentariosDialog v-model="comentariosAbertos" :noticia-id="noticia.id" @comentario-adicionado="onComentarioAdicionado"/>
</article>
</template>
<style scoped>
.noticia-card{position:relative;background:#fff;border:1px solid rgba(13,31,60,.08);border-radius:16px;overflow:hidden;font-family:'DM Sans',sans-serif}.admin-acoes{position:absolute;top:10px;right:10px;z-index:5;display:flex;gap:6px}.admin-btn{display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:999px;border:0;background:rgba(13,31,60,.55);color:#fff;cursor:pointer;backdrop-filter:blur(2px)}.admin-btn:hover{background:rgba(13,31,60,.75)}.admin-btn-excluir:hover{background:#dc2626}
</style>