<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    protected $fillable = ['name', 'city_id', 'type', 'address'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

}
