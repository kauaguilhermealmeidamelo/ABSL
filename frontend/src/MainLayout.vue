  <script setup>
  import menuLateral from '@/components/menuLateral.vue'
  import { onMounted, ref } from 'vue';
  import api from '@/services/api'


  const msg = ref('servidor off')

  onMounted(async()=>{
    try{
      const response = await api.get('/teste')
      msg.value = response.data.mensagem
    }catch(error){
      console.error(error)
    }
    
  })
  </script> 
  <template>
    <v-app>
      <menuLateral></menuLateral>
      <v-main>
        <router-view/>
      </v-main>
    </v-app>
  </template>
  <style scoped>

  </style>
