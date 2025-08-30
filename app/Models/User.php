<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'level_id',
        'role_id',
        'name',
        'phone',
        'email',
        'password',
        'daily_target_minutes',
        'preferred_start_time',
        'preferred_days',
        'onboarding_completed_at',
        'last_message_at',
        'state',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
            'last_message_at' => 'datetime',
            'preferred_days' => 'array',
            'state' => 'array',
            'password' => 'hashed',
        ];
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function tests(): HasMany
    {
        return $this->hasMany(Test::class);
    }

    public function lastTest(): HasOne
    {
        return $this->hasOne(Test::class)->latestOfMany();
    }

    public function englishJourneyLogs(): HasMany
    {
        return $this->hasMany(EnglishJourneyLog::class);
    }

    public function lastEnglishJourneyLog(): HasOne
    {
        return $this->hasOne(EnglishJourneyLog::class)->latestOfMany();
    }
}
