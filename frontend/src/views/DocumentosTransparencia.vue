<script setup>
import { computed, onMounted, ref } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import BackButton from '@/components/common/BackButton.vue'
import { transparenciaService } from '@/services/transparencia'

const props = defineProps({ tipo: { type: String, required: true } })
const documentos = ref([])
const erro = ref('')
const carregando = ref(true)
const titulo = computed(() => props.tipo === 'reunioes' ? 'Atas das Reuniões' : 'Estatuto e Regimento')
const filtrados = computed(() => documentos.value.filter((d) => {
  if (props.tipo === 'reunioes') return d.categoria === 'atas' || d.tipo_documento === 'ata'
  return ['estatuto', 'regimento', 'institucional', 'gremio'].includes(d.categoria)
    || ['estatuto', 'regimento'].includes(d.tipo_documento)
}))
onMounted(async () => {
  try { documentos.value = await transparenciaService.list() }
  catch { erro.value = 'Não foi possível carregar os documentos.' }
  finally { carregando.value = false }
})
</script>
<template>
  <main class="documentos">
    <PageHeader label="Transparência" :title="titulo" subtitle="Documentos institucionais do Grêmio Athos Bulcão." />
    <BackButton to="/transparencia" label="Voltar à transparência" />
    <p v-if="carregando" role="status">Carregando documentos...</p>
    <p v-else-if="erro" role="alert">{{ erro }}</p>
    <p v-else-if="!filtrados.length">Nenhum documento publicado nesta categoria.</p>
    <div v-else class="lista">
      <article v-for="d in filtrados" :key="d.id" class="documento">
        <h2>{{ d.titulo }}</h2>
        <p>{{ d.descricao }}</p>
        <p v-if="d.data_documento"><small>Data: {{ d.data_documento }}</small></p>
        <a v-if="d.arquivo_url" :href="d.arquivo_url" target="_blank" rel="noopener noreferrer">Abrir documento</a>
      </article>
    </div>
  </main>
</template>
<style scoped>
.documentos { padding: 24px; max-width: 1000px; margin: auto; color: #0b132b; }
.documentos > a { color: #16509b; display: inline-block; margin: 12px 0 24px; }
.lista { display: grid; gap: 16px; }
.documento { padding: 20px; border: 1px solid #dce4f0; border-radius: 14px; }
.documento h2 { font-size: 20px; margin: 0 0 12px; }
.documento a { color: #16509b; font-weight: 600; }
@media(max-width:480px) { .documentos { padding: 16px; } }
</style>
