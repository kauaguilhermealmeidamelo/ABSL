<script setup>
import api from "@/services/api"
import logoImg from "@/assets/logo.png"
import { computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

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

async function logout() {
  try {
    await api.post("/logout")
  } catch (error) {
    console.error(error)
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('usuario')
    router.replace('/')
  }
}
</script>

<template>
  <v-navigation-drawer permanent color="#1a2f4a">
    <div class="drawer-content">
      <div class="logo-wrap">
        <div class="brand-row">
          <div class="logo-circle">
            <v-img :src="logoImg" class="logo" contain />
          </div>
          <div class="brand-text">
            <span>Grêmio</span>
            <strong>Athos Bulcão</strong>
          </div>
        </div>
        <v-menu location="end" open-on-hover offset="10" open-delay="10" close-delay="30">
          
          <v-list bg-color="#F17100" class="d-flex pa-0 new-btn" width="460">
            <v-list-item class="l-menu" link to="/">Início</v-list-item>
            <v-list-item class="l-menu" link to="/horario">Horário das Aulas</v-list-item>
            <v-list-item class="l-menu" link to="/noticias">Notícias</v-list-item>
            <v-list-item class="l-menu" link to="/projetos">Projetos</v-list-item>
          </v-list>
        </v-menu>
      </div>

      <!-- Lista de Menu Principal Atualizada -->
      <div>
        <v-list-item link class="lista-menu pa-4 itemMenu" to="/">
          <v-icon color="blue-lighten-3" class="mr-2">mdi-home</v-icon>
          Início
        </v-list-item>
        
        <v-list-item link class="lista-menu pa-4 itemMenu" to="/horario">
          <v-icon color="blue-lighten-3" class="mr-2">mdi-clock-outline</v-icon>
          Horário das Aulas
        </v-list-item>

        <v-list-item link class="lista-menu pa-4 itemMenu" to="/noticias">
          <v-icon color="blue-lighten-3" class="mr-2">mdi-newspaper</v-icon>
          Notícias
        </v-list-item>

        <v-list-item link class="lista-menu pa-4 itemMenu" to="/projetos">
          <v-icon color="blue-lighten-3" class="mr-2">mdi-lightbulb-on</v-icon>
          Projetos
        </v-list-item>

        <v-list-item link class="lista-menu pa-4 itemMenu" to="/gabarito">
          <v-icon color="blue-lighten-3" class="mr-2">mdi-check-all</v-icon>
          Gabarito Provão
        </v-list-item>

        <v-list-item link class="lista-menu pa-4 itemMenu" to="/transparencia">
          <v-icon color="blue-lighten-3" class="mr-2">mdi-scale-balance</v-icon>
          Transparência
        </v-list-item>
      </div>

      <div class="menu-divider"/>

      <!-- Mantive o Gerenciamento de Usuários caso seja uma área administrativa interna -->
      <router-link v-if="isAdmin" to="/usuarios" class="card-usuarios itemMenu">
        <v-icon color="#5B8DB8" size="36">mdi-account-supervisor</v-icon>
        <span class="card-usuarios-text">Gerenciamento<br>de Usuários</span>
      </router-link>

      <v-spacer />

      <div class="d-flex justify-end pa-4">
        <v-btn icon variant="flat" color="blue-grey-darken-1" size="auto" to="/perfil">
          <v-icon color="blue-grey-darken-3" size="x-large">mdi-account</v-icon>
        </v-btn>
      </div>
      
      <v-btn v-if="isAdmin" color="#0F2F4B" text="SAIR" @click="logout" class="mx-4 mb-4">
        <v-icon color="blue-lighten-3" class="mr-2">mdi-logout</v-icon> Sair
      </v-btn>
    </div>
  </v-navigation-drawer>
</template>

<style scoped>
/* Mantenha seus estilos existentes (logo, new-btn, lista-menu, etc.) */
.logo-wrap {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 8px 12px 0;
}

.brand-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 6px 4px;
}

.logo-circle {
  width: 70px;
  height: 70px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 50%;
  overflow: hidden;
  background: transparent;
  box-shadow: none;
  flex-shrink: 0;
}

.logo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}

.brand-text {
  display: flex;
  flex-direction: column;
  color: #ffffff;
  line-height: 1.1;
}

.brand-text span {
  font-size: 13px;
  font-weight: 600;
  opacity: 0.9;
}

.brand-text strong {
  font-size: 16px;
  font-weight: 700;
}
.new-btn {
  font-size: 23px;
  padding: 0;
  height: 50px;
  gap: 18px;
}
.lista-menu {
  font-size: 17px;
  color: #F2F2F2;
  border-left: 3px solid transparent;
  transition: all 0.2s ease;
}
.lista-menu:hover {
  background-color: rgba(255, 255, 255, 0.08) !important;
  border-left: 3px solid #00adf1;
  color: white !important;
}
.drawer-content {
  height: 100%;
  display: flex;
  flex-direction: column;
  gap: 20px;
  padding-top: 10px;
}
.itemMenu {
  text-decoration: none;
  height: 70px;
}
.menu-divider {
  height: 1px;
  background: rgba(255, 255, 255, 0.1);
  margin: 6px 16px;
}
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
</style>   