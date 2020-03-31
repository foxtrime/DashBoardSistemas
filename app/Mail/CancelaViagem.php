<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\helpers\geral;
use App\Models\Viagem;
use App\Models\User;


class CancelaViagem extends Mailable
{
    use Queueable, SerializesModels;
    public $viagem;

    public function __construct(Viagem $viagem)
    {
        $this->viagem = $viagem;
    }

    
    public function build()
    {
        return $this->markdown('email.viagem.cancela')
                    ->subject("Solicitação de Viagem")
                    ->with(['viagem' => $this->viagem]);
    }
}
