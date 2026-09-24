<script setup>
import { ref, watch } from 'vue'
import { noticiasService } from '@/services/noticias'
import { user } from '@/stores/auth'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  noticiaId: { type: [Number, String], default: null },
})

const emit = defineEmits(['update:modelValue', 'comentario-adicionado'])

const comentarios = ref([])
const loading = ref(false)
const error = ref('')
const texto = ref('')
const enviando = ref(false)

async function carregar() {
  if (!props.noticiaId) return
  loading.value = true
  error.value = ''
  try {
    comentarios.value = await noticiasService.listarComentarios(props.noticiaId)
  } catch {
    error.value = 'Não foi possível carregar os comentários.'
  } finally {
    loading.value = false
  }
}

watch(() => [props.modelValue, props.noticiaId], ([open]) => {
  if (open) carregar()
})

async function enviar() {
  if (!texto.value.trim()) return
  enviando.value = true
  try {
    const criado = await noticiasService.comentar(props.noticiaId, texto.value)
    comentarios.value = [...comentarios.value, criado]
    texto.value = ''
    emit('comentario-adicionado')
  } catch (err) {
    error.value = err?.response?.status === 401
      ? 'Faça login para comentar.'
      : 'Não foi possível enviar o comentário.'
  } finally {
    enviando.value = false
  }
}

function fechar() {
  emit('update:modelValue', false)
}
</script>

<template>
  <v-bottom-sheet :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" inset>
    <v-card class="comentarios-modal">
      <v-card-title class="modal-title">
        Comentários
        <button type="button" class="btn-close" @click="fechar">
          <v-icon size="18">mdi-close</v-icon>
        </button>
      </v-card-title>

      <v-card-text class="modal-body">
        <p v-if="loading" class="status-msg">Carregando comentários...</p>
        <p v-else-if="error" class="status-msg status-erro">{{ error }}</p>

        <div v-else class="comentarios-lista">
          <div v-for="c in comentarios" :key="c.id" class="comentario-item">
            <p class="comentario-autor">{{ c.autor || 'Aluno' }}</p>
            <p class="comentario-texto">{{ c.texto }}</p>
          </div>
          <p v-if="!comentarios.length" class="status-msg status-vazio">
            Nenhum comentário ainda. Seja o primeiro!
          </p>
        </div>
      </v-card-text>

      <div class="comentar-form">
        <input
          v-model="texto"
          class="comentar-input"
          placeholder="Adicione um comentário..."
          :disabled="!user"
          @keydown.enter="enviar"
        />
        <button type="button" class="comentar-enviar" :disabled="enviando || !texto.trim() || !user" @click="enviar">
          Enviar
        </button>
      </div>
      <p v-if="!user" class="aviso-login">Faça login para comentar.</p>
    </v-card>
  </v-bottom-sheet>
</template>

<style scoped>
.comentarios-modal {
  border-radius: 18px !important;
  font-family: 'DM Sans', sans-serif;
  padding: 4px;
  max-height: 80vh;
  width: min(100%, 560px);
  margin: 0 auto;
  display: flex;
  flex-direction: column;
}

.modal-title {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-weight: 700;
  font-size: 16px;
  color: #0d1f3c;
  padding: 16px 20px 8px;
}

.btn-close {
  border: none;
  background: transparent;
  color: #5a6a85;
  cursor: pointer;
  display: flex;
}

.modal-body {
  overflow-y: auto;
  padding: 8px 20px;
}

.status-msg {
  color: #5a6a85;
  font-size: 13px;
  padding: 12px 0;
}
.status-erro {
  color: #dc2626;
}
.status-vazio {
  color: #94a3b8;
}

.comentarios-lista {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.comentario-item {
  border-bottom: 1px solid rgba(13, 31, 60, 0.06);
  padding-bottom: 10px;
}
.comentario-item:last-child {
  border-bottom: none;
}

.comentario-autor {
  font-size: 12px;
  font-weight: 700;
  color: #0d1f3c;
  margin: 0 0 2px;
}

.comentario-texto {
  font-size: 13px;
  color: #3d4a5c;
  line-height: 1.5;
  margin: 0;
}

.comentar-form {
  display: flex;
  gap: 8px;
  padding: 12px 20px;
  border-top: 1px solid rgba(13, 31, 60, 0.08);
}

.comentar-input {
  flex: 1;
  border: 1px solid rgba(13, 31, 60, 0.15);
  border-radius: 999px;
  padding: 9px 16px;
  font-size: 13px;
  background: #eef3fb;
  font-family: inherit;
  color: #0d1f3c;
}
.comentar-input:disabled {
  opacity: 0.6;
}

.comentar-enviar {
  border: none;
  background: #1a3f8f;
  color: #ffffff;
  padding: 9px 18px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}
.comentar-enviar:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.aviso-login {
  text-align: center;
  font-size: 11.5px;
  color: #94a3b8;
  padding: 0 20px 12px;
  margin: 0;
}
</style>