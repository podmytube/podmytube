<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeToPodmytube extends Mailable
{
    use Queueable, SerializesModels;

    /** App\User $user model */
    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = __('emails.welcome_aboard');
        return $this->subject($subject)
            ->view('emails.welcome')
            ->with(['subject' => $subject, 'user' => $this->user]);
    }
}
