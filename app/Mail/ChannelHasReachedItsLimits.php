<?php

namespace App\Mail;

use App\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChannelHasReachedItsLimits extends Mailable
{
    use Queueable, SerializesModels;

    protected $media;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(
            __('emails.limitsReached_subject', [
                'media_title' => $this->media->title,
            ])
        )
            ->view('emails.channelHasReachedItsLimits')
            ->with([
                'media' => $this->media,
                'channel' => $this->media->channel,
                'user' => $this->media->channel->user,
            ]);
    }
}
