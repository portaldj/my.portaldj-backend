<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    const ROLE_ADMIN = ['id' => 1, 'name' => 'administrator', 'slug' => 'admin'];
    const ROLE_EDITOR = ['id' => 2, 'name' => 'editor', 'slug' => 'editor'];
    const ROLE_GUEST = ['id' => 3, 'name' => 'guest', 'slug' => 'guest'];
    protected $fillable = ['id', 'name', 'slug'];

    /**
     * Un Rol puede ser poseído por muchos usuarios.
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
