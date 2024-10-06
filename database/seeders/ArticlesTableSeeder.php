<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleImage;
use App\Models\Author;
use App\Models\Category;
use App\Services\ImageService;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ArticlesTableSeeder extends Seeder
{
    private $imageService;

    public function __construct(ImageService $imageService){
        $this->imageService = $imageService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (config('translatable.locales') as $locale){
            app()->setLocale($locale);
            foreach (Category::all() as $category){
                $articlesUrl = "https://deschide.md/api/articles.json";

                $resp = Http::withQueryParameters([
                    'language' => $locale,
                    'section' => $category->old_number,
                    'items_per_page' => 100,
                    'sort[published]' => 'desc',
                    'type' => 'stiri',
                    'page' => 1
                ])->timeout(360)->withOptions(['verify' => false])->accept('application/json')->get($articlesUrl);

                if (property_exists($resp->object(), 'items')){
                    foreach($resp->object()->items as $item) {

                        $article = Article::where('old_number', $item->number)->first();
                        if (!$article) {
                            $article = Article::create([
                                'old_number' => $item->number,
                                'category_id' => $category->id,
                                'title' => $item->title,
                                'slug' => Str::slug($item->title),
                                'lead' => $item->fields->lead ?? null,
                                'body' => $item->fields->Continut ?? null,
                                'published_at' => Carbon::parse($item->published),
                                'status' => $item->status === 'Y'? "P": "S",
                                'is_flash' => false,
                                'is_breaking' => false,
                                'is_alert' => false,
                                'is_live' => false,
                                'embed' => $item->fields->Embed ?? null,
                            ]);
                        } else {
                            $article->update([
                                'old_number' => $item->number,
                                'category_id' => $category->id,
                                'title' => $item->title,
                                'slug' => $category->id != 11 ? Str::slug($item->title) : Str::slug($item->number.'-'.Str::random()),
                                'lead' => $item->fields->lead ?? null,
                                'body' => $item->fields->Continut ?? null,
                                'published_at' => Carbon::parse($item->published),
                                'status' => $item->status === 'Y'? "P": "S",
                                'is_flash' => false,
                                'is_breaking' => false,
                                'is_alert' => false,
                                'is_live' => false,
                                'embed' => $item->fields->Embed ?? null,
                            ]);
                        }


                        $this->getArticleAuthors($article, $locale);

                        $this->getArticleImagesByNumber($article, $locale);
                    }
                }

            }

        }
    }

    public function getArticleAuthors($article, $locale){

        $url = "https://deschide.md/api/authors/article/{$article->old_number}/{$locale}.json";

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
        $url = "https://deschide.md/api/authors/{$number}.json";
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

    private function getArticleImagesByNumber($article, $locale) {
        $url = "https://deschide.md/api/articles/{$article->old_number}/{$locale}/images.json";
        $articleImages = Http::withOptions(['verify' => false])
            ->withQueryParameters([
                'items_per_page' => 100
            ])
            ->get($url);

        if (property_exists($articleImages->object(), 'items')) {
            foreach ($articleImages->object()->items as $item){

                $remoteImage = $this->getImageByNumber($item->id);

                $imageUrl = "https://deschide.md/images/{$remoteImage->basename}";

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

        $url = "https://deschide.md/api/images/{$number}.json";
        $image = Http::withOptions(['verify' => false])->get($url);
        return $image->object();


    }
}
