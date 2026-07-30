<template>
  <section class="team">
    <h2>Equipe</h2>
    <div class="team-grid">
      <article class="team-card" v-for="dept in departments" :key="dept.name">
        <header>
          <span class="dept-icon">{{ dept.icon }}</span>
          <h4>{{ dept.name }}</h4>
        </header>
        <ul>
          <li v-for="m in dept.members" :key="m.role">
            <span class="role">{{ m.role }}:</span> {{ m.name }}
          </li>
        </ul>
      </article>
    </div>
  </section>
</template>

<script setup>
const departments = [
  {
    name: 'PRESIDÊNCIA',
    icon: '👑',
    members: [
      { role: 'Presidente', name: 'Samuel' },
      { role: 'Vice-Presidente', name: 'Bárbara' },
      { role: '1º Vice-Presidente', name: 'Josué' },
    ],
  },
  {
    name: 'SECRETARIA',
    icon: '📋',
    members: [
      { role: 'Secretário-Geral', name: 'Marcos Paulo' },
      { role: '1ª Secretária', name: 'Giovanna' },
      { role: '2ª Secretária', name: 'Brenda' },
    ],
  },
  {
    name: 'TESOURARIA',
    icon: '🔥',
    members: [
      { role: 'Tesoureira-Geral', name: 'Andressa' },
      { role: '1º Tesoureiro', name: 'Rafael' },
      { role: '2º Tesoureiro', name: 'Vitor' },
    ],
  },
  {
    name: 'ESPORTE E LAZER',
    icon: '⚽',
    members: [
      { role: 'Diretor-Geral', name: 'Igor' },
      { role: '1ª Diretora', name: 'Rafaela' },
      { role: '2º Diretor', name: 'Arthur Anaripe' },
    ],
  },
  {
    name: 'CULTURA',
    icon: '🎭',
    members: [
      { role: 'Diretora-Geral', name: 'Jannyfer' },
      { role: '1ª Diretora', name: 'Ludmila' },
      { role: '2º Diretor', name: 'Thiago' },
    ],
  },
  {
    name: 'POLÍTICAS EDUCACIONAIS',
    icon: '📚',
    members: [
      { role: 'Diretor-Geral', name: 'Enzo' },
      { role: '1ª Diretora', name: 'Lorena' },
      { role: '2ª Diretora', name: 'Larissa' },
    ],
  },
  {
    name: 'SAÚDE E MEIO AMBIENTE',
    icon: '🌱',
    members: [
      { role: 'Diretora-Geral', name: 'Ayla' },
      { role: '1ª Diretora', name: 'Maryane' },
      { role: '2ª Diretora', name: 'Joana' },
    ],
  },
  {
    name: 'DIRETORIA SOCIAL',
    icon: '🤝',
    members: [
      { role: 'Diretor-Geral', name: 'David' },
      { role: '1ª Diretora', name: 'Mariana' },
      { role: '2º Diretor', name: 'Matheus' },
    ],
  },
  {
    name: 'IMPRENSA E COMUNICAÇÃO',
    icon: '📣',
    members: [
      { role: 'Diretora-Geral', name: 'Yana' },
      { role: '1ª Diretora', name: 'Giulia' },
      { role: '2ª Diretora', name: 'Ana Julia' },
    ],
  },
  {
    name: 'TECNOLOGIA E INOVAÇÃO',
    icon: '💻',
    members: [
      { role: 'Diretor-Geral', name: 'Kauan Guilherme' },
      { role: '1º Diretor', name: 'Pedro Lucas' },
      { role: '2ª Diretora', name: 'Maria Eduarda' },
    ],
  },
]
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,600;0,700;1,600&family=Inter:wght@400;500;600;700&display=swap');

.team {
  margin-top: 56px;
  font-family: 'Inter', system-ui, sans-serif;
  color: #1c2333;
}
.team h2 {
  font-family: 'Fraunces', serif;
  font-size: 26px;
  font-weight: 700;
  margin-bottom: 14px;
}
.team-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 18px;
}
.team-card {
  background: #ffffff;
  border: 1px solid #ececf3;
  border-radius: 14px;
  padding: 18px 22px;
  box-shadow: 0 1px 3px rgba(20, 25, 50, 0.04);
}
.team-card header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 10px;
}
.dept-icon {
  font-size: 16px;
}
.team-card h4 {
  font-size: 12.5px;
  letter-spacing: 0.04em;
  font-weight: 700;
  color: #1c2f63;
  margin: 0;
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
  color: #4c5470;
}
.role {
  color: #8a90a8;
}

@media (max-width: 720px) {
  .team-grid {
    grid-template-columns: 1fr;
  }
}
</style>