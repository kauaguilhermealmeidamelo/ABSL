<script setup>
import { onMounted, ref } from 'vue';
import api from '@/services/api'
const msg = ref('servidor off')
</script> 

<template>
  <div>
    <h1>Bem-vindo ao Grêmio Athos Bulcão</h1>
    <p>Selecione uma opção no menu lateral.</p>
  </div>
</template>
<style scoped>
template{
  background-color: rgb(33, 112, 180);
}
div{
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100vw;
  height: 100vh;
}
</style>
