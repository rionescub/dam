<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $resetUrl)
    {
        $this->user = $user;
        $this->resetUrl = $resetUrl;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('emails.password_reset')
                    ->subject('Reset Your Password')
                    ->with([
                        'user' => $this->user,
                        'resetUrl' => $this->resetUrl,
                    ]);
    }
}
