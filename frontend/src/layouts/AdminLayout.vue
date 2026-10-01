<script setup>
import { computed, ref } from 'vue'
import { useDisplay } from 'vuetify'
import { useRoute } from 'vue-router'
import { useAdmin } from '@/composables/useAdmin'
import { user } from '@/stores/auth'
import logoImg from '@/assets/logo.png'

const { mobile } = useDisplay()
const route = useRoute()
const { isSuperAdmin } = useAdmin()
const drawer = ref(true)

const contentItems = [
  { label: 'Notícias', to: '/admin/noticias', icon: 'mdi-newspaper' },
  { label: 'Projetos', to: '/admin/projetos', icon: 'mdi-folder-multiple-outline' },
  { label: 'O Grêmio', to: '/admin/o-gremio', icon: 'mdi-account-group-outline' },
  { label: 'Transparência', to: '/admin/transparencia', icon: 'mdi-shield-outline' },
  { label: 'Mídia', to: '/admin/midia', icon: 'mdi-filmstrip' },
]
const schoolItems = [
  { label: 'Turmas', to: '/admin/turmas', icon: 'mdi-school-outline' },
  { label: 'Horários', to: '/admin/horarios', icon: 'mdi-clock-outline' },
  { label: 'Cardápio', to: '/admin/cardapio', icon: 'mdi-silverware-fork-knife' },
]
const participationItems = [
  { label: 'Ouvidoria', to: '/admin/ouvidoria', icon: 'mdi-forum-outline' },
]
const administrationItems = computed(() => isSuperAdmin.value
  ? [
      { label: 'Usuários', to: '/admin/usuarios', icon: 'mdi-account-supervisor-outline' },
      { label: 'Logs', to: '/admin/logs', icon: 'mdi-text-box-search-outline' },
    ]
  : [])
const isActive = (to) => route.path === to || route.path.startsWith(to + '/')
function navigate() { if (mobile.value) drawer.value = false }
const displayName = computed(() => user.value?.name || user.value?.username || 'Equipe')
</script>

<template>
  <v-app>
    <v-navigation-drawer v-model="drawer" :temporary="mobile" :permanent="!mobile" color="#0F2038" width="280">
      <div class="admin-drawer">
        <div class="brand">
          <div class="brand-logo"><v-img :src="logoImg" contain alt="ABSL" /></div>
          <div><strong>ABSL</strong><span>Painel administrativo</span></div>
        </div>
        <nav class="navigation" aria-label="Navegação administrativa">
          <router-link to="/admin" class="nav-item" :class="{ active: route.path === '/admin' }" @click="navigate">
            <v-icon>mdi-view-dashboard-outline</v-icon><span>Visão geral</span>
          </router-link>
          <div class="nav-section">Conteúdo</div>
          <router-link v-for="item in contentItems" :key="item.to" :to="item.to" class="nav-item" :class="{ active: isActive(item.to) }" @click="navigate">
            <v-icon>{{ item.icon }}</v-icon><span>{{ item.label }}</span>
          </router-link>
          <div class="nav-section">Escola</div>
          <router-link v-for="item in schoolItems" :key="item.to" :to="item.to" class="nav-item" :class="{ active: isActive(item.to) }" @click="navigate">
            <v-icon>{{ item.icon }}</v-icon><span>{{ item.label }}</span>
          </router-link>
          <div class="nav-section">Participação</div>
          <router-link v-for="item in participationItems" :key="item.to" :to="item.to" class="nav-item" :class="{ active: isActive(item.to) }" @click="navigate">
            <v-icon>{{ item.icon }}</v-icon><span>{{ item.label }}</span>
          </router-link>
          <template v-if="isSuperAdmin">
            <div class="nav-section">Administração</div>
            <router-link v-for="item in administrationItems" :key="item.to" :to="item.to" class="nav-item" :class="{ active: isActive(item.to) }" @click="navigate">
              <v-icon>{{ item.icon }}</v-icon><span>{{ item.label }}</span>
            </router-link>
          </template>
        </nav>
        <div class="drawer-footer">
          <div class="user-info"><v-icon size="20">mdi-account-circle-outline</v-icon><span>{{ displayName }}</span></div>
          <router-link to="/" class="portal-link" @click="navigate"><v-icon size="18">mdi-open-in-new</v-icon><span>Ver portal</span></router-link>
        </div>
      </div>
    </v-navigation-drawer>
    <v-app-bar v-if="mobile" color="#0F2038" elevation="0">
      <v-app-bar-nav-icon color="white" @click="drawer = !drawer" />
      <v-toolbar-title class="mobile-title">Painel ABSL</v-toolbar-title>
    </v-app-bar>
    <v-main class="admin-main"><div class="admin-content"><router-view /></div></v-main>
  </v-app>
</template>

<style scoped>
.admin-drawer{min-height:100%;display:flex;flex-direction:column;padding:18px 12px}.brand{display:flex;align-items:center;gap:12px;padding:4px 10px 22px;color:#fff}.brand-logo{width:42px;height:42px;flex:0 0 42px;border-radius:10px;overflow:hidden}.brand strong,.brand span{display:block}.brand strong{font-size:18px}.brand span{margin-top:2px;color:#8FA3BF;font-size:12px}.navigation{display:flex;flex-direction:column;gap:4px}.nav-section{margin:18px 12px 6px;color:#6F84A1;font-size:11px;font-weight:700;letter-spacing:.09em;text-transform:uppercase}.nav-item,.portal-link{display:flex;align-items:center;gap:11px;min-height:44px;padding:9px 12px;border-radius:10px;color:#AAB9CD;text-decoration:none;font-size:14px;font-weight:500}.nav-item .v-icon{font-size:20px}.nav-item:hover,.nav-item.active{color:#fff;background:rgba(255,255,255,.08)}.nav-item.active{background:#16509B}.drawer-footer{margin-top:auto;padding:18px 8px 4px;border-top:1px solid rgba(255,255,255,.08)}.user-info{display:flex;align-items:center;gap:9px;padding:0 4px 12px;color:#C7D2E2;font-size:13px}.portal-link{min-height:40px;padding-left:4px;color:#8FA3BF}.admin-main{min-height:100vh;background:#F6F8FB}.admin-content{width:100%;max-width:1440px;margin:0 auto;padding:32px}.mobile-title{color:#fff;font-size:16px;font-weight:700}@media(max-width:600px){.admin-content{padding:22px 16px 32px}}
</style>
