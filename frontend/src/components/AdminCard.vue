<script setup>
import { computed, ref } from 'vue'
import logoImg from '@/assets/logo.png'
import { useRouter } from 'vue-router'
import { logout } from '@/services/auth'
import { user } from '@/stores/auth'
import { useAdmin } from '@/composables/useAdmin'
import LoginForm from '@/components/auth/LoginForm.vue'
import RegisterForm from '@/components/auth/RegisterForm.vue'
import AccountForm from '@/components/auth/AccountForm.vue'
const router = useRouter()
const { isAdmin } = useAdmin()
const open = ref(false)
const modo = ref('login')
const aba = ref('conta')
const isLoggedIn = computed(() => !!user.value)
const titulo = computed(() => {
  if (!isLoggedIn.value) return modo.value === 'login' ? 'Entrar' : 'Criar conta'
  return 'Minha conta'
})
function abrir(m) { modo.value = m; aba.value = 'conta'; open.value = true }
function aoEntrar() { open.value = false; if (isAdmin.value) router.push('/admin') }
async function doLogout() { try { await logout() } finally { open.value = false; router.replace('/') } }
function goUsuarios() { open.value = false; router.push('/admin') }
</script>

<template>
  <div class="admin-card">
    <div v-if="!isLoggedIn">
      <v-btn variant="flat" class="login-btn" block @click="abrir('login')">
        <v-icon color="#F17100" class="mr-2">mdi-login-variant</v-icon>
        <span class="login-text">Entrar</span>
      </v-btn>
      <v-dialog v-model="open" max-width="480" content-class="admin-modal-wrap">
        <v-card class="admin-modal">
          <v-card-title class="popup-title">
            <div class="title-row">
              <v-img :src="logoImg" class="logo-small" contain />
              <div class="title-block">
                <div class="title">{{ titulo }}</div>
                <div class="subtitle">ABSL — Grêmio Athos Bulcão</div>
              </div>
            </div>
            <v-btn icon variant="text" class="close-btn" aria-label="Fechar" @click="open = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text class="modal-scroll-area">
            <div class="tabs" role="tablist" aria-label="Alternar entre entrar e criar conta">
              <button type="button" role="tab" :aria-selected="modo === 'login'" class="tab" :class="{ ativo: modo === 'login' }" @click="modo = 'login'">Entrar</button>
              <button type="button" role="tab" :aria-selected="modo === 'cadastro'" class="tab" :class="{ ativo: modo === 'cadastro' }" @click="modo = 'cadastro'">Criar conta</button>
            </div>
            <LoginForm v-if="modo === 'login'" @logado="aoEntrar" />
            <RegisterForm v-else @registrado="aoEntrar" />
          </v-card-text>
        </v-card>
      </v-dialog>
    </div>
    <div v-else class="admin-options">
      <v-btn variant="flat" class="login-btn" block @click="abrir('conta')">
        <v-icon color="#F17100" class="mr-2">mdi-account-circle-outline</v-icon>
        <span class="login-text">Minha conta</span>
      </v-btn>
      <v-btn v-if="isAdmin" variant="flat" class="login-btn" block @click="goUsuarios">
        <v-icon color="#F17100" class="mr-2">mdi-account-supervisor</v-icon>
        <span class="login-text">Área administrativa</span>
      </v-btn>
      <v-btn variant="flat" class="login-btn" block @click="doLogout">
        <v-icon color="#F17100" class="mr-2">mdi-logout</v-icon>
        <span class="login-text">Sair</span>
      </v-btn>
      <v-dialog v-model="open" max-width="480" content-class="admin-modal-wrap">
        <v-card class="admin-modal">
          <v-card-title class="popup-title">
            <div class="title-row">
              <v-img :src="logoImg" class="logo-small" contain />
              <div class="title-block">
                <div class="title">Minha conta</div>
                <div class="subtitle">ABSL — Grêmio Athos Bulcão</div>
              </div>
            </div>
            <v-btn icon variant="text" class="close-btn" aria-label="Fechar" @click="open = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text class="modal-scroll-area">
            <AccountForm @salvo="open = false" />
          </v-card-text>
        </v-card>
      </v-dialog>
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

.tabs {
  display: flex;
  background: #eef1f6;
  border-radius: 999px;
  padding: 3px;
  margin-bottom: 16px;
}
.tab {
  flex: 1;
  border: none;
  background: transparent;
  border-radius: 999px;
  padding: 10px 8px;
  font-size: 14px;
  font-weight: 600;
  color: #5a6a85;
  cursor: pointer;
  font-family: inherit;
  min-height: 44px;
}
.tab.ativo {
  background: #fff;
  color: #0f2038;
  box-shadow: 0 1px 4px rgba(13, 31, 60, 0.12);
}
.tab:focus-visible {
  outline: 2px solid #16509b;
  outline-offset: 1px;
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