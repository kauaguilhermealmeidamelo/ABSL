<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Sobre from '@/components/inicio/Sobre.vue'
import Equipe from '@/components/inicio/Equipe.vue'
import InformacoesInstitucionais from '@/components/gremio/InformacoesInstitucionais.vue'
import { useGremioConteudos } from '@/composables/useGremioConteudos'

const router = useRouter()
const { itens, error, fetchConteudos } = useGremioConteudos()

onMounted(() => fetchConteudos(true))

function irPara(rota) {
  router.push(rota)
}
</script>

<template>
  <main class="gremio-page">
    <header class="page-header">
      <span class="kicker">O Grêmio</span>
      <h1>Grêmio Estudantil Athos Bulcão</h1>
      <p>
        Conheça a organização, a atuação e os canais de participação do Grêmio no CEM Setor Leste.
      </p>
    </header>

    <section class="section" aria-labelledby="quem-somos-title">
      <div class="section-heading">
        <h2 id="quem-somos-title">Quem somos</h2>
      </div>
      <Sobre />
    </section>

    <section class="section" aria-labelledby="principios-title">
      <div class="section-heading">
        <h2 id="principios-title">Missão e princípios</h2>
      </div>

      <div class="principios-grid">
        <article class="info-card">
          <h3>Representação</h3>
          <p>Representação dos estudantes e encaminhamento de suas demandas pelos canais apropriados.</p>
        </article>
        <article class="info-card">
          <h3>Participação</h3>
          <p>Incentivo à participação estudantil nas atividades e decisões relacionadas à comunidade escolar.</p>
        </article>
        <article class="info-card">
          <h3>Organização</h3>
          <p>Atuação organizada por meio das estruturas e responsáveis cadastrados para o Grêmio.</p>
        </article>
      </div>
    </section>

    <InformacoesInstitucionais :itens="itens" :erro="error" />

    <section class="section" aria-labelledby="diretoria-title">
      <div class="section-heading">
        <h2 id="diretoria-title">Diretoria e mandato</h2>
      </div>
      <Equipe />
    </section>

    <section class="section" aria-labelledby="participar-title">
      <div class="section-heading">
        <h2 id="participar-title">Como participar</h2>
      </div>

      <div class="participar-grid">
        <button type="button" class="action-card" @click="irPara('/ouvintes')">
          <v-icon size="24" aria-hidden="true">mdi-forum-outline</v-icon>
          <strong>Ouvidoria</strong>
          <span>Enviar sugestão, crítica ou opinião.</span>
        </button>
        <button type="button" class="action-card" @click="irPara('/noticias')">
          <v-icon size="24" aria-hidden="true">mdi-newspaper-variant-outline</v-icon>
          <strong>Notícias</strong>
          <span>Acompanhar as publicações do portal.</span>
        </button>
      </div>
    </section>

    <section class="section" aria-labelledby="contato-title">
      <div class="section-heading">
        <h2 id="contato-title">Contato</h2>
      </div>

      <div class="contato-card">
        <a href="https://www.instagram.com/gremioathosb/" target="_blank" rel="noopener noreferrer">
          <v-icon size="21" aria-hidden="true">mdi-instagram</v-icon>
          <span>Instagram do Grêmio</span>
        </a>
        <a href="mailto:gremio.athosbulcao.sl@gmail.com">
          <v-icon size="21" aria-hidden="true">mdi-email-outline</v-icon>
          <span>gremio.athosbulcao.sl@gmail.com</span>
        </a>
      </div>
    </section>
  </main>
</template>

<style scoped>
.gremio-page {
  width: min(100%, 1180px);
  margin: 0 auto;
  padding: 32px 32px 64px;
  font-family: var(--font-body, 'DM Sans', sans-serif);
}

.page-header {
  padding: 40px 0 28px;
  border-bottom: 1px solid rgba(13, 31, 60, 0.08);
}

.kicker {
  display: block;
  margin-bottom: 8px;
  color: var(--color-navy-soft, #16509b);
  font-family: var(--font-mono, 'DM Mono', monospace);
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.page-header h1,
.section-heading h2 {
  margin: 0;
  color: var(--color-navy, #0f2038);
  font-family: var(--font-heading, 'Playfair Display', serif);
  line-height: 1.15;
}

.page-header h1 {
  font-size: clamp(32px, 6vw, 52px);
}

.page-header p {
  max-width: 720px;
  margin: 12px 0 0;
  color: var(--color-text-secondary, #6b7c93);
  line-height: 1.6;
}

.section {
  margin-top: 48px;
  scroll-margin-top: 24px;
}

.section-heading {
  margin-bottom: 20px;
}

.section-heading h2 {
  font-size: clamp(25px, 4vw, 34px);
}

.principios-grid,
.participar-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 16px;
}

.info-card {
  padding: 20px;
  background: var(--color-surface, #ffffff);
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 12px;
}

.info-card h3 {
  margin: 0 0 8px;
  color: var(--color-navy, #0f2038);
  font-size: 17px;
}

.info-card p {
  margin: 0;
  color: var(--color-text-secondary, #6b7c93);
  font-size: 14px;
  line-height: 1.6;
}

.action-card {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 8px;
  padding: 20px;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 12px;
  background: var(--color-surface, #ffffff);
  color: var(--color-navy, #0f2038);
  text-align: left;
  cursor: pointer;
}

.action-card:hover {
  border-color: rgba(26, 63, 143, 0.25);
  box-shadow: 0 5px 16px rgba(13, 31, 60, 0.06);
}

.action-card span {
  color: var(--color-text-secondary, #6b7c93);
  font-size: 13px;
  line-height: 1.5;
}

.contato-card {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.contato-card a {
  display: inline-flex;
  align-items: center;
  gap: 9px;
  padding: 12px 16px;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 11px;
  background: var(--color-surface, #ffffff);
  color: var(--color-navy, #0f2038);
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
}

.contato-card a:hover {
  background: var(--color-surface-muted, #f7f8fc);
}

@media (max-width: 800px) {
  .principios-grid,
  .participar-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 620px) {
  .gremio-page {
    padding: 20px 16px 48px;
  }

  .page-header {
    padding-top: 24px;
  }

  .section {
    margin-top: 40px;
  }

  .principios-grid,
  .participar-grid {
    grid-template-columns: 1fr;
  }
}
</style>