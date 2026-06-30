<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    protected $fillable = [
        'filename',
        'mime_type',
        'size',
        'path',
    ];

    public function message(): MorphTo
    {
        return $this->morphTo();
    }
}
