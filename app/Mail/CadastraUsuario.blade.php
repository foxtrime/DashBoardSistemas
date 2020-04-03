<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\helpers\geral;
use App\Models\User;


class CadastraUsuario extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $senha;

    public function __construct(User $user, $senha)
    {
        $this->user  = $user;
        $this->senha = $senha;
    }
    
    
    public function build()
    {
        return $this->markdown('email.usuario.cadastra')
                    ->subject("Cadastro de Novo Usuário")
                    ->with(['user' => $this->user, 'senha' => $this->senha]);
    }
}
