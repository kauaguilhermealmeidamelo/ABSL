<script setup>
import menuLateral from '@/components/menuLateral.vue'
import BottomNav from '@/components/BottomNav.vue'
import { onMounted, ref } from 'vue'
import { useDisplay } from 'vuetify'
import api from '@/services/api'
import { team } from '@/stores/appData'


const { mobile } = useDisplay()

const msg = ref('servidor off')
const dividerItems = Array.from({ length: 36 }, (_, i) => i % 3)

onMounted(async () => {
  try {
    const response = await api.get('/teste')
    msg.value = response.data.mensagem
  } catch (error) {
    console.error(error)
  }
})
</script>

<template>
  <v-app>
    <menuLateral v-if="!mobile" :model-value="true" :mobile="false" />

    <v-main :class="{ 'has-bottom-nav': mobile }">
      <div class="geo-divider" role="presentation" aria-hidden="true">
        <div class="divider-strip">
          <span
            v-for="(item, index) in dividerItems"
            :key="index"
            :class="['divider-item', `divider-item-${item}`]"
          />
        </div>
      </div>
      <router-view />
    </v-main>

    <BottomNav v-if="mobile" />
  </v-app>
</template>

<style scoped>
.has-bottom-nav {
  padding-bottom: calc(64px + env(safe-area-inset-bottom, 0));
}

.geo-divider {
  width: 100%;
  padding: 0.85rem 0;
  background-color: rgba(238, 241, 246, 0.75);
  border-bottom: 1px solid rgba(15, 32, 56, 0.06);
  overflow-x: auto;
  overflow-y: hidden;
  scrollbar-width: none;
}

.geo-divider::-webkit-scrollbar {
  display: none;
}

.divider-strip {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  flex-wrap: nowrap;
  gap: 0.55rem;
  padding: 0 1rem;
  width: max-content;
}

.divider-item {
  display: inline-flex;
  width: 10px;
  height: 10px;
  flex-shrink: 0;
}

.divider-item-0 {
  border-radius: 50%;
  background: rgba(115, 118, 130, 0.55);
}

.divider-item-1 {
  border-radius: 2px;
  background: rgba(22, 80, 155, 0.55);
}

.divider-item-2 {
  width: 10px;
  height: 10px;
  transform: rotate(45deg);
  background: rgba(245, 197, 24, 0.55);
  border-radius: 2px;
}

.divider-item:nth-child(3n) {
  margin-right: 1.2rem;
}

@media (max-width: 720px) {
  .geo-divider {
    padding: 0.5rem 0;
  }
  .divider-strip {
    padding: 0 0.75rem;
    gap: 0.4rem;
  }
  .divider-item {
    width: 7px;
    height: 7px;
  }
}
</style>