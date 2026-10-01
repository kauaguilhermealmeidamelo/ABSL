import { createRouter, createWebHistory, type RouteLocationNormalized } from 'vue-router'
import MainLayout from '@/MainLayout.vue'
import { user } from '@/stores/auth'

const adminRoute = {
  path: '/admin',
  component: () => import('@/layouts/AdminLayout.vue'),
  meta: { requiresStaff: true },
  children: [
    { path: '', name: 'admin', component: () => import('@/views/Admin.vue'), meta: { title: 'Painel | ABSL', requiresStaff: true } },
    { path: 'noticias', name: 'admin-noticias', component: () => import('@/components/admin/NoticiasManager.vue'), meta: { title: 'Notícias | Painel ABSL', requiresStaff: true } },
    { path: 'projetos', name: 'admin-projetos', component: () => import('@/components/admin/ProjetosManager.vue'), meta: { title: 'Projetos | Painel ABSL', requiresStaff: true } },
    { path: 'o-gremio', name: 'admin-o-gremio', component: () => import('@/components/admin/OgremioManager.vue'), meta: { title: 'O Grêmio | Painel ABSL', requiresStaff: true } },
    { path: 'transparencia', name: 'admin-transparencia', component: () => import('@/components/admin/TransparenciaManager.vue'), meta: { title: 'Transparência | Painel ABSL', requiresStaff: true } },
    { path: 'midia', name: 'admin-midia', component: () => import('@/components/gerenciamento/MidiaManager.vue'), meta: { title: 'Mídia | Painel ABSL', requiresStaff: true } },
    { path: 'turmas', name: 'admin-turmas', component: () => import('@/components/gerenciamento/TurmasManager.vue'), meta: { title: 'Turmas | Painel ABSL', requiresStaff: true } },
    { path: 'horarios', name: 'admin-horarios', component: () => import('@/components/admin/HorariosManager.vue'), meta: { title: 'Horários | Painel ABSL', requiresStaff: true } },
    { path: 'cardapio', name: 'admin-cardapio', component: () => import('@/components/admin/CardapioManager.vue'), meta: { title: 'Cardápio | Painel ABSL', requiresStaff: true } },
    { path: 'ouvidoria', name: 'admin-ouvidoria', component: () => import('@/components/admin/OuvidoriaManager.vue'), meta: { title: 'Ouvidoria | Painel ABSL', requiresStaff: true } },
    { path: 'usuarios', name: 'admin-usuarios', component: () => import('@/components/gerenciamento/UsuariosAdminManager.vue'), meta: { title: 'Usuários | Painel ABSL', requiresAdmin: true } },
    { path: 'logs', name: 'admin-logs', component: () => import('@/components/gerenciamento/LogsViewer.vue'), meta: { title: 'Logs | Painel ABSL', requiresAdmin: true } },
  ],
}

const routes = [
  adminRoute,
  {
    path: '/',
    component: MainLayout,
    redirect: '/inicio',
    children: [
      {
        path: 'inicio',
        name: 'inicio',
        component: () => import('@/views/inicio.vue'),
        meta: { public: true, title: 'Início | CEMSL - Brasília', description: 'Conheça o CEMSL em Brasília, no Setor Leste. Acesse notícias, projetos, horários e informações da comunidade escolar.' }
      },
      {
        path: 'o-gremio',
        name: 'o-gremio',
        component: () => import('@/views/Ogremio.vue'),
        meta: { public: true, title: 'O Grêmio | CEMSL - Brasília', description: 'Conheça o Grêmio Estudantil Athos Bulcão, sua organização e as diretorias do CEMSL em Brasília, no Setor Leste.' }
      },
      {
        path: 'horario',
        name: 'horario',
        component: () => import('@/views/Horario.vue'),
        meta: { public: true, title: 'Horários | CEMSL - Brasília', description: 'Consulte os horários do CEMSL em Brasília, no Setor Leste, e organize sua rotina escolar com informações atualizadas.' }
      },
      {
        path: 'noticias',
        name: 'noticias',
        component: () => import('@/views/Noticias.vue'),
        meta: { public: true, title: 'Notícias | CEMSL - Brasília', description: 'Acompanhe as notícias do CEMSL em Brasília, no Setor Leste, com novidades, atividades, eventos e informações da comunidade escolar.' }
      },
      {
        path: 'noticias/:id',
        name: 'noticia-compartilhada',
        component: () => import('@/views/NoticiaCompartilhada.vue'),
        meta: { public: true, title: 'Notícia | CEMSL - Brasília', description: 'Confira esta notícia do Grêmio Estudantil Athos Bulcão e da comunidade escolar do CEMSL.' }
      },
      {
        path: 'projetos',
        name: 'projetos',
        component: () => import('@/views/Projetos.vue'),
        meta: { public: true, title: 'Projetos | CEMSL - Brasília', description: 'Conheça os projetos desenvolvidos pelo CEMSL em Brasília, no Setor Leste, e acompanhe as iniciativas da comunidade escolar.' }
      },
      {
        path: 'projetos/:id',
        name: 'projeto-detalhes',
        component: () => import('@/views/ProjetoDetalhes.vue'),
        meta: { public: true, title: 'Projeto | CEMSL - Brasília', description: 'Confira detalhes dos projetos do CEMSL em Brasília, no Setor Leste, e conheça as iniciativas realizadas pela comunidade escolar.' }
      },
      {
        path: 'gabarito',
        name: 'gabarito',
        component: () => import('@/views/Gabarito.vue'),
        meta: { public: true, title: 'Gabarito | CEMSL - Brasília', description: 'Consulte os gabaritos disponibilizados pelo CEMSL em Brasília, no Setor Leste, e acompanhe os conteúdos acadêmicos da escola.' }
      },
      {
        path: 'transparencia',
        name: 'transparencia',
        component: () => import('@/views/Transparencia.vue'),
        meta: { public: true, title: 'Transparência | CEMSL - Brasília', description: 'Acesse informações de transparência do CEMSL em Brasília, no Setor Leste, e consulte dados e informações institucionais.' }
      },
      {
        path: 'cardapio',
        name: 'cardapio',
        component: () => import('@/views/Cardapio.vue'),
        meta: { public: true, title: 'Cardápio | CEMSL - Brasília', description: 'Consulte o cardápio do CEMSL em Brasília, no Setor Leste, e confira as informações sobre a alimentação oferecida aos estudantes.' }
      },
      {
        path: 'ouvintes',
        name: 'ouvintes',
        component: () => import('@/views/Ouvintes.vue'),
        meta: { public: true, title: 'Ouvintes | CEMSL - Brasília', description: 'Acompanhe informações para ouvintes e a comunidade do CEMSL em Brasília, no Setor Leste, por meio dos canais e serviços da escola.' }
      },
      {
        path: 'mapa',
        name: 'mapa',
        component: () => import('@/views/Mapa.vue'),
        meta: { public: true, title: 'Localização | CEMSL - Brasília', description: 'Encontre a localização do CEMSL no Setor Leste de Brasília e consulte informações para chegar à instituição.' }
      },
      {
        path: 'conta',
        name: 'conta',
        component: () => import('@/views/MinhaConta.vue'),
        meta: { title: 'Minha conta | ABSL', description: 'Gerencie seu perfil no portal ABSL.' },
      },
      {
        path: 'usuarios',
        name: 'usuarios-legado',
        redirect: '/admin/usuarios',
        meta: { requiresAdmin: true, title: 'Usuários | ABSL' }
      },
      {
        path: ':pathMatch(.*)*',
        name: 'not-found',
        component: () => import('@/views/NotFound.vue'),
        meta: { public: true, title: 'Página não encontrada | CEMSL', description: 'A página solicitada não foi encontrada no portal do CEMSL em Brasília, no Setor Leste.' }
      },
    ]
  },
]

const router = createRouter({ history: createWebHistory(), routes })

router.beforeEach((to: RouteLocationNormalized) => {
  const currentUser = user.value
  const role = String(currentUser?.role || (currentUser?.is_admin ? 'admin' : 'user')).toLowerCase()
  const isStaff = role === 'admin' || role === 'imprensa'

  if (to.meta.requiresAdmin && role !== 'admin') return '/admin'
  if (to.meta.requiresStaff && !isStaff) return '/'
  if (!to.meta.public && !to.meta.requiresStaff && !currentUser) return '/'
  return true
})

router.afterEach((to, _from, failure) => {
  if (failure) return

  if (typeof to.meta.title === 'string') document.title = to.meta.title

  if (typeof to.meta.description === 'string') {
    let description = document.querySelector<HTMLMetaElement>('meta[name="description"]')
    if (!description) {
      description = document.createElement('meta')
      description.name = 'description'
      document.head.appendChild(description)
    }
    description.content = to.meta.description
  }
})

export default router
