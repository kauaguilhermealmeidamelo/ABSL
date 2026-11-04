<template>
  <div class="page">
    <Banner />

    <Sobre />

    <InicioNoticias />
    <InicioProjetos />

    <InicioEventos />

    <section class="institutional-grid" aria-label="Informações institucionais">
      <article class="institutional-card">
        <p class="section-eyebrow">Comunidade</p>
        <h2>Parcerias</h2>
        <p>
          Construímos iniciativas com a comunidade e organizações que apoiam projetos estudantis.
        </p>
        <p class="muted">
          A área pública de parcerias será disponibilizada quando houver dados reais e uma rota correspondente no sistema.
        </p>
      </article>

      <article class="institutional-card">
        <p class="section-eyebrow">Prestação de informações</p>
        <h2>Transparência</h2>
        <p>
          Consulte as informações institucionais e os documentos públicos já disponibilizados pelo Grêmio.
        </p>
        <router-link class="institutional-link" to="/transparencia">
          Portal de Transparência <span aria-hidden="true">→</span>
        </router-link>
      </article>
    </section>

    <Equipe />
  </div>
</template>

<script setup>
import Banner from '@/components/inicio/Banner.vue'
import Sobre from '@/components/inicio/Sobre.vue'
import Equipe from '@/components/inicio/Equipe.vue'
import InicioNoticias from '@/components/inicio/InicioNoticias.vue'
import InicioProjetos from '@/components/inicio/InicioProjetos.vue'
import InicioEventos from '@/components/inicio/InicioEventos.vue'
</script>

<style scoped>
.page {
  padding: 32px 40px 64px;
  max-width: 1180px;
  margin: 0 auto;
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

.institutional-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
  margin-bottom: 48px;
}

.institutional-card {
  padding: 28px;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 16px;
  background: #ffffff;
}

.institutional-card p:not(.section-eyebrow):not(.muted) {
  color: #3d4a5c;
  line-height: 1.7;
  margin: 12px 0;
}

.muted {
  color: #6b7c93;
  font-size: 13px;
  line-height: 1.6;
}

.institutional-link {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin-top: 4px;
  color: #1a3f8f;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
}

.institutional-link:hover {
  text-decoration: underline;
}

@media (min-width: 760px) {
  .institutional-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 720px) {
  .page {
    padding: 20px;
  }
}

@media (max-width: 520px) {
  h2 {
    font-size: 26px;
  }

  .institutional-card {
    padding: 22px;
  }
}
</style>