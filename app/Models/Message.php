<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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

    protected $appends = ['public_url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function publicUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->file ? Storage::temporaryUrl($this->file, now()->addMinutes(5)) : null;
            }
        );
    }
}
