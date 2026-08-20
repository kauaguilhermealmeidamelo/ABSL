<script setup>
const stats = [
  { label: 'Turmas', valor: '24' },
  { label: 'Alunos', valor: '~1.500' },
  { label: 'Professores', valor: '~90' },
  { label: 'Turnos', valor: '2' },
]
</script>

<template>
  <div class="stats-grid">
    <div v-for="s in stats" :key="s.label" class="stat-card">
      <span class="stat-valor">{{ s.valor }}</span>
      <span class="stat-label">{{ s.label }}</span>
    </div>
  </div>
</template>

<style scoped>
.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin-bottom: 24px;
  font-family: 'DM Sans', sans-serif;
}

@media (min-width: 640px) {
  .stats-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.stat-card {
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 16px;
  padding: 18px 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.stat-valor {
  font-family: 'Playfair Display', serif;
  font-weight: 700;
  font-size: 28px;
  color: #0d1f3c;
  line-height: 1.1;
}

.stat-label {
  margin-top: 4px;
  font-size: 12px;
  color: #5a6a85;
}

@media (max-width: 380px) {
  .stat-valor {
    font-size: 22px;
  }
}
</style>