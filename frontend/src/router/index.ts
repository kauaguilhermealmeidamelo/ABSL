import { createRouter, createWebHistory, type RouteLocationNormalized } from 'vue-router'
import MainLayout from '@/MainLayout.vue'
import { user } from '@/stores/auth'

const routes = [
  {
    path: '/',
    component: MainLayout,
    redirect: '/inicio',
    children: [
      {
        path: 'inicio',
        name: 'inicio',
        component: () => import('@/views/inicio.vue'),
        meta: {
          public: true,
          title: 'Início | CEMSL - Brasília',
          description:
            'Conheça o CEMSL em Brasília, no Setor Leste. Acesse notícias, projetos, horários e informações da comunidade escolar.'
        }
      },
      {
        path: 'horario',
        name: 'horario',
        component: () => import('@/views/Horario.vue'),
        meta: {
          public: true,
          title: 'Horários | CEMSL - Brasília',
          description:
            'Consulte os horários do CEMSL em Brasília, no Setor Leste, e organize sua rotina escolar com informações atualizadas.'
        }
      },
      {
        path: 'noticias',
        name: 'noticias',
        component: () => import('@/views/Noticias.vue'),
        meta: {
          public: true,
          title: 'Notícias | CEMSL - Brasília',
          description:
            'Acompanhe as notícias do CEMSL em Brasília, no Setor Leste, com novidades, atividades, eventos e informações da comunidade escolar.'
        }
      },
      {
        path: 'noticias/:id',
        name: 'noticia-detalhe',
        component: () => import('@/views/NoticiaDetalhe.vue'),
        meta: {
          public: true,
          title: 'Notícia | CEMSL - Brasília',
          description:
            'Leia esta notícia do CEMSL em Brasília, no Setor Leste, e acompanhe as principais informações e novidades da comunidade escolar.'
        }
      },
      {
        path: 'projetos',
        name: 'projetos',
        component: () => import('@/views/Projetos.vue'),
        meta: {
          public: true,
          title: 'Projetos | CEMSL - Brasília',
          description:
            'Conheça os projetos desenvolvidos pelo CEMSL em Brasília, no Setor Leste, e acompanhe as iniciativas da comunidade escolar.'
        }
      },
      {
        path: 'projetos/:id',
        name: 'projeto-detalhes',
        component: () => import('@/views/ProjetoDetalhes.vue'),
        meta: {
          public: true,
          title: 'Projeto | CEMSL - Brasília',
          description:
            'Confira detalhes dos projetos do CEMSL em Brasília, no Setor Leste, e conheça as iniciativas realizadas pela comunidade escolar.'
        }
      },
      {
        path: 'gabarito',
        name: 'gabarito',
        component: () => import('@/views/Gabarito.vue'),
        meta: {
          public: true,
          title: 'Gabarito | CEMSL - Brasília',
          description:
            'Consulte os gabaritos disponibilizados pelo CEMSL em Brasília, no Setor Leste, e acompanhe os conteúdos acadêmicos da escola.'
        }
      },
      {
        path: 'transparencia',
        name: 'transparencia',
        component: () => import('@/views/Transparencia.vue'),
        meta: {
          public: true,
          title: 'Transparência | CEMSL - Brasília',
          description:
            'Acesse informações de transparência do CEMSL em Brasília, no Setor Leste, e consulte dados e informações institucionais.'
        }
      },
      {
        path: 'cardapio',
        name: 'cardapio',
        component: () => import('@/views/Cardapio.vue'),
        meta: {
          public: true,
          title: 'Cardápio | CEMSL - Brasília',
          description:
            'Consulte o cardápio do CEMSL em Brasília, no Setor Leste, e confira as informações sobre a alimentação oferecida aos estudantes.'
        }
      },
      {
        path: 'ouvintes',
        name: 'ouvintes',
        component: () => import('@/views/Ouvintes.vue'),
        meta: {
          public: true,
          title: 'Ouvintes | CEMSL - Brasília',
          description:
            'Acompanhe informações para ouvintes e a comunidade do CEMSL em Brasília, no Setor Leste, por meio dos canais e serviços da escola.'
        }
      },
      {
        path: 'mapa',
        name: 'mapa',
        component: () => import('@/views/Mapa.vue'),
        meta: {
          public: true,
          title: 'Localização | CEMSL - Brasília',
          description:
            'Encontre a localização do CEMSL no Setor Leste de Brasília e consulte informações para chegar à instituição.'
        }
      },
      {
        path: 'usuarios',
        name: 'usuarios',
        component: () => import('@/views/Usuarios.vue'),
        meta: {
          title: 'Usuários | ABSL',
          description:
            'Área de gerenciamento de usuários do sistema ABSL.'
        }
      },
      {
        path: ':pathMatch(.*)*',
        name: 'not-found',
        component: () => import('@/views/NotFound.vue'),
        meta: {
          public: true,
          title: 'Página não encontrada | CEMSL',
          description:
            'A página solicitada não foi encontrada no portal do CEMSL em Brasília, no Setor Leste.'
        }
      },
    ]
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

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
    let description = document.querySelector<HTMLMetaElement>(
      'meta[name="description"]'
    )

    if (!description) {
      description = document.createElement('meta')
      description.name = 'description'
      document.head.appendChild(description)
    }

    description.content = to.meta.description
  }
})

export default router
