<script setup>
import { computed, ref } from 'vue'
import { register } from '@/services/auth'
import TurmaSelect from '@/components/auth/TurmaSelect.vue'
const emit = defineEmits(['registrado'])
const name = ref('')
const username = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const turma = ref('')
const mostrarSenha = ref(false)
const loading = ref(false)
const erroGeral = ref('')
const erros = ref({ name: '', username: '', email: '', password: '', password_confirmation: '', turma: '' })
function norm(v) { return v.trim().replace(/^@+/, '').toLowerCase() }
function validar() {
  erros.value = { name: '', username: '', email: '', password: '', password_confirmation: '', turma: '' }
  if (!name.value.trim()) erros.value.name = 'Informe seu nome.'
  const u = norm(username.value)
  if (!u) erros.value.username = 'Escolha seu @ de usuário.'
  else if (!/^[a-z0-9._]+$/.test(u)) erros.value.username = 'Use apenas letras, números, ponto e sublinhado.'
  else if (u.length > 30) erros.value.username = 'Máximo de 30 caracteres.'
  if (!email.value.trim()) erros.value.email = 'Informe seu e-mail.'
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) erros.value.email = 'Informe um e-mail válido.'
  if (!password.value) erros.value.password = 'Crie uma senha.'
  else if (password.value.length < 8) erros.value.password = 'Mínimo de 8 caracteres.'
  if (!passwordConfirmation.value) erros.value.password_confirmation = 'Confirme a senha.'
  else if (password.value !== passwordConfirmation.value) erros.value.password_confirmation = 'As senhas não coincidem.'
  return Object.values(erros.value).every((e) => !e)
}
const dif = computed(() => !!password.value && !!passwordConfirmation.value && password.value !== passwordConfirmation.value)
async function submit() {
  erroGeral.value = ''
  if (!validar()) return
  loading.value = true
  try {
    await register({ name: name.value.trim(), username: norm(username.value), email: email.value.trim(), password: password.value, password_confirmation: passwordConfirmation.value, turma: turma.value === '__nao_aluno' ? null : turma.value || null })
    emit('registrado')
  } catch (err) { erroGeral.value = err instanceof Error ? err.message : 'Não foi possível criar a conta.' }
  finally { loading.value = false }
}
</script>

<template>
  <form class="auth-form" novalidate @submit.prevent="submit">
    <div class="campo">
      <label class="field-label" for="reg-nome">Nome</label>
      <input id="reg-nome" v-model="name" class="campo-input" :class="{ 'tem-erro': erros.name }" type="text" autocomplete="name" placeholder="Seu nome completo" />
      <p v-if="erros.name" class="erro-campo" role="alert">{{ erros.name }}</p>
    </div>
    <div class="campo">
      <label class="field-label" for="reg-username">@ do usuário</label>
      <div class="arroba-wrap">
        <span class="arroba-prefix" aria-hidden="true">@</span>
        <input id="reg-username" v-model="username" class="campo-input campo-arroba" :class="{ 'tem-erro': erros.username }" type="text" autocomplete="username" placeholder="usuario" maxlength="30" />
      </div>
      <p class="ajuda">Esse @ será seu identificador dentro do site.</p>
      <p v-if="erros.username" class="erro-campo" role="alert">{{ erros.username }}</p>
    </div>
    <div class="campo">
      <label class="field-label" for="reg-email">E-mail</label>
      <input id="reg-email" v-model="email" class="campo-input" :class="{ 'tem-erro': erros.email }" type="email" autocomplete="email" placeholder="seu@email.com" />
      <p class="ajuda">Vamos enviar um e-mail de verificação para este endereço.</p>
      <p v-if="erros.email" class="erro-campo" role="alert">{{ erros.email }}</p>
    </div>
    <div class="campo">
      <label class="field-label" for="reg-senha">Senha</label>
      <div class="senha-wrap">
        <input id="reg-senha" v-model="password" class="campo-input" :class="{ 'tem-erro': erros.password }" :type="mostrarSenha ? 'text' : 'password'" autocomplete="new-password" placeholder="Mínimo de 8 caracteres" />
        <button type="button" class="senha-toggle" :aria-label="mostrarSenha ? 'Ocultar senha' : 'Mostrar senha'" @click="mostrarSenha = !mostrarSenha">
          <v-icon size="20">{{ mostrarSenha ? 'mdi-eye-off-outline' : 'mdi-eye-outline' }}</v-icon>
        </button>
      </div>
      <p v-if="erros.password" class="erro-campo" role="alert">{{ erros.password }}</p>
    </div>
    <div class="campo">
      <label class="field-label" for="reg-senha2">Confirmar senha</label>
      <input id="reg-senha2" v-model="passwordConfirmation" class="campo-input" :class="{ 'tem-erro': erros.password_confirmation || dif }" type="password" autocomplete="new-password" placeholder="Repita a senha" />
      <p v-if="erros.password_confirmation || dif" class="erro-campo" role="alert">{{ erros.password_confirmation || 'As senhas não coincidem.' }}</p>
    </div>
    <div class="campo">
      <label class="field-label" for="reg-turma">Turma <span class="opcional">(opcional)</span></label>
      <TurmaSelect id="reg-turma" v-model="turma" :erro="erros.turma" />
    </div>
    <v-btn type="submit" :loading="loading" class="enter-btn" block>Criar conta</v-btn>
    <p v-if="erroGeral" class="erro-geral" role="alert">{{ erroGeral }}</p>
  </form>
</template>

<style scoped>
.auth-form { display: flex; flex-direction: column; gap: 14px; }
.campo { display: flex; flex-direction: column; }
.field-label { font-size: 14px; font-weight: 600; color: #1a2f4a; margin: 0 0 8px; }
.opcional { font-weight: 400; color: #8fa3bf; font-size: 12.5px; }
.campo-input { width: 100%; background: #eef1f6; border: 2px solid transparent; border-radius: 14px; padding: 13px 16px; font-size: 15px; color: #1a2f4a; font-family: inherit; }
.campo-input::placeholder { color: #8fa3bf; }
.campo-input:focus { outline: 2px solid #16509b; outline-offset: 1px; }
.campo-input.tem-erro { border-color: #dc2626; }
.arroba-wrap { position: relative; }
.arroba-prefix { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #5a6a85; font-weight: 700; font-size: 15px; pointer-events: none; }
.campo-arroba { padding-left: 34px; }
.ajuda { font-size: 12.5px; color: #8fa3bf; margin: 6px 2px 0; }
.senha-wrap { position: relative; }
.senha-wrap .campo-input { padding-right: 48px; }
.senha-toggle { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); border: none; background: transparent; color: #5a6a85; cursor: pointer; padding: 8px; min-width: 44px; min-height: 44px; display: flex; align-items: center; justify-content: center; }
.enter-btn { background-color: #16509b !important; color: #fff !important; border-radius: 28px !important; height: 52px !important; text-transform: none !important; font-weight: 700 !important; font-size: 16px !important; box-shadow: none !important; margin-top: 4px; }
.erro-campo { color: #dc2626; font-size: 12.5px; margin: 6px 2px 0; }
.erro-geral { color: #dc2626; font-size: 13px; text-align: center; margin: 4px 0 0; }
</style>
