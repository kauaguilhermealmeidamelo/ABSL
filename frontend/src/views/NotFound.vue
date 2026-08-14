<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

function irParaInicio() {
  router.push('/inicio')
}
</script>

<template>
  <div class="notfound-page">
    <span class="code">404</span>
    <h1 class="title">Página não encontrada</h1>
    <p class="subtitle">O endereço acessado não existe ou foi movido.</p>

    <button type="button" class="btn-primary" @click="irParaInicio">
      Voltar para o Início
    </button>
  </div>
</template>

<style scoped>
.notfound-page {
  font-family: var(--font-body, 'DM Sans', sans-serif);
  min-height: calc(100vh - 64px);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 40px 24px;
}

.code {
  font-family: var(--font-heading, 'Playfair Display', serif);
  font-weight: 700;
  font-size: 64px;
  color: var(--color-navy, #0F2038);
  line-height: 1;
}

.title {
  font-size: 20px;
  font-weight: 700;
  color: var(--color-navy, #0F2038);
  margin: 12px 0 6px;
}

.subtitle {
  color: var(--color-text-secondary, #6B7C93);
  font-size: 14px;
  margin: 0 0 24px;
}

.btn-primary {
  border: none;
  background: var(--color-navy, #0F2038);
  color: #ffffff;
  padding: 11px 24px;
  border-radius: var(--radius-pill, 999px);
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.btn-primary:hover {
  background: var(--color-navy-soft, #16509B);
}
</style>