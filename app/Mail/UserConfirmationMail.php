<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;

    public function __construct($user, $verificationUrl)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
    }

    public function build()
    {
        $view = match ($this->user->current_team_id) {
            1 => 'emails.user_confirmation_ro',
            2 => 'emails.user_confirmation_hu',
            3 => 'emails.user_confirmation_sl',
            default => 'emails.user_confirmation',
        };

        return $this->view($view)
            ->with([
                'user' => $this->user,
                'verificationUrl' => $this->verificationUrl,
            ])
            ->subject('Confirm Your Email Address');
    }
}
