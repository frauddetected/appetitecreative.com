<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $password, $project, $author, $subject, $tpl)
    {
        $this->user = $user;
        $this->password = $password;
        $this->project = $project;
        $this->author = $author;
        $this->subject = $subject;
        $this->tpl = 'mails.auth.reset.'.$tpl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('app@appetite.link', $this->author)
                    ->subject($this->subject)
                    ->markdown($this->tpl, [
                        'user' => $this->user,
                        'password' => $this->password,
                        'project' => $this->project
                    ]);
    }
}
