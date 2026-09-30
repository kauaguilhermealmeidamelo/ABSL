<script setup>
import { computed, ref } from 'vue'
import { login } from '@/services/auth'

const props = defineProps({
  erroExterno: { type: String, default: '' },
})

const emit = defineEmits(['logado'])

const email = ref('')
const password = ref('')
const remember = ref(false)
const mostrarSenha = ref(false)
const loading = ref(false)
const erro = ref('')
const erros = ref({ email: '', password: '' })

const erroGeral = computed(() => props.erroExterno || erro.value)

function validar() {
  erros.value = { email: '', password: '' }
  if (!email.value.trim()) erros.value.email = 'Informe seu e-mail.'
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim()))
    erros.value.email = 'Informe um e-mail válido.'
  if (!password.value) erros.value.password = 'Informe sua senha.'
  return !erros.value.email && !erros.value.password
}

async function submit() {
  erro.value = ''
  if (!validar()) return
  loading.value = true
  try {
    await login(email.value.trim(), password.value, remember.value)
    email.value = ''
    password.value = ''
    remember.value = false
    emit('logado')
  } catch (err) {
    erro.value = err instanceof Error ? err.message : 'Credenciais inválidas.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <form class="auth-form" novalidate @submit.prevent="submit">
    <div class="campo">
      <label class="field-label" for="login-email">E-mail</label>
      <input
        id="login-email"
        v-model="email"
        class="campo-input"
        :class="{ 'tem-erro': erros.email }"
        type="email"
        name="email"
        autocomplete="email"
        placeholder="seu@email.com"
        :aria-invalid="erros.email ? 'true' : 'false'"
        :aria-describedby="erros.email ? 'login-email-erro' : undefined"
      />
      <p v-if="erros.email" id="login-email-erro" class="erro-campo" role="alert">{{ erros.email }}</p>
    </div>

    <div class="campo">
      <label class="field-label" for="login-senha">Senha</label>
      <div class="senha-wrap">
        <input
          id="login-senha"
          v-model="password"
          class="campo-input"
          :class="{ 'tem-erro': erros.password }"
          :type="mostrarSenha ? 'text' : 'password'"
          name="password"
          autocomplete="current-password"
          placeholder="Sua senha"
          :aria-invalid="erros.password ? 'true' : 'false'"
          :aria-describedby="erros.password ? 'login-senha-erro' : undefined"
          @keydown.enter="submit"
        />
        <button
          type="button"
          class="senha-toggle"
          :aria-label="mostrarSenha ? 'Ocultar senha' : 'Mostrar senha'"
          @click="mostrarSenha = !mostrarSenha"
        >
          <v-icon size="20">{{ mostrarSenha ? 'mdi-eye-off-outline' : 'mdi-eye-outline' }}</v-icon>
        </button>
      </div>
      <p v-if="erros.password" id="login-senha-erro" class="erro-campo" role="alert">{{ erros.password }}</p>
    </div>

    <label class="lembrar">
      <input v-model="remember" type="checkbox" name="remember" />
      <span>Lembrar de mim</span>
    </label>

    <v-btn type="submit" :loading="loading" class="enter-btn" block>Entrar</v-btn>

    <p v-if="erroGeral" class="erro-geral" role="alert">{{ erroGeral }}</p>
  </form>
</template>

<style scoped>
.auth-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.campo {
  display: flex;
  flex-direction: column;
}
.field-label {
  font-size: 14px;
  font-weight: 600;
  color: #1a2f4a;
  margin: 0 0 8px;
}
.campo-input {
  width: 100%;
  background: #eef1f6;
  border: 2px solid transparent;
  border-radius: 14px;
  padding: 13px 16px;
  font-size: 15px;
  color: #1a2f4a;
  font-family: inherit;
}
.campo-input::placeholder {
  color: #8fa3bf;
}
.campo-input:focus {
  outline: 2px solid #16509b;
  outline-offset: 1px;
}
.campo-input.tem-erro {
  border-color: #dc2626;
}
.senha-wrap {
  position: relative;
}
.senha-wrap .campo-input {
  padding-right: 48px;
}
.senha-toggle {
  position: absolute;
  right: 6px;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  background: transparent;
  color: #5a6a85;
  cursor: pointer;
  padding: 8px;
  min-width: 44px;
  min-height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.lembrar {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #1a2f4a;
  cursor: pointer;
  user-select: none;
}
.lembrar input {
  width: 18px;
  height: 18px;
  accent-color: #16509b;
  cursor: pointer;
}
.enter-btn {
  background-color: #16509b !important;
  color: #fff !important;
  border-radius: 28px !important;
  height: 52px !important;
  text-transform: none !important;
  font-weight: 700 !important;
  font-size: 16px !important;
  box-shadow: none !important;
  margin-top: 4px;
}
.erro-campo {
  color: #dc2626;
  font-size: 12.5px;
  margin: 6px 2px 0;
}
.erro-geral {
  color: #dc2626;
  font-size: 13px;
  text-align: center;
  margin: 4px 0 0;
}
</style>
