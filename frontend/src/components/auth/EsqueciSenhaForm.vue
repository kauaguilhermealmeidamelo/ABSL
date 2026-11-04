<script setup>
import { ref } from 'vue'
import { forgotPassword } from '@/services/auth'

const email = ref('')
const loading = ref(false)
const erro = ref('')
const enviado = ref('')

async function submit() {
  erro.value = ''
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
    erro.value = 'Informe um e-mail válido.'
    return
  }
  loading.value = true
  try {
    enviado.value = await forgotPassword(email.value.trim())
  } catch (err) {
    erro.value = err instanceof Error ? err.message : 'Não foi possível enviar o e-mail.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div v-if="enviado" class="aviso-ok" role="status">
    <v-icon size="20">mdi-email-check-outline</v-icon>
    <p>{{ enviado }}</p>
  </div>

  <form v-else class="auth-form" novalidate @submit.prevent="submit">
    <label class="field-label" for="esqueci-email">E-mail da sua conta</label>
    <input id="esqueci-email" v-model="email" class="campo-input" :class="{ 'tem-erro': erro }" type="email"
      autocomplete="email" placeholder="seu@email.com" />
    <p v-if="erro" class="erro-campo" role="alert">{{ erro }}</p>
    <v-btn type="submit" :loading="loading" class="enter-btn" block>Enviar link de redefinição</v-btn>
  </form>
</template>

<style scoped>
.auth-form{display:flex;flex-direction:column;gap:10px}
.field-label{font-size:14px;font-weight:600;color:#1a2f4a}
.campo-input{width:100%;box-sizing:border-box;background:#eef1f6;border:2px solid transparent;border-radius:14px;padding:13px 16px;font-size:15px;color:#1a2f4a;font-family:inherit}
.campo-input:focus{outline:2px solid #16509b;outline-offset:1px}
.campo-input.tem-erro{border-color:#dc2626}
.enter-btn{background-color:#16509b!important;color:#fff!important;border-radius:28px!important;height:52px!important;text-transform:none!important;font-weight:700!important;font-size:15px!important;box-shadow:none!important;margin-top:6px}
.erro-campo{color:#dc2626;font-size:13px;margin:0}
.aviso-ok{display:flex;gap:10px;align-items:flex-start;background:#e6f6ec;color:#15803d;border-radius:14px;padding:14px 16px;font-size:14px;line-height:1.5}
.aviso-ok p{margin:0}
</style>
