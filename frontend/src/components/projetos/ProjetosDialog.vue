<script setup>
import { computed } from 'vue'
import ProjetoCard from './ProjetoCard.vue'
import { catEmoji } from '@/utils/projetoCategorias'

const props = defineProps({
  categoria: { type: String, required: true }, // '__geral__' | nome da categoria
  projetos: { type: Array, required: true },
  isAdmin: { type: Boolean, default: false },
})

const emit = defineEmits(['fechar', 'novo', 'editar', 'excluir', 'abrir-detalhe'])

const isGeral = computed(() => props.categoria === '__geral__')
const emoji = computed(() => (isGeral.value ? '🏛️' : catEmoji(props.categoria)))
const titulo = computed(() => (isGeral.value ? 'Todos os Projetos' : props.categoria))
</script>

<template>
  <div class="overlay" @click.self="$emit('fechar')">
    <div class="dialog">
      <header class="dialog-header">
        <span class="dialog-emoji">{{ emoji }}</span>
        <div class="dialog-titulo-wrap">
          <h2 class="dialog-titulo">{{ titulo }}</h2>
          <p class="dialog-contagem">{{ projetos.length }} projeto{{ projetos.length !== 1 ? 's' : '' }}</p>
        </div>
        <button v-if="isAdmin" type="button" class="btn-novo" @click="$emit('novo')">
          <v-icon size="14">mdi-plus</v-icon>
          Novo
        </button>
        <button type="button" class="btn-fechar" @click="$emit('fechar')">
          <v-icon size="18">mdi-close</v-icon>
        </button>
      </header>

      <div class="dialog-body">
        <div v-if="projetos.length === 0" class="vazio">
          <v-icon size="32" color="#5A6A85" style="opacity: 0.3">mdi-folder-multiple-outline</v-icon>
          <p>Nenhum projeto cadastrado para esta diretoria.</p>
          <button v-if="isAdmin" type="button" class="btn-adicionar" @click="$emit('novo')">
            <v-icon size="13">mdi-plus</v-icon>
            Adicionar projeto
          </button>
        </div>

        <div v-else class="projetos-grid">
          <ProjetoCard
            v-for="projeto in projetos"
            :key="projeto.id"
            :projeto="projeto"
            :is-admin="isAdmin"
            @abrir="$emit('abrir-detalhe', $event)"
            @editar="$emit('editar', $event)"
            @excluir="$emit('excluir', $event)"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.overlay {
  position: fixed;
  inset: 0;
  z-index: 50;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.4);
  padding: 16px;
}

.dialog {
  background: #eef3fb;
  border-radius: 16px;
  box-shadow: 0 25px 50px rgba(4, 20, 40, 0.3);
  width: 100%;
  max-width: 680px;
  max-height: 85vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  font-family: 'DM Sans', sans-serif;
}

.dialog-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 24px;
  background: #ffffff;
  border-bottom: 1px solid rgba(13, 31, 60, 0.08);
  flex-shrink: 0;
}

.dialog-emoji {
  font-size: 24px;
  flex-shrink: 0;
}

.dialog-titulo-wrap {
  flex: 1;
  min-width: 0;
}

.dialog-titulo {
  font-family: 'Playfair Display', serif;
  font-weight: 700;
  font-size: 17px;
  color: #0d1f3c;
  margin: 0;
  line-height: 1.2;
}

.dialog-contagem {
  font-size: 10px;
  color: #5a6a85;
  margin: 2px 0 0;
}

.btn-novo {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  border-radius: 999px;
  border: none;
  background: #1a3f8f;
  color: #ffffff;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  white-space: nowrap;
  transition: background-color 0.15s ease;
  flex-shrink: 0;
}
.btn-novo:hover {
  background: #0d1f3c;
}

.btn-fechar {
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: transparent;
  color: #5a6a85;
  padding: 6px;
  border-radius: 8px;
  cursor: pointer;
  flex-shrink: 0;
  transition: background-color 0.15s ease;
}
.btn-fechar:hover {
  background: #eef3fb;
}

.dialog-body {
  overflow-y: auto;
  padding: 20px;
  flex: 1;
}

.vazio {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  padding: 48px 16px;
  color: #5a6a85;
  text-align: center;
}

.vazio p {
  font-size: 14px;
  margin: 0;
}

.btn-adicionar {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 4px;
  padding: 9px 18px;
  border-radius: 999px;
  border: none;
  background: #1a3f8f;
  color: #ffffff;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}

.projetos-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 16px;
}

@media (min-width: 560px) {
  .projetos-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
