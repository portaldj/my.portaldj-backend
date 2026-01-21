<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'banned_until',
        'ban_reason',
        'openai_key',
        'gemini_key',
        'last_trial_at',
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
            'banned_until' => 'datetime',
            'password' => 'hashed',
            'openai_key' => 'encrypted',
            'gemini_key' => 'encrypted',
            'last_trial_at' => 'datetime',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function socialNetworks()
    {
        return $this->hasMany(SocialNetwork::class)->orderBy('order');
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class)->orderByDesc('start_date');
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function genres()
    {
        return $this->belongsToMany(MusicGenre::class, 'genre_user', 'user_id', 'genre_id');
    }

    public function equipment()
    {
        return $this->hasMany(DjEquipment::class);
    }

    public function completedChapters()
    {
        return $this->belongsToMany(Chapter::class, 'chapter_user')->withTimestamps();
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function getIsProAttribute()
    {
        // Check for role 'Admin' (always pro)
        if ($this->hasRole('Admin')) {
            return true;
        }

        // Check active subscription
        return $this->subscriptions()->active()->exists();
    }
}
