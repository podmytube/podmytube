<?php

namespace App\Mail;

use App\user;
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
        $period =
            Carbon::now()->locale($this->user->preferredLocale())->monthName .
            ' ' .
            date('Y');

        $subject = __('emails.newsletter_subject', [
            'period' => $period,
        ]);

        return $this->view('emails.newsletter')
            ->subject($subject)
            ->with([
                'subject' => $subject,
                'user' => $this->user,
            ]);
    }
}
