<script setup>
import { ref, computed } from 'vue'
import api from '@/services/api'
import logoImg from "@/assets/logo.png"
import { useRouter } from 'vue-router'

const router = useRouter()
const open = ref(false)
const password = ref('')
const loading = ref(false)
const error = ref('')
const TEMP_ADMIN_PASSWORD = 'absl2026'
const TEMP_ADMIN_USER = {
  id: 1,
  name: 'Admin Teste',
  role: 'admin'
}

const isLoggedIn = computed(() => !!localStorage.getItem('token'))

function rawUser() {
  const raw = localStorage.getItem('usuario') || ''
  try {
    return JSON.parse(raw || '{}')
  } catch {
    // fallback para valores simples armazenados como texto
    return {}
  }
}

const isAdmin = computed(() => {
  const parsedUser = rawUser()
  if (!parsedUser) return false

  const role = String(parsedUser.role || parsedUser.tipo || parsedUser.perfil || parsedUser.is_admin || parsedUser.administrador || '').toLowerCase()
  return role === 'admin' || role === 'administrator' || role === 'administrador' || role === 'super_admin' || role === 'super-admin' || parsedUser.is_admin === true || parsedUser.administrador === true
})

async function submit() {
  error.value = ''
  loading.value = true

  try {
    if (password.value === TEMP_ADMIN_PASSWORD) {
      localStorage.setItem('token', 'frontend-admin-test-token')
      localStorage.setItem('usuario', JSON.stringify(TEMP_ADMIN_USER))
      password.value = ''
      open.value = false
      return
    }

    const resp = await api.post('/login', { password: password.value })
    if (resp && resp.data) {
      localStorage.setItem('token', resp.data.token || resp.data.access_token || '')
      localStorage.setItem('usuario', JSON.stringify(resp.data.user || resp.data.usuario || {}))
      open.value = false
    }
  } catch (err) {
    error.value = 'Credenciais inválidas'
    console.error(err)
  } finally {
    loading.value = false
  }
}

async function doLogout() {
  try {
    await api.post('/logout')
  } catch (err) {
    console.error(err)
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('usuario')
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

      <v-dialog v-model="open" persistent max-width="480" >
        <v-card class="admin-modal">
          <v-card-title class="popup-title">
            <div class="title-row">
              <v-img :src="logoImg" class="logo-small" contain />
              <div class="title-block">
                <div class="title">Login Administrativo</div>
                <div class="subtitle">ABSL — Grêmio Athos Bulcão</div>
              </div>
              <v-btn icon variant="text" class="close-btn" @click="open = false"><v-icon>mdi-close</v-icon></v-btn>
            </div>
          </v-card-title>

          <v-card-text>
            <label class="field-label">Senha de administrador</label>
            <v-text-field
              v-model="password"
              placeholder="Digite a senha"
              type="password"
              density="comfortable"
              hide-details
              variant="solo"
              flat
              class="password-field"
            />

            <div class="hint">
              Senha temporária para testes: <span class="hint-code">absl2026</span>
            </div>

            <div class="actions-row">
              <v-btn :loading="loading" class="enter-btn" block @click="submit">Entrar</v-btn>
            </div>

            <div class="error" v-if="error">{{ error }}</div>
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
  padding: 24px 24px 8px;
}

.title-row {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
}

.logo-small {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  flex-shrink: 0;
}

.title-block .title {
  font-weight: 700;
  font-size: 19px;
  color: #0F2038;
}

.title-block .subtitle {
  font-size: 13px;
  color: #6B7C93;
}

.close-btn {
  margin-left: auto;
  color: #6B7C93;
}

.admin-modal {
  border-radius: 20px;
  overflow: visible;
  padding: 8px 8px 24px;
  box-shadow: 0 20px 50px rgba(4, 20, 40, 0.25);
}

.field-label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #1a2f4a;
  margin: 4px 0 8px;
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

.hint {
  font-size: 13px;
  color: #6B7C93;
  margin-top: 18px;
}

.hint-code {
  background-color: #EEF1F6;
  color: #1a2f4a;
  padding: 2px 8px;
  border-radius: 6px;
  font-weight: 600;
  font-size: 13px;
}

.centered {
  text-align: center;
}

.error {
  color: #ff6b6b;
  font-size: 13px;
  margin-top: 10px;
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
</style>