<script setup>
import { gabMatGroups, gabVesGroups } from '@/stores/appData'

const secoes = [
  { titulo: 'Matutino', lista: gabMatGroups },
  { titulo: 'Vespertino', lista: gabVesGroups },
]

function addGrupo(lista) {
  lista.push('')
}
function removeGrupo(lista, i) {
  lista.splice(i, 1)
}
</script>

<template>
  <div class="gab-manager">
    <div v-for="secao in secoes" :key="secao.titulo" class="card">
      <h3 class="card-title uppercase">{{ secao.titulo }}</h3>
      <div class="grupo-list">
        <div v-for="(label, i) in secao.lista" :key="i" class="grupo-row">
          <span class="grupo-index">Grupo {{ i + 1 }}</span>
          <input class="field-input" v-model="secao.lista[i]" placeholder="Ex: 2A até 2D" />
          <button type="button" class="icon-btn icon-btn-danger" @click="removeGrupo(secao.lista, i)">
            <v-icon size="14">mdi-close</v-icon>
          </button>
        </div>
      </div>
      <button type="button" class="btn-add" @click="addGrupo(secao.lista)">
        <v-icon size="14">mdi-plus</v-icon>
        Adicionar grupo
      </button>
    </div>
  </div>
</template>

<style scoped>
.gab-manager {
  display: flex;
  flex-direction: column;
  gap: 24px;
  font-family: 'DM Sans', sans-serif;
}
.card {
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 16px;
  padding: 24px;
}
.card-title {
  font-weight: 700;
  color: #0d1f3c;
  font-size: 14px;
  margin: 0 0 16px;
}
.uppercase {
  text-transform: uppercase;
  letter-spacing: 0.04em;
  font-size: 12px;
}
.grupo-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 12px;
}
.grupo-row {
  display: flex;
  align-items: center;
  gap: 8px;
}
.grupo-index {
  font-size: 12px;
  color: #5a6a85;
  width: 64px;
  flex-shrink: 0;
}
.field-input {
  flex: 1;
  border: 1px solid rgba(13, 31, 60, 0.15);
  border-radius: 12px;
  padding: 8px 12px;
  font-size: 14px;
  background: #eef3fb;
  color: #0d1f3c;
  box-sizing: border-box;
  font-family: inherit;
}
.field-input:focus {
  outline: none;
  border-color: #1a3f8f;
}
.icon-btn {
  border: none;
  background: transparent;
  padding: 6px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
}
.icon-btn-danger {
  color: #f87171;
}
.icon-btn-danger:hover {
  background: #fef2f2;
  color: #dc2626;
}
.btn-add {
  display: flex;
  align-items: center;
  gap: 8px;
  border: none;
  background: #eef3fb;
  color: #1a3f8f;
  padding: 9px 18px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.15s ease;
}
.btn-add:hover {
  background: #d6e4ff;
}
</style>