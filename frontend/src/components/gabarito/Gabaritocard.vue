<script setup>
import { computed } from 'vue'
import GabaritoDocumento from './GabaritoDocumento.vue'

const props = defineProps({
  label: { type: String, required: true },
  documentos: { type: Array, required: true },
  isAdmin: { type: Boolean, default: false },
})

const emit = defineEmits(['substituir', 'excluir', 'excluir-secao'])

const subgrupos = computed(() => {
  const mapa = new Map()
  for (const doc of props.documentos) {
    const chave = doc.tipo_prova || 'Sem tipo informado'
    if (!mapa.has(chave)) mapa.set(chave, [])
    mapa.get(chave).push(doc)
  }
  return [...mapa.entries()].map(([tipo_prova, docs]) => ({
    tipo_prova,
    documentos: [...docs].sort((a) => (a.tipo_documento === 'gabarito' ? -1 : 1)),
  }))
})

function excluirSecao(sub) {
  if (!confirm(`Excluir toda a seção "${sub.tipo_prova}" (${sub.documentos.length} documento(s))?`)) return
  emit('excluir-secao', sub.documentos.map((d) => d.id))
}
</script>

<template>
  <div class="gab-card">
    <p class="gab-card-label">{{ label }}</p>

    <template v-for="(sub, index) in subgrupos" :key="sub.tipo_prova">
      <div class="gab-card-divisor" v-if="index > 0" />

      <div class="gab-card-subtitle-row">
        <p class="gab-card-subtitle">
          <v-icon size="12" color="#5a6a85">mdi-file-document-multiple-outline</v-icon>
          {{ sub.tipo_prova }}
        </p>
        <button v-if="isAdmin" type="button" class="btn-excluir-secao" @click="excluirSecao(sub)">
          <v-icon size="12">mdi-trash-can-outline</v-icon>
          Excluir seção
        </button>
      </div>

      <GabaritoDocumento
        v-for="documento in sub.documentos"
        :key="documento.id"
        :documento="documento"
        :is-admin="isAdmin"
        @substituir="(...args) => $emit('substituir', ...args)"
        @excluir="(id) => $emit('excluir', id)"
      />
    </template>

    <p v-if="!documentos.length" class="gab-card-vazio">Nenhum documento cadastrado ainda.</p>
  </div>
</template>

<style scoped>
/* ... estilos existentes mantidos ... */
.gab-card-subtitle-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  margin-bottom: 4px;
}
.btn-excluir-secao {
  display: flex;
  align-items: center;
  gap: 4px;
  border: none;
  background: transparent;
  color: #f87171;
  font-size: 10px;
  font-weight: 600;
  cursor: pointer;
  padding: 2px 6px;
  border-radius: 6px;
  transition: background-color 0.15s ease, color 0.15s ease;
  white-space: nowrap;
}
.btn-excluir-secao:hover {
  background: #fef2f2;
  color: #dc2626;
}
</style>