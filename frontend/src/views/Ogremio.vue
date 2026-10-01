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

    <nav class="sumario" aria-label="Tópicos de O Grêmio">
      <a v-for="numero in 6" :key="numero" :href="'#gremio-topico-' + numero">
        {{ numero }}
      </a>
    </nav>

    <section id="gremio-topico-1" class="section" aria-labelledby="quem-somos-title">
      <div class="section-heading">
        <span class="kicker">1 · Quem somos</span>
        <h2 id="quem-somos-title">Representação e participação estudantil</h2>
      </div>
      <Sobre />
    </section>

    <section id="gremio-topico-2" class="section" aria-labelledby="principios-title">
      <div class="section-heading">
        <span class="kicker">2 · Missão e princípios</span>
        <h2 id="principios-title">Princípios institucionais</h2>
        <p>
          Esta seção apresenta somente as informações institucionais próprias do Grêmio.
        </p>
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

    <section id="gremio-topico-3" class="section" aria-labelledby="funciona-title">
      <div class="section-heading">
        <span class="kicker">3 · Como o Grêmio funciona</span>
        <h2 id="funciona-title">Representação e organização</h2>
      </div>

      <div class="empty-state">
        <v-icon size="34" aria-hidden="true">mdi-information-outline</v-icon>
        <p>Conteúdo específico sobre o funcionamento do Grêmio ainda não está cadastrado.</p>
        <small>
          A seção fica reservada para informações institucionais próprias do Grêmio, sem duplicar
          projetos, documentos ou outros conteúdos do portal.
        </small>
      </div>
    </section>

    <section id="gremio-topico-4" class="section" aria-labelledby="diretoria-title">
      <div class="section-heading">
        <span class="kicker">4 · Diretoria e mandato</span>
        <h2 id="diretoria-title">Diretorias e integrantes cadastrados</h2>
        <p>
          A estrutura de diretoria é exibida a partir do cadastro existente, sem criar uma segunda
          fonte de dados.
        </p>
      </div>
      <Equipe />
    </section>

    <section id="gremio-topico-5" class="section" aria-labelledby="participar-title">
      <div class="section-heading">
        <span class="kicker">5 · Como participar</span>
        <h2 id="participar-title">Canais de participação</h2>
        <p>
          Os fluxos abaixo apenas direcionam para as páginas próprias do portal. O conteúdo de cada
          área continua sendo administrado no seu respectivo módulo.
        </p>
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

    <section id="gremio-topico-6" class="section" aria-labelledby="contato-title">
      <div class="section-heading">
        <span class="kicker">6 · Contato</span>
        <h2 id="contato-title">Canais cadastrados do Grêmio</h2>
        <p>Somente canais institucionais já cadastrados no portal são apresentados aqui.</p>
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
.page-header p,.section-heading p{max-width:720px;margin:12px 0 0;color:#5a6a85;line-height:1.6}
.sumario{display:flex;gap:8px;flex-wrap:wrap;margin:24px 0 8px}
.sumario a{display:grid;place-items:center;width:31px;height:31px;border-radius:50%;background:#eef3fb;color:#1a3f8f;text-decoration:none;font-size:12px;font-weight:700}
.sumario a:hover,.sumario a:focus-visible{background:#dfe8f8}
.sumario a:focus-visible{outline:2px solid #1a3f8f;outline-offset:2px}
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
.empty-state small{max-width:620px;font-size:12px;line-height:1.5;color:#7b879c}
@media(max-width:800px){.principios-grid,.participar-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:620px){.gremio-page{padding:20px 16px 48px}.page-header{padding-top:24px}.section{margin-top:40px}.principios-grid,.participar-grid{grid-template-columns:1fr}.sumario{gap:6px}.sumario a{width:29px;height:29px}}
</style>