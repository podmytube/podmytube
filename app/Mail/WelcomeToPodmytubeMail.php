<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeToPodmytubeMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    public $subject;

    public function __construct(public User $user)
    {
    }

    public function build()
    {
        $this->subject = 'Welcome on Podmytube, ' . $this->user->firstname;

        return $this->view('emails.welcome');
    }
}
