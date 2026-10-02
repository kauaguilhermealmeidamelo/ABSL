<script setup>
import DiretoriasManager from '@/components/gerenciamento/DiretoriasManager.vue'
import GremioConteudosManager from '@/components/gerenciamento/GremioConteudosManager.vue'
</script>
<template>
<div class="manager">
<div class="intro"><h2>O Grêmio</h2><p>Administre aqui os dados estruturais que a página pública apresenta.</p></div>
<section><h3>Diretoria e equipe</h3><DiretoriasManager/></section>
<section><h3>Informações institucionais</h3><GremioConteudosManager/></section>
</div>
</template>
<style scoped>
.manager{display:flex;flex-direction:column;gap:22px;font-family:'DM Sans',sans-serif}.intro h2{margin:0;color:#0d1f3c}.intro p{margin:6px 0 0;color:#5a6a85;font-size:13px}.manager h3{margin:0 0 12px;color:#0d1f3c;font-size:15px}
</style>