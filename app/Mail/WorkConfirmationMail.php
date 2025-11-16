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
        $view = match ($this->user->current_team_id) {
            1 => 'emails.work_confirmation_ro',
            2 => 'emails.work_confirmation_hu',
            3 => 'emails.work_confirmation_sl',
            4 => 'emails.work_confirmation_au',
            5 => 'emails.work_confirmation_ua',
            6 => 'emails.work_confirmation_cz',
            default => 'emails.work_confirmation_au',
        };

        return $this->view($view)
            ->with([
                'user' => $this->user,
            ])
            ->subject('Welcome to Danube Art Master');
    }
}
