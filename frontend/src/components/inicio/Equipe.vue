<template>
  <section class="team">
    <h2>Equipe</h2>
    <div class="team-grid">
      <article class="team-card" v-for="dir in team" :key="dir.name">
        <header>
          <v-icon class="dept-icon" color="#f4c430">{{ dir.icon }}</v-icon>
          <span class="dept-name">{{ dir.name }}</span>
        </header>
        <ul>
          <li v-for="m in dir.members" :key="m.cargo">
            <span class="cargo">{{ m.cargo }}:</span> {{ m.nome }}
          </li>
        </ul>
      </article>
    </div>
  </section>
</template>

<script setup>
const team = [
  { icon: 'mdi-crown', name: 'Presidência', members: [{ cargo: 'Presidente', nome: 'Samuel' }, { cargo: 'Vice-Presidente', nome: 'Bárbara' }, { cargo: '1º Vice-Presidente', nome: 'Jósue' }] },
  { icon: 'mdi-clipboard-text-outline', name: 'Secretaria', members: [{ cargo: 'Secretário-Geral', nome: 'Marcus Paulo' }, { cargo: '1ª Secretária', nome: 'Giovanna' }, { cargo: '2ª Secretária', nome: 'Brenda' }] },
  { icon: 'mdi-cash', name: 'Tesouraria', members: [{ cargo: 'Tesoureira-Geral', nome: 'Andressa' }, { cargo: '1º Tesoureiro', nome: 'Rafael' }, { cargo: '2º Tesoureiro', nome: 'Vitor' }] },
  { icon: 'mdi-run-fast', name: 'Esporte e Lazer', members: [{ cargo: 'Diretor-Geral', nome: 'Igor' }, { cargo: '1ª Diretora', nome: 'Rafaela' }, { cargo: '2º Diretor', nome: 'Artur Araripe' }] },
  { icon: 'mdi-theater', name: 'Cultura', members: [{ cargo: 'Diretora-Geral', nome: 'Jennyfer' }, { cargo: '1ª Diretora', nome: 'Ludmila' }, { cargo: '2º Diretor', nome: 'Thiago' }] },
  { icon: 'mdi-school-outline', name: 'Políticas Educacionais', members: [{ cargo: 'Diretor-Geral', nome: 'Enzo' }, { cargo: '1ª Diretora', nome: 'Lorena' }, { cargo: '2ª Diretora', nome: 'Larissa' }] },
  { icon: 'mdi-leaf', name: 'Saúde e Meio Ambiente', members: [{ cargo: 'Diretora-Geral', nome: 'Ayla' }, { cargo: '1ª Diretora', nome: 'Maryane' }, { cargo: '2ª Diretora', nome: 'Joana' }] },
  { icon: 'mdi-handshake-outline', name: 'Diretoria Social', members: [{ cargo: 'Diretor-Geral', nome: 'David' }, { cargo: '1ª Diretora', nome: 'Mariana' }, { cargo: '2º Diretor', nome: 'Matheus' }] },
  { icon: 'mdi-bullhorn-variant-outline', name: 'Imprensa e Comunicação', members: [{ cargo: 'Diretora-Geral', nome: 'Yara' }, { cargo: '1ª Diretora', nome: 'Giulia' }, { cargo: '2ª Diretora', nome: 'Ana Júlia' }] },
  { icon: 'mdi-laptop', name: 'Tecnologia e Inovação', members: [{ cargo: 'Diretor-Geral', nome: 'Kauan Guilherme' }, { cargo: '1º Diretor', nome: 'Pedro Lucas' }, { cargo: '2º Diretor', nome: 'Maria Eduarda' }] },
]
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700;1,900&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400;1,9..40,700&display=swap');

.team {
  font-family: 'DM Sans', sans-serif;
}
.team h2 {
  font-family: 'Playfair Display', serif;
  font-size: 24px;
  font-weight: 700;
  color: #0d1f3c;
  margin-bottom: 16px;
}

.team-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 16px;
}

.team-card {
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 12px;
  padding: 20px;
}
.team-card header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
}
.dept-icon {
  font-size: 20px;
}
.dept-name {
  color: #1a3f8f;
  font-weight: 700;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}
.team-card ul {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.team-card li {
  font-size: 13px;
  color: #5a6a85;
}
.cargo {
  color: #0d1f3c;
  font-weight: 500;
}

</style>  