<script setup>
import { AREAS_MAPA, CATEGORIAS } from '@/utils/mapaEscolaAreas'

const props = defineProps({
  selecionado: { type: String, default: null },
  categoriaAtiva: { type: String, default: null },
})

const emit = defineEmits(['selecionar'])

function corDe(area) {
  return CATEGORIAS[area.categoria]?.cor ?? '#8FA3BF'
}

function estaEsmaecida(area) {
  return props.categoriaAtiva !== null && area.categoria !== props.categoriaAtiva
}

function selecionar(area) {
  emit('selecionar', props.selecionado === area.id ? null : area.id)
}
</script>

<template>
  <div class="planta-wrapper">
    <p class="scroll-hint">arraste para o lado →</p>
    <div class="scroll-area">
      <div class="planta-grid">
        <button
          v-for="area in AREAS_MAPA"
          :key="area.id"
          type="button"
          class="celula"
          :class="[
            `celula-${area.id}`,
            {
              'celula-selecionada': selecionado === area.id,
              'celula-esmaecida': estaEsmaecida(area),
            },
          ]"
          :style="{ '--cor-area': corDe(area) }"
          @click="selecionar(area)"
        >
          <span class="celula-nome">{{ area.nome }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.planta-wrapper {
  font-family: 'DM Sans', sans-serif;
}

.scroll-hint {
  display: none;
  font-size: 11px;
  color: #5a6a85;
  margin: 0 0 6px;
}

.scroll-area {
  overflow-x: auto;
  border-radius: 16px;
}

.planta-grid {
  display: grid;
  grid-template-columns: repeat(5, minmax(96px, 1fr));
  grid-template-rows: repeat(6, minmax(56px, auto));
  gap: 4px;
  min-width: 620px;
  grid-template-areas:
    'audit verde  verde   verde  estac'
    'soe   verde  verde   verde  estac'
    'bibli entr   bl1     bl1    estac'
    'secre entr   bl2     enf    estac'
    'dire  esp    bl3     vest   pisc'
    'greme gina   quadras s4453  pisc';
}

.celula {
  border: none;
  border-radius: 10px;
  background: #eef3fb; /* fallback para navegadores sem suporte a color-mix() */
  background: color-mix(in srgb, var(--cor-area) 18%, white);
  border: 1.5px solid rgba(13, 31, 60, 0.15); /* fallback */
  border: 1.5px solid color-mix(in srgb, var(--cor-area) 55%, white);
  color: #0d1f3c;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 8px 6px;
  cursor: pointer;
  transition: transform 0.12s ease, box-shadow 0.12s ease, opacity 0.15s ease;
}

.celula-nome {
  font-size: 11px;
  font-weight: 700;
  line-height: 1.25;
  text-transform: uppercase;
  letter-spacing: 0.01em;
}

.celula:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 10px rgba(13, 31, 60, 0.12);
}

.celula-selecionada {
  background: var(--cor-area);
  border-color: var(--cor-area);
}
.celula-selecionada .celula-nome {
  color: #ffffff;
}

.celula-esmaecida {
  opacity: 0.25;
}

/* Mapeia cada id de área para sua posição no grid-template-areas acima. */
.celula-audit   { grid-area: audit; }
.celula-soe     { grid-area: soe; }
.celula-bibli   { grid-area: bibli; }
.celula-secre   { grid-area: secre; }
.celula-dire    { grid-area: dire; }
.celula-verde   { grid-area: verde; }
.celula-entr    { grid-area: entr; }
.celula-bl1     { grid-area: bl1; }
.celula-bl2     { grid-area: bl2; }
.celula-enf     { grid-area: enf; }
.celula-bl3     { grid-area: bl3; }
.celula-esp     { grid-area: esp; }
.celula-vest    { grid-area: vest; }
.celula-greme   { grid-area: greme; }
.celula-gina    { grid-area: gina; }
.celula-quadras { grid-area: quadras; }
.celula-s4453   { grid-area: s4453; }
.celula-pisc    { grid-area: pisc; }
.celula-estac   { grid-area: estac; }

@media (max-width: 640px) {
  .scroll-hint {
    display: block;
  }
}
</style>