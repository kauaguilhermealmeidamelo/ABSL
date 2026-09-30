<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import PageHeader from '@/components/common/PageHeader.vue'
import AccountForm from '@/components/auth/AccountForm.vue'
import { user } from '@/stores/auth'
const route = useRoute()
const verificado = computed(() => route.query.verificado === '1')
const logado = computed(() => !!user.value)
</script>

<template>
  <div class="conta-page">
    <PageHeader label="ABSL" title="Minha conta" subtitle="Gerencie seu nome, @, e-mail e turma." />
    <p v-if="verificado" class="ok-banner" role="status">E-mail verificado com sucesso.</p>
    <div v-if="logado" class="card"><AccountForm /></div>
    <div v-else class="card vazio"><p>Você precisa entrar para gerenciar sua conta.</p></div>
  </div>
</template>

<style scoped>
.conta-page { font-family: 'DM Sans', sans-serif; padding: 24px; max-width: 560px; margin: 0 auto; }
.card { background: #fff; border: 1px solid rgba(13,31,60,.08); border-radius: 16px; padding: 20px; }
.ok-banner { background: #dcfce7; color: #166534; border-radius: 12px; padding: 10px 14px; font-size: 13.5px; margin: 0 0 16px; }
.vazio p { color: #5a6a85; font-size: 14px; margin: 0; text-align: center; }
@media (max-width: 480px) { .conta-page { padding: 16px; } .card { padding: 16px; } }
</style>
