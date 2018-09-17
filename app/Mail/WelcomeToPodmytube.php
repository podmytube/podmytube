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
    public $logo;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->logo = public_path('images/logo-small.png');
        $this->user = $user;        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        
        return $this->view('emails.welcome')
            ->with('podmytubeLogo', $this->logo);

    }
}
