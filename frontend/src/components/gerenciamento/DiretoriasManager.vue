<script setup>
import { ref } from 'vue'
import { team, addDiretoria, removeDiretoria, saveDiretoriaMembers } from '@/stores/appData'

import { team, addDiretoria, removeDiretoria, saveDiretoriaMembers, moveDiretoria } from '@/stores/appData'

function mover(idx, direcao) {
  moveDiretoria(idx, direcao)
}

const novaDir = ref({ name: '', diretorGeral: '', primeiro: '', segundo: '' })

function cadastrar() {
  if (!novaDir.value.name.trim() || !novaDir.value.diretorGeral.trim()) return
  addDiretoria({ ...novaDir.value })
  novaDir.value = { name: '', diretorGeral: '', primeiro: '', segundo: '' }
}

const expandido = ref(null)
const editMembers = ref([])

function abrir(idx) {
  expandido.value = idx
  editMembers.value = team[idx].members.map((m) => ({ ...m }))
}

function fechar() {
  expandido.value = null
}

function salvar(idx) {
  saveDiretoriaMembers(idx, editMembers.value)
  expandido.value = null
}

function adicionarIntegrante() {
  editMembers.value.push({ cargo: '', nome: '' })
}

function removerIntegrante(i) {
  editMembers.value.splice(i, 1)
}
</script>

<template>
  <div class="dir-manager">
    <div class="card">
      <h3 class="card-title">Cadastrar nova diretoria</h3>
      <div class="form-stack">
        <div>
          <label class="field-label">Nome da diretoria</label>
          <input class="field-input" v-model="novaDir.name" placeholder="Ex: Diretoria de Esporte" />
        </div>
        <div>
          <label class="field-label">Diretor(a)-Geral</label>
          <input class="field-input" v-model="novaDir.diretorGeral" placeholder="Nome completo" />
        </div>
        <div>
          <label class="field-label">1º(ª) Diretor(a)</label>
          <input class="field-input" v-model="novaDir.primeiro" placeholder="Nome completo (opcional)" />
        </div>
        <div>
          <label class="field-label">2º(ª) Diretor(a)</label>
          <input class="field-input" v-model="novaDir.segundo" placeholder="Nome completo (opcional)" />
        </div>
        <button type="button" class="btn-add btn-block" :disabled="!novaDir.name.trim() || !novaDir.diretorGeral.trim()"
          @click="cadastrar">
          Cadastrar diretoria
        </button>
      </div>
    </div>

    <div class="lista">
      <div v-for="(dir, idx) in team" :key="dir.name" class="card card-dir">
        <div class="dir-row">
          <v-icon size="18" color="#1a3f8f">{{ dir.icon }}</v-icon>
          <span class="dir-nome">{{ dir.name }}</span>
          <span class="dir-total">{{ dir.members.length }} integrante{{ dir.members.length !== 1 ? 's' : '' }}</span>
          <button type="button" class="icon-btn icon-btn-danger" @click="removeDiretoria(idx)">
            <v-icon size="15">mdi-trash-can-outline</v-icon>
          </button>
          <button type="button" class="btn-editar" @click="expandido === idx ? fechar() : abrir(idx)">
            <v-icon size="12">mdi-pencil-outline</v-icon>
            Editar
          </button>
        </div>

        <div v-if="expandido === idx" class="dir-edit">
          <div v-for="(m, mi) in editMembers" :key="mi" class="dir-edit-row">
            <input class="field-input field-input-sm" v-model="m.cargo" placeholder="Cargo" />
            <input class="field-input field-input-sm" v-model="m.nome" placeholder="Nome" />
            <button type="button" class="icon-btn icon-btn-danger" @click="removerIntegrante(mi)">
              <v-icon size="13">mdi-close</v-icon>
            </button>
          </div>
          <button type="button" class="btn-link" @click="adicionarIntegrante">
            <v-icon size="12">mdi-plus</v-icon>
            Adicionar integrante
          </button>
          <div class="dir-edit-actions">
            <button type="button" class="btn-cancelar" @click="fechar">Cancelar</button>
            <button type="button" class="btn-salvar" @click="salvar(idx)">Salvar</button>
          </div>
        </div>
      </div>
    </div>
    <div class="dir-row">
      <v-icon size="18" color="#1a3f8f">{{ dir.icon }}</v-icon>
      <span class="dir-nome">{{ dir.name }}</span>
      <span class="dir-total">{{ dir.members.length }} integrante{{ dir.members.length !== 1 ? 's' : '' }}</span>

      <div class="ordem-btns">
        <button type="button" class="icon-btn" :disabled="idx === 0" title="Mover para cima"
          @click="mover(idx, 'cima')">
          <v-icon size="15">mdi-chevron-up</v-icon>
        </button>
        <button type="button" class="icon-btn" :disabled="idx === team.length - 1" title="Mover para baixo"
          @click="mover(idx, 'baixo')">
          <v-icon size="15">mdi-chevron-down</v-icon>
        </button>
      </div>

      <button type="button" class="icon-btn icon-btn-danger" @click="removeDiretoria(idx)">
        <v-icon size="15">mdi-trash-can-outline</v-icon>
      </button>
      <button type="button" class="btn-editar" @click="expandido === idx ? fechar() : abrir(idx)">
        <v-icon size="12">mdi-pencil-outline</v-icon>
        Editar
      </button>
    </div>
  </div>
</template>

<style scoped>
.dir-manager {
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

.form-stack {
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-width: 420px;
}

.field-label {
  display: block;
  font-size: 12px;
  font-weight: 500;
  color: #5a6a85;
  margin-bottom: 4px;
}

.field-input {
  width: 100%;
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

.field-input-sm {
  padding: 6px 10px;
  font-size: 12px;
  border-radius: 8px;
}

.btn-add {
  border: none;
  background: #1a3f8f;
  color: #ffffff;
  padding: 10px 20px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.btn-add:hover:not(:disabled) {
  background: #0d1f3c;
}

.btn-add:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.btn-block {
  width: 100%;
  margin-top: 4px;
}

.lista {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.card-dir {
  padding: 0;
  overflow: hidden;
}

.dir-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 20px;
  flex-wrap: wrap;
}

.dir-nome {
  flex: 1;
  font-weight: 700;
  color: #0d1f3c;
  font-size: 14px;
  min-width: 120px;
}

.dir-total {
  font-size: 12px;
  color: #5a6a85;
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

.btn-editar {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 8px;
  background: #eef3fb;
  color: #1a3f8f;
  font-size: 12px;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.btn-editar:hover {
  background: #d6e4ff;
}

.dir-edit {
  border-top: 1px solid rgba(13, 31, 60, 0.08);
  padding: 16px 20px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.dir-edit-row {
  display: flex;
  gap: 8px;
  align-items: center;
}

.btn-link {
  display: flex;
  align-items: center;
  gap: 4px;
  border: none;
  background: transparent;
  color: #1a3f8f;
  font-size: 12px;
  cursor: pointer;
  padding: 0;
  width: fit-content;
}

.btn-link:hover {
  text-decoration: underline;
}

.dir-edit-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 4px;
}

.btn-cancelar,
.btn-salvar {
  padding: 7px 16px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  border: none;
}

.btn-cancelar {
  background: transparent;
  color: #5a6a85;
  border: 1px solid rgba(13, 31, 60, 0.15);
}

.btn-salvar {
  background: #1a3f8f;
  color: #ffffff;
}

.btn-salvar:hover {
  background: #0d1f3c;
}

.ordem-btns {
  display: flex;
  gap: 2px;
}
.icon-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}
@media (max-width: 480px) {
  .dir-row {
    gap: 8px;
  }

  .dir-edit-row {
    flex-wrap: wrap;
  }
}
</style>