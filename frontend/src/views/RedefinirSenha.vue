<script setup>
import PageHeader from '@/components/common/PageHeader.vue'
import RedefinirSenhaForm from '@/components/auth/RedefinirSenhaForm.vue'
</script>

<template>
  <div class="recuperar-page">
    <PageHeader label="ABSL" title="Redefinir senha" subtitle="Escolha uma nova senha para a sua conta." />
    <div class="card">
      <RedefinirSenhaForm />
    </div>
  </div>
</template>

<style scoped>
.recuperar-page{max-width:480px;margin:0 auto;padding:24px 16px 48px;font-family:'DM Sans',sans-serif}
.card{background:#fff;border:1px solid rgba(13,31,60,.08);border-radius:16px;padding:24px}
</style>
