<?php

namespace App\Mail;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Message $contactMessage)
    {
    }

    public function build()
    {
        return $this->subject('Nouveau message — Lova Web')
            ->replyTo($this->contactMessage->email, $this->contactMessage->name)
            ->view('emails.contact');
    }
}