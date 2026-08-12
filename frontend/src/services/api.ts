import axios from 'axios'
import { token } from '@/stores/auth'

const api = axios.create({
    baseURL: '/api'
})

api.interceptors.request.use((config)=>{
        const t = token?.value || ''
        if (t) {
                config.headers.Authorization = `Bearer ${t}`
        }
    return config
})

export default api