<template>
  <div class="page-header">
    <span class="page-header-eyebrow">{{ label }}</span>
    <h1 class="page-header-title">{{ title }}</h1>
    <p v-if="subtitle" class="page-header-subtitle">{{ subtitle }}</p>
  </div>
</template>

<script setup>
defineProps({
  label: { type: String, required: true },
  title: { type: String, required: true },
  subtitle: { type: String, default: '' },
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700;1,900&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400;1,9..40,700&display=swap');

.page-header {
  margin-bottom: 32px;
  font-family: 'DM Sans', sans-serif;
}

.page-header-eyebrow {
  font-family: 'DM Mono', monospace;
  font-size: 12px;
  font-weight: 500;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #1a3f8f;
}

.page-header-title {
  font-family: 'Playfair Display', serif;
  font-size: 36px;
  font-weight: 700;
  color: #0d1f3c;
  margin: 4px 0 8px;
  line-height: 1.15;
}

.page-header-subtitle {
  color: #5a6a85;
  font-size: 16px;
  margin: 0;
}

@media (max-width: 480px) {
  .page-header-title {
    font-size: 28px;
  }
}
</style>