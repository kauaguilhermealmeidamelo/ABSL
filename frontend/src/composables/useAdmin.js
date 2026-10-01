import { computed } from 'vue'
import { user } from '@/stores/auth'

export function useAdmin() {
  const role = computed(() => {
    const u = user.value
    if (!u) return null
    return String(u.role || (u.is_admin ? 'admin' : 'user')).toLowerCase()
  })

  // Pode acessar o painel e os módulos operacionais.
  const isStaff = computed(() => role.value === 'admin' || role.value === 'imprensa')

  // Mantido como alias para componentes existentes que usam isAdmin como acesso ao painel.
  const isAdmin = isStaff

  // Somente administrador pode gerenciar contas e logs.
  const isSuperAdmin = computed(() => role.value === 'admin')

  return { isAdmin, isStaff, isSuperAdmin }
}