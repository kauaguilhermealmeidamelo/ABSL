# Portal de Transparência Financeira — implementação em andamento

Branch: `kaua/transparencia-financeira`. Não publicar na `main` sem validação.

## Implementado

- Migrations para gestões, movimentações, comprovantes, relatórios mensais e retificações.
- API administrativa protegida por Sanctum + middleware `admin`.
- Cálculo de saldo com valores inteiros em centavos e apenas movimentações confirmadas.
- Publicação de relatórios de meses encerrados e bloqueio de lançamentos retroativos em meses publicados.
- Encerramento de gestão restrito a administradores e condicionado a ausência de pendências e relatórios dos meses com movimentação.
- Nova gestão exige saldo inicial igual ao saldo final da gestão anterior encerrada.
- Upload privado de comprovantes com download exclusivo para administradores.
- Páginas públicas de estatuto/regimento, atas e prestação de contas.
- Painel administrativo básico e testes de integração iniciais.

## Pendências obrigatórias antes de produção

1. Executar `php artisan test`, `php artisan route:list`, `php artisan migrate:fresh --env=testing` em banco de testes e `npm run build`; corrigir eventuais falhas.
2. Implementar retificação excepcional com justificativa, autorização, versionamento dos relatórios e trilha de auditoria imutável. A tabela de retificações existe, mas o fluxo ainda não.
3. Implementar publicação de cópias redigidas dos comprovantes para consulta pública, sem dados pessoais; os arquivos originais devem permanecer privados.
4. Revisar a regra de fechamento de gestão sem movimentações e a transferência formal do saldo, com recibo/termo de passagem.
5. Revisar limites de período, relatórios anuais, paginação e categorias financeiras.
6. Revisar a política de retenção documental antes de automatizar qualquer exclusão.
7. Validar UX responsiva, acessibilidade, autorização e segurança de upload.
8. Verificar documentos antigos de Transparência antes de qualquer correção de armazenamento. Nunca excluir ou sincronizar destrutivamente `storage/app` no servidor.

## Observações

Os relatórios são snapshots de valores apurados; a publicação impede novas movimentações confirmadas naquele mês. A interface atual exibe totais públicos por mês, mas ainda não disponibiliza comprovantes públicos nem relatórios anuais consolidados.

A branch não está pronta para merge ou deploy.
