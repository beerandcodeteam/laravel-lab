<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'test_id',
        'type',
        'question',
        'question_audio_path',
        'options',
        'answer',
        'answer_path',
        'points',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'json'
        ];
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }
}
