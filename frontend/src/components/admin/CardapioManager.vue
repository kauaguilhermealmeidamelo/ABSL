<script setup>
import CardapioSemana from '@/components/cardapio/CardapioSemana.vue'
import CardapioTabela from '@/components/cardapio/CardapioTabela.vue'
import { CARDAPIO_DIAS, cardapioSemana, cardapioDias, setCardapioSemana, setCardapioDia } from '@/stores/appData'
function updateSemana(v){setCardapioSemana(v)}
function updateDia({dia,valor}){setCardapioDia(dia,valor)}
</script>
<template><div class="manager"><div><h2>Cardápio</h2><p>Atualize a semana e os itens do cardápio.</p></div><CardapioSemana :semana="cardapioSemana" :is-admin="true" @update="updateSemana"/><CardapioTabela :dias="CARDAPIO_DIAS" :cardapio="cardapioDias" :is-admin="true" @update-dia="updateDia"/></div></template>
<style scoped>.manager{display:flex;flex-direction:column;gap:18px;font-family:'DM Sans',sans-serif}.manager h2{margin:0;color:#0d1f3c}.manager p{margin:5px 0 0;color:#5a6a85;font-size:13px}</style>