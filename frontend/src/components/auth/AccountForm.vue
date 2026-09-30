<script setup>
import { computed, ref } from 'vue'
import { updateProfile, resendVerification } from '@/services/auth'
import { user } from '@/stores/auth'
import TurmaSelect from '@/components/auth/TurmaSelect.vue'
const emit = defineEmits(['salvo'])
const name = ref(user.value?.name ?? '')
const username = ref(user.value?.username ?? '')
const email = ref(user.value?.email ?? '')
const turma = ref(user.value?.turma ?? '')
const loading = ref(false)
const reenviando = ref(false)
const ok = ref('')
const erro = ref('')
const erros = ref({ name: '', username: '', email: '', turma: '' })
const naoVerificado = computed(() => !!user.value && !user.value.email_verified_at)
function norm(v) { return String(v ?? '').trim().replace(/^@+/, '').toLowerCase() }
function validar() {
  erros.value = { name: '', username: '', email: '', turma: '' }
  if (!name.value.trim()) erros.value.name = 'Informe seu nome.'
  const u = norm(username.value)
  if (!u) erros.value.username = 'Escolha seu @ de usuário.'
  else if (!/^[a-z0-9._]+$/.test(u)) erros.value.username = 'Use apenas letras, números, ponto e sublinhado.'
  if (!email.value.trim()) erros.value.email = 'Informe seu e-mail.'
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) erros.value.email = 'Informe um e-mail válido.'
  return Object.values(erros.value).every((e) => !e)
}
async function submit() {
  ok.value = ''; erro.value = ''
  if (!validar()) return
  loading.value = true
  try {
    const data = await updateProfile({ name: name.value.trim(), username: norm(username.value), email: email.value.trim(), turma: turma.value === '__nao_aluno' ? null : turma.value || null })
    ok.value = data?.email_verification_required ? 'Perfil salvo. Verifique seu novo e-mail.' : 'Perfil salvo.'
    emit('salvo')
  } catch (err) { erro.value = err instanceof Error ? err.message : 'Não foi possível salvar.' }
  finally { loading.value = false }
}
async function reenviar() {
  erro.value = ''; ok.value = ''
  reenviando.value = true
  try { await resendVerification(); ok.value = 'E-mail de verificação reenviado.' }
  catch { erro.value = 'Não foi possível reenviar agora. Tente mais tarde.' }
  finally { reenviando.value = false }
}
</script>

<template>
  <form class="auth-form" novalidate @submit.prevent="submit">
    <p v-if="naoVerificado" class="aviso-verificar" role="alert">Seu e-mail ainda não foi verificado. Verifique sua caixa de entrada. <button type="button" class="link" :disabled="reenviando" @click="reenviar">{{ reenviando ? 'Enviando...' : 'Reenviar e-mail' }}</button></p>
    <div class="campo">
      <label class="field-label" for="conta-nome">Nome</label>
      <input id="conta-nome" v-model="name" class="campo-input" :class="{ 'tem-erro': erros.name }" type="text" autocomplete="name" />
      <p v-if="erros.name" class="erro-campo" role="alert">{{ erros.name }}</p>
    </div>
    <div class="campo">
      <label class="field-label" for="conta-username">@ do usuário</label>
      <div class="arroba-wrap"><span class="arroba-prefix" aria-hidden="true">@</span><input id="conta-username" v-model="username" class="campo-input campo-arroba" :class="{ 'tem-erro': erros.username }" type="text" autocomplete="username" maxlength="30" /></div>
      <p v-if="erros.username" class="erro-campo" role="alert">{{ erros.username }}</p>
    </div>
    <div class="campo">
      <label class="field-label" for="conta-email">E-mail</label>
      <input id="conta-email" v-model="email" class="campo-input" :class="{ 'tem-erro': erros.email }" type="email" autocomplete="email" />
      <p v-if="erros.email" class="erro-campo" role="alert">{{ erros.email }}</p>
    </div>
    <div class="campo">
      <label class="field-label" for="conta-turma">Turma</label>
      <TurmaSelect id="conta-turma" v-model="turma" :erro="erros.turma" />
    </div>
    <v-btn type="submit" :loading="loading" class="enter-btn" block>Salvar</v-btn>
    <p v-if="ok" class="ok" role="status">{{ ok }}</p>
    <p v-if="erro" class="erro-geral" role="alert">{{ erro }}</p>
  </form>
</template>

<style scoped>
.auth-form { display: flex; flex-direction: column; gap: 14px; }
.aviso-verificar { background: #fef3c7; border: 1px solid #f59e0b55; color: #92400e; font-size: 13px; border-radius: 12px; padding: 10px 12px; margin: 0; line-height: 1.5; }
.link { background: none; border: none; color: #16509b; font-weight: 700; cursor: pointer; padding: 0; font-size: 13px; }
.link:disabled { opacity: 0.6; }
.campo { display: flex; flex-direction: column; }
.field-label { font-size: 14px; font-weight: 600; color: #1a2f4a; margin: 0 0 8px; }
.campo-input { width: 100%; background: #eef1f6; border: 2px solid transparent; border-radius: 14px; padding: 13px 16px; font-size: 15px; color: #1a2f4a; font-family: inherit; }
.campo-input:focus { outline: 2px solid #16509b; outline-offset: 1px; }
.campo-input.tem-erro { border-color: #dc2626; }
.arroba-wrap { position: relative; }
.arroba-prefix { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #5a6a85; font-weight: 700; font-size: 15px; pointer-events: none; }
.campo-arroba { padding-left: 34px; }
.enter-btn { background-color: #16509b !important; color: #fff !important; border-radius: 28px !important; height: 52px !important; text-transform: none !important; font-weight: 700 !important; font-size: 16px !important; box-shadow: none !important; margin-top: 4px; }
.erro-campo { color: #dc2626; font-size: 12.5px; margin: 6px 2px 0; }
.erro-geral { color: #dc2626; font-size: 13px; text-align: center; margin: 4px 0 0; }
.ok { color: #15803d; font-size: 13px; text-align: center; margin: 4px 0 0; }
</style>
