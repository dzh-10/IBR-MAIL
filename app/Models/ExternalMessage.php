<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ExternalMessage extends Model
{
    protected $fillable = [
        'mail_account_id',
        'message_id',
        'thread_id',
        'from',
        'to',
        'cc',
        'bcc',
        'subject',
        'body_text',
        'body_html',
        'direction',
        'folder',
        'is_read',
        'starred',
        'sent_at',
        'received_at',
    ];

    protected $casts = [
        'from' => 'array',
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'is_read' => 'boolean',
        'starred' => 'boolean',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function mailAccount(): BelongsTo
    {
        return $this->belongsTo(MailAccount::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'message');
    }
}
