<script setup>
defineProps({
  label: { type: String, required: true },
  icon: { type: String, default: 'mdi-plus' },
  block: { type: Boolean, default: false },
})
defineEmits(['click'])
</script>

<template>
  <button type="button" class="novo-item-btn" :class="{ 'novo-item-btn--block': block }" @click="$emit('click')">
    <v-icon size="18">{{ icon }}</v-icon>
    <span>{{ label }}</span>
  </button>
</template>

<style scoped>
.novo-item-btn{flex:none;display:inline-flex;align-items:center;justify-content:center;gap:6px;background:#1a3f8f;color:#fff;border:none;padding:9px 18px;border-radius:999px;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s}
.novo-item-btn:hover{background:#0d1f3c}
.novo-item-btn--block{width:100%}
@media (max-width:560px){.novo-item-btn{width:100%}}
</style>
