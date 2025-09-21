<?php

namespace App\Mail;

use App\Models\Candidatura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NovaCandidaturaMail extends Mailable
{
    use Queueable, SerializesModels;

    public Candidatura $candidatura;

    public function __construct(Candidatura $candidatura)
    {
        $this->candidatura = $candidatura;
    }

    public function build()
    {
        return $this->subject('Nova Candidatura: '.$this->candidatura->nome)
            ->markdown('emails.nova_candidatura')
            ->attachFromStorage($this->candidatura->curriculo_path);
    }
}