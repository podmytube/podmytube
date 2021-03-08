<?php

namespace App\Mail;

use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * This mailable is sent on every first day of month.
 * It will display a list of medias for the user. Those grabbed and not grabbed.
 * With a call to action : upgrade your plan (if one (at least)) episode is not grabbed.
 */
class Newsletter extends Mailable
{
    use Queueable, SerializesModels;

    /** @var App\User $user */
    protected $user;

    /** @var string $newsletter the body of the newsletter */
    protected $newsletterBody;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $newsletterBody)
    {
        $this->user = $user;
        $this->newsletterBody = $newsletterBody;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $period = Carbon::now()->locale('en')->monthName . ' ' . date('Y');

        $subject = "Podmytube Newsletter - {$period}";
        return $this->subject($subject)
            ->view('emails.newsletter')
            ->with([
                'subject' => $subject,
                'newsletterBody' => $this->newsletterBody,
                'user' => $this->user,
            ]);
    }
}
