<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'slug',
        'lead',
        'body',
        'keywords',
        'status',
        'published_at',
        'publish_at',
        'is_locked',
        'locked_by_user',
        'is_flash',
        'is_alert',
        'is_breaking',
        'is_live',
        'embed',
        'telegram_post',
        'telegram_embed',
        'keywords',
        'facebook_post_id'
    ];

    public function article(){
        return $this->belongsTo(Article::class);
    }

    protected $casts = [
        'is_locked' => 'boolean',
        'publish_at' => 'datetime',
        'is_flash' => 'boolean',
        'is_alert' => 'boolean',
        'is_breaking' => 'boolean',
        'is_live' => 'boolean',
        'published_at' => 'datetime',
        'keywords' => 'json',
        'facebook_post_id' => 'string'
    ];

}
