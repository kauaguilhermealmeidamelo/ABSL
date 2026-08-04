<script setup>
import logoImg from "@/assets/logo.png"
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import AdminCard from '@/components/AdminCard.vue'

const route = useRoute()

const isAdmin = computed(() => {
  const rawUser = localStorage.getItem('usuario')
  if (!rawUser) return false

  try {
    const parsedUser = JSON.parse(rawUser)
    if (parsedUser && typeof parsedUser === 'object') {
      const role = String(parsedUser.role || parsedUser.tipo || parsedUser.perfil || parsedUser.is_admin || parsedUser.administrador || '').toLowerCase()
      return role === 'admin' || role === 'administrator' || role === 'administrador' || role === 'super_admin' || role === 'super-admin' || parsedUser.is_admin === true || parsedUser.administrador === true
    }
  } catch {
    // fallback para valores simples armazenados como texto
  }

  return String(rawUser).toLowerCase().includes('admin')
})

const isLoggedIn = computed(() => !!localStorage.getItem('token'))

const menuItems = [
  { label: 'Início', to: '/', icon: 'mdi-home' },
  { label: 'Horário das Aulas', to: '/horario', icon: 'mdi-clock-outline' },
  { label: 'Notícias', to: '/noticias', icon: 'mdi-newspaper' },
  { label: 'Projetos', to: '/projetos', icon: 'mdi-folder-multiple-outline' },
  { label: 'Gabarito Provão', to: '/gabarito', icon: 'mdi-file-document-outline' },
  { label: 'Transparência', to: '/transparencia', icon: 'mdi-shield-outline' },
  { label: 'Cardápio Semanal', to: '/cardapio', icon: 'mdi-silverware-fork-knife' },
  { label: 'Os Ouvintes', to: '/ouvintes', icon: 'mdi-forum-outline' },
  { label: 'Conhecendo sua escola', to: '/mapa', icon: 'mdi-map-outline' },
]

function isActive(to) {
  return route.path === to
}

// AdminCard component encapsula login e ações administrativas
</script>

<template>
  <v-navigation-drawer permanent color="#0F2038">
    <div class="drawer-content">
      <!-- Cabeçalho / Logo -->
      <div class="logo-wrap">
        <div class="brand-row">
          <div class="logo-square">
            <v-img :src="logoImg" class="logo" contain />
          </div>
          <div class="brand-text">
            <strong>ABSL</strong>
            <span>Grêmio Athos Bulcão</span>
          </div>
        </div>
      </div>

      <!-- Lista de Menu Principal -->
      <div class="menu-list">
        <router-link
          v-for="item in menuItems"
          :key="item.to"
          :to="item.to"
          class="lista-menu itemMenu"
          :class="{ 'lista-menu-ativo': isActive(item.to) }"
        >
          <v-icon :color="isActive(item.to) ? '#ffffff' : '#8FA3BF'" class="mr-3">{{ item.icon }}</v-icon>
          <span>{{ item.label }}</span>
        </router-link>
      </div>

      <!-- Área administrativa (apenas se for admin) -->
      <template v-if="isAdmin">
        <div class="menu-divider" />
        <router-link to="/usuarios" class="card-usuarios itemMenu">
          <v-icon color="#5B8DB8" size="36">mdi-account-supervisor</v-icon>
          <span class="card-usuarios-text">Gerenciamento<br>de Usuários</span>
        </router-link>
      </template>

      <v-spacer />

      <!-- Rodapé: login/logout -->
      <div class="footer-actions">
        <AdminCard />

        <div class="footer-text">
          <span>Grêmio Estudantil Athos Bulcão · ABSL</span>
          <span>Brasília, DF</span>
        </div>
      </div>
    </div>
  </v-navigation-drawer>
</template>

<style scoped>
.drawer-content {
  height: 100%;
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding-top: 16px;
}

/* Cabeçalho */
.logo-wrap {
  padding: 0 16px 8px;
}

.brand-row {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-square {
  width: 44px;
  height: 44px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 10px;
  overflow: hidden;
  background: transparent;
  flex-shrink: 0;
}

.logo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 10px;
}

.brand-text {
  display: flex;
  flex-direction: column;
  color: #ffffff;
  line-height: 1.2;
}

.brand-text strong {
  font-size: 18px;
  font-weight: 700;
}

.brand-text span {
  font-size: 12px;
  font-weight: 400;
  opacity: 0.75;
}

/* Lista de menu */
.menu-list {
  display: flex;
  flex-direction: column;
  gap: 6px;
  padding: 8px 12px;
}

.lista-menu {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  border-radius: 14px;
  font-size: 16px;
  font-weight: 500;
  color: #8FA3BF;
  text-decoration: none;
  transition: all 0.2s ease;
}

.lista-menu:hover {
  background-color: rgba(255, 255, 255, 0.06);
  color: #ffffff;
}

.lista-menu-ativo {
  background: linear-gradient(90deg, #16509B, #1B63BD);
  color: #ffffff;
  font-weight: 700;
  box-shadow: 0 4px 10px rgba(22, 80, 155, 0.35);
}

.itemMenu {
  text-decoration: none;
}

.menu-divider {
  height: 1px;
  background: rgba(255, 255, 255, 0.1);
  margin: 6px 16px;
}

/* Gerenciamento de usuários */
.card-usuarios {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 6px 12px;
  padding: 12px 14px;
  border: 1.5px solid #173c80;
  border-radius: 10px;
  background: rgba(30, 60, 100, 0.45);
  cursor: pointer;
  transition: background 0.2s;
}

.card-usuarios:hover {
  background: rgba(30, 60, 100, 0.7);
}

.card-usuarios-text {
  font-size: 15px;
  font-weight: 700;
  color: #F2F2F2;
  line-height: 1.3;
  flex: 1;
}

/* Rodapé */
.footer-actions {
  padding: 0 16px 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
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

.footer-text {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  color: #6B7C93;
  font-size: 11px;
  line-height: 1.4;
}
</style>