<script setup>
import { ref, computed } from 'vue'
import logoImg from '@/assets/logo.png'
import { useRouter } from 'vue-router'
import { login, logout } from '@/services/auth'

const router = useRouter()
const open = ref(false)
const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

const isLoggedIn = computed(() => !!localStorage.getItem('token'))

async function submit() {
  error.value = ''

  if (!email.value || !password.value) {
    error.value = 'Informe e-mail e senha.'
    return
  }

  loading.value = true
  try {
    await login(email.value, password.value)
    email.value = ''
    password.value = ''
    open.value = false
  } catch (err) {
    error.value = err?.response?.data?.message || 'Credenciais inválidas.'
  } finally {
    loading.value = false
  }
}

async function doLogout() {
  try {
    await logout()
  } finally {
    router.replace('/')
  }
}

function goUsuarios() {
  router.push('/usuarios')
}
</script>

<template>
  <div class="admin-card">
    <div v-if="!isLoggedIn">
      <v-btn variant="flat" class="login-btn" block @click="open = true">
        <v-icon color="#F17100" class="mr-2">mdi-login-variant</v-icon>
        <span class="login-text">Login administrativo</span>
      </v-btn>

      <v-dialog v-model="open" max-width="480" content-class="admin-modal-wrap">
        <v-card class="admin-modal">
          <v-card-title class="popup-title">
            <div class="title-row">
              <v-img :src="logoImg" class="logo-small" contain />
              <div class="title-block">
                <div class="title">Login Administrativo</div>
                <div class="subtitle">ABSL — Grêmio Athos Bulcão</div>
              </div>
            </div>
            <v-btn icon variant="text" class="close-btn" @click="open = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>

          <v-card-text class="modal-scroll-area">
            <label class="field-label">E-mail</label>
            <v-text-field
              v-model="email"
              placeholder="admin@absl.local"
              type="email"
              density="comfortable"
              hide-details
              variant="solo"
              flat
              class="password-field"
              @keydown.enter="submit"
            />

            <label class="field-label field-label-spaced">Senha</label>
            <v-text-field
              v-model="password"
              placeholder="Digite a senha"
              type="password"
              density="comfortable"
              hide-details
              variant="solo"
              flat
              class="password-field"
              @keydown.enter="submit"
            />

            <div class="actions-row">
              <v-btn :loading="loading" class="enter-btn" block @click="submit">Entrar</v-btn>
            </div>

            <div v-if="error" class="error">{{ error }}</div>
          </v-card-text>
        </v-card>
      </v-dialog>
    </div>

    <div v-else class="admin-options">
      <v-btn variant="flat" class="login-btn" block @click="goUsuarios">
        <v-icon color="#F17100" class="mr-2">mdi-account-supervisor</v-icon>
        <span class="login-text">Área administrativa</span>
      </v-btn>

      <v-btn variant="flat" class="login-btn" block @click="doLogout">
        <v-icon color="#F17100" class="mr-2">mdi-logout</v-icon>
        <span class="login-text">Sair</span>
      </v-btn>
    </div>
  </div>
</template>

<style scoped>
.admin-card {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.popup-title {
  position: relative;
  padding: 20px 44px 8px 20px;
  white-space: normal !important;
  overflow: visible !important;
}

.title-row {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  min-width: 0;
}

.logo-small {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  flex-shrink: 0;
}

.title-block {
  flex: 1;
  min-width: 0;
}

.title-block .title {
  font-weight: 700;
  font-size: 16px;
  line-height: 1.3;
  color: #0F2038;
  white-space: normal;
}

.title-block .subtitle {
  font-size: 12px;
  color: #6B7C93;
  white-space: normal;
  margin-top: 2px;
}

.close-btn {
  position: absolute;
  top: 14px;
  right: 10px;
  color: #6B7C93;
}

.admin-modal {
  border-radius: 20px;
  overflow: visible;
  padding: 8px 8px 24px;
  box-shadow: 0 20px 50px rgba(4, 20, 40, 0.25);
}

.modal-scroll-area {
  overflow-y: auto;
  padding: 8px 20px 4px;
}

.field-label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #1a2f4a;
  margin: 4px 0 8px;
}

.field-label-spaced {
  margin-top: 18px;
}

.password-field {
  border-radius: 14px;
  overflow: hidden;
}

.password-field :deep(.v-field) {
  border-radius: 14px !important;
  background-color: #EEF1F6 !important;
  box-shadow: none !important;
}

.password-field :deep(.v-field__input) {
  padding-top: 14px;
  padding-bottom: 14px;
  color: #1a2f4a;
}

.actions-row {
  margin-top: 20px;
}

.enter-btn {
  background-color: #16509B !important;
  color: #fff !important;
  border-radius: 28px !important;
  height: 52px !important;
  text-transform: none !important;
  font-weight: 700 !important;
  font-size: 16px !important;
  box-shadow: none !important;
}

.error {
  color: #dc2626;
  font-size: 13px;
  margin-top: 12px;
  text-align: center;
}

.admin-options {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.login-btn {
  background-color: #1c1f26 !important;
  border-radius: 14px !important;
  height: 52px !important;
  text-transform: none !important;
  justify-content: flex-start !important;
  padding: 0 16px !important;
}

.login-text {
  color: #F17100;
  font-weight: 700;
  font-size: 15px;
}

.admin-modal-wrap {
  width: calc(100vw - 32px);
  margin: 0 16px;
}

@media (max-width: 420px) {
  .popup-title {
    padding: 20px 16px 8px;
  }
  .title-block .title {
    font-size: 16px;
  }
}
</style>