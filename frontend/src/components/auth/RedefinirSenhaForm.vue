<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { resetPassword } from '@/services/auth'

const route = useRoute()
const router = useRouter()

const token = String(route.query.token ?? '')
const email = String(route.query.email ?? '')
const linkInvalido = !token || !email

const password = ref('')
const confirmacao = ref('')
const mostrarSenha = ref(false)
const loading = ref(false)
const erro = ref('')
const sucesso = ref('')

async function submit() {
  erro.value = ''
  if (password.value.length < 8) { erro.value = 'A senha precisa ter pelo menos 8 caracteres.'; return }
  if (password.value !== confirmacao.value) { erro.value = 'As senhas não coincidem.'; return }
  loading.value = true
  try {
    sucesso.value = await resetPassword({ token, email, password: password.value, password_confirmation: confirmacao.value })
  } catch (err) {
    erro.value = err instanceof Error ? err.message : 'Não foi possível redefinir a senha.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <p v-if="linkInvalido" class="erro-campo" role="alert">Link inválido. Peça um novo link em "Esqueci minha senha".</p>

  <div v-else-if="sucesso" class="aviso-ok" role="status">
    <v-icon size="20">mdi-check-circle-outline</v-icon>
    <div>
      <p>{{ sucesso }}</p>
      <button type="button" class="link" @click="router.push('/')">Ir para o início</button>
    </div>
  </div>

  <form v-else class="auth-form" novalidate @submit.prevent="submit">
    <label class="field-label" for="nova-senha">Nova senha</label>
    <div class="senha-wrap">
      <input id="nova-senha" v-model="password" class="campo-input" :type="mostrarSenha ? 'text' : 'password'"
        autocomplete="new-password" placeholder="Mínimo 8 caracteres" />
      <button type="button" class="senha-toggle" :aria-label="mostrarSenha ? 'Ocultar senha' : 'Mostrar senha'"
        @click="mostrarSenha = !mostrarSenha">
        <v-icon size="20">{{ mostrarSenha ? 'mdi-eye-off-outline' : 'mdi-eye-outline' }}</v-icon>
      </button>
    </div>

    <label class="field-label" for="confirmar-senha">Confirmar nova senha</label>
    <input id="confirmar-senha" v-model="confirmacao" class="campo-input" :type="mostrarSenha ? 'text' : 'password'"
      autocomplete="new-password" placeholder="Repita a senha" />

    <p v-if="erro" class="erro-campo" role="alert">{{ erro }}</p>
    <v-btn type="submit" :loading="loading" class="enter-btn" block>Salvar nova senha</v-btn>
  </form>
</template>

<style scoped>
.auth-form{display:flex;flex-direction:column;gap:10px}
.field-label{font-size:14px;font-weight:600;color:#1a2f4a}
.campo-input{width:100%;box-sizing:border-box;background:#eef1f6;border:2px solid transparent;border-radius:14px;padding:13px 16px;font-size:15px;color:#1a2f4a;font-family:inherit}
.campo-input:focus{outline:2px solid #16509b;outline-offset:1px}
.senha-wrap{position:relative}
.senha-wrap .campo-input{padding-right:48px}
.senha-toggle{position:absolute;right:6px;top:50%;transform:translateY(-50%);border:none;background:transparent;color:#5a6a85;cursor:pointer;min-width:44px;min-height:44px;display:flex;align-items:center;justify-content:center}
.enter-btn{background-color:#16509b!important;color:#fff!important;border-radius:28px!important;height:52px!important;text-transform:none!important;font-weight:700!important;font-size:15px!important;box-shadow:none!important;margin-top:6px}
.erro-campo{color:#dc2626;font-size:13px;margin:0}
.aviso-ok{display:flex;gap:10px;align-items:flex-start;background:#e6f6ec;color:#15803d;border-radius:14px;padding:14px 16px;font-size:14px;line-height:1.5}
.aviso-ok p{margin:0 0 6px}
.link{border:0;background:transparent;color:#16509b;font:inherit;font-weight:600;cursor:pointer;padding:0}
</style>
