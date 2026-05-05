<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WorkConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        $link = $this->user->currentTeam?->link ?? 'en';

        $view = match ($link) {
            'ro' => 'emails.work_confirmation_ro',
            'hu' => 'emails.work_confirmation_hu',
            'sl' => 'emails.work_confirmation_sl',
            'au' => 'emails.work_confirmation_au',
            'de' => 'emails.work_confirmation_de',
            'ua' => 'emails.work_confirmation_ua',
            'cz' => 'emails.work_confirmation_cz',
            'rs' => 'emails.work_confirmation_rs',
            'sk' => 'emails.work_confirmation_sk',
            'bs' => 'emails.work_confirmation_bs',
            default => 'emails.work_confirmation_en',
        };

        return $this->view($view)
            ->with(['user' => $this->user])
            ->subject('Welcome to Danube Art Master');
    }
}
