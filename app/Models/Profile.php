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

}
