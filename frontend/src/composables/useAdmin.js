import { computed } from 'vue'

/**
 * useAdmin
 * Detecta se o usuário logado (localStorage.usuario) possui papel de administrador.
 * Centraliza a lógica antes duplicada em AdminCard.vue, menuLateral.vue,
 * Gabarito.vue e Ouvintes.vue.
 */
export function useAdmin() {
  const isAdmin = computed(() => {
    const raw = localStorage.getItem('usuario')
    if (!raw) return false

    try {
      const parsed = JSON.parse(raw)
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
      // fallback para valores simples armazenados como texto
    }

    return String(raw).toLowerCase().includes('admin')
  })

  return { isAdmin }
}
