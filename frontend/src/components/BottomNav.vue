<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import AdminCard from '@/components/AdminCard.vue'
import { useAdmin } from '@/composables/useAdmin'

const route = useRoute()
const { isAdmin } = useAdmin()

const primaryItems = [
    { label: 'Início', to: '/inicio', icon: 'mdi-home-outline', iconActive: 'mdi-home' },
    { label: 'Horário', to: '/horario', icon: 'mdi-clock-outline', iconActive: 'mdi-clock' },
    { label: 'Cardápio', to: '/cardapio', icon: 'mdi-silverware-fork-knife', iconActive: 'mdi-silverware-fork-knife' },
    { label: 'Gabarito', to: '/gabarito', icon: 'mdi-file-document-outline', iconActive: 'mdi-file-document' },
]

const moreItems = computed(() => {
    const items = [
        { label: 'Notícias', to: '/noticias', icon: 'mdi-newspaper-variant-outline' },
        { label: 'Projetos', to: '/projetos', icon: 'mdi-folder-multiple-outline' },
        { label: 'Transparência', to: '/transparencia', icon: 'mdi-shield-outline' },
        { label: 'Os Ouvintes', to: '/ouvintes', icon: 'mdi-forum-outline' },
        { label: 'Conhecendo sua escola', to: '/mapa', icon: 'mdi-map-outline' },
    ]
    return items
})

const sheetOpen = ref(false)

function isActive(to) {
    return route.path === to
}

const isMoreActive = computed(() => moreItems.value.some((item) => isActive(item.to)))

function fechar() {
    sheetOpen.value = false
}
</script>

<template>
    <nav class="bottom-nav" role="navigation" aria-label="Navegação principal">
        <router-link v-for="item in primaryItems" :key="item.to" :to="item.to" class="bottom-nav-item"
            :class="{ 'bottom-nav-item-active': isActive(item.to) }">
            <v-icon size="21">{{ isActive(item.to) ? item.iconActive : item.icon }}</v-icon>
            <span>{{ item.label }}</span>
        </router-link>

        <button type="button" class="bottom-nav-item" :class="{ 'bottom-nav-item-active': isMoreActive || sheetOpen }"
            @click="sheetOpen = true">
            <v-icon size="21">mdi-menu</v-icon>
            <span>Mais</span>
        </button>
    </nav>

    <v-bottom-sheet v-model="sheetOpen">
        <div class="more-sheet">
            <div class="more-sheet-handle" />
            <p class="more-sheet-title">Mais opções</p>

            <router-link v-for="item in moreItems" :key="item.to" :to="item.to" class="more-sheet-item"
                :class="{ 'more-sheet-item-active': isActive(item.to) }" @click="fechar">
                <v-icon size="19" :color="isActive(item.to) ? '#1a3f8f' : '#5a6a85'">{{ item.icon }}</v-icon>
                <span>{{ item.label }}</span>
            </router-link>

            <div class="more-sheet-divider" />

            <div class="more-sheet-admin">
                <AdminCard />
            </div>
        </div>
    </v-bottom-sheet>
</template>

<style scoped>
.bottom-nav {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 20;
    display: flex;
    align-items: stretch;
    background: #ffffff;
    border-top: 1px solid rgba(13, 31, 60, 0.08);
    box-shadow: 0 -2px 10px rgba(13, 31, 60, 0.06);
    padding-bottom: env(safe-area-inset-bottom, 0);
    font-family: 'DM Sans', sans-serif;
}

.bottom-nav-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
    padding: 8px 4px 10px;
    border: none;
    background: transparent;
    color: #8a95ab;
    text-decoration: none;
    font-size: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: color 0.15s ease;
}

.bottom-nav-item span {
    white-space: nowrap;
}

.bottom-nav-item-active {
    color: #1a3f8f;
}

.more-sheet {
    background: #ffffff;
    border-radius: 20px 20px 0 0;
    padding: 8px 8px calc(20px + env(safe-area-inset-bottom, 0));
    font-family: 'DM Sans', sans-serif;
}

.more-sheet-handle {
    width: 36px;
    height: 4px;
    border-radius: 999px;
    background: rgba(13, 31, 60, 0.15);
    margin: 4px auto 12px;
}

.more-sheet-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #5a6a85;
    padding: 0 12px;
    margin: 0 0 8px;
}

.more-sheet-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px;
    border-radius: 12px;
    color: #0d1f3c;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
}

.more-sheet-item:hover,
.more-sheet-item-active {
    background: rgba(26, 63, 143, 0.08);
    color: #1a3f8f;
}

.more-sheet-divider {
    height: 1px;
    background: rgba(13, 31, 60, 0.08);
    margin: 8px 12px;
}

.more-sheet-admin {
    padding: 4px 12px 0;
}
</style>