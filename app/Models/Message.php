<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'mail_account_id',
        'message_id',
        'thread_id',
        'from_email',
        'from_name',
        'to',
        'cc',
        'bcc',
        'subject',
        'body_text',
        'body_html',
        'direction',
        'folder',
        'is_read',
        'is_starred',
        'is_snoozed',
        'is_draft',
        'is_spam',
        'is_trash',
        'raw_headers',
        'sent_at',
        'received_at',
    ];

    protected $casts = [
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'is_read' => 'boolean',
        'is_starred' => 'boolean',
        'is_snoozed' => 'boolean',
        'is_draft' => 'boolean',
        'is_spam' => 'boolean',
        'is_trash' => 'boolean',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function mailAccount()
    {
        return $this->belongsTo(MailAccount::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class, 'message_labels');
    }
}
