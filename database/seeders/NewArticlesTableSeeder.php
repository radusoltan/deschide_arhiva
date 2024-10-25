<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Services\ImportService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewArticlesTableSeeder extends Seeder
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
            foreach (Category::all() as $category){

                $articleUrl = env('ARHIVA_URL')."/api/articles.json";

                $resp = Http::withQueryParameters([
                    'language' => $locale,
                    'section' => $category->old_number,
                    'items_per_page' => 400,
                    'sort[published]' => 'desc',
                    'type' => 'stiri',
                    'page' => 58
                ])
                    ->timeout(360)
                    ->withOptions([
                        'verify' => false
                    ])
                    ->accept('application/json')
                    ->get($articleUrl);

                if(property_exists($resp->object(),'items')){

                    foreach ($resp->object()->items as $oldArticle){

                        $article = Article::where('old_number', $oldArticle->number)->first();
                        app()->setLocale($locale);
                        if (!$article) {
                            $article = Article::create([
                                'old_number' => $oldArticle->number,
                                'category_id' => $category->id,
                                'title' => $oldArticle->title,
                                'slug' => Str::slug($oldArticle->title).'-'.Str::random(10),
                                'lead' => $oldArticle->fields->lead ?? null,
                                'body' => $item->fields->Continut ?? null,
                                'published_at' => Carbon::parse($oldArticle->published),
                                'status' => $oldArticle->status === 'Y'? "P": "S",
                                'is_flash' => false,
                                'is_breaking' => false,
                                'is_alert' => false,
                                'is_live' => false,
                                'embed' => $oldArticle->fields->Embed ?? null,
                            ]);

                            $this->importService->getArticleAuthors($article, $locale);
                            $this->importService->getArticleImagesByNumber($article, $locale);

                            // Article Main Image
                            $articleBigImage = collect($oldArticle->renditions)->firstWhere('caption', 'articlebig');

                            if ($articleBigImage && isset($articleBigImage->details->original->src)){
                                $imageUrl = $articleBigImage->details->original->src;
                                $imageName = basename($imageUrl);

                                $this->importService->getArticleMainImage($article, explode('|',basename(urldecode(urldecode($imageName))))[1]);
                            }
                            Log::info('Article '.$article->id.' imported '.strtoupper($locale));

                        }
                        $this->importService->getArticleTranslations($article, $locale);
                        Log::info('Article '.$article->id.' checked '.strtoupper($locale));

                    }
                }
            }
        }
    }
}
