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

            if (!str_contains($html, $this->verificationUrl)) {
                $html .= '<p><a href="' . $this->verificationUrl . '">' . $this->verificationUrl . '</a></p>';
            }

            return $this->html($html)->subject('Confirm Your Email Address');
        }

        $link = $this->user->currentTeam?->link ?? 'en';

        $view = match ($link) {
            'ro' => 'emails.user_confirmation_ro',
            'hu' => 'emails.user_confirmation_hu',
            'sl' => 'emails.user_confirmation_sl',
            'au' => 'emails.user_confirmation_au',
            'ua' => 'emails.user_confirmation_ua',
            'cz' => 'emails.user_confirmation_cz',
            'rs' => 'emails.user_confirmation_rs',
            'sk' => 'emails.user_confirmation_sk',
            'bih' => 'emails.user_confirmation_bih',
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
