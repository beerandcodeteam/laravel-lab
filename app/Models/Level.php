<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function englishJourneyLogs(): HasMany
    {
        return $this->hasMany(EnglishJourneyLog::class);
    }
}
