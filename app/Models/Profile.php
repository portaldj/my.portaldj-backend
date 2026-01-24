<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'username',
        'first_name',
        'last_name',
        'phone',
        'birthdate',
        'biography',
        'country_id',
        'city_id',
        'address',
        'profile_image_path',
        'dj_type_id',
        'theme',
        'is_email_public',
        'is_phone_public',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function djType()
    {
        return $this->belongsTo(DjType::class);
    }

    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image_path
            ? '/storage/' . $this->profile_image_path
            : null;
    }

    public function getThumbUrlAttribute()
    {
        if (!$this->profile_image_path)
            return null;

        $path = $this->getVariantPath($this->profile_image_path, 'thumb');

        return \Illuminate\Support\Facades\Storage::disk('public')->exists($path)
            ? '/storage/' . $path
            : $this->profile_image_url;
    }

    public function getMediumUrlAttribute()
    {
        if (!$this->profile_image_path)
            return null;

        $path = $this->getVariantPath($this->profile_image_path, 'medium');

        return \Illuminate\Support\Facades\Storage::disk('public')->exists($path)
            ? '/storage/' . $path
            : $this->profile_image_url;
    }

    protected function getVariantPath($originalPath, $variant)
    {
        $info = pathinfo($originalPath);
        return $info['dirname'] . '/' . $info['filename'] . '_' . $variant . '.' . $info['extension'];
    }

    protected $appends = ['profile_image_url', 'thumb_url', 'medium_url'];
}
