<?php

namespace App\Jobs;

use App\Mail\KlaimJhtMail;
use App\Models\KlaimJht;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendKlaimJhtConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 300;  // 5 minutes

    public function __construct(
        protected KlaimJht $klaim,
        protected array $emailData = []
    ) {}

    public function handle(): void
    {
        try {
            Mail::to($this->klaim->email)->send(new KlaimJhtMail($this->emailData));
        } catch (\Exception $e) {
            \Log::error('Failed to send klaim confirmation email', [
                'klaim_id'     => $this->klaim->id,
                'no_klaim'     => $this->klaim->no_klaim,
                'email'        => $this->klaim->email,
                'error'        => $e->getMessage(),
                'request_id'   => request()->attributes->get('request_id'),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Log::critical('Email job permanently failed', [
            'klaim_id'   => $this->klaim->id,
            'no_klaim'   => $this->klaim->no_klaim,
            'email'      => $this->klaim->email,
            'error'      => $exception->getMessage(),
            'attempts'   => $this->attempts(),
        ]);
    }
}
