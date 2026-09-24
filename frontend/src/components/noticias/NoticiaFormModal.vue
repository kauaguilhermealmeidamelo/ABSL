<script setup>
import { ref, watch, computed } from 'vue'
import { noticiasService } from '@/services/noticias'
import NoticiaMidiaManager from './NoticiaMidiaManager.vue'
const props=defineProps({modelValue:{type:Boolean,default:false},noticia:{type:Object,default:null},erroServidor:{type:String,default:''}})
const emit=defineEmits(['update:modelValue','criada','salvar'])
const form=ref({titulo:'',data_publicacao:'',texto:''}),idAtual=ref(null),midias=ref([]),erroData=ref(''),erroLocal=ref(''),salvando=ref(false)
function formatDateForInput(d){if(!d)return'';const b=/^(\d{2})\/(\d{2})\/(\d{4})$/.exec(d);if(b){const[,day,month,year]=b;return`${year}-${month}-${day}`}const i=/^(\d{4})-(\d{2})-(\d{2})/.exec(d);return i?`${i[1]}-${i[2]}-${i[3]}`:''}
watch(()=>props.modelValue,open=>{if(!open)return;erroData.value='';erroLocal.value='';if(props.noticia){form.value={titulo:props.noticia.titulo,data_publicacao:formatDateForInput(props.noticia.data_publicacao),texto:props.noticia.texto};idAtual.value=props.noticia.id;midias.value=props.noticia.midias??[]}else{form.value={titulo:'',data_publicacao:'',texto:''};idAtual.value=null;midias.value=[]}},{immediate:true})
function fechar(){emit('update:modelValue',false)}
const podeSalvar=computed(()=>!!form.value.titulo.trim()&&!!form.value.data_publicacao&&midias.value.length>0)
async function salvar(){
  erroData.value='';erroLocal.value=''
  if(!form.value.titulo.trim())return
  if(!form.value.data_publicacao){erroData.value='Data de Publicação Obrigatória';return}
  if(!idAtual.value&&!midias.value.length){erroLocal.value='Adicione pelo menos uma foto ou vídeo para salvar a notícia.';return}
  const payload={titulo:form.value.titulo,data_publicacao:form.value.data_publicacao,texto:form.value.texto,categoria:props.noticia?.categoria??'gremio'}
  salvando.value=true
  try{
    if(idAtual.value){emit('salvar',{id:idAtual.value,payload});return}
    const pendentes=midias.value.filter(m=>m.arquivo).map(m=>m.arquivo)
    const criada=await noticiasService.create(payload)
    try{
      const atualizada=await noticiasService.adicionarMidias(criada.id,pendentes)
      const final={...criada,midias:atualizada.midias??[]}
      idAtual.value=final.id;midias.value=final.midias;emit('criada',final)
    }catch(uploadError){
      await noticiasService.remove(criada.id).catch(()=>{})
      throw uploadError
    }
  }catch(err){erroLocal.value=err?.response?.data?.errors?.data_publicacao?.[0]||err?.response?.data?.message||'Erro ao salvar notícia.'}
  finally{salvando.value=false}
}
</script>
<template>
<v-dialog :model-value="modelValue" @update:model-value="$emit('update:modelValue',$event)" max-width="520" persistent>
<v-card class="noticia-modal">
<v-card-title class="modal-title">{{idAtual?'Editar notícia':'Nova notícia'}}</v-card-title>
<v-card-text class="modal-body">
<label class="field-label">Título</label><input v-model="form.titulo" type="text" class="field-input" placeholder="Título da notícia"/>
<label class="field-label">Data de publicação</label><input v-model="form.data_publicacao" type="date" class="field-input" :class="{'field-input-erro':erroData||erroServidor||erroLocal}"/>
<span v-if="erroData||erroServidor||erroLocal" class="field-erro">{{erroData||erroServidor||erroLocal}}</span>
<label class="field-label">Resumo / texto</label><textarea v-model="form.texto" rows="4" class="field-textarea" placeholder="Descrição breve da notícia"/>
<NoticiaMidiaManager v-model="midias" :noticia-id="idAtual"/>
</v-card-text>
<v-card-actions class="modal-actions">
<button type="button" class="btn-cancelar" @click="fechar">{{idAtual?'Concluir':'Cancelar'}}</button>
<button type="button" class="btn-salvar" :disabled="salvando||!podeSalvar" @click="salvar">{{salvando?'Salvando...':'Salvar'}}</button>
</v-card-actions>
</v-card>
</v-dialog>
</template>
<style scoped>
.noticia-modal{border-radius:18px!important;font-family:'DM Sans',sans-serif;padding:8px}.modal-title{color:#0d1f3c;font-weight:700;font-size:18px;padding:20px 20px 4px}.modal-body{display:flex;flex-direction:column;gap:4px;padding:8px 20px 4px}.field-label{font-size:12px;font-weight:600;color:#5a6a85;margin-top:12px;margin-bottom:4px}.field-input,.field-textarea{width:100%;border:1px solid rgba(13,31,60,.15);border-radius:12px;padding:10px 12px;font-size:14px;background:#eef3fb;font-family:inherit;color:#0d1f3c;box-sizing:border-box}.field-textarea{resize:none;line-height:1.6}.field-input:focus,.field-textarea:focus{outline:none;border-color:#1a3f8f}.field-input-erro{border-color:#dc2626!important}.field-erro{display:block;color:#dc2626;font-size:11px;margin-top:4px}.modal-actions{padding:12px 20px 20px;display:flex;justify-content:flex-end;gap:10px}.btn-cancelar,.btn-salvar{padding:9px 20px;border-radius:999px;font-size:13px;font-weight:600;cursor:pointer;border:none}.btn-cancelar{background:transparent;color:#5a6a85;border:1px solid rgba(13,31,60,.15)}.btn-salvar{background:#1a3f8f;color:#fff}.btn-salvar:disabled{opacity:.6;cursor:not-allowed}.btn-salvar:hover:not(:disabled){background:#0d1f3c}
</style>