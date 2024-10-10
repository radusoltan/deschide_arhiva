<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleImage;
use App\Models\Author;
use App\Models\Category;
use App\Services\ImageService;
use App\Services\ImportService;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ArticlesTableSeeder extends Seeder
{
    private $importService;

    public function __construct(ImportService $importService){
        $this->importService = $importService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (config('translatable.locales') as $locale){
            app()->setLocale($locale);
            foreach (Category::all() as $category){
                $articlesUrl = "http://arhiva.deschide.md/api/articles.json";

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

                        $this->importService->getArticleAuthors($article, $locale);
                        $this->importService->getArticleImagesByNumber($article, $locale);
                    }
                }

            }

        }
    }
}
