<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\TeamSettingsRepository;

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
        $teamId = $this->user->current_team_id;
        $customHtml = (new TeamSettingsRepository($teamId, 'emails'))->getSetting('email_user_confirmation');

        if ($customHtml) {
            $html = str_replace(
                ['{first_name}', '{verification_url}', '{year}'],
                [$this->user->first_name, $this->verificationUrl, date('Y')],
                $customHtml
            );
            return $this->html($html)->subject('Confirm Your Email Address');
        }

        $view = match ($teamId) {
            1 => 'emails.user_confirmation_ro',
            2 => 'emails.user_confirmation_hu',
            3 => 'emails.user_confirmation_sl',
            4 => 'emails.user_confirmation_au',
            5 => 'emails.user_confirmation_ua',
            6 => 'emails.user_confirmation_cz',
            7 => 'emails.user_confirmation_rs',
            8 => 'emails.user_confirmation_sk',
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
