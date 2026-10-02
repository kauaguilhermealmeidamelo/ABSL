<script setup>
defineProps({
  titulo: { type: String, default: 'Informações institucionais' },
  itens: { type: Array, default: () => [] },
  erro: { type: String, default: '' },
  mensagemVazia: {
    type: String,
    default: 'Os demais conteúdos institucionais ainda não foram publicados.',
  },
})
</script>

<template>
  <section class="info-inst" aria-labelledby="info-inst-title">
    <div class="info-inst-heading">
      <h2 id="info-inst-title">{{ titulo }}</h2>
    </div>

    <p v-if="erro" class="info-inst-erro" role="alert">{{ erro }}</p>

    <div v-else-if="itens.length" class="info-inst-grid">
      <article v-for="item in itens" :key="item.id" class="info-inst-item">
        <h3>{{ item.titulo }}</h3>
        <p v-if="item.conteudo">{{ item.conteudo }}</p>
        <a
          v-if="item.arquivo_pdf"
          class="info-inst-pdf"
          :href="item.arquivo_pdf"
          target="_blank"
          rel="noopener noreferrer"
        >
          <v-icon size="18" aria-hidden="true">mdi-file-pdf-box</v-icon>
          <span>Baixar PDF</span>
        </a>
      </article>
    </div>

    <div v-else class="info-inst-card" role="note">
      <v-icon size="28" aria-hidden="true">mdi-information-outline</v-icon>
      <p>{{ mensagemVazia }}</p>
    </div>
  </section>
</template>

<style scoped>
.info-inst {
  margin-top: 48px;
  font-family: var(--font-body, 'DM Sans', sans-serif);
}

.info-inst-heading h2 {
  margin: 0 0 20px;
  color: var(--color-navy, #0f2038);
  font-family: var(--font-heading, 'Playfair Display', serif);
  font-size: clamp(25px, 4vw, 34px);
  line-height: 1.15;
}

.info-inst-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
}

.info-inst-item {
  padding: 20px;
  background: var(--color-surface, #ffffff);
  border: 1px solid rgba(13, 31, 60, 0.08);
  border-radius: 12px;
  /* min-width: 0 impede que o conteúdo force a coluna da grid a crescer. */
  min-width: 0;
  overflow-wrap: anywhere;
}

.info-inst-item h3 {
  margin: 0 0 8px;
  color: var(--color-navy, #0f2038);
  font-size: 17px;
  overflow-wrap: anywhere;
}

.info-inst-item p {
  margin: 0;
  color: var(--color-text-secondary, #6b7c93);
  font-size: 14px;
  line-height: 1.6;
  white-space: pre-line;
  /* Quebra palavras longas sem espaço (ex.: AAAA... ou URLs coladas). */
  overflow-wrap: anywhere;
}

.info-inst-pdf {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-top: 14px;
  padding: 9px 14px;
  border: 1px solid rgba(13, 31, 60, 0.12);
  border-radius: 10px;
  background: var(--color-surface-muted, #f7f8fc);
  color: var(--color-navy-soft, #16509b);
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
}

.info-inst-pdf:hover {
  background: #eef3fb;
  border-color: rgba(22, 80, 155, 0.35);
}

.info-inst-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 20px;
  border: 1px dashed rgba(13, 31, 60, 0.14);
  border-radius: var(--radius-card, 16px);
  background: var(--color-surface, #ffffff);
  color: var(--color-text-secondary, #6b7c93);
}

.info-inst-card p {
  margin: 0;
  font-size: 14px;
  line-height: 1.6;
}

.info-inst-erro {
  margin: 0;
  color: #dc2626;
  font-size: 14px;
}

@media (max-width: 620px) {
  .info-inst {
    margin-top: 40px;
  }

  .info-inst-grid {
    grid-template-columns: 1fr;
  }

  .info-inst-card {
    flex-direction: column;
    text-align: center;
    padding: 24px 16px;
  }
}
</style>