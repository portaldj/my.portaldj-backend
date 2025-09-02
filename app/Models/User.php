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
        'first_name',
        'last_name',
        'birth_date',
        'city_id'
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

    /**
     * Un usuario puede tener múltiples roles.
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Un usuario pertenece a una ciudad.
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Un usuario puede tener múltiples tipos de perfil.
     * @return BelongsToMany
     */
    public function profileTypes(): BelongsToMany
    {
        return $this->belongsToMany(ProfileType::class);
    }

    /**
     * Un usuario tiene muchas redes sociales, con la URL específica en la tabla pivot.
     * @return BelongsToMany
     */
    public function socialNetworks(): BelongsToMany
    {
        return $this->belongsToMany(SocialNetwork::class)->withPivot('url')->withTimestamps();
    }

    /**
     * Un usuario puede haber trabajado/ser residente en muchos clubs.
     * @return BelongsToMany
     */
    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class)->withPivot('start_date', 'end_date', 'is_resident')->withTimestamps();
    }

    /**
     * Un usuario tiene muchos skills.
     * @return BelongsToMany
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }

    /**
     * Un usuario se especializa en muchos géneros musicales.
     * @return BelongsToMany
     */
    public function musicGenres(): BelongsToMany
    {
        return $this->belongsToMany(MusicGenre::class);
    }

    /**
     * Un usuario posee varios modelos de equipos.
     * @return BelongsToMany
     */
    public function equipmentModels(): BelongsToMany
    {
        return $this->belongsToMany(EquipmentModel::class);
    }

    /**
     * Un usuario puede crear muchos posts.
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Un usuario puede escribir muchos comentarios.
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Un usuario (Administrador o Editor) tiene una agenda con muchos eventos.
     * @return HasMany
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Un usuario (Administrador, Editor o Invitado) puede realizar muchas solicitudes de booking.
     * @return HasMany
     */
    public function bookingsMade(): HasMany
    {
        return $this->hasMany(Booking::class, 'guest_user_id');
    }

    /**
     * Un usuario (Administrador o editor) puede recibir muchas solicitudes de booking.
     * @return HasMany
     */
    public function bookingRequests(): HasMany
    {
        return $this->hasMany(Booking::class, 'dj_user_id');
    }

    /**
     * Relación polimórfica para la foto de perfil.
     * @return MorphOne
     */
    public function profileImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'profile');
    }

    /**
     * Relación polimórfica para todas las imágenes de un usuario.
     * @return MorphMany
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Helper function for roles
     * @param $roleSlug
     * @return bool
     */
    public function hasRole($roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }
}
