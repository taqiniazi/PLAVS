<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OwnerMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $sender;
    public $messageContent;
    public $subjectLine;

    public function __construct($sender, $subject, $messageContent)
    {
        $this->sender = $sender;
        $this->subjectLine = $subject;
        $this->messageContent = $messageContent;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->replyTo($this->sender->email, $this->sender->name)
                    ->view('emails.owner_message');
    }
}
