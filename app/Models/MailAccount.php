<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MailAccount extends Model
{
    protected $fillable = [
        'user_id',
        'from_name',
        'from_email',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'imap_host',
        'imap_port',
        'imap_username',
        'imap_password',
        'imap_encryption',
        'pop_host',
        'pop_port',
        'pop_username',
        'pop_password',
        'pop_encryption',
        'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
        // Encrypt the passwords natively
        'smtp_password' => 'encrypted',
        'imap_password' => 'encrypted',
        'pop_password' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function externalMessages(): HasMany
    {
        return $this->hasMany(ExternalMessage::class);
    }
}
