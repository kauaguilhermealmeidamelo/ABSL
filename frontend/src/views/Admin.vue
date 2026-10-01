<script setup>
import PageHeader from '@/components/common/PageHeader.vue'
import { useAdmin } from '@/composables/useAdmin'
const { isSuperAdmin } = useAdmin()
</script>

<template>
  <div class="admin-home">
    <PageHeader label="Painel" title="Visão geral" subtitle="Central de administração do portal ABSL." />
    <section class="welcome-card" aria-labelledby="welcome-title">
      <div><span class="eyebrow">Acesso autorizado</span><h2 id="welcome-title">Gerencie o portal em um só lugar.</h2><p>Conteúdo, informações da escola e participação ficam organizados por módulo. Os recursos disponíveis dependem do seu nível de acesso.</p></div>
      <v-icon size="54" class="welcome-icon">mdi-view-dashboard-outline</v-icon>
    </section>
    <section class="quick-grid" aria-label="Módulos do painel">
      <router-link to="/admin/o-gremio" class="quick-card"><v-icon>mdi-account-group-outline</v-icon><strong>O Grêmio</strong><span>Informações institucionais.</span></router-link>
      <router-link v-if="isSuperAdmin" to="/admin/usuarios" class="quick-card"><v-icon>mdi-account-supervisor-outline</v-icon><strong>Usuários</strong><span>Contas e acessos do sistema.</span></router-link>
      <router-link v-if="isAdmin" to="/admin/logs" class="quick-card"><v-icon>mdi-text-box-search-outline</v-icon><strong>Logs</strong><span>Registro das ações administrativas.</span></router-link>
    </section>
  </div>
</template>

<style scoped>
.admin-home{font-family:'DM Sans',sans-serif}.welcome-card{display:flex;align-items:center;justify-content:space-between;gap:24px;padding:28px;border-radius:18px;background:#fff;border:1px solid #E4E9F0;margin-bottom:22px}.eyebrow{display:block;margin-bottom:7px;color:#1A3F8F;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}.welcome-card h2{margin:0 0 8px;color:#0D1F3C;font-size:25px}.welcome-card p{max-width:720px;margin:0;color:#5A6A85;line-height:1.6}.welcome-icon{color:#16509B;opacity:.8}.quick-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.quick-card{display:flex;flex-direction:column;gap:8px;min-height:150px;padding:22px;border-radius:16px;background:#fff;border:1px solid #E4E9F0;color:inherit;text-decoration:none;transition:transform .18s ease,box-shadow .18s ease}.quick-card:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(15,32,56,.08)}.quick-card .v-icon{color:#16509B;margin-bottom:4px}.quick-card strong{color:#0D1F3C;font-size:16px}.quick-card span{color:#5A6A85;font-size:13px;line-height:1.45}@media(max-width:900px){.quick-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:600px){.welcome-card{align-items:flex-start;padding:22px}.welcome-icon{display:none}.quick-grid{grid-template-columns:1fr}}
</style>
