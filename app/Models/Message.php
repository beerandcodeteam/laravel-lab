<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    protected $fillable = [
        'user_id',
        'from',
        'type',
        'mime',
        'file',
        'transcription',
        'message',
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
