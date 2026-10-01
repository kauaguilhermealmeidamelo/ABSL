<?php

namespace Database\Seeders;

use App\Models\Diretoria;
use App\Models\Noticia;
use App\Models\Projeto;
use App\Models\Transparencia;
use App\Models\Turma;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminPassword = env('SEED_ADMIN_PASSWORD');

        if (! $adminPassword) {
            $adminPassword = Str::random(16);

            $this->command?->warn(
                "SEED_ADMIN_PASSWORD não definida — senha gerada para admin@absl.local: {$adminPassword}"
            );
        }

        $admin = User::updateOrCreate(
            ['email' => 'admin@absl.local'],
            [
                'name' => 'Admin ABSL',
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
                'is_admin' => true,
            ]
        );

        /*
         * Conteúdo demonstrativo é opt-in para evitar que dados fictícios
         * sejam publicados acidentalmente em ambientes reais.
         *
         * .env:
         * SEED_DEMO_CONTENT=true
         */
        if (filter_var(env('SEED_DEMO_CONTENT', false), FILTER_VALIDATE_BOOLEAN)) {
            $this->seedDemoContent($admin);
        }
    }

    private function seedDemoContent(User $admin): void
    {
        $noticias = [
            [
                'titulo' => 'Conteúdo demonstrativo — Notícias do Grêmio',
                'categoria' => 'Grêmio',
                'descricao' => 'Exemplo de notícia para validar a listagem e a página inicial durante o desenvolvimento.',
                'conteudo' => 'Este registro é demonstrativo e deve ser substituído por conteúdo institucional real antes da publicação.',
                'destaque' => true,
            ],
            [
                'titulo' => 'Conteúdo demonstrativo — Vida escolar',
                'categoria' => 'Escola',
                'descricao' => 'Exemplo de publicação relacionada à comunidade escolar.',
                'conteudo' => 'Este registro é demonstrativo. O conteúdo definitivo deve ser cadastrado pela equipe responsável.',
                'destaque' => false,
            ],
            [
                'titulo' => 'Conteúdo demonstrativo — Cultura e comunidade',
                'categoria' => 'Cultura',
                'descricao' => 'Exemplo de notícia para testar categorias, filtros e cards.',
                'conteudo' => 'Este registro é demonstrativo e não representa um evento ou ação real.',
                'destaque' => false,
            ],
            [
                'titulo' => 'Conteúdo demonstrativo — Participação estudantil',
                'categoria' => 'Cidadania',
                'descricao' => 'Exemplo de publicação sobre participação e diálogo na comunidade escolar.',
                'conteudo' => 'Este registro é demonstrativo e deve ser substituído por informação verificada.',
                'destaque' => false,
            ],
        ];

        foreach ($noticias as $noticia) {
            Noticia::updateOrCreate(
                ['titulo' => $noticia['titulo']],
                [
                    ...$noticia,
                    'imagem_url' => null,
                    'ativo' => true,
                    'autor_id' => $admin->id,
                    'data_publicacao' => now(),
                ]
            );
        }

        $projetos = [
            [
                'nome' => 'Projeto demonstrativo — Participação estudantil',
                'descricao' => 'Registro de exemplo para validar a vitrine de projetos do ABSL.',
                'categoria' => 'Participação',
                'diretoria' => null,
                'conteudo_detalhado' => 'Conteúdo demonstrativo. Substitua pelos dados, objetivos, período e resultados de um projeto real.',
                'status' => 'em_andamento',
                'destaque' => true,
            ],
            [
                'nome' => 'Projeto demonstrativo — Cultura',
                'descricao' => 'Registro de exemplo para testar projetos culturais.',
                'categoria' => 'Cultura',
                'diretoria' => null,
                'conteudo_detalhado' => 'Conteúdo demonstrativo. Não representa uma iniciativa real.',
                'status' => 'em_andamento',
                'destaque' => true,
            ],
            [
                'nome' => 'Projeto demonstrativo — Comunidade',
                'descricao' => 'Registro de exemplo para testar a integração entre projetos e comunidade.',
                'categoria' => 'Comunidade',
                'diretoria' => null,
                'conteudo_detalhado' => 'Conteúdo demonstrativo. Deve ser substituído por informações verificadas.',
                'status' => 'concluido',
                'destaque' => false,
            ],
            [
                'nome' => 'Projeto demonstrativo — Esporte',
                'descricao' => 'Registro de exemplo para testar a categoria de esporte.',
                'categoria' => 'Esporte',
                'diretoria' => null,
                'conteudo_detalhado' => 'Conteúdo demonstrativo para ambiente de desenvolvimento.',
                'status' => 'em_andamento',
                'destaque' => false,
            ],
        ];

        foreach ($projetos as $projeto) {
            Projeto::updateOrCreate(
                ['nome' => $projeto['nome']],
                [
                    ...$projeto,
                    'imagem_url' => null,
                    'responsavel_id' => $admin->id,
                ]
            );
        }

        $turmas = [
            ['codigo' => 'DEMO-1', 'turno' => 'Matutino', 'ano' => '1º ano'],
            ['codigo' => 'DEMO-2', 'turno' => 'Vespertino', 'ano' => '2º ano'],
            ['codigo' => 'DEMO-3', 'turno' => 'Matutino', 'ano' => '3º ano'],
        ];

        foreach ($turmas as $turma) {
            Turma::updateOrCreate(
                ['codigo' => $turma['codigo']],
                [
                    ...$turma,
                    'criado_por' => $admin->id,
                    'ativo' => true,
                ]
            );
        }

        $diretorias = [
            [
                'name' => 'Diretoria demonstrativa — Comunicação',
                'icon' => null,
                'members' => [
                    ['nome' => 'Membro demonstrativo', 'cargo' => 'Responsável'],
                ],
            ],
            [
                'name' => 'Diretoria demonstrativa — Projetos',
                'icon' => null,
                'members' => [
                    ['nome' => 'Membro demonstrativo', 'cargo' => 'Responsável'],
                ],
            ],
        ];

        foreach ($diretorias as $diretoria) {
            Diretoria::updateOrCreate(
                ['name' => $diretoria['name']],
                [
                    ...$diretoria,
                    'criado_por' => $admin->id,
                    'ativo' => true,
                ]
            );
        }

        $transparencias = [
            [
                'titulo' => 'Documento demonstrativo — Transparência',
                'descricao' => 'Registro de exemplo para validar a área de transparência.',
                'categoria' => 'administrativo',
                'tipo_documento' => 'documento',
            ],
            [
                'titulo' => 'Documento demonstrativo — Projeto',
                'descricao' => 'Registro de exemplo para testar documentos relacionados a projetos.',
                'categoria' => 'projetos',
                'tipo_documento' => 'relatório',
            ],
        ];

        foreach ($transparencias as $documento) {
            Transparencia::updateOrCreate(
                ['titulo' => $documento['titulo']],
                [
                    ...$documento,
                    'arquivo_url' => null,
                    'data_documento' => now()->toDateString(),
                    'data_publicacao' => now(),
                    'ativo' => true,
                    'publicado_por' => $admin->id,
                ]
            );
        }

        $this->command?->info('Conteúdo demonstrativo ABSL criado/atualizado.');
    }
}
