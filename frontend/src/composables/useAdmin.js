import { computed } from 'vue'
import { user } from '@/stores/auth'

export function useAdmin() {
  const role = computed(() => {
    const u = user.value
    if (!u) return null
    return String(u.role || (u.is_admin ? 'admin' : 'user')).toLowerCase()
  })

  // Pode editar conteúdo (notícias, projetos, cardápio, etc.)
  const isAdmin = computed(() => role.value === 'admin' || role.value === 'imprensa')

  // Pode gerenciar contas de usuário (aba "Administradores")
  const isSuperAdmin = computed(() => role.value === 'admin')

  return { isAdmin, isSuperAdmin }
}