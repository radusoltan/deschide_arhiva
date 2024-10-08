<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Services\ImportService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AdvertorialSeeder extends Seeder
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
        $locales = config('translatable.locales');

        foreach ($locales as $locale) {

            app()->setLocale($locale);

            $articlesUrl = "http://arhiva.deschide.md/api/articles.json";

            $resp = Http::withQueryParameters([
                'language' => $locale,
                'section' => 25,
                'items_per_page' => 10,
                'sort[published]' => 'desc',
                'type' => 'stiri',
                'page' => 1
            ])
                ->timeout(360)
                ->withOptions(['verify' => FALSE])
                ->accept('application/json')
                ->get($articlesUrl);

            $category = Category::where('old_number', 25)->first();

            foreach ($resp->object()->items as $item) {

                $article = Article::where('old_number', $item->number)->first();

                if (!$article) {
                    $article = Article::create([
                        'old_number' => $item->number,
                        'category_id' => $category->id,
                        'title' => $item->title,
                        'slug' => Str::slug($item->title),
                        'lead' => $item->fields->lead ?? NULL,
                        'body' => $item->fields->Continut ?? NULL,
                        'published_at' => Carbon::parse($item->published),
                        'status' => $item->status === 'Y' ? "P" : "S",
                        'is_flash' => FALSE,
                        'is_breaking' => FALSE,
                        'is_alert' => FALSE,
                        'is_live' => FALSE,
                        'embed' => $item->fields->Embed ?? NULL,
                    ]);
                }
                else {
                    $article->update([
                        'old_number' => $item->number,
                        'category_id' => $category->id,
                        'title' => $item->title,
                        'slug' => $category->id != 11 ? Str::slug($item->title) : Str::slug($item->number . '-' . Str::random()),
                        'lead' => $item->fields->lead ?? NULL,
                        'body' => $item->fields->Continut ?? NULL,
                        'published_at' => Carbon::parse($item->published),
                        'status' => $item->status === 'Y' ? "P" : "S",
                        'is_flash' => FALSE,
                        'is_breaking' => FALSE,
                        'is_alert' => FALSE,
                        'is_live' => FALSE,
                        'embed' => $item->fields->Embed ?? NULL,
                    ]);
                }
                $this->importService->getArticleAuthors($article, app()->getLocale());
                $this->importService->getArticleImagesByNumber($article, app()->getLocale());
            }
        }
    }
}
