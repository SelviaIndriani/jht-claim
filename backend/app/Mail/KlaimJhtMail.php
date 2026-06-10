<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KlaimJhtMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $klaimData) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Pengajuan Klaim JHT - ' . $this->klaimData['no_klaim'],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.klaim-jht',
            with: [
                'klaim' => $this->klaimData,
            ],
        );
    }
}
