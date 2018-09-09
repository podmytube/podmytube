<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class WelcomeToPodmytube extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $logo = '/home/www/dashboard.podmytube.com/public/images/logo-small.png';

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
        
        return $this->from('frederick@podmytube.com')
            ->view('emails.welcome')
            ->with('podmytubeLogo', $this->logo);

    }
}
