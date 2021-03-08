<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChannelIsInTroubleWarningMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var array $channelInTroubleMessages */
    public $channelInTroubleMessages = [];
    /** @var string $mailTitle */
    public $mailTitle;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $channelInTroubleMessages)
    {
        $this->channelInTroubleMessages = $channelInTroubleMessages;
        $this->mailTitle = 'Following channels are in trouble.';
        $this->subject = "[Alert] {$this->mailTitle}";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.channelIsInTrouble')
            ->with([
                'mailTitle' => $this->mailTitle,
                'channelInTroubleMessages' => $this->channelInTroubleMessages,
            ]);
    }
}
