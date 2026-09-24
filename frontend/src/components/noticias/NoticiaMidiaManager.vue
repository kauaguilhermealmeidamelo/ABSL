<script setup>
import { ref } from 'vue'
import { noticiasService } from '@/services/noticias'

const props = defineProps({
    noticiaId: { type: [Number, String], required: true },
    modelValue: { type: Array, default: () => [] }, // [{ id, tipo, url }]
})
const emit = defineEmits(['update:modelValue'])

const fileInput = ref(null)
const enviando = ref(false)
const error = ref('')

function abrirSeletor() {
    fileInput.value?.click()
}

async function onFilesChange(event) {
    const arquivos = Array.from(event.target.files || [])
    event.target.value = ''
    if (!arquivos.length) return

    enviando.value = true
    error.value = ''
    try {
        const atualizada = await noticiasService.adicionarMidias(props.noticiaId, arquivos)
        emit('update:modelValue', atualizada.midias ?? [])
    } catch {
        error.value = 'Não foi possível enviar os arquivos. Verifique o formato e o tamanho.'
    } finally {
        enviando.value = false
    }
}

async function remover(midiaId) {
    if (!confirm('Remover esta mídia da publicação?')) return
    try {
        await noticiasService.removerMidia(props.noticiaId, midiaId)
        emit('update:modelValue', props.modelValue.filter((m) => m.id !== midiaId))
    } catch {
        error.value = 'Não foi possível remover a mídia.'
    }
}
</script>

<template>
    <div class="midia-manager">
        <label class="field-label">Fotos e vídeos ({{ modelValue.length }})</label>

        <div class="midia-grid">
            <div v-for="m in modelValue" :key="m.id" class="midia-thumb">
                <video v-if="m.tipo === 'video'" :src="m.url" muted />
                <img v-else :src="m.url" :alt="'Mídia ' + m.id" />
                <button type="button" class="midia-remove" @click="remover(m.id)">
                    <v-icon size="13" color="white">mdi-close</v-icon>
                </button>
            </div>

            <button type="button" class="midia-add" :disabled="enviando" @click="abrirSeletor">
                <v-icon size="22" color="#5a6a85">{{ enviando ? 'mdi-loading mdi-spin' : 'mdi-plus' }}</v-icon>
            </button>
        </div>

        <input ref="fileInput" type="file" accept="image/*,video/*" multiple hidden @change="onFilesChange" />

        <p v-if="error" class="midia-erro">{{ error }}</p>
        <p class="midia-dica">A primeira mídia adicionada aparece primeiro no carrossel do post.</p>
    </div>
</template>

<style scoped>
.midia-manager {
    margin-top: 8px;
}

.field-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #5a6a85;
    margin-bottom: 8px;
}

.midia-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(84px, 1fr));
    gap: 8px;
}

.midia-thumb {
    position: relative;
    aspect-ratio: 1;
    border-radius: 10px;
    overflow: hidden;
    background: #000;
}

.midia-thumb img,
.midia-thumb video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.midia-remove {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 20px;
    height: 20px;
    border-radius: 999px;
    border: none;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.midia-add {
    aspect-ratio: 1;
    border-radius: 10px;
    border: 1.5px dashed rgba(13, 31, 60, 0.25);
    background: #eef3fb;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.midia-add:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.midia-erro {
    color: #dc2626;
    font-size: 11.5px;
    margin: 8px 0 0;
}

.midia-dica {
    color: #94a3b8;
    font-size: 11px;
    margin: 6px 0 0;
}
</style>