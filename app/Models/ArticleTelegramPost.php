<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleTelegramPost extends Model
{
    protected $fillable = [
        'article_title',
        'telegram_message_id'
    ];

    protected $table = 'article_telegram_post';

//    public function article(){
//        return $this->belongsTo(Article::class);
//    }
}
