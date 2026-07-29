<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from './services/api'

const resposta = ref('carregando...')

onMounted(async () => {
  try {
    const { data } = await api.get('/ping')
    resposta.value = data.message
  } catch (error) {
    resposta.value = 'erro ao conectar com a API'
    console.error(error)
  }
})
</script>

<template>
  <div>
    <h1>Teste de conexão com a API</h1>
    <p>Resposta do backend: {{ resposta }}</p>
  </div>
</template>