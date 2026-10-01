<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import ProjetosDialog from '@/components/projetos/ProjetosDialog.vue'
import ProjetoFormModal from '@/components/projetos/ProjetoFormModal.vue'
import DiretoriaSelectorGrid from '@/components/projetos/DiretoriaSelectorGrid.vue'
import { useProjetos } from '@/composables/useProjetos'

const router = useRouter()
const { projetos, loading, error, fetchProjetos, adicionar, atualizar, remover } = useProjetos()
const categoria = ref(null)
const modal = ref(false)
const editando = ref(null)

const visiveis = computed(() => {
  if (categoria.value === null) return []
  if (categoria.value === '__geral__') return projetos.value
  return projetos.value.filter((p) => p.categoria === categoria.value)
})

onMounted(() => fetchProjetos(true))

function novo() {
  editando.value = null
  modal.value = true
}

function editar(projeto) {
  editando.value = projeto
  modal.value = true
}

function abrirDetalhe(projeto) {
  router.push(`/projetos/${projeto.id}`)
}

async function salvar(dados) {
  try {
    if (editando.value) await atualizar(editando.value.id, dados)
    else await adicionar(dados)
    modal.value = false
  } catch {
    error.value = 'Não foi possível salvar o projeto.'
  }
}

async function excluir(id) {
  try {
    await remover(id)
  } catch {
    error.value = 'Não foi possível excluir o projeto.'
  }
}
</script>

<template>
  <div class="manager">
    <div class="toolbar">
      <div>
        <h2>Projetos</h2>
        <p>Cadastre e organize os projetos sem sair do painel.</p>
      </div>
      <button class="primary" type="button" @click="novo">
        <v-icon size="16">mdi-plus</v-icon>
        Novo projeto
      </button>
    </div>

    <p v-if="loading" class="state">Carregando projetos...</p>
    <p v-else-if="error" class="state error">{{ error }}</p>

    <template v-else>
      <DiretoriaSelectorGrid
        :projetos="projetos"
        :selecionado="categoria"
        @selecionar="categoria = $event"
      />

      <ProjetosDialog
        v-if="categoria !== null"
        :categoria="categoria"
        :projetos="visiveis"
        :is-admin="true"
        @fechar="categoria = null"
        @novo="novo"
        @editar="editar"
        @excluir="excluir"
        @abrir-detalhe="abrirDetalhe"
      />
    </template>

    <ProjetoFormModal
      v-model="modal"
      :projeto="editando"
      :categoria-padrao="categoria && categoria !== '__geral__' ? categoria : undefined"
      @salvar="salvar"
    />
  </div>
</template>

<style scoped>
.manager{display:flex;flex-direction:column;gap:18px;font-family:'DM Sans',sans-serif}.toolbar{display:flex;justify-content:space-between;align-items:center;gap:16px}.toolbar h2{margin:0;color:#0d1f3c}.toolbar p{margin:5px 0 0;color:#5a6a85;font-size:13px}.primary{display:flex;align-items:center;gap:7px;border:0;border-radius:10px;background:#1a3f8f;color:#fff;padding:10px 16px;font-weight:600;cursor:pointer}.state{padding:28px 0;color:#5a6a85;text-align:center}.error{color:#dc2626}@media(max-width:600px){.toolbar{align-items:stretch;flex-direction:column}.primary{justify-content:center}}
</style>
