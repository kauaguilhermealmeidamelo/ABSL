<script setup>
import { computed, onMounted, ref } from 'vue'
import TransparenciaItem from '@/components/transparencia/TransparenciaItem.vue'
import TransparenciaFormModal from '@/components/transparencia/TransparenciaFormModal.vue'
import { transparenciaService } from '@/services/transparencia'
const aba=ref('atas'), documentos=ref([]), loading=ref(false), error=ref(''), modal=ref(false)
const lista=computed(()=>documentos.value.filter(d=>d.categoria===aba.value))
async function carregar(){loading.value=true;error.value='';try{documentos.value=await transparenciaService.list()}catch{error.value='Não foi possível carregar os documentos.'}finally{loading.value=false}}
onMounted(carregar)
async function salvar(d){try{documentos.value=[await transparenciaService.create(d,aba.value),...documentos.value]}catch{error.value='Não foi possível salvar o documento.'}}
async function excluir(id){try{await transparenciaService.remove(id);documentos.value=documentos.value.filter(d=>d.id!==id)}catch{error.value='Não foi possível excluir o documento.'}}
</script>
<template>
<div class="manager">
<div class="toolbar"><div><h2>Transparência</h2><p>Gerencie atas e prestações de contas publicadas.</p></div><button class="primary" type="button" @click="modal=true"><v-icon size="16">mdi-plus</v-icon>{{aba==='atas'?'Nova ata':'Nova prestação de contas'}}</button></div>
<div class="tabs"><button :class="{active:aba==='atas'}" @click="aba='atas'">Atas</button><button :class="{active:aba==='contas'}" @click="aba='contas'">Prestação de contas</button></div>
<p v-if="loading" class="state">Carregando...</p><p v-else-if="error" class="state error">{{error}}</p>
<div v-else class="list"><TransparenciaItem v-for="d in lista" :key="d.id" :titulo="d.referencia" :descricao="d.descricao" :arquivo-url="d.arquivo_url" :is-admin="true" @excluir="excluir(d.id)"/><p v-if="!lista.length" class="state">Nenhum documento cadastrado.</p></div>
<TransparenciaFormModal v-model="modal" :tipo="aba==='atas'?'ata':'contas'" @salvar="salvar"/>
</div>
</template>
<style scoped>
.manager{display:flex;flex-direction:column;gap:18px;font-family:'DM Sans',sans-serif}.toolbar{display:flex;justify-content:space-between;align-items:center;gap:16px}.toolbar h2{margin:0;color:#0d1f3c}.toolbar p{margin:5px 0 0;color:#5a6a85;font-size:13px}.primary{display:flex;align-items:center;gap:7px;border:0;border-radius:10px;background:#1a3f8f;color:#fff;padding:10px 16px;font-weight:600;cursor:pointer}.tabs{display:flex;gap:4px}.tabs button{border:0;border-radius:9px;padding:9px 14px;background:#eef3fb;color:#5a6a85;font-weight:600;cursor:pointer}.tabs .active{background:#1a3f8f;color:#fff}.list{display:flex;flex-direction:column;gap:12px}.state{padding:28px 0;color:#5a6a85;text-align:center}.error{color:#dc2626}@media(max-width:600px){.toolbar{align-items:stretch;flex-direction:column}.primary{justify-content:center}}
</style>