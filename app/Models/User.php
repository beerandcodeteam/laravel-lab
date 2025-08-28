<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'level_id',
        'last_message_at',
        'daily_target_minutes',
        'preferred_start_time',
        'preferred_days',
        'onboarding_completed_at'
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
            'last_message_at' => 'datetime',
            'password' => 'hashed',
            'preferred_days' => 'array',
            'preferred_start_time' => 'datetime:H:i:s',
            'state' => 'json'
        ];
    }

    /**
     * Get the role that the user belongs to.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the level that the user belongs to.
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Get the journey logs for the user.
     */
    public function journeyLogs(): HasMany
    {
        return $this->hasMany(EnglishJourneyLog::class);
    }

    public function lastJourneyLog(): HasOne
    {
        return $this->hasOne(EnglishJourneyLog::class)->latestOfMany();
    }

    public function preferredFoci(): BelongsToMany
    {
        return $this->belongsToMany(PreferredFocus::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
