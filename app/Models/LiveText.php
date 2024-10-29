<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveText extends Model
{
    //
    protected $fillable = [
        'description',
        'name',
    ];

    public function records()
    {
        return $this->hasMany(LiveTextRecord::class);
    }
}
