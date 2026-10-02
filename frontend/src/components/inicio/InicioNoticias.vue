<script setup>
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import NoticiaCard from '@/components/noticias/NoticiaCard.vue'
import { useNoticias } from '@/composables/useNoticias'

const router = useRouter()
const { noticias, loading, error, fetchNoticias } = useNoticias()

const noticiasRecentes = computed(() => noticias.value.slice(0, 3))

onMounted(() => {
  fetchNoticias()
})

function abrirNoticia(id) {
  router.push(`/noticias/${id}`)
}
</script>

<template>
  <section class="home-section" aria-labelledby="noticias-titulo">
    <div class="section-heading">
      <div>
        <p class="section-eyebrow">Informação</p>
        <h2 id="noticias-titulo">Notícias</h2>
      </div>
      <router-link class="section-link" to="/noticias">Ver todas</router-link>
    </div>

    <p v-if="loading" class="status-msg" role="status">Carregando notícias...</p>

    <p v-else-if="error" class="status-msg status-error" role="alert">
      Não foi possível carregar este conteúdo.
    </p>

    <p v-else-if="!noticiasRecentes.length" class="empty-state">
      Ainda não há notícias publicadas.
    </p>

    <div v-else class="news-grid">
      <article v-for="noticia in noticiasRecentes" :key="noticia.id" class="news-item">
        <NoticiaCard :noticia="noticia" />
        <button type="button" class="detail-link" @click="abrirNoticia(noticia.id)">
          Ler notícia
          <span aria-hidden="true">→</span>
        </button>
      </article>
    </div>
  </section>
</template>

<style scoped>
.home-section {
  margin-bottom: 48px;
}

.section-heading {
  display: flex;
  align-items: end;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 18px;
}

.section-eyebrow {
  margin: 0 0 4px;
  color: #1a3f8f;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

h2 {
  margin: 0;
  color: #0d1f3c;
  font-family: 'Playfair Display', serif;
  font-size: 30px;
}

.section-link,
.detail-link {
  color: #1a3f8f;
  font-weight: 700;
  text-decoration: none;
}

.section-link:hover,
.detail-link:hover {
  text-decoration: underline;
}

.news-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
}

.news-item {
  min-width: 0;
}

.detail-link {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin-top: 10px;
  border: 0;
  padding: 0;
  background: transparent;
  font-size: 13px;
  cursor: pointer;
}

.status-msg,
.empty-state {
  margin: 0;
  padding: 28px 16px;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 12px;
  color: #5a6a85;
  text-align: center;
}

.status-error {
  color: #b91c1c;
}

@media (min-width: 760px) {
  .news-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 520px) {
  .section-heading {
    align-items: flex-start;
    flex-direction: column;
  }

  h2 {
    font-size: 26px;
  }
}
</style>