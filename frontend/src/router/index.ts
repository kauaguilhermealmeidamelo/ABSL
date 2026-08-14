import { createRouter, createWebHistory, type RouteLocationNormalized } from 'vue-router'
import MainLayout from '@/MainLayout.vue'
import inicio from '@/views/inicio.vue'
import { user } from '@/stores/auth'

import Horario from '@/views/Horario.vue'
import Noticias from '@/views/Noticias.vue'
import NoticiaDetalhe from '@/views/NoticiaDetalhe.vue'
import Projetos from '@/views/Projetos.vue'
import Gabarito from '@/views/Gabarito.vue'
import Transparencia from '@/views/Transparencia.vue'
import Cardapio from '@/views/Cardapio.vue'
import Ouvintes from '@/views/Ouvintes.vue'
import Mapa from '@/views/Mapa.vue'
import Usuarios from '@/views/Usuarios.vue'
import ProjetoDetalhes from '@/views/ProjetoDetalhes.vue'

const routes = [
  {
    path: '/',
    component: MainLayout,
    redirect: '/inicio',
    children: [
      { path: 'inicio', name: 'inicio', component: inicio, meta: { public: true } },
      { path: 'horario', name: 'horario', component: Horario, meta: { public: true } },
      { path: 'noticias', name: 'noticias', component: Noticias, meta: { public: true } },
      { path: 'noticias/:id', name: 'noticia-detalhe', component: NoticiaDetalhe, meta: { public: true } },
      { path: 'projetos', name: 'projetos', component: Projetos, meta: { public: true } },
      { path: 'projetos/:id', name: 'projeto-detalhes', component: ProjetoDetalhes, meta: { public: true } },
      { path: 'gabarito', name: 'gabarito', component: Gabarito, meta: { public: true } },
      { path: 'transparencia', name: 'transparencia', component: Transparencia, meta: { public: true } },
      { path: 'cardapio', name: 'cardapio', component: Cardapio, meta: { public: true } },
      { path: 'ouvintes', name: 'ouvintes', component: Ouvintes, meta: { public: true } },
      { path: 'mapa', name: 'mapa', component: Mapa, meta: { public: true } },
      { path: 'usuarios', name: 'usuarios', component: Usuarios }, // não é público
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