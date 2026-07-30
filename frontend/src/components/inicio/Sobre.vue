<template>
  <section class="about">
    <h2>Grêmio Athos Bulcão</h2>
    <p>
      O Grêmio Estudantil Athos Bulcão — ABSL — é a entidade representativa dos
      estudantes do Colégio Athos Bulcão, em Brasília. Eleito democraticamente, o
      grêmio atua como canal oficial entre o corpo discente e a direção da escola,
      levando demandas, propondo projetos e garantindo que a voz de cada aluno seja
      ouvida. Qualquer estudante pode propor pauta, participar de reuniões e
      contribuir com a gestão.
    </p>

    <div class="pillars">
      <article class="pillar" v-for="p in pillars" :key="p.title">
        <span class="pillar-dash">—</span>
        <h3>{{ p.title }}</h3>
        <p>{{ p.text }}</p>
      </article>
    </div>
  </section>
</template>

<script setup>
const pillars = [
  {
    title: 'Representar',
    text: 'Levamos as demandas dos estudantes à direção e acompanhamos as respostas até o fim.',
  },
  {
    title: 'Cuidar',
    text: 'Campanhas solidárias, acolhimento e projetos de bem-estar dentro da escola.',
  },
  {
    title: 'Movimentar',
    text: 'Eventos, torneios, feiras culturais e tudo que faz a escola ter vida.',
  },
]
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,600;0,700;1,600&family=Inter:wght@400;500;600;700&display=swap');

.about {
  margin-top: 56px;
  font-family: 'Inter', system-ui, sans-serif;
  color: #1c2333;
}
.about h2 {
  font-family: 'Fraunces', serif;
  font-size: 26px;
  font-weight: 700;
  margin-bottom: 14px;
}
.about p {
  color: #4c5470;
  font-size: 14.5px;
  line-height: 1.7;
  max-width: 780px;
  margin-bottom: 34px;
}

.pillars {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}
.pillar {
  background: #f7f8fc;
  border-radius: 14px;
  padding: 22px 20px;
}
.pillar-dash {
  color: #9aa2bd;
  font-weight: 700;
}
.pillar h3 {
  font-size: 16px;
  margin: 6px 0 8px;
}
.pillar p {
  font-size: 13.5px;
  color: #6b7290;
  line-height: 1.55;
  margin: 0;
}

@media (max-width: 720px) {
  .pillars {
    grid-template-columns: 1fr;
  }
}
</style>