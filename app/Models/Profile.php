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
        if (!$this->profile_image_path)
            return null;

        // Check Local (Legacy)
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->profile_image_path)) {
            return '/storage/' . $this->profile_image_path;
        }

        // Return R2 Public URL
        return \Illuminate\Support\Facades\Storage::disk('r2-public')->url($this->profile_image_path);
    }

    public function getThumbUrlAttribute()
    {
        if (!$this->profile_image_path)
            return null;

        $path = $this->getVariantPath($this->profile_image_path, 'thumb');

        // Check Local (Legacy)
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return '/storage/' . $path;
        }

        // Return R2 Public URL (Assume variant exists or standard URL pattern)
        return \Illuminate\Support\Facades\Storage::disk('r2-public')->url($path);
    }

    public function getMediumUrlAttribute()
    {
        if (!$this->profile_image_path)
            return null;

        $path = $this->getVariantPath($this->profile_image_path, 'medium');

        // Check Local (Legacy)
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return '/storage/' . $path;
        }

        // Return R2 Public URL
        return \Illuminate\Support\Facades\Storage::disk('r2-public')->url($path);
    }

    protected function getVariantPath($originalPath, $variant)
    {
        $info = pathinfo($originalPath);
        return $info['dirname'] . '/' . $info['filename'] . '_' . $variant . '.' . $info['extension'];
    }

    protected $appends = ['profile_image_url', 'thumb_url', 'medium_url'];
}
