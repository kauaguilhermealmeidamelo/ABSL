<script setup>
import { team } from '@/stores/appData'
</script>

<template>
  <section id="equipe" class="team">
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