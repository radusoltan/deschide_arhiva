<?php

namespace App\Services;

use App\Models\ArticleImage;
use App\Models\Author;
use App\Models\Image;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportService {

    private $imageService;

    public function __construct(ImageService $imageService) {
        $this->imageService = $imageService;
    }

    public function getArticleAuthors($article, $locale){

        $url = env('ARHIVA_URL')."/api/authors/article/{$article->old_number}/{$locale}.json";

        $resp = Http::get($url)->object();

        if (property_exists($resp, 'items')) {
            foreach ($resp->items as $item) {
                $author = $this->getAuthorByNumber($item->author->id);

                if (!$article->authors->contains($author)) {
                    $article->authors()->attach($author);
                }
            }
        }
    }

    public function getArticleMainImage($article, $imageName){
        $imageUrl = env('ARHIVA_URL')."/images/{$imageName}";
        $image = $this->imageService->uploadFromUrl($imageUrl, $imageName);
        $mainImage = ArticleImage::where('article_id',$article->id)
            ->where('image_id',$image->id)->first();
        $mainImage->setMain();

    }

    public function getArticleTranslations($article, $locale) {
        $oldArticleResponse = Http::get(env('ARHIVA_URL') . '/api/articles/' . $article->old_number . 'json?language=' . $locale);
        if(array_key_exists('translations',$oldArticleResponse->json())) {
            foreach ($oldArticleResponse->json()['translations'] as $language => $url){
                app()->setLocale($language);

                if(!$article->hasTranslation($language)) {

                    $translatedOld = Http::get(env('ARHIVA_URL') . '/api/articles/' . $article->old_number . 'json?language=' . app()->getLocale());

                    $article->update([
                        'title' => $translatedOld->object()->title,
                        'slug' => Str::slug($translatedOld->object()->title).'-'.Str::random(10),
                        'lead' => $translatedOld->object()->fields->lead ?? null,
                        'body' => $translatedOld->object()->fields->Continut ?? null,
                        'published_at' => $translatedOld->object()->status !== 'Y' ? Carbon::now() : Carbon::parse($translatedOld->object()->published),
                        'status' => $translatedOld->object()->status === 'Y'? "P": "S",
                        'is_flash' => false,
                        'is_breaking' => false,
                        'is_alert' => false,
                        'is_live' => false,
                        'embed' => $translatedOld->object()->fields->Embed ?? null,
                    ]);
                    Log::info('Article '.$article->id.' translated in '.app()->getLocale());
                }
            }
        }
    }

    private function getAuthorByNumber($number){
        $url = env('ARHIVA_URL')."/api/authors/{$number}.json";
        $author = Http::withOptions(['verify' => false])->get($url);
        $localAuthor = Author::where('old_number', $author->object()->id)->first();
        if(!$localAuthor) {
            $localAuthor = Author::create([
                'first_name' => $author->object()->firstName,
                "last_name" => $author->object()->lastName,
                "full_name" => $author->object()->firstName . ' ' . $author->object()->lastName,
                'slug' => Str::slug($author->object()->firstName . ' ' . $author->object()->lastName).'-'.Str::random(5),
                'old_number' => $author->object()->id,
            ]);
        } else {
            $localAuthor->update([
                'first_name' => $author->object()->firstName,
                "last_name" => $author->object()->lastName,
                "full_name" => $author->object()->firstName . ' ' . $author->object()->lastName,
                'slug' => Str::slug($author->object()->firstName . ' ' . $author->object()->lastName).'-'.Str::random(5),
                'old_number' => $author->object()->id,
            ]);
        }
        return $localAuthor;
    }

    public function getArticleImagesByNumber($article, $locale) {
        $url = env('ARHIVA_URL')."/api/articles/{$article->old_number}/{$locale}/images.json";
        $articleImages = Http::withOptions(['verify' => false])
            ->withQueryParameters([
                'items_per_page' => 100,
            ])
            ->get($url);

        if (property_exists($articleImages->object(), 'items')) {
            foreach ($articleImages->object()->items as $item){

                $remoteImage = $this->getImageByNumber($item->id);
                $imageUrl = env('ARHIVA_URL')."/images/{$remoteImage->basename}";

                $image = $this->imageService->uploadFromUrl($imageUrl, $remoteImage->basename);

                $image->update([
                    'description' => property_exists($remoteImage, 'description') ? $remoteImage->description : "Poza simbol",
                    'source' => property_exists($remoteImage, 'photographer') ? $remoteImage->photographer : "deschide.md",
                    'author' => property_exists($remoteImage, 'photographer') ? $remoteImage->photographer : "deschide.md",
                    'old_number' => $remoteImage->id,
                ]);

                if (!$article->images->contains($image)) {
                    $article->images()->attach($image);
                }

            }
        }

    }

    private function getImageByNumber($number){

        $url = env('ARHIVA_URL')."/api/images/{$number}.json";
        $image = Http::withOptions(['verify' => false])->get($url);
        return $image->object();


    }

}
