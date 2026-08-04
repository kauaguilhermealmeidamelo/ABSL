import { createRouter, createWebHistory, type RouteLocationNormalized } from 'vue-router'
import MainLayout from '@/MainLayout.vue'
import inicio from '@/views/inicio.vue'

// Importe as novas views
import Horario from '@/views/Horario.vue'
import Noticias from '@/views/Noticias.vue'
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
      { path: 'projetos', name: 'projetos', component: Projetos },
      { path: 'gabarito', name: 'gabarito', component: Gabarito },
      { path: 'transparencia', name: 'transparencia', component: Transparencia },
        { path: 'cardapio', name: 'cardapio', component: Cardapio },
        { path: 'ouvintes', name: 'ouvintes', component: Ouvintes },
        { path: 'mapa', name: 'mapa', component: Mapa },
        { path: 'usuarios', name: 'usuarios', component: Usuarios },
    ]
  }
  ,
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// ... (seu código de beforeEach continua aqui)


router.beforeEach((to: RouteLocationNormalized) => {
    const publicPages = ['/', '/inicio', '/horario', '/noticias', '/projetos', '/gabarito', '/transparencia', '/cardapio', '/ouvintes', '/mapa']
    const authRequired = !publicPages.includes(to.path)
    const token = localStorage.getItem('token')

    // 1. Se requer auth e não tem token -> volta para home
    if (authRequired && !token) {
        return '/' 
    }

    // 2. Se é página pública E tem token -> tenta ir para uma área protegida
    // CORREÇÃO DO LOOP: Só redireciona se NÃO estivermos já em '/inicio'

    // 3. Se nenhuma condição acima for verdadeira, permite a navegação (substitui o next())
    return true 
})

export default router   