<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
const props = defineProps({ midias:{type:Array,default:()=>[]}, titulo:{type:String,required:true}, curtido:{type:Boolean,default:false} })
const emit = defineEmits(['curtir'])
const indiceAtual=ref(0), trilhaRef=ref(null), videoRefs=ref([])
function registrarVideo(el,i){if(el)videoRefs.value[i]=el}
function atualizarIndice(){const el=trilhaRef.value;if(!el||!el.clientWidth)return;const n=Math.max(0,Math.min(props.midias.length-1,Math.round(el.scrollLeft/el.clientWidth)));if(n===indiceAtual.value)return;videoRefs.value[indiceAtual.value]?.pause();indiceAtual.value=n;videoRefs.value[n]?.play().catch(()=>{})}
function irPara(i){const el=trilhaRef.value;if(!el)return;const n=Math.max(0,Math.min(props.midias.length-1,i));el.scrollTo({left:n*el.clientWidth,behavior:'smooth'})}
function anterior(){irPara(indiceAtual.value-1)} function proxima(){irPara(indiceAtual.value+1)}
function iniciarVideo(){nextTick(()=>videoRefs.value[indiceAtual.value]?.play().catch(()=>{}))}
onMounted(()=>{iniciarVideo();window.addEventListener('resize',iniciarVideo)})
onBeforeUnmount(()=>{window.removeEventListener('resize',iniciarVideo);videoRefs.value.forEach(v=>v?.pause())})
const mostrarCoracao=ref(false);let timeoutClique=null
function onClique(){if(timeoutClique){clearTimeout(timeoutClique);timeoutClique=null;mostrarCoracao.value=false;requestAnimationFrame(()=>{mostrarCoracao.value=true;setTimeout(()=>{mostrarCoracao.value=false},700)});if(!props.curtido)emit('curtir');return}timeoutClique=setTimeout(()=>{timeoutClique=null},260)}
const temMultiplas=computed(()=>props.midias.length>1)
</script>
<template>
<div class="carrossel">
  <div v-if="midias.length" ref="trilhaRef" class="carrossel-trilha" @scroll.passive="atualizarIndice">
    <div v-for="(m,i) in midias" :key="m.id ?? m.url ?? i" class="carrossel-item">
      <video v-if="m.tipo==='video'" :ref="el=>registrarVideo(el,i)" :src="m.url" muted loop playsinline preload="metadata" draggable="false" @click.stop="onClique"/>
      <img v-else :src="m.url" :alt="titulo" draggable="false" @click.stop="onClique"/>
    </div>
  </div>
  <div v-else class="carrossel-textura"/>
  <template v-if="temMultiplas">
    <button v-if="indiceAtual>0" type="button" class="carrossel-seta carrossel-seta-esquerda" aria-label="Mídia anterior" @click.stop="anterior"><v-icon size="20">mdi-chevron-left</v-icon></button>
    <button v-if="indiceAtual<midias.length-1" type="button" class="carrossel-seta carrossel-seta-direita" aria-label="Próxima mídia" @click.stop="proxima"><v-icon size="20">mdi-chevron-right</v-icon></button>
    <span class="carrossel-contador">{{indiceAtual+1}}/{{midias.length}}</span>
    <div class="carrossel-dots"><button v-for="(m,i) in midias" :key="m.id ?? i" type="button" class="dot" :class="{'dot-ativo':i===indiceAtual}" :aria-label="'Ir para mídia '+(i+1)" @click.stop="irPara(i)"/></div>
  </template>
  <transition name="coracao-pop"><v-icon v-if="mostrarCoracao" size="84" color="#ffffff" class="coracao-flutuante">mdi-heart</v-icon></transition>
</div>
</template>
<style scoped>
.carrossel{position:relative;height:340px;overflow:hidden;background:#000}.carrossel-trilha{display:flex;width:100%;height:100%;overflow-x:auto;overflow-y:hidden;overscroll-behavior-x:contain;scroll-snap-type:x mandatory;scrollbar-width:none;-webkit-overflow-scrolling:touch;touch-action:pan-x}.carrossel-trilha::-webkit-scrollbar{display:none}.carrossel-item{flex:0 0 100%;min-width:100%;height:100%;scroll-snap-align:start;display:flex;align-items:center;justify-content:center;background:#000}.carrossel-item img,.carrossel-item video{width:100%;height:100%;display:block;object-fit:contain;background:#000;border:0}.carrossel-item video{touch-action:pan-x;user-select:none}.carrossel-textura{height:100%;background:linear-gradient(135deg,#1a3f8f,#16509b)}.carrossel-seta{position:absolute;top:50%;transform:translateY(-50%);width:34px;height:34px;border:0;border-radius:50%;background:rgba(0,0,0,.48);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:3;backdrop-filter:blur(3px)}.carrossel-seta:hover{background:rgba(0,0,0,.7)}.carrossel-seta-esquerda{left:10px}.carrossel-seta-direita{right:10px}.carrossel-contador{position:absolute;top:12px;right:12px;z-index:3;background:rgba(0,0,0,.5);color:#fff;font-size:11px;font-weight:600;padding:3px 9px;border-radius:999px;font-family:'DM Mono',monospace}.carrossel-dots{position:absolute;bottom:10px;left:0;right:0;display:flex;justify-content:center;gap:5px;z-index:3}.dot{width:6px;height:6px;padding:0;border:0;border-radius:999px;background:rgba(255,255,255,.5);cursor:pointer}.dot-ativo{background:#fff;transform:scale(1.15)}.coracao-flutuante{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);filter:drop-shadow(0 2px 10px rgba(0,0,0,.35));pointer-events:none;z-index:4}.coracao-pop-enter-active{transition:transform .25s cubic-bezier(.2,1.4,.4,1),opacity .25s ease}.coracao-pop-leave-active{transition:opacity .3s ease}.coracao-pop-enter-from{transform:translate(-50%,-50%) scale(.4);opacity:0}.coracao-pop-leave-to{opacity:0}@media(max-width:700px){.carrossel-seta{display:none}}
</style>