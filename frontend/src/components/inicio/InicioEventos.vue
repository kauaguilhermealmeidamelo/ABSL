<script setup>
import { computed, onMounted } from 'vue'
import EventoCard from '@/components/eventos/EventoCard.vue'
import { useEventos } from '@/composables/useEventos'

const { eventos, loading, error, fetchEventos } = useEventos()

// Só eventos de hoje em diante (a lista da API já vem em ordem de data).
const proximos = computed(() => {
  const inicioDoDia = new Date()
  inicioDoDia.setHours(0, 0, 0, 0)
  return eventos.value
    .filter((e) => new Date(e.data_evento) >= inicioDoDia)
    .sort((a, b) => new Date(a.data_evento) - new Date(b.data_evento))
    .slice(0, 3)
})

onMounted(() => fetchEventos(true))
</script>

<template>
  <section class="home-section" aria-labelledby="eventos-titulo">
    <div class="section-heading">
      <div class="section-heading__copy">
        <p class="section-eyebrow">Agenda</p>
        <h2 id="eventos-titulo">Próximos eventos</h2>
      </div>
      <router-link class="section-link" to="/eventos">
        Ver todos os eventos
        <v-icon size="16" aria-hidden="true">mdi-arrow-right</v-icon>
      </router-link>
    </div>

    <p v-if="loading" class="status-msg" role="status">Carregando eventos...</p>

    <p v-else-if="error" class="status-msg status-error" role="alert">
      Não foi possível carregar este conteúdo.
    </p>

    <p v-else-if="!proximos.length" class="empty-state">
      Nenhum evento programado por enquanto.
    </p>

    <div v-else class="events-grid">
      <EventoCard v-for="evento in proximos" :key="evento.id" :evento="evento" />
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

.events-grid {
  display: grid;
  grid-template-columns: minmax(0, 1fr);
  align-items: start;
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
  .events-grid {
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
