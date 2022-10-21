<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class YouHaveNewReferralMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    public $subject;
    public User $referrer;

    public function __construct(public Channel $channel)
    {
        $this->referrer = $this->channel->user->referrer;
    }

    public function build()
    {
        $this->subject = 'Congratulations ' . $this->referrer->firstname;

        return $this->view('emails.youHaveNewReferral');
    }
}
