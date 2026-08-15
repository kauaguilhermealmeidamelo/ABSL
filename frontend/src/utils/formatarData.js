// Formata datas ISO (vindas do backend/MySQL, ex: "2026-08-12T00:00:00.000000Z")
// para o padrão brasileiro dd/mm/aaaa. Centralizado aqui para reaproveitar
// entre NoticiaCard, NoticiaDetalhe e qualquer outra tela que precise
// exibir uma data — evita repetir a mesma lógica de parse em cada componente.
export function formatarDataBR(data) {
  if (!data) return ''
  const dt = new Date(data)
  if (Number.isNaN(dt.getTime())) return ''
  return dt.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' })
}