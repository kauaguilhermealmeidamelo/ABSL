import { computed } from 'vue'
import { user } from '@/stores/auth'
/**
 * useAdmin
 * Detecta se o usuário logado (localStorage.usuario) possui papel de administrador.
 * Centraliza a lógica antes duplicada em AdminCard.vue, menuLateral.vue,
 * Gabarito.vue e Ouvintes.vue.
 */
export function useAdmin() {
  const isAdmin = computed(() => {
    const parsed = user.value
    if (!parsed) return false
    try {
      if (parsed && typeof parsed === 'object') {
        const role = String(
          parsed.role || parsed.tipo || parsed.perfil || parsed.is_admin || parsed.administrador || ''
        ).toLowerCase()
        return (
          role === 'admin' ||
          role === 'administrator' ||
          role === 'administrador' ||
          role === 'super_admin' ||
          role === 'super-admin' ||
          parsed.is_admin === true ||
          parsed.administrador === true
        )
      }
    } catch {
      // fallback
    }
    return String(parsed).toLowerCase().includes('admin')
  })

  return { isAdmin }
}
