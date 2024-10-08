<?php

namespace App\Services;

use App\Models\ArticleImage;
use App\Models\Author;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportService {

    private $imageService;

    public function __construct(ImageService $imageService) {
        $this->imageService = $imageService;
    }

    public function getArticleAuthors($article, $locale){

        $url = "http://arhiva.deschide.md/api/authors/article/{$article->old_number}/{$locale}.json";

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

    private function getAuthorByNumber($number){
        $url = "http://arhiva.deschide.md/api/authors/{$number}.json";
        $author = Http::withOptions(['verify' => false])->get($url);
        $localAuthor = Author::where('old_number', $author->object()->id)->first();
        if(!$localAuthor) {
            $localAuthor = Author::create([
                'first_name' => $author->object()->firstName,
                "last_name" => $author->object()->lastName,
                "full_name" => $author->object()->firstName . ' ' . $author->object()->lastName,
                'slug' => Str::slug($author->object()->firstName . ' ' . $author->object()->lastName),
                'old_number' => $author->object()->id,
            ]);
        } else {
            $localAuthor->update([
                'first_name' => $author->object()->firstName,
                "last_name" => $author->object()->lastName,
                "full_name" => $author->object()->firstName . ' ' . $author->object()->lastName,
                'slug' => app()->getLocale() === 'ru' && $localAuthor->id === 14 ? Str::slug($author->object()->firstName . ' ' . $author->object()->lastName).'-'.Str::random(2) : Str::slug($author->object()->firstName . ' ' . $author->object()->lastName),
                'old_number' => $author->object()->id,
            ]);
        }
        return $localAuthor;
    }

    public function getArticleImagesByNumber($article, $locale) {
        $url = "http://arhiva.deschide.md/api/articles/{$article->old_number}/{$locale}/images.json";
        $articleImages = Http::withOptions(['verify' => false])
            ->withQueryParameters([
                'items_per_page' => 100
            ])
            ->get($url);

        if (property_exists($articleImages->object(), 'items')) {
            foreach ($articleImages->object()->items as $item){

                $remoteImage = $this->getImageByNumber($item->id);

                $imageUrl = "http://arhiva.deschide.md/images/{$remoteImage->basename}";

                $image = $this->imageService->uploadFromUrl($imageUrl, $remoteImage->basename);

                $image->update([
                    'description' => property_exists($remoteImage, 'description') ? $remoteImage->description : "Poza simbol",
                    'source' => property_exists($remoteImage, 'photographer') ? $remoteImage->photographer : "deschide.md",
                    'author' => property_exists($remoteImage, 'photographer') ? $remoteImage->photographer : "deschide.md",
                ]);

                if (!$article->images->contains($image)) {
                    $article->images()->attach($image);
                }

                if ($article->images()->count() >= 1) {
                    $image = $article->images()->first();
                    $mainImage = ArticleImage::where('article_id',$article->id)
                        ->where('image_id',$image->id)->first();
                    $mainImage->setMain();
                }

            }
        }

    }

    private function getImageByNumber($number){

        $url = "http://arhiva.deschide.md/api/images/{$number}.json";
        $image = Http::withOptions(['verify' => false])->get($url);
        return $image->object();


    }

}
