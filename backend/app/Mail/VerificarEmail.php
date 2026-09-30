<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

/**
 * E-mail de verificação de e-mail no layout institucional ABSL.
 *
 * Renderiza:
 * resources/views/emails/institucional.blade.php
 *
 * Utiliza a logo localizada em:
 * public/images/logo.png
 */
class VerificarEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $assunto;

    public string $preheader;

    public string $saudacao;

    public string $nome;

    public string $titulo;

    public string $mensagem;

    public string $ctaTexto;

    public string $ctaUrl;

    public bool $temLogo = false;

    public string $logoPath;

    public ?string $conteudo = null;

    public ?string $rodape = null;

    /**
     * @var array<int, array{label: string, valor: string}>
     */
    public array $informacoes = [];

    public function __construct(User $user)
    {
        $expiracao = (int) config(
            'auth.verification.expire',
            60
        );

        /*
         * ============================================================
         * LOGO
         * ============================================================
         *
         * Caminho físico:
         *
         * backend/public/images/logo.png
         *
         * public_path() funciona tanto localmente quanto no servidor.
         */

        $this->logoPath = public_path('images/logo.png');

        $this->temLogo = file_exists($this->logoPath);


        /*
         * ============================================================
         * URL DE VERIFICAÇÃO
         * ============================================================
         *
         * URL assinada que corresponde à rota:
         *
         * /email/verify/{id}/{hash}
         *
         * Expira conforme auth.verification.expire.
         */

        $this->ctaUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes($expiracao),
            [
                'id' => $user->id,
                'hash' => sha1(
                    $user->getEmailForVerification()
                ),
            ]
        );


        /*
         * ============================================================
         * CONTEÚDO DO E-MAIL
         * ============================================================
         */

        $this->assunto = 'Verifique seu e-mail — ABSL';

        $this->preheader =
            'Confirme seu endereço de e-mail para ativar sua conta no ABSL.';

        $this->saudacao = 'Olá,';

        $this->nome = $user->name;

        $this->titulo = 'Verifique seu e-mail';

        $this->mensagem =
            "Para concluir seu cadastro no ABSL, clique no botão abaixo "
            . "para confirmar seu endereço de e-mail.\n"
            . "O link expira em {$expiracao} minutos.";

        $this->ctaTexto = 'VERIFICAR E-MAIL';


        /*
         * ============================================================
         * INFORMAÇÕES DA CONTA
         * ============================================================
         */

        $this->informacoes = array_values(
            array_filter([
                $user->username
                    ? [
                        'label' => 'Conta',
                        'valor' => '@' . $user->username,
                    ]
                    : null,

                [
                    'label' => 'E-mail',
                    'valor' => $user->email,
                ],
            ])
        );


        /*
         * ============================================================
         * ASSUNTO DO E-MAIL
         * ============================================================
         */

        $this->subject($this->assunto);
    }


    /**
     * Define o conteúdo do e-mail.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.institucional'
        );
    }
}