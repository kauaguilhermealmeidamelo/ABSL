# Transparência financeira — backend ABSL

Branch de desenvolvimento: `kaua/transparencia-financeira`. Não publicar em `main` sem validação.

## Acesso

- Públicas: `GET /api/financeiro/gestoes`, `GET /api/financeiro/relatorios`, PDFs e versões de relatórios, declarações publicadas, esclarecimentos e transições publicadas.
- Administrador: planejamento/ativação/encerramento de gestões, cadastro de membros, vínculos, transições e notas posteriores.
- Tesoureiro: movimentações, confirmações, cancelamentos e comprovantes privados na gestão ativa.
- Tesoureiro-Geral titular ou interino válido: operações do Tesoureiro, fechamento/publicação mensal, retificação e conciliação.
- Inatividade: aprovação da Tesouraria-Geral e de Administrador por **duas pessoas distintas**; edições do rascunho invalidam as aprovações.
- Transição administrativa: operações exclusivamente de Administradores, com justificativa de essencialidade e relatório consolidado.

## Regras contábeis

- Valores em centavos inteiros.
- Saldo contábil: saldo inicial + receitas confirmadas - despesas confirmadas.
- Movimentações pendentes e canceladas não afetam o saldo.
- O saldo real conciliado é transferido à próxima gestão; retificações não o alteram automaticamente.
- Fechamento mensal é separado da publicação e inclui meses sem movimentações.
- PDFs de versões publicadas são preservados em `versoes_relatorios_financeiros`; comprovantes permanecem privados.
- Encerramento definitivo exige meses publicados, nenhuma pendência, saldo real conciliado e declaração de inatividade publicada se não houver movimentações confirmadas.
- Após encerramento, somente notas públicas de esclarecimento podem ser acrescentadas.

## Migrações novas

Migrações `2026_11_04_000001` a `2026_11_04_000012` da branch. Executar somente em ambiente de desenvolvimento/staging após backup.

## Validação antes de produção

1. Executar `php artisan migrate --pretend` e revisar alterações do schema.
2. Executar `php artisan migrate` em banco de testes.
3. Executar `php artisan route:list --path=financeiro`.
4. Executar `php artisan test --filter=Financeiro`.
5. Validar PDFs com leitor de PDF, inclusive múltiplas páginas e acentuação.
6. Validar concorrência: fechamento/publicação, confirmação/cancelamento, ativação de gestão e transição.
7. Validar permissões, interinidade, dupla aprovação, imutabilidade e acesso privado aos comprovantes.
8. Não apagar nem sincronizar com exclusão `storage/app` da produção.

**Importante:** os testes e as migrações não foram executados neste ambiente. O backend não está homologado para produção.
