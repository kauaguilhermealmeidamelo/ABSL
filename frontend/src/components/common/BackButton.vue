<script setup>
import { useRouter } from 'vue-router'

const props = defineProps({
  to: { type: String, default: '' },
  label: { type: String, default: 'Voltar' },
})
const router = useRouter()
function voltar() {
  if (props.to) router.push(props.to)
  else router.back()
}
</script>

<template>
  <button type="button" class="back-button" @click="voltar">
    <v-icon size="18" aria-hidden="true">mdi-arrow-left</v-icon>
    <span>{{ label }}</span>
  </button>
</template>

<style scoped>
.back-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 10px 16px;
  margin: 12px 0 24px;
  border: 1px solid #cbd7ea;
  border-radius: 10px;
  background: #fff;
  color: #16509b;
  font: inherit;
  font-size: 14px;
  font-weight: 600;
  line-height: 1.3;
  text-decoration: none;
  cursor: pointer;
  transition: background-color .15s, border-color .15s;
}
.back-button:hover { background: #eef3fb; border-color: #16509b; }
.back-button:focus-visible { outline: 3px solid #7299d7; outline-offset: 2px; }
</style>
