<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\TeamSettingsRepository;

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
        $teamId = $this->user->current_team_id;
        $customHtml = (new TeamSettingsRepository($teamId, 'emails'))->getSetting('email_work_confirmation');

        if ($customHtml) {
            $html = str_replace(
                ['{first_name}', '{year}'],
                [$this->user->first_name, date('Y')],
                $customHtml
            );
            return $this->html($html)->subject('Welcome to Danube Art Master');
        }

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
            'bih' => 'emails.work_confirmation_bih',
            default => 'emails.work_confirmation_en',
        };

        return $this->view($view)
            ->with(['user' => $this->user])
            ->subject('Welcome to Danube Art Master');
    }
}
