import { ref } from 'vue'

// TODO: substituir por chamada à API quando o endpoint de notícias existir no backend
const noticias = ref([
    { id: 1, categoria: 'gremio', data_publicacao: '14 de março de 2026', titulo: 'Resultado da eleição: chapa Movimento assume o grêmio', texto: 'Com 71% dos votos e alta participação, a nova gestão foi eleita em votação aberta no pátio da escola.' },
    { id: 2, categoria: 'gremio', data_publicacao: '03 de abril de 2026', titulo: 'Feira Cultural ABSL reúne 20 turmas no ginásio', texto: 'Apresentações de teatro, dança e exposição de artes marcaram a primeira edição do ano, organizada pela Diretoria de Cultura.' },
    { id: 3, categoria: 'gremio', data_publicacao: '12 de abril de 2026', titulo: 'Torneio Interclasses abre inscrições para o 1º semestre', texto: 'Futebol, vôlei e handebol. Inscrições até o dia 20, feitas com o representante de cada turma.' },
    { id: 4, categoria: 'gremio', data_publicacao: '28 de maio de 2026', titulo: 'Campanha do Agasalho: escola arrecada mais de 400 peças', texto: 'Em parceria com a Diretoria Social, a campanha bateu recorde e as doações foram entregues à ONG Recomeçar.' },
    { id: 5, categoria: 'gremio', data_publicacao: '10 de junho de 2026', titulo: 'Grêmio leva pauta de reforma do refeitório à direção', texto: 'Após 340 assinaturas de alunos, a presidência entregou documento formal e aguarda resposta até o fim do semestre.' },
    { id: 6, categoria: 'escola', data_publicacao: '18 de junho de 2026', titulo: 'Semana da Saúde Mental: atividades gratuitas para todos', texto: 'Rodas de conversa, meditação guiada e atendimento com profissionais durante os intervalos da semana.' },
    { id: 7, categoria: 'escola', data_publicacao: '22 de junho de 2026', titulo: 'Calendário de provas do 2º trimestre é divulgado', texto: 'A coordenação pedagógica publicou as datas de avaliações de todas as séries. Confira o calendário completo no mural da secretaria.' },
    { id: 8, categoria: 'escola', data_publicacao: '30 de junho de 2026', titulo: 'Escola inaugura novo laboratório de ciências', texto: 'O espaço reformado conta com bancadas equipadas e material atualizado para aulas práticas de Física, Química e Biologia.' },
])

/**
 * useNoticias
 * Estado único (singleton) compartilhado entre a listagem (Noticias.vue)
 * e a página de detalhe (NoticiaDetalhe.vue), já que não há backend ainda.
 */
export function useNoticias() {
    function getById(id) {
        return noticias.value.find((n) => String(n.id) === String(id))
    }

    function adicionar(dados) {
        noticias.value.unshift({ id: Date.now(), ...dados })
    }

    function atualizar(id, dados) {
        noticias.value = noticias.value.map((n) => (n.id === id ? { ...n, ...dados } : n))
    }

    function remover(id) {
        noticias.value = noticias.value.filter((n) => n.id !== id)
    }

    return { noticias, getById, adicionar, atualizar, remover }
}