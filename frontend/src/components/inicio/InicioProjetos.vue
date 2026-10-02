<script setup>
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import ProjetoCard from '@/components/projetos/ProjetoCard.vue'
import { useProjetos } from '@/composables/useProjetos'

const router = useRouter()
const { projetos, loading, error, fetchProjetos } = useProjetos()

const projetosRecentes = computed(() => projetos.value.slice(0, 3))

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
      <div>
        <p class="section-eyebrow">Iniciativas</p>
        <h2 id="projetos-titulo">Últimos projetos</h2>
      </div>
      <router-link class="section-link" to="/projetos">Ver todos os projetos</router-link>
    </div>

    <p v-if="loading" class="status-msg" role="status">Carregando projetos...</p>

    <p v-else-if="error" class="status-msg status-error" role="alert">
      Não foi possível carregar este conteúdo.
    </p>

    <p v-else-if="!projetosRecentes.length" class="empty-state">
      Ainda não há projetos publicados.
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

.section-link {
  color: #1a3f8f;
  font-weight: 700;
  text-decoration: none;
}

.section-link:hover {
  text-decoration: underline;
}

.projects-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
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
  .projects-grid {
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