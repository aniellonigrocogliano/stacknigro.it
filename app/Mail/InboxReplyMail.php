<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InboxReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subject,
        public string $userMessage,
        public string $replyBody,
        public string $senderName,
        public string $senderEmail
    ) {}

    public function build()
    {
        return $this->from($this->senderEmail, $this->senderName)
            ->subject($this->subject)
            ->view('emails.inbox-reply');
    }
}
