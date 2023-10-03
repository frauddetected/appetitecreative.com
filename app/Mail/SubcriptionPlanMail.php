<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubcriptionPlanMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $contact)
    {
        $this->user = $user;
        $this->contact = $contact;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.send.contact', [
            'user' => $this->user,
            'contact' => $this->contact
        ])->subject($this->contact->subject);
        // return $this->view('mails.send.contact', [
        //     'user' => $this->user,
        //     'contact' => $this->contact
        // ]);
    }

    public function from($address, $name = null)
    {
        return $this->from($this->user->email, $this->contact->name);
    }
}
