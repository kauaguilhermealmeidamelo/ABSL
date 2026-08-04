<script setup>
import GabaritoDocumento from './GabaritoDocumento.vue'

defineProps({
  label: { type: String, required: true },
  isAdmin: { type: Boolean, default: false },
})
</script>

<template>
  <div class="gab-card">
    <p class="gab-card-label">Turmas {{ label }}</p>

    <p class="gab-card-subtitle">📝 Provão</p>
    <GabaritoDocumento :name="`Gabarito Provão – ${label}.pdf`" type="gabarito" :is-admin="isAdmin" />
    <GabaritoDocumento :name="`Prova Provão – ${label}.pdf`" type="prova" :is-admin="isAdmin" />

    <div class="gab-card-divisor" />

    <p class="gab-card-subtitle">📋 2ª Chamada</p>
    <GabaritoDocumento :name="`Gabarito 2ª Chamada – ${label}.pdf`" type="gabarito" :is-admin="isAdmin" />
    <GabaritoDocumento :name="`Prova 2ª Chamada – ${label}.pdf`" type="prova" :is-admin="isAdmin" />
  </div>
</template>

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

.gab-card-subtitle {
  font-size: 10px;
  font-weight: 600;
  color: #5a6a85;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin: 0 0 4px;
}

.gab-card-divisor {
  margin: 8px 0;
  border-top: 1px dashed rgba(13, 31, 60, 0.1);
}
</style>
