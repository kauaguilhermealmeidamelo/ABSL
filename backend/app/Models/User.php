<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Mail\VerificarEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'username', 'email', 'password', 'turma'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Envia o e-mail de verificação com o layout institucional ABSL
     * (resources/views/emails/institucional.blade.php) em vez da
     * notificação markdown padrão do Laravel. Usado tanto pelo evento
     * Registered (cadastro) quanto pelo endpoint de reenvio.
     */
    public function sendEmailVerificationNotification(): void
    {
        Mail::to($this)->send(new VerificarEmail($this));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->is_admin === true;
    }

    public function isStaff(): bool
    {
        // "staff" = pode gerenciar conteúdo (notícias, projetos, etc.)
        return $this->isAdmin() || $this->role === 'imprensa';
    }
}
