<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

/**
 * E-mail de redefinição de senha no layout institucional ABSL
 * (resources/views/emails/institucional.blade.php).
 *
 * O link aponta para a SPA (FRONTEND_URL/redefinir-senha), que envia o
 * token + e-mail para POST /api/reset-password.
 */
class RedefinirSenhaEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $assunto = 'Redefinição de senha — ABSL';

    public string $preheader = 'Use o link para criar uma nova senha no ABSL.';

    public string $saudacao = 'Olá,';

    public string $nome;

    public string $titulo = 'Redefina sua senha';

    public string $mensagem;

    public string $ctaTexto = 'REDEFINIR SENHA';

    public string $ctaUrl;

    public bool $temLogo = false;

    public string $logoPath;

    public ?string $conteudo = null;

    public ?string $rodape = 'Se você não pediu a redefinição, ignore este e-mail: sua senha continua a mesma.';

    /** @var array<int, array{label: string, valor: string}> */
    public array $informacoes = [];

    public function __construct(User $user, string $token)
    {
        $expiracao = (int) config('auth.passwords.users.expire', 60);

        $this->logoPath = public_path('images/logo.png');
        $this->temLogo = file_exists($this->logoPath);

        $this->ctaUrl = rtrim(config('app.frontend_url'), '/')
            .'/redefinir-senha?token='.urlencode($token)
            .'&email='.urlencode($user->getEmailForPasswordReset());

        $this->nome = $user->name;
        $this->mensagem = "Recebemos um pedido para redefinir a senha da sua conta no ABSL.\n"
            ."Clique no botão abaixo para criar uma nova senha. O link expira em {$expiracao} minutos.";

        $this->informacoes = [['label' => 'E-mail', 'valor' => $user->email]];

        $this->subject($this->assunto);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.institucional');
    }
}
