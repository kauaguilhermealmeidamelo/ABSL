<script setup>
import PageHeader from '@/components/common/PageHeader.vue'
import EsqueciSenhaForm from '@/components/auth/EsqueciSenhaForm.vue'
</script>

<template>
  <div class="recuperar-page">
    <PageHeader label="ABSL" title="Esqueci minha senha" subtitle="Informe o e-mail da sua conta e enviaremos um link para criar uma nova senha." />
    <div class="card">
      <EsqueciSenhaForm />
    </div>
  </div>
</template>

<style scoped>
.recuperar-page{max-width:480px;margin:0 auto;padding:24px 16px 48px;font-family:'DM Sans',sans-serif}
.card{background:#fff;border:1px solid rgba(13,31,60,.08);border-radius:16px;padding:24px}
</style>
