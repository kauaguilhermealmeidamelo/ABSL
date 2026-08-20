export const CATEGORIAS = {
  entrada: { label: 'Entrada', cor: '#1B63BD' },
  administracao: { label: 'Administração', cor: '#16509B' },
  ensino: { label: 'Salas de Aula / Biblioteca', cor: '#F5A623' },
  natureza: { label: 'Área Verde', cor: '#6B8F71' },
  esporte: { label: 'Esportes e Lazer', cor: '#5A6A85' },
  saude: { label: 'Enfermaria', cor: '#D97706' },
  estacionamento: { label: 'Estacionamento / Piscina', cor: '#8FA3BF' },
  gremio: { label: 'Sala do Grêmio (ABSL)', cor: '#0F2038' },
}
 
export const AREAS_MAPA = [
  {
    id: 'entr',
    nome: 'Entrada',
    categoria: 'entrada',
    descricao: 'Portão principal de acesso à escola.',
  },
  {
    id: 'audit',
    nome: 'Auditório',
    categoria: 'administracao',
    descricao: 'Espaço para eventos, palestras e apresentações.',
  },
  {
    id: 'soe',
    nome: 'SOE',
    categoria: 'administracao',
    descricao: 'Serviço de Orientação Educacional — apoio pedagógico aos estudantes.',
  },
  {
    id: 'bibli',
    nome: 'Biblioteca',
    categoria: 'ensino',
    descricao: 'Espaço de estudo, leitura e empréstimo de livros.',
  },
  {
    id: 'secre',
    nome: 'Secretaria',
    categoria: 'administracao',
    descricao: 'Emissão de documentos e atendimento administrativo.',
  },
  {
    id: 'dire',
    nome: 'Direção',
    categoria: 'administracao',
    descricao: 'Direção e coordenação pedagógica da escola.',
  },
  {
    id: 'verde',
    nome: 'Área Verde',
    categoria: 'natureza',
    descricao: 'Área verde e de convivência ao ar livre.',
  },
  {
    id: 'bl1',
    nome: 'Bloco de Salas 1',
    categoria: 'ensino',
    descricao: 'Salas de aula, com banheiros no próprio bloco.',
  },
  {
    id: 'bl2',
    nome: 'Bloco 2 de Salas',
    categoria: 'ensino',
    descricao: 'Salas de aula do 2º bloco.',
  },
  {
    id: 'bl3',
    nome: 'Bloco 3 de Salas',
    categoria: 'ensino',
    descricao: 'Salas de aula do 3º bloco, próximo aos vestiários.',
  },
  {
    id: 'enf',
    nome: 'Enfermaria',
    categoria: 'saude',
    descricao: 'Primeiros socorros e atendimento básico de saúde.',
  },
  {
    id: 'esp',
    nome: 'Espaço Cultural',
    categoria: 'administracao',
    descricao: 'Atividades culturais, exposições e eventos artísticos.',
  },
  {
    id: 'gina',
    nome: 'Ginásio',
    categoria: 'esporte',
    descricao: 'Quadra coberta para educação física e eventos esportivos.',
  },
  {
    id: 'quadras',
    nome: 'Área Esportiva',
    categoria: 'esporte',
    descricao: 'Reúne o PEC, a quadra descoberta, a quadra coberta e o quiosque de convivência.',
  },
  {
    id: 'vest',
    nome: 'Vestiários',
    categoria: 'esporte',
    descricao: 'Vestiários de apoio à quadra e à piscina.',
  },
  {
    id: 's4453',
    nome: 'Salas 44, 53 e 56',
    categoria: 'ensino',
    descricao: 'Salas de aula próximas à piscina.',
  },
  {
    id: 'pisc',
    nome: 'Piscina',
    categoria: 'estacionamento',
    descricao: 'Piscina da escola, com vestiários próprios.',
  },
  {
    id: 'estac',
    nome: 'Estacionamento',
    categoria: 'estacionamento',
    descricao: 'Estacionamento de veículos.',
  },
  {
    id: 'greme',
    nome: 'Sala do Grêmio (ABSL)',
    categoria: 'gremio',
    descricao: 'Sede do Grêmio Estudantil Athos Bulcão — venha nos visitar!',
  },
]
 
export function getArea(id) {
  return AREAS_MAPA.find((a) => a.id === id) ?? null
}