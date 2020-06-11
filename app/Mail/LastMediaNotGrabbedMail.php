<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LastMediaNotGrabbedMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $channelsInTrouble = [];
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $channelsInTrouble)
    {
        $this->channelsInTrouble = $channelsInTrouble;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject =
            '[Alert] Following channels are in trouble with their last episode (at least).';
        return $this->subject($subject)
            ->view('emails.mediaNotGrabbedWarning')
            ->with([
                'mailTitle' => $subject,
                'channelsInTrouble' => $this->channelsInTrouble,
            ]);
    }
}
