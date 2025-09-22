<?php

namespace App\Mail;

use App\Models\Candidatura;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NovaCandidaturaMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Candidatura $candidatura) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nova Candidatura: '.$this->candidatura->nome,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.nova_candidatura',
            with: ['candidatura' => $this->candidatura],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromStorage($this->candidatura->curriculo_path)
                ->as($this->candidatura->curriculo_original),
        ];
    }
}