<script setup>
import { computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PageHeader from '@/components/common/PageHeader.vue'
import { useNoticias } from '@/composables/useNoticias'

const route = useRoute()
const router = useRouter()
const { getById, fetchNoticias } = useNoticias()

onMounted(() => {
  // Garante os dados carregados também quando a página é acessada
  // diretamente por URL (sem passar antes por /noticias).
  fetchNoticias()
})

const noticia = computed(() => getById(route.params.id))

function voltar() {
  router.push('/noticias')
}
</script>

<template>
  <div class="detalhe-page">
    <template v-if="noticia">
      <button type="button" class="btn-voltar" @click="voltar">
        <v-icon size="16">mdi-arrow-left</v-icon>
        Voltar para Notícias
      </button>

      <div class="detalhe-imagem">
        <div class="detalhe-imagem-textura" />
      </div>

      <span class="detalhe-badge" :class="noticia.categoria === 'gremio' ? 'badge-gremio' : 'badge-escola'">
        {{ noticia.categoria === 'gremio' ? 'Notícias do Grêmio' : 'Notícias da Escola' }}
      </span>

      <p class="detalhe-data">{{ noticia.data_publicacao }}</p>
      <h1 class="detalhe-titulo">{{ noticia.titulo }}</h1>
      <p class="detalhe-texto">{{ noticia.texto }}</p>
    </template>

    <template v-else>
      <PageHeader label="ABSL" title="Notícia não encontrada" />
      <button type="button" class="btn-voltar" @click="voltar">
        <v-icon size="16">mdi-arrow-left</v-icon>
        Voltar para Notícias
      </button>
    </template>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700;1,900&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400;1,9..40,700&display=swap');

.detalhe-page {
  font-family: 'DM Sans', sans-serif;
  max-width: 720px;
  margin: 0 auto;
  padding: 32px 24px 64px;
}

.btn-voltar {
  display: flex;
  align-items: center;
  gap: 6px;
  border: none;
  background: transparent;
  color: #1a3f8f;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  padding: 0;
  margin-bottom: 20px;
}
.btn-voltar:hover {
  text-decoration: underline;
}

.detalhe-imagem {
  height: 220px;
  border-radius: 16px;
  overflow: hidden;
  background: linear-gradient(135deg, #1a3f8f, #16509b);
  position: relative;
  margin-bottom: 20px;
}
.detalhe-imagem-textura {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(rgba(255, 255, 255, 0.18) 1.5px, transparent 1.5px);
  background-size: 16px 16px;
}

.detalhe-badge {
  display: inline-block;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  padding: 4px 12px;
  border-radius: 999px;
  margin-bottom: 12px;
}
.badge-gremio {
  background: #dbeafe;
  color: #1a3f8f;
}
.badge-escola {
  background: #fef3c7;
  color: #b45309;
}

.detalhe-data {
  font-family: 'DM Mono', monospace;
  font-size: 12px;
  color: #5a6a85;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin: 0 0 8px;
}

.detalhe-titulo {
  font-family: 'Playfair Display', serif;
  font-weight: 700;
  font-size: 28px;
  color: #0d1f3c;
  line-height: 1.3;
  margin: 0 0 20px;
}

.detalhe-texto {
  color: #3d4a5c;
  font-size: 16px;
  line-height: 1.8;
  margin: 0;
}

@media (max-width: 480px) {
  .detalhe-page {
    padding: 20px;
  }
  .detalhe-titulo {
    font-size: 22px;
  }
}
</style>