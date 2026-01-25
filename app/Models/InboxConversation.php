<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InboxConversation extends Model
{
    use SoftDeletes;

    protected $table = 'inbox_conversations';

    protected $fillable = [
        'source',
        'name',
        'email',
        'phone',
        'subject',
        'user_message',
        'how_found',
        'privacy_accepted',
        'privacy_accepted_at',
        'quote_payload',
        'ip_address',
        'user_agent',
        'read_at',
        'archived_at',
        'replied_at',
        'reply_subject',
        'reply_body',
        'reply_to_email',
    ];

    protected $casts = [
        'privacy_accepted'    => 'boolean',
        'privacy_accepted_at' => 'datetime',
        'quote_payload'       => 'array',
        'read_at'             => 'datetime',
        'archived_at'         => 'datetime',
        'replied_at'          => 'datetime',
        'deleted_at'          => 'datetime',
    ];

    public function getIsUnreadAttribute(): bool
    {
        return is_null($this->read_at);
    }
}
