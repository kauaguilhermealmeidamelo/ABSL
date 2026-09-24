<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import NoticiaCard from '@/components/noticias/NoticiaCard.vue'
import { noticiasService } from '@/services/noticias'

const route = useRoute()
const router = useRouter()
const noticia = ref(null)
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    noticia.value = await noticiasService.get(route.params.id)
  } catch (err) {
    error.value = err?.response?.data?.message || 'Não foi possível carregar esta notícia.'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="shared-page">
    <button type="button" class="back-btn" @click="router.push('/noticias')">
      <v-icon size="20">mdi-arrow-left</v-icon>
      <span>Voltar para notícias</span>
    </button>

    <p v-if="loading" class="status">Carregando notícia...</p>
    <p v-else-if="error" class="status error">{{ error }}</p>

    <NoticiaCard v-else-if="noticia" :noticia="noticia" />

    <div v-else class="status">
      <p>Notícia não encontrada.</p>
      <button type="button" class="back-link" @click="router.push('/noticias')">Ver todas as notícias</button>
    </div>
  </div>
</template>

<style scoped>
.shared-page{max-width:620px;margin:0 auto;padding:24px 16px 48px;font-family:'DM Sans',sans-serif}
.back-btn{display:flex;align-items:center;gap:7px;margin-bottom:16px;border:0;background:transparent;color:#1a3f8f;font-size:13px;font-weight:600;cursor:pointer;padding:6px 2px}
.status{text-align:center;color:#5a6a85;padding:40px 0}
.error{color:#dc2626}
.back-link{margin-top:10px;border:0;background:transparent;color:#1a3f8f;font-weight:600;cursor:pointer}
</style>