<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveTextRecord extends Model
{
    //
    protected $fillable = [
        'title',
        'content',
        'published_at',
        'tg_embed',
        'live_text_id'

    ];

    public function liveText()
    {
        return $this->belongsTo(LiveText::class);
    }
}
