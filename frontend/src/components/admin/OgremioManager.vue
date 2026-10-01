<script setup>
import DiretoriasManager from '@/components/gerenciamento/DiretoriasManager.vue'
</script>
<template>
<div class="manager">
<div class="intro"><h2>O Grêmio</h2><p>Administre aqui os dados estruturais que a página pública apresenta.</p></div>
<section><h3>Diretoria e equipe</h3><DiretoriasManager/></section>
<section class="empty"><v-icon size="30">mdi-information-outline</v-icon><div><strong>Informações institucionais</strong><p>Os demais conteúdos institucionais serão conectados quando houver fonte administrativa própria no backend.</p></div></section>
</div>
</template>
<style scoped>
.manager{display:flex;flex-direction:column;gap:22px;font-family:'DM Sans',sans-serif}.intro h2{margin:0;color:#0d1f3c}.intro p{margin:6px 0 0;color:#5a6a85;font-size:13px}.manager h3{margin:0 0 12px;color:#0d1f3c;font-size:15px}.empty{display:flex;gap:12px;align-items:flex-start;padding:18px;background:#fff;border:1px dashed rgba(13,31,60,.14);border-radius:14px;color:#5a6a85}.empty strong{color:#0d1f3c}.empty p{margin:4px 0 0;font-size:13px}
</style>