<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApiSendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fromAuthor, $subject, $tpl, $prize = null)
    {
        $this->fromAuthor = $fromAuthor;
        $this->subject = $subject;
        $this->tpl = 'mails.send.'.$tpl;
        $this->prize = $prize;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('app@appetite.link', $this->fromAuthor)
                    ->subject($this->subject)
                    ->markdown($this->tpl);
    }
}
