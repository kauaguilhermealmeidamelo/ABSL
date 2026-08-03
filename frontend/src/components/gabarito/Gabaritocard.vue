<template>
  <div class="gab-card">
    <p class="gab-card-label">Turmas {{ label }}</p>
    <GabaritoDocumento :name="`Gabarito Provão – ${label}.pdf`" type="gabarito" :is-admin="isAdmin" />
    <GabaritoDocumento :name="`Prova Provão – ${label}.pdf`" type="prova" :is-admin="isAdmin" />
  </div>
</template>

<script setup>
import GabaritoDocumento from './GabaritoDocumento.vue'

defineProps({
  label: { type: String, required: true },
  isAdmin: { type: Boolean, default: false },
})
</script>

<style scoped>
.gab-card {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid rgba(13, 31, 60, 0.08);
  padding: 16px;
  transition: box-shadow 0.15s ease;
}

.gab-card:hover {
  box-shadow: 0 4px 10px rgba(13, 31, 60, 0.1);
}

.gab-card-label {
  font-family: 'DM Mono', monospace;
  font-size: 12px;
  font-weight: 700;
  color: #1a3f8f;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin: 0 0 12px;
}
</style>