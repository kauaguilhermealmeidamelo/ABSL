<script setup>
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import ProjetoCard from '@/components/projetos/ProjetoCard.vue'
import { useProjetos } from '@/composables/useProjetos'

const router = useRouter()
const { projetos, loading, error, fetchProjetos } = useProjetos()

function timestampAtualizacao(projeto) {
  const valor = projeto.updated_at
    ?? projeto.data_atualizacao
    ?? projeto.created_at
    ?? projeto.data_criacao

  if (!valor) return 0

  const timestamp = Date.parse(valor)
  return Number.isNaN(timestamp) ? 0 : timestamp
}

const projetosRecentes = computed(() =>
  [...projetos.value]
    .sort((a, b) => timestampAtualizacao(b) - timestampAtualizacao(a))
    .slice(0, 3),
)

onMounted(() => {
  fetchProjetos()
})

function abrirProjeto(projeto) {
  router.push(`/projetos/${projeto.id}`)
}
</script>

<template>
  <section class="home-section" aria-labelledby="projetos-titulo">
    <div class="section-heading">
      <div class="section-heading__copy">
        <p class="section-eyebrow">Iniciativas</p>
        <h2 id="projetos-titulo">Últimos projetos</h2>
        <p class="section-description">Conheça as iniciativas mais recentes da nossa comunidade.</p>
      </div>
      <router-link class="section-link" to="/projetos">
        Ver todos os projetos
        <v-icon size="16" aria-hidden="true">mdi-arrow-right</v-icon>
      </router-link>
    </div>

    <p v-if="loading" class="status-msg" role="status">Carregando projetos...</p>

    <p v-else-if="error" class="status-msg status-error" role="alert">
      Não foi possível carregar este conteúdo.
    </p>

    <p v-else-if="!projetosRecentes.length" class="empty-state">
      Ainda não há projetos para exibir.
    </p>

    <div v-else class="projects-grid">
      <ProjetoCard
        v-for="projeto in projetosRecentes"
        :key="projeto.id"
        :projeto="projeto"
        @abrir="abrirProjeto"
      />
    </div>
  </section>
</template>

<style scoped>
.home-section {
  margin-bottom: clamp(40px, 6vw, 64px);
}

.section-heading {
  display: flex;
  align-items: end;
  justify-content: space-between;
  gap: 24px;
  margin-bottom: 24px;
}

.section-heading__copy {
  min-width: 0;
}

.section-eyebrow {
  margin: 0 0 6px;
  color: #1a3f8f;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.14em;
  text-transform: uppercase;
}

h2 {
  margin: 0;
  color: #0d1f3c;
  font-family: 'Playfair Display', serif;
  font-size: clamp(26px, 3vw, 34px);
  line-height: 1.2;
}

.section-description {
  max-width: 580px;
  margin: 10px 0 0;
  color: #5a6a85;
  font-size: 14px;
  line-height: 1.6;
}

.section-link {
  display: inline-flex;
  flex-shrink: 0;
  align-items: center;
  gap: 7px;
  padding: 10px 0 8px;
  border-bottom: 2px solid transparent;
  color: #1a3f8f;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  transition: border-color 0.18s ease, gap 0.18s ease;
}

.section-link:hover,
.section-link:focus-visible {
  gap: 10px;
  border-bottom-color: currentColor;
}

.section-link:focus-visible {
  outline: 2px solid #1a3f8f;
  outline-offset: 4px;
  border-radius: 2px;
}

.projects-grid {
  display: grid;
  grid-template-columns: minmax(0, 1fr);
  align-items: stretch;
  gap: 20px;
}

.status-msg,
.empty-state {
  margin: 0;
  padding: 30px 18px;
  border: 1px solid rgba(13, 31, 60, 0.1);
  border-radius: 14px;
  background: #fff;
  color: #5a6a85;
  text-align: center;
  line-height: 1.6;
}

.status-error {
  border-color: rgba(185, 28, 28, 0.2);
  color: #b91c1c;
}

@media (min-width: 760px) {
  .projects-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 600px) {
  .section-heading {
    align-items: flex-start;
    flex-direction: column;
    gap: 12px;
  }

  .section-link {
    padding-top: 4px;
  }
}

@media (prefers-reduced-motion: reduce) {
  .section-link {
    transition: none;
  }
}
</style>
