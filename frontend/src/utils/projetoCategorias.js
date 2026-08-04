// Categorias (diretorias) usadas na tela de Projetos.
// Centralizado aqui para reaproveitar entre o seletor de diretorias,
// o formulário de projeto e o diálogo de detalhe — evita repetir a
// mesma lista/emoji em vários componentes.

export const PROJECT_CATS = [
  'Presidência',
  'Secretaria',
  'Tesouraria',
  'Esporte e Lazer',
  'Cultura',
  'Políticas Educacionais',
  'Saúde e Meio Ambiente',
  'Diretoria Social',
  'Imprensa e Comunicação',
  'Tecnologia e Inovação',
]

export const CAT_EMOJI = {
  'Presidência': '👑',
  'Secretaria': '📋',
  'Tesouraria': '🔥',
  'Esporte e Lazer': '🌐',
  'Cultura': '🎭',
  'Políticas Educacionais': '📚',
  'Saúde e Meio Ambiente': '🌱',
  'Diretoria Social': '🤝',
  'Imprensa e Comunicação': '📢',
  'Tecnologia e Inovação': '💻',
}

export function catEmoji(categoria) {
  return CAT_EMOJI[categoria] ?? '📁'
}
