<?php

namespace App\Models;

use App\Http\Resources\ArticleResource;
use App\Models\Traits\Searchable;
use App\Observers\ArticleOserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([ArticleOserver::class])]
class Article extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;
    use SoftDeletes;
    use Searchable;

    public array $translatedAttributes = [
        'title',
        'slug',
        'lead',
        'body',
        'status',
        'published_at',
        'is_locked',
        'locked_by_user',
        'is_flash',
        'is_alert',
        'is_breaking',
        'publish_at',
        'is_live',
        'embed',
        'telegram_post',
        'telegram_embed',
        'keywords',
        "index_id"
    ];

    protected $fillable = ['category_id','old_number', "is_video", 'checked'];

    protected $casts = [
        'is_flash' => 'boolean',
        'is_alert' => 'boolean',
        'is_breaking' => 'boolean',
        "is_video" => 'boolean'
        //        'related' => 'array'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function authors(){
        return $this->belongsToMany(Author::class, 'article_authors', 'article_id', 'author_id');
    }

    public function images(){
        return $this->belongsToMany(Image::class, 'article_images')->withPivot('is_main');
    }

    public function toSearchArray(){
        return new ArticleResource($this);
    }

}
