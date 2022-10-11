<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    public $subject;

    public function __construct(public string $url)
    {
        // code
    }

    public function build()
    {
        $this->subject = 'Verify Email Address';

        return $this->view('emails.verification');
    }
}
