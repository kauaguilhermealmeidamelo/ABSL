<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAdmin } from '@/composables/useAdmin'
import Sobre from '@/components/inicio/Sobre.vue'
import Equipe from '@/components/inicio/Equipe.vue'
import OgremioEditor from '@/components/inicio/OgremioEditor.vue'
import { transparenciaService } from '@/services/transparencia'
import { useProjetos } from '@/composables/useProjetos'

const router = useRouter()
const { isAdmin } = useAdmin()
const { projetos, loading: projetosLoading, error: projetosError, fetchProjetos } = useProjetos()

const documentos = ref([])
const documentosLoading = ref(false)
const documentosError = ref('')

async function carregarDocumentos() {
  documentosLoading.value = true
  documentosError.value = ''

  try {
    documentos.value = await transparenciaService.list()
  } catch {
    documentosError.value = 'Não foi possível carregar os documentos disponíveis.'
  } finally {
    documentosLoading.value = false
  }
}

onMounted(() => {
  carregarDocumentos()
  fetchProjetos(true)
})

const projetosAtivos = computed(() => projetos.value.slice(0, 6))
const documentosAtivos = computed(() => documentos.value.slice(0, 6))

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

    <OgremioEditor />

    <nav class="sumario" aria-label="Tópicos de O Grêmio">
      <a v-for="numero in 9" :key="numero" :href="'#gremio-topico-' + numero">
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

    <section id="gremio-topico-2" class="section" aria-labelledby="missao-title">
      <div class="section-heading">
        <span class="kicker">2 · Missão e princípios</span>
        <h2 id="missao-title">Princípios já presentes no conteúdo institucional</h2>
        <p>
          Esta seção reaproveita o conteúdo existente da página sobre o Grêmio, sem criar uma nova
          versão institucional.
        </p>
      </div>

      <div class="principios-grid">
        <article class="info-card">
          <h3>Representar</h3>
          <p>Levar as demandas dos estudantes à direção e acompanhar as respostas.</p>
        </article>
        <article class="info-card">
          <h3>Cuidar</h3>
          <p>Campanhas solidárias, acolhimento e projetos de bem-estar dentro da escola.</p>
        </article>
        <article class="info-card">
          <h3>Movimentar</h3>
          <p>Eventos, torneios, feiras culturais e ações que movimentam a escola.</p>
        </article>
      </div>
    </section>

    <section id="gremio-topico-3" class="section" aria-labelledby="funciona-title">
      <div class="section-heading">
        <span class="kicker">3 · Como o Grêmio funciona</span>
        <h2 id="funciona-title">Representação, participação e organização</h2>
      </div>

      <div class="empty-state">
        <v-icon size="34" aria-hidden="true">mdi-information-outline</v-icon>
        <p>Conteúdo específico sobre o funcionamento do Grêmio ainda não está cadastrado.</p>
        <small>A estrutura de edição desta seção já está reservada no gerenciamento de O Grêmio.</small>
      </div>
    </section>

    <section id="gremio-topico-4" class="section" aria-labelledby="diretoria-title">
      <div class="section-heading">
        <span class="kicker">4 · Diretoria e mandato</span>
        <h2 id="diretoria-title">Diretorias e integrantes cadastrados</h2>
      </div>
      <Equipe />
    </section>

    <section id="gremio-topico-5" class="section" aria-labelledby="documentos-title">
      <div class="section-heading">
        <span class="kicker">5 · Estatuto e documentos</span>
        <h2 id="documentos-title">Documentos já publicados</h2>
        <p>
          Os documentos abaixo são carregados da mesma fonte usada pelo Portal de Transparência.
        </p>
      </div>

      <p v-if="documentosLoading" class="status-msg">Carregando documentos...</p>
      <p v-else-if="documentosError" class="status-msg status-erro">{{ documentosError }}</p>

      <div v-else-if="documentosAtivos.length" class="documentos-grid">
        <article v-for="documento in documentosAtivos" :key="documento.id" class="info-card documento-card">
          <span>{{ documento.categoria || 'Documento' }}</span>
          <h3>{{ documento.referencia || documento.titulo }}</h3>
          <p>{{ documento.descricao }}</p>
          <a
            v-if="documento.arquivo_url"
            :href="documento.arquivo_url"
            target="_blank"
            rel="noopener noreferrer"
          >
            Abrir documento
            <v-icon size="15" aria-hidden="true">mdi-open-in-new</v-icon>
          </a>
        </article>
      </div>

      <div v-else class="empty-state">
        <v-icon size="34" aria-hidden="true">mdi-file-document-outline</v-icon>
        <p>Nenhum documento publicado nesta fonte ainda.</p>
      </div>

      <button type="button" class="secondary-action" @click="irPara('/transparencia')">
        Ver Portal de Transparência
        <v-icon size="16" aria-hidden="true">mdi-arrow-right</v-icon>
      </button>
    </section>

    <section id="gremio-topico-6" class="section" aria-labelledby="eleicoes-title">
      <div class="section-heading">
        <span class="kicker">6 · Eleições</span>
        <h2 id="eleicoes-title">Informações e documentos disponíveis</h2>
        <p>
          A página não cria informações eleitorais novas. Quando houver documentos cadastrados,
          eles devem ser apresentados a partir das fontes oficiais já disponíveis no sistema.
        </p>
      </div>

      <div class="empty-state">
        <v-icon size="34" aria-hidden="true">mdi-ballot-outline</v-icon>
        <p>Não há uma fonte específica de eleições cadastrada no sistema neste momento.</p>
        <small>O tópico permanece preparado para receber documentos e informações verificáveis.</small>
      </div>
    </section>

    <section id="gremio-topico-7" class="section" aria-labelledby="projetos-title">
      <div class="section-heading">
        <span class="kicker">7 · Projetos e atuação</span>
        <h2 id="projetos-title">Projetos cadastrados</h2>
        <p>Os projetos são exibidos diretamente do cadastro existente, sem duplicação de conteúdo.</p>
      </div>

      <p v-if="projetosLoading" class="status-msg">Carregando projetos...</p>
      <p v-else-if="projetosError" class="status-msg status-erro">{{ projetosError }}</p>

      <div v-else-if="projetosAtivos.length" class="projetos-grid">
        <article v-for="projeto in projetosAtivos" :key="projeto.id" class="info-card">
          <span>{{ projeto.categoria || 'Projeto' }}</span>
          <h3>{{ projeto.titulo }}</h3>
          <p>{{ projeto.descricao }}</p>
          <button type="button" class="card-link" @click="irPara('/projetos/' + projeto.id)">
            Conhecer projeto
            <v-icon size="15" aria-hidden="true">mdi-arrow-right</v-icon>
          </button>
        </article>
      </div>

      <div v-else class="empty-state">
        <v-icon size="34" aria-hidden="true">mdi-folder-open-outline</v-icon>
        <p>Nenhum projeto cadastrado ainda.</p>
      </div>

      <button type="button" class="secondary-action" @click="irPara('/projetos')">
        Ver todos os projetos
        <v-icon size="16" aria-hidden="true">mdi-arrow-right</v-icon>
      </button>
    </section>

    <section id="gremio-topico-8" class="section" aria-labelledby="participar-title">
      <div class="section-heading">
        <span class="kicker">8 · Como participar</span>
        <h2 id="participar-title">Use os fluxos que já existem no portal</h2>
      </div>

      <div class="participar-grid">
        <button type="button" class="action-card" @click="irPara('/ouvintes')">
          <v-icon size="24" aria-hidden="true">mdi-forum-outline</v-icon>
          <strong>Ouvidoria</strong>
          <span>Enviar sugestão, crítica ou opinião.</span>
        </button>
        <button type="button" class="action-card" @click="irPara('/projetos')">
          <v-icon size="24" aria-hidden="true">mdi-folder-multiple-outline</v-icon>
          <strong>Projetos</strong>
          <span>Conhecer os projetos já cadastrados.</span>
        </button>
        <button type="button" class="action-card" @click="irPara('/noticias')">
          <v-icon size="24" aria-hidden="true">mdi-newspaper-variant-outline</v-icon>
          <strong>Notícias</strong>
          <span>Acompanhar a atuação publicada pelo portal.</span>
        </button>
      </div>
    </section>

    <section id="gremio-topico-9" class="section" aria-labelledby="contato-title">
      <div class="section-heading">
        <span class="kicker">9 · Contato</span>
        <h2 id="contato-title">Canais cadastrados do Grêmio</h2>
        <p>Somente canais que já estão cadastrados no portal são apresentados aqui.</p>
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
.principios-grid,.documentos-grid,.projetos-grid,.participar-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.info-card{padding:20px;background:#fff;border:1px solid rgba(13,31,60,.08);border-radius:12px}
.info-card h3{margin:0 0 8px;color:#0d1f3c;font-size:17px}
.info-card p{margin:0;color:#5a6a85;font-size:14px;line-height:1.6}
.info-card>span{display:block;margin-bottom:7px;color:#1a3f8f;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em}
.documento-card a,.card-link{display:inline-flex;align-items:center;gap:5px;margin-top:14px;color:#1a3f8f;font-size:13px;font-weight:700;text-decoration:none}
.card-link{padding:0;border:0;background:transparent;cursor:pointer}
.secondary-action{display:inline-flex;align-items:center;gap:6px;margin-top:18px;padding:9px 15px;border:1px solid rgba(13,31,60,.12);border-radius:999px;background:#fff;color:#0d1f3c;font-size:13px;font-weight:700;cursor:pointer}
.secondary-action:hover{background:#f5f7fb}
.action-card{display:flex;flex-direction:column;align-items:flex-start;gap:8px;padding:20px;border:1px solid rgba(13,31,60,.08);border-radius:12px;background:#fff;color:#0d1f3c;text-align:left;cursor:pointer}
.action-card:hover{border-color:rgba(26,63,143,.25);box-shadow:0 5px 16px rgba(13,31,60,.06)}
.action-card span{color:#5a6a85;font-size:13px;line-height:1.5}
.contato-card{display:flex;flex-wrap:wrap;gap:12px}
.contato-card a{display:inline-flex;align-items:center;gap:9px;padding:12px 16px;border:1px solid rgba(13,31,60,.08);border-radius:11px;background:#fff;color:#0d1f3c;text-decoration:none;font-size:13px;font-weight:600}
.contato-card a:hover{background:#f5f7fb}
.empty-state{display:flex;flex-direction:column;align-items:center;gap:8px;padding:34px 16px;border:1px dashed rgba(13,31,60,.14);border-radius:12px;color:#5a6a85;text-align:center}
.empty-state p{margin:0;font-size:14px}
.empty-state small{max-width:620px;font-size:12px;line-height:1.5;color:#7b879c}
.status-msg{color:#5a6a85;font-size:14px;padding:12px 0}
.status-erro{color:#dc2626}
@media(max-width:800px){.principios-grid,.documentos-grid,.projetos-grid,.participar-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:620px){.gremio-page{padding:20px 16px 48px}.page-header{padding-top:24px}.section{margin-top:40px}.principios-grid,.documentos-grid,.projetos-grid,.participar-grid{grid-template-columns:1fr}.sumario{gap:6px}.sumario a{width:29px;height:29px}}
</style>
