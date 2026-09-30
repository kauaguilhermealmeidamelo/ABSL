<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import api from '@/services/api'

export interface TurmaOption {
  turno: string
  ano: string
  codigo: string
}

const props = defineProps({
  modelValue: { type: String, default: '' },
  id: { type: String, default: 'turma' },
  mostrarNaoAluno: { type: Boolean, default: true },
  erro: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])

const opcoes = ref<TurmaOption[]>([])
const carregando = ref(true)

const valor = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v),
})

async function carregar() {
  carregando.value = true
  try {
    const { data } = await api.get<TurmaOption[]>('/turmas')
    if (Array.isArray(data)) {
      opcoes.value = [...data].sort((a, b) => a.codigo.localeCompare(b.codigo, 'pt-BR'))
    }
  } catch {
    opcoes.value = []
  } finally {
    carregando.value = false
  }
}

onMounted(carregar)

watch(opcoes, () => {
  if (valor.value && valor.value !== '__nao_aluno' && !opcoes.value.some((t) => t.codigo === valor.value)) {
    valor.value = ''
  }
})
</script>

<template>
  <div class="turma-select-wrap">
    <select
      :id="id"
      v-model="valor"
      class="turma-select"
      :class="{ 'tem-erro': erro }"
      :aria-invalid="erro ? 'true' : 'false'"
      :aria-describedby="erro ? `${id}-erro` : undefined"
      autocomplete="off"
    >
      <option value="" disabled>{{ carregando ? 'Carregando turmas...' : 'Selecione sua turma' }}</option>
      <option v-for="t in opcoes" :key="t.codigo" :value="t.codigo">
        {{ t.codigo }} — {{ t.ano }} ({{ t.turno }})
      </option>
      <option v-if="mostrarNaoAluno" value="__nao_aluno">Não sou aluno</option>
    </select>
    <p v-if="erro" :id="`${id}-erro`" class="erro-campo" role="alert">{{ erro }}</p>
  </div>
</template>

<style scoped>
.turma-select-wrap {
  width: 100%;
}
.turma-select {
  width: 100%;
  appearance: none;
  background-color: #eef1f6;
  border: 2px solid transparent;
  border-radius: 14px;
  padding: 13px 36px 13px 16px;
  font-size: 15px;
  color: #1a2f4a;
  cursor: pointer;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%235a6a85' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
}
.turma-select:focus {
  outline: 2px solid #16509b;
  outline-offset: 1px;
}
.turma-select.tem-erro {
  border-color: #dc2626;
}
.erro-campo {
  color: #dc2626;
  font-size: 12.5px;
  margin: 6px 2px 0;
}
</style>