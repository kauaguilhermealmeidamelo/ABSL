<script setup>
import { ref, onMounted } from 'vue'
import { usuariosAdminService } from '@/services/usuariosAdmin'
import { user as usuarioLogado } from '@/stores/auth'

const usuarios = ref([])
const loading = ref(false)
const error = ref('')

const novo = ref({ name: '', email: '', password: '', password_confirmation: '', role: 'imprensa' })
const criando = ref(false)

const senhaEditando = ref(null) // id do usuário com o form de senha aberto
const novaSenha = ref('')
const salvandoSenha = ref(false)

const feedback = ref(null)
let feedbackTimeoutId = null

function mostrarFeedback(tipo, mensagem) {
  feedback.value = { tipo, mensagem }
  clearTimeout(feedbackTimeoutId)
  feedbackTimeoutId = setTimeout(() => { feedback.value = null }, 3500)
}

async function carregar() {
  loading.value = true
  error.value = ''
  try {
    usuarios.value = await usuariosAdminService.list()
  } catch {
    error.value = 'Não foi possível carregar os usuários.'
  } finally {
    loading.value = false
  }
}

onMounted(carregar)

async function cadastrar() {
  if (!novo.value.name.trim() || !novo.value.email.trim() || !novo.value.password) {
    mostrarFeedback('erro', 'Preencha nome, e-mail e senha.')
    return
  }
  if (novo.value.password !== novo.value.password_confirmation) {
    mostrarFeedback('erro', 'As senhas não coincidem.')
    return
  }
  if (novo.value.password.length < 8) {
    mostrarFeedback('erro', 'A senha precisa ter pelo menos 8 caracteres.')
    return
  }

  criando.value = true
  try {
    const criado = await usuariosAdminService.create({
      name: novo.value.name,
      email: novo.value.email,
      password: novo.value.password,
      role: novo.value.role,
    })
    usuarios.value = [...usuarios.value, criado].sort((a, b) => a.name.localeCompare(b.name, 'pt-BR'))
    novo.value = { name: '', email: '', password: '', password_confirmation: '', role: 'imprensa' }
    mostrarFeedback('sucesso', 'Usuário criado com sucesso.')
  } catch (err) {
    mostrarFeedback('erro', err?.response?.data?.message || 'Não foi possível criar o usuário.')
  } finally {
    criando.value = false
  }
}

function abrirSenha(id) {
  senhaEditando.value = id
  novaSenha.value = ''
}

function fecharSenha() {
  senhaEditando.value = null
  novaSenha.value = ''
}

async function salvarSenha(id) {
  if (!novaSenha.value || novaSenha.value.length < 8) {
    mostrarFeedback('erro', 'A nova senha precisa ter pelo menos 8 caracteres.')
    return
  }
  salvandoSenha.value = true
  try {
    await usuariosAdminService.updatePassword(id, novaSenha.value)
    mostrarFeedback('sucesso', 'Senha atualizada.')
    fecharSenha()
  } catch (err) {
    mostrarFeedback('erro', err?.response?.data?.message || 'Não foi possível atualizar a senha.')
  } finally {
    salvandoSenha.value = false
  }
}

async function excluir(u) {
  if (!confirm(`Excluir o usuário "${u.name}"? Essa ação não pode ser desfeita.`)) return
  try {
    await usuariosAdminService.remove(u.id)
    usuarios.value = usuarios.value.filter((item) => item.id !== u.id)
    mostrarFeedback('sucesso', 'Usuário excluído.')
  } catch (err) {
    mostrarFeedback('erro', err?.response?.data?.message || 'Não foi possível excluir o usuário.')
  }
}

function ehVoceMesmo(u) {
  return usuarioLogado.value?.id === u.id
}

function roleLabel(u) {
  if (u.role === 'admin') return 'Administrador'
  if (u.role === 'imprensa') return 'Imprensa'
  return 'Usuário'
}
</script>

<template>
  <div class="admin-users">
    <!-- Cadastro de novo usuário -->
    <div class="card">
      <h3 class="card-title">Cadastrar novo usuário</h3>

      <div class="form-grid">
        <div>
          <label class="field-label">Nome completo</label>
          <input v-model="novo.name" class="field-input" placeholder="Nome" />
        </div>
        <div>
          <label class="field-label">E-mail</label>
          <input v-model="novo.email" type="email" class="field-input" placeholder="email@exemplo.com" />
        </div>
        <div>
          <label class="field-label">Senha</label>
          <input v-model="novo.password" type="password" class="field-input" placeholder="Mínimo 8 caracteres" />
        </div>
        <div>
          <label class="field-label">Confirmar senha</label>
          <input v-model="novo.password_confirmation" type="password" class="field-input" placeholder="Repita a senha" />
        </div>
      </div>

      <label class="field-label">Tipo de acesso</label>
      <select v-model="novo.role" class="field-input field-select">
        <option value="admin">Administrador (acesso total)</option>
        <option value="imprensa">Imprensa (todas as páginas, exceto Administradores)</option>
      </select>

      <button type="button" class="btn-add" :disabled="criando" @click="cadastrar">
        <v-icon size="14">mdi-account-plus-outline</v-icon>
        {{ criando ? 'Cadastrando...' : 'Cadastrar usuário' }}
      </button>

      <transition name="feedback-fade">
        <p v-if="feedback" class="feedback-msg" :class="feedback.tipo === 'sucesso' ? 'feedback-sucesso' : 'feedback-erro'">
          <v-icon size="14">{{ feedback.tipo === 'sucesso' ? 'mdi-check-circle' : 'mdi-alert-circle' }}</v-icon>
          {{ feedback.mensagem }}
        </p>
      </transition>
    </div>

    <!-- Lista de usuários -->
    <div class="card">
      <h3 class="card-title">Usuários cadastrados</h3>

      <p v-if="loading" class="status-msg">Carregando usuários...</p>
      <p v-else-if="error" class="status-msg status-erro">{{ error }}</p>

      <div v-else class="lista-usuarios">
        <div v-for="u in usuarios" :key="u.id" class="user-row">
          <div class="user-info">
            <p class="user-nome">
              {{ u.name }}
              <span v-if="ehVoceMesmo(u)" class="badge-voce">você</span>
            </p>
            <p class="user-email">{{ u.email }}</p>
          </div>

          <span class="badge-role" :class="`badge-${u.role}`">
            {{ roleLabel(u) }}
          </span>

          <div class="user-acoes">
            <button type="button" class="btn-senha" @click="senhaEditando === u.id ? fecharSenha() : abrirSenha(u.id)">
              <v-icon size="13">mdi-key-outline</v-icon>
              Senha
            </button>
            <button
              type="button"
              class="btn-excluir"
              :disabled="ehVoceMesmo(u)"
              :title="ehVoceMesmo(u) ? 'Você não pode excluir sua própria conta' : 'Excluir usuário'"
              @click="excluir(u)"
            >
              <v-icon size="13">mdi-trash-can-outline</v-icon>
            </button>
          </div>

          <div v-if="senhaEditando === u.id" class="senha-form">
            <input v-model="novaSenha" type="password" class="field-input" placeholder="Nova senha (mín. 8 caracteres)" />
            <button type="button" class="btn-salvar" :disabled="salvandoSenha" @click="salvarSenha(u.id)">
              {{ salvandoSenha ? 'Salvando...' : 'Salvar' }}
            </button>
            <button type="button" class="btn-cancelar" @click="fecharSenha">Cancelar</button>
          </div>
        </div>

        <p v-if="!usuarios.length" class="status-msg">Nenhum usuário cadastrado.</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.admin-users {
  display: flex;
  flex-direction: column;
  gap: 24px;
  font-family: 'DM Sans', sans-serif;
}

.card {
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 16px;
  padding: 24px;
}

.card-title {
  font-weight: 700;
  color: #0d1f3c;
  font-size: 14px;
  margin: 0 0 16px;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 12px;
  margin-bottom: 12px;
}

@media (min-width: 640px) {
  .form-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

.field-label {
  display: block;
  font-size: 12px;
  font-weight: 500;
  color: #5a6a85;
  margin-bottom: 4px;
}

.field-input {
  width: 100%;
  border: 1px solid rgba(13, 31, 60, 0.15);
  border-radius: 12px;
  padding: 8px 12px;
  font-size: 14px;
  background: #eef3fb;
  color: #0d1f3c;
  box-sizing: border-box;
  font-family: inherit;
}

.field-input:focus {
  outline: none;
  border-color: #1a3f8f;
}

.field-select {
  cursor: pointer;
  margin-bottom: 16px;
}

.btn-add {
  display: flex;
  align-items: center;
  gap: 8px;
  border: none;
  background: #1a3f8f;
  color: #ffffff;
  padding: 10px 20px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.15s ease;
}
.btn-add:hover:not(:disabled) {
  background: #0d1f3c;
}
.btn-add:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.feedback-msg {
  display: flex;
  align-items: center;
  gap: 6px;
  margin: 12px 0 0;
  padding: 8px 14px;
  border-radius: 10px;
  font-size: 12.5px;
  font-weight: 500;
  width: fit-content;
}
.feedback-sucesso { background: #dcfce7; color: #15803d; }
.feedback-erro { background: #fef2f2; color: #dc2626; }
.feedback-fade-enter-active, .feedback-fade-leave-active { transition: opacity 0.2s ease; }
.feedback-fade-enter-from, .feedback-fade-leave-to { opacity: 0; }

.status-msg { color: #5a6a85; font-size: 14px; padding: 8px 0; }
.status-erro { color: #dc2626; }

.lista-usuarios {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.user-row {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 12px;
}

.user-info {
  flex: 1;
  min-width: 160px;
}

.user-nome {
  font-weight: 600;
  color: #0d1f3c;
  font-size: 13.5px;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 6px;
}

.badge-voce {
  font-size: 9px;
  font-weight: 600;
  color: #1a3f8f;
  background: #eef3fb;
  padding: 1px 6px;
  border-radius: 999px;
}

.user-email {
  font-size: 12px;
  color: #5a6a85;
  margin: 2px 0 0;
}

.badge-role {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  padding: 4px 10px;
  border-radius: 999px;
  white-space: nowrap;
}
.badge-admin { background: #dbeafe; color: #1a3f8f; }
.badge-imprensa { background: #fef3c7; color: #b45309; }
.badge-user { background: #f3f4f6; color: #6b7280; }

.user-acoes {
  display: flex;
  gap: 8px;
}

.btn-senha {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 6px 12px;
  border-radius: 8px;
  border: 1px solid rgba(13, 31, 60, 0.15);
  background: transparent;
  color: #1a3f8f;
  font-size: 12px;
  cursor: pointer;
}
.btn-senha:hover { background: #eef3fb; }

.btn-excluir {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border-radius: 8px;
  border: none;
  background: transparent;
  color: #f87171;
  cursor: pointer;
}
.btn-excluir:hover:not(:disabled) { background: #fef2f2; color: #dc2626; }
.btn-excluir:disabled { opacity: 0.3; cursor: not-allowed; }

.senha-form {
  flex-basis: 100%;
  display: flex;
  gap: 8px;
  margin-top: 8px;
  flex-wrap: wrap;
}
.senha-form .field-input { flex: 1 1 200px; }

.btn-salvar, .btn-cancelar {
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  border: none;
}
.btn-salvar {
  background: #1a3f8f;
  color: #ffffff;
}
.btn-salvar:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-cancelar {
  background: transparent;
  border: 1px solid rgba(13, 31, 60, 0.15);
  color: #5a6a85;
}

@media (max-width: 480px) {
  .user-acoes { margin-left: auto; }
}
</style>