import { createRouter, createWebHistory, type RouteLocationNormalized } from 'vue-router'
import MainLayout from '@/MainLayout.vue'
import inicio from '@/views/inicio.vue'

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

const routes = [
  {
    path: '/',
    component: MainLayout,
    redirect: '/inicio',
    children: [
      { path: 'inicio', name: 'inicio', component: inicio },
      { path: 'horario', name: 'horario', component: Horario },
      { path: 'noticias', name: 'noticias', component: Noticias },
      { path: 'noticias/:id', name: 'noticia-detalhe', component: NoticiaDetalhe },
      { path: 'projetos', name: 'projetos', component: Projetos },
      { path: 'gabarito', name: 'gabarito', component: Gabarito },
      { path: 'transparencia', name: 'transparencia', component: Transparencia },
      { path: 'cardapio', name: 'cardapio', component: Cardapio },
      { path: 'ouvintes', name: 'ouvintes', component: Ouvintes },
      { path: 'mapa', name: 'mapa', component: Mapa },
      { path: 'usuarios', name: 'usuarios', component: Usuarios },
    ]
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to: RouteLocationNormalized) => {
    const publicPrefixes = ['/', '/inicio', '/horario', '/noticias', '/projetos', '/gabarito', '/transparencia', '/cardapio', '/ouvintes', '/mapa']
    const authRequired = !publicPrefixes.some((p) => to.path === p || to.path.startsWith(`${p}/`))
    const token = localStorage.getItem('token')

    if (authRequired && !token) {
        return '/'
    }

    return true
})

export default router