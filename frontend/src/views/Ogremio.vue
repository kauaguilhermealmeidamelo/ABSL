<script setup>
import Sobre from '@/components/inicio/Sobre.vue'
import Equipe from '@/components/inicio/Equipe.vue'
</script>

<template>
  <main class="gremio-page">
    <header class="page-header">
      <span class="kicker">O Grêmio</span>
      <h1>Grêmio Estudantil Athos Bulcão</h1>
      <p>
        Conheça a organização, a atuação e a equipe do Grêmio no CEM Setor Leste.
      </p>
    </header>

    <section class="section" aria-labelledby="quem-somos-title">
      <div class="section-heading">
        <span class="kicker">Quem somos</span>
        <h2 id="quem-somos-title">Representação e participação estudantil</h2>
      </div>
      <Sobre />
    </section>

    <section class="section" aria-labelledby="organizacao-title">
      <div class="section-heading">
        <span class="kicker">Organização</span>
        <h2 id="organizacao-title">Equipe e diretorias</h2>
        <p>Conheça as diretorias e os integrantes cadastrados no portal.</p>
      </div>
      <Equipe />
    </section>
  </main>
</template>

<style scoped>
.gremio-page{width:min(100%,1180px);margin:0 auto;padding:32px 32px 64px;font-family:'DM Sans',sans-serif}.page-header{padding:40px 0 28px;border-bottom:1px solid rgba(13,31,60,.08)}.kicker{display:block;margin-bottom:8px;color:#1a3f8f;font-family:'DM Mono',monospace;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}.page-header h1,.section-heading h2{margin:0;color:#0d1f3c;font-family:'Playfair Display',serif;line-height:1.15}.page-header h1{font-size:clamp(32px,6vw,52px)}.page-header p,.section-heading p{max-width:720px;margin:12px 0 0;color:#5a6a85;line-height:1.6}.section{margin-top:48px}.section-heading{margin-bottom:20px}.section-heading h2{font-size:clamp(25px,4vw,34px)}@media(max-width:620px){.gremio-page{padding:20px 16px 48px}.page-header{padding-top:24px}.section{margin-top:40px}}
</style>
