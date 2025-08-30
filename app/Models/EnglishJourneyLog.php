<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnglishJourneyLog extends Model
{
    protected $fillable = [
        'user_id',
        'level_id',
        'level_summary',
        'ia_summary',
        'difficulties',
        'confidence_level',
    ];

    protected function casts(): array
    {
        return [
            'confidence_level' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
}
