<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'points',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'test_id');
    }
}
