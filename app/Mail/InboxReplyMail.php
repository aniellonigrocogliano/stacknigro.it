<?php

namespace App\Mail;

use App\Models\InboxConversation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InboxReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public InboxConversation $conversation;
    public string $replyBody;
    public string $subjectLine;

    public function __construct(InboxConversation $conversation, string $replyBody, string $subjectLine)
    {
        $this->conversation = $conversation;
        $this->replyBody = $replyBody;
        $this->subjectLine = $subjectLine;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->replyTo('aniello@stacknigro.it', 'Aniello Nigro')
            ->view('emails.inbox_reply');
    }
}
