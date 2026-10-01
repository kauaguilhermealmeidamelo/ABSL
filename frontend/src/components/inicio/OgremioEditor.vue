<script setup>
import { useRouter } from 'vue-router'
import { useAdmin } from '@/composables/useAdmin'

const router = useRouter()
const { isAdmin } = useAdmin()

const secoes = [
  { id: 1, titulo: 'Quem somos', fonte: 'Sobre.vue', rota: null },
  { id: 2, titulo: 'Missão e princípios', fonte: 'Conteúdo existente em Sobre.vue', rota: null },
  { id: 3, titulo: 'Como o Grêmio funciona', fonte: 'Ainda não há conteúdo persistido específico', rota: null },
  { id: 4, titulo: 'Diretoria e mandato', fonte: 'Diretorias cadastradas no sistema', rota: '/usuarios' },
  { id: 5, titulo: 'Estatuto e documentos', fonte: 'Documentos já publicados em Transparência', rota: '/transparencia' },
  { id: 6, titulo: 'Eleições', fonte: 'Informações/documentos existentes', rota: null },
  { id: 7, titulo: 'Projetos e atuação', fonte: 'Projetos cadastrados no sistema', rota: '/projetos' },
  { id: 8, titulo: 'Como participar', fonte: 'Fluxos públicos já existentes', rota: '/ouvintes' },
  { id: 9, titulo: 'Contato', fonte: 'Canais já cadastrados no rodapé', rota: null },
]

function abrirFonte(rota) {
  if (rota) router.push(rota)
}
</script>

<template>
  <aside v-if="isAdmin" class="editor" aria-labelledby="gremio-editor-title">
    <div class="editor-heading">
      <div>
        <span class="kicker">Administração</span>
        <h2 id="gremio-editor-title">Edição de O Grêmio</h2>
      </div>
      <span class="editor-badge">9 tópicos</span>
    </div>

    <p class="editor-intro">
      A página é organizada pelos nove tópicos abaixo. Esta etapa não cria conteúdo novo:
      cada seção usa sua fonte existente ou fica preparada para receber conteúdo quando houver
      uma fonte persistida no sistema.
    </p>

    <ol class="editor-list">
      <li v-for="secao in secoes" :key="secao.id" class="editor-item">
        <div class="editor-number">{{ secao.id }}</div>

        <div class="editor-content">
          <strong>{{ secao.titulo }}</strong>
          <span>{{ secao.fonte }}</span>
        </div>

        <button
          v-if="secao.rota"
          type="button"
          class="editor-action"
          :aria-label="'Editar ' + secao.titulo"
          @click="abrirFonte(secao.rota)"
        >
          <v-icon size="17">mdi-pencil-outline</v-icon>
        </button>
        <span v-else class="editor-pendente">Preparado</span>
      </li>
    </ol>
  </aside>
</template>

<style scoped>
.editor {
  margin: 0 0 48px;
  padding: 20px;
  background: #f8fafc;
  border: 1px solid rgba(13, 31, 60, 0.09);
  border-radius: 16px;
  font-family: 'DM Sans', sans-serif;
}

.editor-heading {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
}

.kicker {
  display: block;
  margin-bottom: 5px;
  color: #1a3f8f;
  font-family: 'DM Mono', monospace;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .08em;
  text-transform: uppercase;
}

.editor h2 {
  margin: 0;
  color: #0d1f3c;
  font-family: 'Playfair Display', serif;
  font-size: 24px;
}

.editor-badge {
  flex: none;
  padding: 5px 9px;
  border-radius: 999px;
  background: #e9eef8;
  color: #1a3f8f;
  font-size: 11px;
  font-weight: 700;
}

.editor-intro {
  max-width: 760px;
  margin: 12px 0 18px;
  color: #5a6a85;
  font-size: 13px;
  line-height: 1.55;
}

.editor-list {
  display: grid;
  gap: 8px;
  margin: 0;
  padding: 0;
  list-style: none;
}

.editor-item {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
  padding: 11px 12px;
  background: #ffffff;
  border: 1px solid rgba(13, 31, 60, 0.07);
  border-radius: 11px;
}

.editor-number {
  display: grid;
  flex: none;
  width: 27px;
  height: 27px;
  place-items: center;
  border-radius: 50%;
  background: #0d1f3c;
  color: #ffffff;
  font-size: 11px;
  font-weight: 700;
}

.editor-content {
  display: flex;
  flex: 1;
  min-width: 0;
  flex-direction: column;
  gap: 2px;
}

.editor-content strong {
  color: #0d1f3c;
  font-size: 13px;
}

.editor-content span {
  overflow: hidden;
  color: #718096;
  font-size: 11px;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.editor-action {
  display: grid;
  flex: none;
  width: 32px;
  height: 32px;
  place-items: center;
  border: 0;
  border-radius: 8px;
  background: #eef3fb;
  color: #1a3f8f;
  cursor: pointer;
}

.editor-action:hover,
.editor-action:focus-visible {
  background: #dfe8f8;
}

.editor-action:focus-visible {
  outline: 2px solid #1a3f8f;
  outline-offset: 2px;
}

.editor-pendente {
  flex: none;
  color: #718096;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
}

@media (max-width: 620px) {
  .editor {
    margin-bottom: 40px;
    padding: 16px;
  }

  .editor-heading {
    align-items: center;
  }

  .editor h2 {
    font-size: 21px;
  }

  .editor-content span {
    white-space: normal;
  }
}
</style>