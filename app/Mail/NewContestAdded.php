<?php

namespace App\Mail;

use App\Models\Contest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewContestAdded extends Mailable
{
    use Queueable, SerializesModels;

    public $contest;

    /**
     * Create a new message instance.
     *
     * @param Contest $contest
     * @return void
     */
    public function __construct(Contest $contest)
    {
        $this->contest = $contest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Contest Available!')
            ->view('emails.new_contest')
            ->with(['contest' => $this->contest]);
    }
}
