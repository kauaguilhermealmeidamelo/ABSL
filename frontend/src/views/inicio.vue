<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import Banner from '@/components/inicio/Banner.vue'
import { useNoticias } from '@/composables/useNoticias'
import { useProjetos } from '@/composables/useProjetos'

const router = useRouter()
const { noticias, loading: noticiasLoading, error: noticiasError, fetchNoticias } = useNoticias()
const { projetos, loading: projetosLoading, error: projetosError, fetchProjetos } = useProjetos()

const noticiasRecentes = computed(() => noticias.value.slice(0, 9))
const projetosDestaque = computed(() => {
  const destacados = projetos.value.filter((projeto) => projeto.destaque)
  return (destacados.length ? destacados : projetos.value).slice(0, 3)
})

function primeiraMidia(noticia) {
  return noticia.midias?.find((midia) => midia.url)?.url || ''
}

function primeiraMidiaTipo(noticia) {
  return noticia.midias?.find((midia) => midia.url)?.tipo || ''
}

const noticiasTrilhaRef = ref(null)
const noticiaIndice = ref(0)

function atualizarIndiceNoticias() {
  const el = noticiasTrilhaRef.value
  if (!el || !el.clientWidth) return
  const item = el.querySelector('.news-slide')
  if (!item) return
  const passo = item.getBoundingClientRect().width + 16
  noticiaIndice.value = Math.max(0, Math.min(noticiasRecentes.value.length - 1, Math.round(el.scrollLeft / passo)))
}

function moverNoticias(direcao) {
  const el = noticiasTrilhaRef.value
  if (!el) return
  const item = el.querySelector('.news-slide')
  if (!item) return
  const passo = item.getBoundingClientRect().width + 16
  el.scrollBy({ left: direcao * passo, behavior: 'smooth' })
}

onMounted(() => {
  fetchNoticias()
  fetchProjetos()
})

function irPara(path) {
  router.push(path)
}

function conhecerGremio() {
  router.push('/o-gremio')
}
</script>

<template>
  <main class="home">
    <Banner @conhecer-gremio="conhecerGremio" />

    <section class="section" aria-labelledby="noticias-title">
      <div class="heading">
        <div><span class="kicker">Informação</span><h2 id="noticias-title">O que está acontecendo</h2><p>Notícias e comunicados publicados no portal.</p></div>
        <button class="link" type="button" @click="irPara('/noticias')">Ver todas <v-icon size="16">mdi-arrow-right</v-icon></button>
      </div>
      <div v-if="noticiasLoading" class="state">Carregando notícias...</div>
      <div v-else-if="noticiasError" class="state error">{{ noticiasError }}</div>
      <div v-else-if="!noticiasRecentes.length" class="state">Nenhuma notícia publicada no momento.</div>
      <div v-else class="news-carousel-wrapper">
        <button v-if="noticiasRecentes.length > 1" type="button" class="news-carousel-arrow news-carousel-arrow-left" aria-label="Notícia anterior" :disabled="noticiaIndice === 0" @click="moverNoticias(-1)"><v-icon size="22">mdi-chevron-left</v-icon></button>
        <div ref="noticiasTrilhaRef" class="news-carousel" @scroll.passive="atualizarIndiceNoticias">
          <article v-for="noticia in noticiasRecentes" :key="noticia.id" class="card news-card news-slide">
          <img
            v-if="primeiraMidiaTipo(noticia) === 'imagem'"
            :src="primeiraMidia(noticia)"
            :alt="noticia.titulo"
            class="news-image"
            loading="lazy"
          >
          <video
            v-else-if="primeiraMidiaTipo(noticia) === 'video'"
            :src="primeiraMidia(noticia)"
            :aria-label="noticia.titulo"
            class="news-image"
            controls
            preload="metadata"
            playsinline
          />
          <div v-else class="news-image news-image-placeholder" aria-hidden="true">
            <v-icon size="32">mdi-newspaper-variant-outline</v-icon>
          </div>
          <small>{{ noticia.categoria || 'Notícia' }} <span v-if="noticia.data_publicacao">· {{ noticia.data_publicacao }}</span></small>
          <h3>{{ noticia.titulo }}</h3>
          <p>{{ noticia.texto }}</p>
          <button class="link" type="button" @click="irPara(`/noticias/${noticia.id}`)">Ler notícia <v-icon size="15">mdi-arrow-right</v-icon></button>
          </article>
        </div>
        <button v-if="noticiasRecentes.length > 1" type="button" class="news-carousel-arrow news-carousel-arrow-right" aria-label="Próxima notícia" :disabled="noticiaIndice >= noticiasRecentes.length - 1" @click="moverNoticias(1)"><v-icon size="22">mdi-chevron-right</v-icon></button>
      </div>
    </section>

    <section class="section" aria-labelledby="projetos-title">
      <div class="heading">
        <div><span class="kicker">Ação</span><h2 id="projetos-title">Projetos em destaque</h2><p>Iniciativas cadastradas no portfólio do Grêmio.</p></div>
        <button class="link" type="button" @click="irPara('/projetos')">Ver projetos <v-icon size="16">mdi-arrow-right</v-icon></button>
      </div>
      <div v-if="projetosLoading" class="state">Carregando projetos...</div>
      <div v-else-if="projetosError" class="state error">{{ projetosError }}</div>
      <div v-else-if="!projetosDestaque.length" class="state">Nenhum projeto disponível no momento.</div>
      <div v-else class="grid">
        <article v-for="projeto in projetosDestaque" :key="projeto.id" class="card project" tabindex="0" role="link" @click="irPara(`/projetos/${projeto.id}`)" @keydown.enter="irPara(`/projetos/${projeto.id}`)">
          <img v-if="projeto.imagem_url" :src="projeto.imagem_url" :alt="projeto.titulo" loading="lazy">
          <small>{{ projeto.categoria || 'Projeto' }} · {{ projeto.status === 'concluido' ? 'Concluído' : 'Em andamento' }}</small>
          <h3>{{ projeto.titulo }}</h3>
          <p>{{ projeto.descricao }}</p>
          <span class="link">Conhecer projeto <v-icon size="15">mdi-arrow-right</v-icon></span>
        </article>
      </div>
    </section>

    <section class="section participation" aria-labelledby="participacao-title">
      <div><span class="kicker">Participe</span><h2 id="participacao-title">Acompanhe a vida escolar e o Grêmio.</h2><p>Use os canais e áreas que já estão disponíveis no portal.</p></div>
      <div class="quick-grid">
        <button class="quick" type="button" @click="irPara('/ouvintes')"><v-icon>mdi-forum-outline</v-icon><strong>Os Ouvintes</strong><span>Manifestações pelos canais existentes.</span></button>
        <button class="quick" type="button" @click="irPara('/transparencia')"><v-icon>mdi-shield-outline</v-icon><strong>Transparência</strong><span>Conteúdos de transparência publicados.</span></button>
        <button class="quick" type="button" @click="irPara('/horario')"><v-icon>mdi-clock-outline</v-icon><strong>Horários</strong><span>Consulte a rotina escolar.</span></button>
        <button class="quick" type="button" @click="irPara('/cardapio')"><v-icon>mdi-silverware-fork-knife</v-icon><strong>Cardápio</strong><span>Veja as informações cadastradas.</span></button>
      </div>
    </section>

    <section class="section" aria-labelledby="equipe-title">
      <div class="heading"><div><span class="kicker">Organização</span><h2 id="equipe-title">Como o Grêmio se organiza</h2><p>Equipe e diretorias cadastradas no portal.</p></div></div>
      <Equipe />
    </section>

    <section class="final" aria-labelledby="final-title">
      <div><span class="kicker">Portal ABSL</span><h2 id="final-title">Informação, participação e transparência em um só lugar.</h2></div>
      <div class="actions">
        <button class="primary" type="button" @click="irPara('/noticias')">Notícias</button>
        <button class="secondary" type="button" @click="irPara('/projetos')">Projetos</button>
        <button class="secondary" type="button" @click="irPara('/transparencia')">Transparência</button>
      </div>
    </section>
  </main>
</template>

<style scoped>
.home{width:min(100%,1180px);margin:0 auto;padding:24px 32px 64px;font-family:'DM Sans',sans-serif;box-sizing:border-box}.section{margin-top:56px;scroll-margin-top:24px}.heading{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:20px}.kicker{display:block;margin-bottom:7px;color:#1a3f8f;font-family:'DM Mono',monospace;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}.heading h2,.participation h2,.final h2{margin:0;color:#0d1f3c;font-family:'Playfair Display',serif;font-size:clamp(25px,4vw,34px);line-height:1.15}.heading p,.participation p{margin:8px 0 0;color:#5a6a85;line-height:1.6}.link{display:inline-flex;align-items:center;gap:5px;padding:5px 0;border:0;background:transparent;color:#1a3f8f;font-weight:700;cursor:pointer;text-decoration:none}.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.news-carousel-wrapper{position:relative;display:flex;align-items:center;gap:10px}.news-carousel{display:flex;gap:16px;width:100%;overflow-x:auto;overflow-y:hidden;scroll-snap-type:x mandatory;scroll-behavior:smooth;scrollbar-width:none;-webkit-overflow-scrolling:touch;touch-action:pan-x}.news-carousel::-webkit-scrollbar{display:none}.news-slide{flex:0 0 calc((100% - 32px) / 3);min-width:0;scroll-snap-align:start}.news-carousel-arrow{flex:0 0 38px;width:38px;height:38px;border:1px solid rgba(13,31,60,.12);border-radius:50%;background:#fff;color:#0d1f3c;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 4px 12px rgba(13,31,60,.08);z-index:2}.news-carousel-arrow:hover:not(:disabled){background:#f4f6fa}.news-carousel-arrow:disabled{opacity:.35;cursor:default}.card{padding:20px;border:1px solid rgba(13,31,60,.08);border-radius:16px;background:#fff;box-shadow:0 4px 14px rgba(13,31,60,.04)}.card small{color:#1a3f8f;font-weight:700;text-transform:uppercase;font-size:10px}.card h3{margin:10px 0 8px;color:#0d1f3c;font-size:17px}.card p{display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden;margin:0 0 14px;color:#5a6a85;font-size:13px;line-height:1.6}.news-card{overflow:hidden}.news-image{display:block;width:calc(100% + 40px);height:170px;margin:-20px -20px 16px;object-fit:cover}.news-image-placeholder{display:flex;align-items:center;justify-content:center;background:#eef2f7;color:#94a3b8}.project{cursor:pointer}.project img{display:block;width:calc(100% + 40px);height:150px;margin:-20px -20px 16px;object-fit:cover}.state{display:flex;align-items:center;justify-content:center;min-height:100px;padding:20px;border:1px dashed #cbd5e1;border-radius:14px;color:#5a6a85;text-align:center}.error{color:#b91c1c}.participation{display:grid;grid-template-columns:minmax(240px,.8fr) minmax(0,1.4fr);gap:28px;padding:28px;border-radius:20px;background:#f4f6fa}.quick-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.quick{min-height:120px;padding:16px;border:1px solid rgba(13,31,60,.08);border-radius:14px;background:#fff;text-align:left;color:#0d1f3c;cursor:pointer}.quick strong,.quick span{display:block}.quick strong{margin-top:9px}.quick span{margin-top:5px;color:#64748b;font-size:12px;line-height:1.4}.final{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-top:56px;padding:28px;border-radius:20px;background:#0d1f3c}.final h2,.final .kicker{color:#fff}.actions{display:flex;flex-wrap:wrap;gap:8px}.primary,.secondary{padding:9px 16px;border-radius:999px;font-weight:700;cursor:pointer}.primary{border:0;background:#f5c518;color:#0d1f3c}.secondary{border:1px solid rgba(255,255,255,.3);background:transparent;color:#fff}button:focus-visible,.project:focus-visible{outline:3px solid rgba(245,197,24,.8);outline-offset:3px}@media(max-width:900px){.home{padding-inline:20px}.grid{grid-template-columns:1fr 1fr}.participation,.final{grid-template-columns:1fr;display:grid}.actions{justify-content:flex-start}}@media(max-width:620px){.home{padding:16px 16px 48px}.section{margin-top:40px}.heading{align-items:flex-start;flex-direction:column;gap:8px}.grid,.quick-grid{grid-template-columns:1fr}.news-carousel-wrapper{display:block}.news-carousel{gap:16px}.news-slide{flex-basis:100%}.news-carousel-arrow{display:none}.participation,.final{padding:20px}.actions{flex-direction:column}.actions button{width:100%}}
</style>
