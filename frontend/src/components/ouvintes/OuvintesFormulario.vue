<template>
  <div class="form-card">
    <h2 class="form-title">Enviar mensagem</h2>

    <!-- Alternância de modo -->
    <div class="modo-toggle">
      <button
        type="button"
        class="modo-btn"
        :class="{ 'modo-btn-active': modo === 'identificada' }"
        @click="modo = 'identificada'"
      >
        <v-icon size="14">mdi-account-outline</v-icon>
        Com identificação
      </button>
      <button
        type="button"
        class="modo-btn"
        :class="{ 'modo-btn-active': modo === 'anonima' }"
        @click="modo = 'anonima'"
      >
        <v-icon size="14">mdi-eye-off-outline</v-icon>
        Anônima
      </button>
    </div>

    <!-- Aviso conforme o modo -->
    <div class="info-banner" :class="modo === 'anonima' ? 'info-banner-anonima' : 'info-banner-identificada'">
      <v-icon size="14" class="info-icon">{{ modo === 'anonima' ? 'mdi-eye-off-outline' : 'mdi-account-outline' }}</v-icon>
      <span>
        {{
          modo === 'anonima'
            ? 'Sua identidade não será revelada. Nenhum dado pessoal será coletado ou armazenado.'
            : 'Seus dados serão visíveis apenas para a diretoria do grêmio e tratados com sigilo.'
        }}
      </span>
    </div>

    <!-- Campos de identificação -->
    <div v-if="modo === 'identificada'" class="fields-grid">
      <div>
        <label class="field-label">Nome completo</label>
        <input v-model="nome" type="text" class="field-input" placeholder="Seu nome" />
      </div>
      <div>
        <label class="field-label">E-mail (opcional)</label>
        <input v-model="email" type="email" class="field-input" placeholder="seu@email.com" />
      </div>
    </div>

    <!-- Mensagem -->
    <div class="message-field">
      <label class="field-label">Sua mensagem — sugestão, crítica ou opinião</label>
      <textarea
        v-model="texto"
        rows="4"
        class="field-textarea"
        placeholder="Escreva aqui sua sugestão, crítica ou opinião..."
      />
    </div>

    <div class="submit-row">
      <button type="button" class="submit-btn" :disabled="!texto.trim()" @click="enviar">
        <v-icon size="14">mdi-send</v-icon>
        Enviar mensagem
      </button>
      <span v-if="sent" class="sent-confirm">
        <v-icon size="15" color="#16a34a">mdi-check</v-icon>
        Mensagem enviada!
      </span>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const emit = defineEmits(['enviar'])

const modo = ref('identificada')
const nome = ref('')
const email = ref('')
const texto = ref('')
const sent = ref(false)

let timeoutId = null

function enviar() {
  if (!texto.value.trim()) return

  emit('enviar', {
    anonimo: modo.value === 'anonima',
    nome: modo.value === 'identificada' ? nome.value : undefined,
    email: modo.value === 'identificada' ? email.value : undefined,
    texto: texto.value,
  })

  nome.value = ''
  email.value = ''
  texto.value = ''
  sent.value = true

  clearTimeout(timeoutId)
  timeoutId = setTimeout(() => { sent.value = false }, 3500)
}
</script>

<style scoped>
.form-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid rgba(13, 31, 60, 0.08);
  padding: 24px;
  box-shadow: 0 1px 3px rgba(13, 31, 60, 0.06);
  font-family: 'DM Sans', sans-serif;
}

.form-title {
  color: #0d1f3c;
  font-weight: 700;
  font-size: 16px;
  margin: 0 0 16px;
}

.modo-toggle {
  display: flex;
  gap: 4px;
  background: #eef3fb;
  border-radius: 12px;
  padding: 4px;
  margin-bottom: 20px;
  width: 100%;
}

@media (min-width: 640px) {
  .modo-toggle {
    width: fit-content;
  }
}

.modo-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 8px;
  border: none;
  background: transparent;
  color: #5a6a85;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  flex: 1;
  transition: all 0.15s ease;
}

@media (min-width: 640px) {
  .modo-btn {
    flex: none;
  }
}

.modo-btn:hover {
  color: #0d1f3c;
}

.modo-btn-active,
.modo-btn-active:hover {
  background: #1a3f8f;
  color: #ffffff;
  box-shadow: 0 1px 3px rgba(13, 31, 60, 0.1);
}

.info-banner {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 12px;
  margin-bottom: 16px;
  font-size: 12px;
  line-height: 1.5;
}

.info-icon {
  flex-shrink: 0;
  margin-top: 2px;
}

.info-banner-anonima {
  background: rgba(13, 31, 60, 0.05);
  color: #5a6a85;
}

.info-banner-identificada {
  background: #eff6ff;
  color: #1a3f8f;
}

.fields-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 12px;
  margin-bottom: 12px;
}

@media (min-width: 640px) {
  .fields-grid {
    grid-template-columns: 1fr 1fr;
  }
}

.field-label {
  display: block;
  font-size: 12px;
  font-weight: 500;
  color: #5a6a85;
  margin-bottom: 4px;
}

.field-input,
.field-textarea {
  width: 100%;
  border: 1px solid rgba(13, 31, 60, 0.15);
  border-radius: 12px;
  padding: 8px 12px;
  font-size: 14px;
  background: #eef3fb;
  font-family: inherit;
  color: #0d1f3c;
  box-sizing: border-box;
}

.field-textarea {
  padding: 12px;
  resize: none;
  line-height: 1.6;
}

.field-input:focus,
.field-textarea:focus {
  outline: none;
  border-color: #1a3f8f;
}

.message-field {
  margin-bottom: 16px;
}

.submit-row {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
}

.submit-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  border-radius: 999px;
  border: none;
  background: #1a3f8f;
  color: #ffffff;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.submit-btn:hover:not(:disabled) {
  background: #0d1f3c;
}

.submit-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.sent-confirm {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #16a34a;
  font-size: 14px;
  font-weight: 500;
}
</style>