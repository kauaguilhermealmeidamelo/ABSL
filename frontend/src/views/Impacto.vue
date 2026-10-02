<script setup>
import PageHeader from '@/components/common/PageHeader.vue'

const indicadores = [
  { titulo: 'Projetos', descricao: 'Projetos cadastrados no portfólio do Grêmio.', icone: 'mdi-folder-multiple-outline' },
  { titulo: 'Eventos', descricao: 'Eventos e atividades que vierem a ser registrados.', icone: 'mdi-calendar-outline' },
  { titulo: 'Ações', descricao: 'Ações realizadas e registradas pelo Grêmio.', icone: 'mdi-hand-heart-outline' },
  { titulo: 'Participantes', descricao: 'Participações contabilizadas quando houver dado disponível.', icone: 'mdi-account-group-outline' },
  { titulo: 'Parcerias', descricao: 'Parcerias institucionais registradas no sistema.', icone: 'mdi-handshake-outline' },
]
</script>

<template>
  <div class="impacto-page">
    <PageHeader
      label="ABSL"
      title="Impacto"
      subtitle="Resultados reais das ações e projetos do Grêmio Athos Bulcão."
    />

    <section class="intro" aria-labelledby="impacto-intro-title">
      <div class="intro-icon">
        <v-icon size="28">mdi-chart-line</v-icon>
      </div>
      <div>
        <h2 id="impacto-intro-title">Dados que podem ser acompanhados</h2>
        <p>
          Esta área será alimentada conforme o sistema passar a registrar os indicadores das ações do Grêmio.
          Nenhum número é exibido sem uma fonte de dados real.
        </p>
      </div>
    </section>

    <section class="indicadores" aria-labelledby="indicadores-title">
      <div class="section-heading">
        <span class="kicker">Indicadores</span>
        <h2 id="indicadores-title">Resultados registrados</h2>
      </div>

      <div class="indicadores-grid">
        <article v-for="indicador in indicadores" :key="indicador.titulo" class="indicador-card">
          <div class="indicador-icon">
            <v-icon size="22">{{ indicador.icone }}</v-icon>
          </div>
          <h3>{{ indicador.titulo }}</h3>
          <p>{{ indicador.descricao }}</p>
          <span class="indisponivel">Ainda não informado</span>
        </article>
      </div>
    </section>
  </div>
</template>

<style scoped>
.impacto-page {
  font-family: 'DM Sans', sans-serif;
  padding: 24px;
}

.intro {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  padding: 20px;
  margin-bottom: 32px;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 16px;
  background: #f8faff;
}

.intro-icon,
.indicador-icon {
  display: grid;
  place-items: center;
  flex: 0 0 44px;
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: #eef3fb;
  color: #1a3f8f;
}

.intro h2,
.section-heading h2 {
  margin: 0;
  color: #0d1f3c;
}

.intro h2 {
  font-size: 20px;
}

.intro p {
  margin: 6px 0 0;
  max-width: 760px;
  color: #5a6a85;
  font-size: 14px;
  line-height: 1.6;
}

.section-heading {
  margin-bottom: 16px;
}

.kicker {
  color: #1a3f8f;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.indicadores-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 16px;
}

.indicador-card {
  min-width: 0;
  padding: 20px;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 16px;
  background: #ffffff;
}

.indicador-card h3 {
  margin: 16px 0 6px;
  color: #0d1f3c;
  font-size: 16px;
}

.indicador-card p {
  margin: 0 0 16px;
  color: #5a6a85;
  font-size: 13px;
  line-height: 1.55;
}

.indisponivel {
  display: inline-block;
  color: #5a6a85;
  background: #f4f6fa;
  border-radius: 999px;
  padding: 5px 10px;
  font-size: 11px;
  font-weight: 600;
}

@media (max-width: 800px) {
  .indicadores-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 480px) {
  .impacto-page {
    padding: 16px;
  }

  .intro {
    padding: 16px;
  }

  .indicadores-grid {
    grid-template-columns: 1fr;
  }
}
</style>
