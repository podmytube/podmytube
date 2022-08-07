<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewCategories extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
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
        return $this->view('emails.newCategories')->with(
            'podmytubeLogo',
            $this->logo
        );
    }
}
