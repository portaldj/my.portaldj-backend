<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
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
        'name',
        'email',
        'password',
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
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }


    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function profileTypes(): BelongsToMany
    {
        return $this->belongsToMany(ProfileType::class);
    }

    public function socialNetworks(): BelongsToMany
    {
        return $this->belongsToMany(SocialNetwork::class)->withPivot('url')->withTimestamps();
    }

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class)->withPivot('start_date', 'end_date', 'is_resident')->withTimestamps();
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }

    public function musicGenres(): BelongsToMany
    {
        return $this->belongsToMany(MusicGenre::class);
    }

    public function equipmentModels(): BelongsToMany
    {
        return $this->belongsToMany(EquipmentModel::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function bookingsMade(): HasMany
    {
        return $this->hasMany(Booking::class, 'guest_user_id');
    }

    public function bookingRequests(): HasMany
    {
        return $this->hasMany(Booking::class, 'dj_user_id');
    }

    public function profileImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'profile');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function hasRole($roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }
}
