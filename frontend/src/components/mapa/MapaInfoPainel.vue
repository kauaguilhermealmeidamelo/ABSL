<script setup>
import { computed } from 'vue'
import { CATEGORIAS, getArea } from '@/utils/mapaEscolaAreas'

const props = defineProps({
  selecionado: { type: String, default: null },
})

const area = computed(() => (props.selecionado ? getArea(props.selecionado) : null))
const categoria = computed(() => (area.value ? CATEGORIAS[area.value.categoria] : null))
</script>

<template>
  <div class="info-painel" :class="{ 'info-painel-vazio': !area }">
    <template v-if="area">
      <div class="info-header">
        <span class="info-dot" :style="{ backgroundColor: categoria?.cor }" />
        <h3 class="info-nome">{{ area.nome }}</h3>
      </div>
      <span class="info-categoria">{{ categoria?.label }}</span>
      <p class="info-descricao">{{ area.descricao }}</p>
    </template>

    <template v-else>
      <v-icon size="22" color="#8FA3BF">mdi-hand-pointing-up</v-icon>
      <p class="info-vazio-texto">Toque em um ambiente do mapa para ver mais detalhes.</p>
    </template>
  </div>
</template>

<style scoped>
.info-painel {
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 16px;
  padding: 20px;
  margin-top: 16px;
  font-family: 'DM Sans', sans-serif;
  min-height: 88px;
}

.info-painel-vazio {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  gap: 8px;
  color: #8fa3bf;
}

.info-vazio-texto {
  font-size: 13px;
  margin: 0;
  max-width: 260px;
}

.info-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 4px;
}

.info-dot {
  width: 10px;
  height: 10px;
  border-radius: 999px;
  flex-shrink: 0;
}

.info-nome {
  font-family: 'Playfair Display', serif;
  font-weight: 700;
  font-size: 18px;
  color: #0d1f3c;
  margin: 0;
}

.info-categoria {
  display: inline-block;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: #5a6a85;
  margin-bottom: 8px;
}

.info-descricao {
  font-size: 13.5px;
  color: #3d4a5c;
  line-height: 1.6;
  margin: 0;
}
</style>