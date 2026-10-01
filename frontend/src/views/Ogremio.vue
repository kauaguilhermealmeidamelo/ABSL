<script setup>
import { useRouter } from 'vue-router'
import Sobre from '@/components/inicio/Sobre.vue'
import Equipe from '@/components/inicio/Equipe.vue'

const router = useRouter()

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

    <section class="section" aria-labelledby="funciona-title">
      <div class="section-heading">
        <h2 id="funciona-title">Como o Grêmio funciona</h2>
      </div>

      <div class="empty-state">
        <v-icon size="34" aria-hidden="true">mdi-information-outline</v-icon>
        <p>Conteúdo específico sobre o funcionamento do Grêmio ainda não está cadastrado.</p>
      </div>
    </section>

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
.gremio-page{width:min(100%,1180px);margin:0 auto;padding:32px 32px 64px;font-family:'DM Sans',sans-serif}
.page-header{padding:40px 0 28px;border-bottom:1px solid rgba(13,31,60,.08)}
.kicker{display:block;margin-bottom:8px;color:#1a3f8f;font-family:'DM Mono',monospace;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}
.page-header h1,.section-heading h2{margin:0;color:#0d1f3c;font-family:'Playfair Display',serif;line-height:1.15}
.page-header h1{font-size:clamp(32px,6vw,52px)}
.page-header p{max-width:720px;margin:12px 0 0;color:#5a6a85;line-height:1.6}
.section{margin-top:48px;scroll-margin-top:24px}
.section-heading{margin-bottom:20px}
.section-heading h2{font-size:clamp(25px,4vw,34px)}
.principios-grid,.participar-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.info-card{padding:20px;background:#fff;border:1px solid rgba(13,31,60,.08);border-radius:12px}
.info-card h3{margin:0 0 8px;color:#0d1f3c;font-size:17px}
.info-card p{margin:0;color:#5a6a85;font-size:14px;line-height:1.6}
.action-card{display:flex;flex-direction:column;align-items:flex-start;gap:8px;padding:20px;border:1px solid rgba(13,31,60,.08);border-radius:12px;background:#fff;color:#0d1f3c;text-align:left;cursor:pointer}
.action-card:hover{border-color:rgba(26,63,143,.25);box-shadow:0 5px 16px rgba(13,31,60,.06)}
.action-card span{color:#5a6a85;font-size:13px;line-height:1.5}
.contato-card{display:flex;flex-wrap:wrap;gap:12px}
.contato-card a{display:inline-flex;align-items:center;gap:9px;padding:12px 16px;border:1px solid rgba(13,31,60,.08);border-radius:11px;background:#fff;color:#0d1f3c;text-decoration:none;font-size:13px;font-weight:600}
.contato-card a:hover{background:#f5f7fb}
.empty-state{display:flex;flex-direction:column;align-items:center;gap:8px;padding:34px 16px;border:1px dashed rgba(13,31,60,.14);border-radius:12px;color:#5a6a85;text-align:center}
.empty-state p{margin:0;font-size:14px}
@media(max-width:800px){.principios-grid,.participar-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:620px){.gremio-page{padding:20px 16px 48px}.page-header{padding-top:24px}.section{margin-top:40px}.principios-grid,.participar-grid{grid-template-columns:1fr}}
</style>