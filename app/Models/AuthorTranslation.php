<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'first_name',
        'last_name',
        'full_name',
        'slug'
    ];
}
