<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $fillable = ['user_id', 'title', 'place', 'description', 'start_date', 'end_date'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $appends = ['is_current'];

    public function getIsCurrentAttribute()
    {
        return is_null($this->end_date);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
