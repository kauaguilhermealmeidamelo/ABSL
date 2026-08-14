import { createRouter, createWebHistory, type RouteLocationNormalized } from 'vue-router'
import MainLayout from '@/MainLayout.vue'
import { user } from '@/stores/auth'

const routes = [
  {
    path: '/',
    component: MainLayout,
    redirect: '/inicio',
    children: [
      { path: 'inicio', name: 'inicio', component: () => import('@/views/inicio.vue'), meta: { public: true } },
      { path: 'horario', name: 'horario', component: () => import('@/views/Horario.vue'), meta: { public: true } },
      { path: 'noticias', name: 'noticias', component: () => import('@/views/Noticias.vue'), meta: { public: true } },
      { path: 'noticias/:id', name: 'noticia-detalhe', component: () => import('@/views/NoticiaDetalhe.vue'), meta: { public: true } },
      { path: 'projetos', name: 'projetos', component: () => import('@/views/Projetos.vue'), meta: { public: true } },
      { path: 'projetos/:id', name: 'projeto-detalhes', component: () => import('@/views/ProjetoDetalhes.vue'), meta: { public: true } },
      { path: 'gabarito', name: 'gabarito', component: () => import('@/views/Gabarito.vue'), meta: { public: true } },
      { path: 'transparencia', name: 'transparencia', component: () => import('@/views/Transparencia.vue'), meta: { public: true } },
      { path: 'cardapio', name: 'cardapio', component: () => import('@/views/Cardapio.vue'), meta: { public: true } },
      { path: 'ouvintes', name: 'ouvintes', component: () => import('@/views/Ouvintes.vue'), meta: { public: true } },
      { path: 'mapa', name: 'mapa', component: () => import('@/views/Mapa.vue'), meta: { public: true } },
      { path: 'usuarios', name: 'usuarios', component: () => import('@/views/Usuarios.vue') }, // não é público
      { path: ':pathMatch(.*)*', name: 'not-found', component: () => import('@/views/NotFound.vue'), meta: { public: true } },
    ]
  },
]

const router = createRouter({ history: createWebHistory(), routes })

router.beforeEach((to: RouteLocationNormalized) => {
  if (!to.meta.public && !user.value) return '/'
  return true
})
export default router