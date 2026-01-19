<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialNetwork extends Model
{
    protected $fillable = ['user_id', 'social_platform_id', 'url', 'order'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function platform()
    {
        return $this->belongsTo(SocialPlatform::class, 'social_platform_id');
    }

}
