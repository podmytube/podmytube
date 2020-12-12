<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class ExceptionEmail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var string $content */
    public string $content;

    /** @var string $css */
    public string $css;

    /**
     * Create a new message instance.
     *
     * @param array $content should contain css and content keys
     */
    public function __construct(array $content)
    {
        $this->css = $content['css'];
        $this->content = $content['content'];
        $this->subject = '[Alert] Exception logged on ' . App::environment();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.exception')
            ->with([
                'css' => $this->css,
                'content' => $this->content,
            ]);
    }
}
