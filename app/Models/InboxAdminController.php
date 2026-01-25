<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InboxConversation extends Model
{
    use SoftDeletes;

    protected $table = 'inbox_conversations';

    protected $fillable = [
        'source',                // contact | quote
        'name',
        'email',
        'phone',
        'subject',
        'user_message',
        'how_found',
        'privacy_accepted',
        'privacy_accepted_at',
        'quote_payload',         // json
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

    /* -----------------------------
     |  Scopes (stile Gmail)
     | ----------------------------- */

    // Inbox = non archiviati (il cestino lo gestisce SoftDeletes)
    public function scopeInbox($q)
    {
        return $q->whereNull('archived_at');
    }

    public function scopeArchived($q)
    {
        return $q->whereNotNull('archived_at');
    }

    public function scopeUnread($q)
    {
        return $q->whereNull('read_at');
    }

    public function scopeRead($q)
    {
        return $q->whereNotNull('read_at');
    }

    public function scopeSource($q, ?string $source)
    {
        if (!$source) return $q;
        return $q->where('source', $source);
    }

    /* -----------------------------
     |  Helpers
     | ----------------------------- */

    public function getIsUnreadAttribute(): bool
    {
        return is_null($this->read_at);
    }

    public function getIsArchivedAttribute(): bool
    {
        return !is_null($this->archived_at);
    }

    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }

    public function markAsUnread(): void
    {
        $this->forceFill(['read_at' => null])->save();
    }

    public function archive(): void
    {
        if (is_null($this->archived_at)) {
            $this->forceFill(['archived_at' => now()])->save();
        }
    }

    public function unarchive(): void
    {
        $this->forceFill(['archived_at' => null])->save();
    }
}
