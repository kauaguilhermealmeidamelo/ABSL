<script setup>
defineProps({
  icon: { type: String, required: true },
  nome: { type: String, required: true },
  total: { type: Number, required: true },
})

defineEmits(['selecionar'])
</script>

<template>
  <button type="button" class="dir-card" @click="$emit('selecionar')">
    <v-icon size="26" color="#1a3f8f" class="dir-icon">{{ icon }}</v-icon>
    <span class="dir-nome">{{ nome }}</span>
    <span class="dir-total">{{ total }} {{ total === 1 ? 'projeto' : 'projetos' }}</span>
  </button>
</template>

<style scoped>
.dir-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 6px;
  background: #ffffff;
  border: none;
  border-radius: 16px;
  padding: 28px 16px;
  cursor: pointer;
  box-shadow: 0 1px 3px rgba(13, 31, 60, 0.08);
  transition: box-shadow 0.15s ease, transform 0.15s ease;
  font-family: 'DM Sans', sans-serif;
  text-align: center;
}

.dir-card:hover {
  box-shadow: 0 8px 20px rgba(13, 31, 60, 0.12);
  transform: translateY(-2px);
}

.dir-icon {
  margin-bottom: 4px;
}

.dir-nome {
  color: #0d1f3c;
  font-weight: 700;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.02em;
}

.dir-total {
  color: #5a6a85;
  font-size: 12px;
}
</style>