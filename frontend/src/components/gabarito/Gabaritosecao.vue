<template>
  <section class="gab-section">
    <div class="gab-section-heading">
      <span class="gab-section-title">{{ title }}</span>
      <div class="gab-section-divider" />
    </div>

    <div class="gab-section-grid">
      <GabaritoCard v-for="grupo in groups" :key="grupo" :label="grupo" :is-admin="isAdmin" />
    </div>
  </section>
</template>

<script setup>
import GabaritoCard from './Gabaritocard.vue'

defineProps({
  title: { type: String, required: true },
  groups: { type: Array, required: true },
  isAdmin: { type: Boolean, default: false },
})
</script>

<style scoped>
.gab-section {
  margin-bottom: 32px;
}

.gab-section:last-child {
  margin-bottom: 0;
}

.gab-section-heading {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.gab-section-title {
  font-family: 'DM Mono', monospace;
  font-size: 12px;
  font-weight: 700;
  color: #1a3f8f;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  white-space: nowrap;
}

.gab-section-divider {
  height: 1px;
  flex: 1;
  background: rgba(13, 31, 60, 0.1);
}

.gab-section-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 16px;
}

@media (min-width: 640px) {
  .gab-section-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>