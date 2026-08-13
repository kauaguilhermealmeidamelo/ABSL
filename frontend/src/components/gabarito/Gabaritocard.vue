<script setup>
import { computed } from 'vue'
import GabaritoDocumento from './GabaritoDocumento.vue'

const props = defineProps({
  label: { type: String, required: true }, // grupo_turma
  documentos: { type: Array, required: true },
  isAdmin: { type: Boolean, default: false },
})

defineEmits(['substituir'])

// Dentro de um grupo de turmas, os documentos ainda se separam por
// tipo_prova (Provão, 2ª Chamada, etc — texto livre, então agrupamos
// dinamicamente em vez de assumir rótulos fixos).
const subgrupos = computed(() => {
  const mapa = new Map()
  for (const doc of props.documentos) {
    const chave = doc.tipo_prova || 'Sem tipo informado'
    if (!mapa.has(chave)) mapa.set(chave, [])
    mapa.get(chave).push(doc)
  }
  return [...mapa.entries()].map(([tipo_prova, docs]) => ({
    tipo_prova,
    // gabarito sempre listado antes da prova, dentro do mesmo subgrupo
    documentos: [...docs].sort((a) => (a.tipo_documento === 'gabarito' ? -1 : 1)),
  }))
})
</script>

<template>
  <div class="gab-card">
    <p class="gab-card-label">{{ label }}</p>

    <template v-for="(sub, index) in subgrupos" :key="sub.tipo_prova">
      <div class="gab-card-divisor" v-if="index > 0" />

      <p class="gab-card-subtitle">
        <v-icon size="12" color="#5a6a85">mdi-file-document-multiple-outline</v-icon>
        {{ sub.tipo_prova }}
      </p>

      <GabaritoDocumento
        v-for="documento in sub.documentos"
        :key="documento.id"
        :documento="documento"
        :is-admin="isAdmin"
        @substituir="(...args) => $emit('substituir', ...args)"
      />
    </template>

    <p v-if="!documentos.length" class="gab-card-vazio">Nenhum documento cadastrado ainda.</p>
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
  display: flex;
  align-items: center;
  gap: 4px;
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

.gab-card-vazio {
  color: #94a3b8;
  font-size: 12px;
  margin: 4px 0 0;
}
</style>