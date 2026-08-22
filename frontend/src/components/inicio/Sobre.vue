<template>
  <section class="about">
    <h2>Grêmio Athos Bulcão</h2>
    <p>
      O Grêmio Estudantil Athos Bulcão é a entidade de representação dos estudantes do CEM Setor Leste. Nosso objetivo é
      ouvir os alunos, defender seus interesses e promover ações que contribuam para uma escola mais participativa,
      democrática e acolhedora.</p>

      <p>Em 2026, após o processo eleitoral, a Chapa Marielle Franco foi eleita pelos estudantes para assumir a gestão do
      Grêmio. Após as eleições, diferentes grupos que participaram do processo decidiram unir forças e construir uma
      gestão conjunta, colocando as diferenças de lado em busca de um objetivo comum: representar e trabalhar pelos
      estudantes do Setor Leste.</p>

      <p>Hoje, o Grêmio Athos Bulcão reúne estudantes com diferentes ideias, experiências e perspectivas, trabalhando
      juntos na realização de projetos, eventos, ações culturais, esportivas e sociais, além da representação dos alunos
      junto à escola.</p>

      <p>Uma gestão feita por estudantes, para estudantes</p>
  </section>

  <section class="pillars">
    <div class="pillar" v-for="p in pillars" :key="p.title">
      <div class="pillar-bar" />
      <h3>{{ p.title }}</h3>
      <p>{{ p.desc }}</p>
    </div>
  </section>
</template>

<script setup>
const pillars = [
  { title: 'Representar', desc: 'Levamos as demandas dos estudantes à direção e acompanhamos as respostas até o fim.' },
  { title: 'Cuidar', desc: 'Campanhas solidárias, acolhimento e projetos de bem-estar dentro da escola.' },
  { title: 'Movimentar', desc: 'Eventos, torneios, feiras culturais e tudo que faz a escola ter vida.' },
]
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700;1,900&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400;1,9..40,700&display=swap');

.about {
  margin-bottom: 40px;
  font-family: 'DM Sans', sans-serif;
}

.about h2 {
  font-family: 'Playfair Display', serif;
  font-size: 24px;
  font-weight: 700;
  color: #0d1f3c;
  margin-bottom: 12px;
}

.about p {
  color: #5a6a85;
  line-height: 1.7;
  max-width: 720px;
  margin: 0;
}

.pillars {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-bottom: 40px;
  font-family: 'DM Sans', sans-serif;
}

.pillar {
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 12px;
  padding: 24px;
  transition: box-shadow 0.15s ease;
}

.pillar:hover {
  box-shadow: 0 4px 10px rgba(13, 31, 60, 0.08);
}

.pillar-bar {
  width: 24px;
  height: 2px;
  background: #f5c518;
  border-radius: 4px;
  margin-bottom: 12px;
}

.pillar h3 {
  color: #0d1f3c;
  font-weight: 700;
  font-size: 16px;
  margin: 0 0 8px;
}

.pillar p {
  color: #5a6a85;
  font-size: 14px;
  line-height: 1.6;
  margin: 0;
}

@media (max-width: 480px) {
  .pillars {
    grid-template-columns: 1fr;
  }
}
</style>