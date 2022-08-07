<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeToPodmytube extends Mailable
{
    use Queueable;
    use SerializesModels;

    /** App\Models\User $user model */
    protected $user;

    /**
     * Create a new message instance.
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
            ->with(['subject' => $subject, 'user' => $this->user])
        ;
    }
}
