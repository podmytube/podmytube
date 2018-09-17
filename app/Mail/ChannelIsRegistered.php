<?php

namespace App\Mail;

use App\User;
use App\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChannelIsRegistered extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $channel;
    public $logo;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Channel $channel)
    {
        $this->logo = public_path('images/logo-small.png');
        $this->user = $user;
        $this->channel = $channel;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.channelRegistered')
        ->with('podmytubeLogo', $this->logo);
    }
}
