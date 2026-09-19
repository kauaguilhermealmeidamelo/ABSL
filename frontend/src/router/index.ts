import { createRouter, createWebHistory, type RouteLocationNormalized } from 'vue-router'
import MainLayout from '@/MainLayout.vue'
import { user } from '@/stores/auth'

const routes = [
  {
    path: '/',
    component: MainLayout,
    redirect: '/inicio',
    children: [
      { path: 'inicio', name: 'inicio', component: () => import('@/views/inicio.vue'), meta: { public: true, title: 'Início | CEMSL - Brasília', description: 'Conheça o CEMSL em Brasília, no Setor Leste. Acesse notícias, projetos, horários e informações da comunidade escolar.' } },
      { path: 'horario', name: 'horario', component: () => import('@/views/Horario.vue'), meta: { public: true, title: 'Horários | CEMSL - Brasília', description: 'Consulte os horários do CEMSL em Brasília, no Setor Leste, e organize sua rotina escolar.' } },
      { path: 'noticias', name: 'noticias', component: () => import('@/views/Noticias.vue'), meta: { public: true, title: 'Notícias | CEMSL - Brasília', description: 'Acompanhe as notícias do CEMSL em Brasília, no Setor Leste, com novidades, atividades e informações da comunidade escolar.' } },
      { path: 'noticias/:id', name: 'noticia-detalhe', component: () => import('@/views/NoticiaDetalhe.vue'), meta: { public: true, title: 'Notícia | CEMSL - Brasília', description: 'Leia notícias do CEMSL em Brasília, no Setor Leste, e acompanhe as novidades da comunidade escolar.' } },
      { path: 'projetos', name: 'projetos', component: () => import('@/views/Projetos.vue'), meta: { public: true, title: 'Projetos | CEMSL - Brasília', description: 'Conheça os projetos do CEMSL em Brasília, no Setor Leste, e acompanhe as iniciativas da comunidade escolar.' } },
      { path: 'projetos/:id', name: 'projeto-detalhes', component: () => import('@/views/ProjetoDetalhes.vue'), meta: { public: true, title: 'Projeto | CEMSL - Brasília', description: 'Confira detalhes dos projetos do CEMSL em Brasília, no Setor Leste, e conheça suas iniciativas.' } },
      { path: 'gabarito', name: 'gabarito', component: () => import('@/views/Gabarito.vue'), meta: { public: true, title: 'Gabaritos e Provas | CEMSL - Brasília', description: 'Consulte gabaritos e provas disponibilizados pelo CEMSL em Brasília, no Setor Leste.' } },
      { path: 'transparencia', name: 'transparencia', component: () => import('@/views/Transparencia.vue'), meta: { public: true, title: 'Transparência | Grêmio Athos Bulcão', description: 'Acesse atas, prestações de contas e documentos oficiais do Grêmio Athos Bulcão, no CEMSL em Brasília.' } },
      { path: 'cardapio', name: 'cardapio', component: () => import('@/views/Cardapio.vue'), meta: { public: true, title: 'Cardápio | CEMSL - Brasília', description: 'Consulte o cardápio do CEMSL em Brasília, no Setor Leste, e confira as opções da semana.' } },
      { path: 'ouvintes', name: 'ouvintes', component: () => import('@/views/Ouvintes.vue'), meta: { public: true, title: 'Ouvidoria | Grêmio Athos Bulcão', description: 'Envie sugestões, críticas e opiniões para o Grêmio Athos Bulcão, no CEMSL em Brasília, Setor Leste.' } },
      { path: 'mapa', name: 'mapa', component: () => import('@/views/Mapa.vue'), meta: { public: true, title: 'Localização | CEMSL - Brasília', description: 'Encontre a localização do CEMSL no Setor Leste de Brasília e consulte informações para chegar à escola.' } },
      { path: 'usuarios', name: 'usuarios', component: () => import('@/views/Usuarios.vue'), meta: { title: 'Gerenciamento | ABSL', description: 'Área administrativa do portal ABSL.' } },
      { path: ':pathMatch(.*)*', name: 'not-found', component: () => import('@/views/NotFound.vue'), meta: { public: true, title: 'Página não encontrada | ABSL', description: 'A página solicitada não foi encontrada no portal ABSL do CEMSL em Brasília.' } },
    ]
  },
]

const router = createRouter({ history: createWebHistory(), routes })

router.beforeEach((to: RouteLocationNormalized) => {
  if (!to.meta.public && !user.value) return '/'
  return true
})

router.afterEach((to, _from, failure) => {
  if (failure) return

  if (typeof to.meta.title === 'string') {
    document.title = to.meta.title
  }

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
