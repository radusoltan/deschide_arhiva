<?php

namespace App\Console\Commands;

use App\Models\Article;
use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CheckTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check articles translations';

    private $elasticsearch;

    public function __construct(Client $elasticsearch){
        parent::__construct();
        $this->elasticsearch = $elasticsearch;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Check article translations. This might take a while...');

        foreach (config('translatable.locales') as $locale) {
            app()->setLocale($locale);
            foreach (Article::cursor() as $article) {
                $oldArticleResponse = Http::get(env('ARHIVA_URL') . '/api/articles/' . $article->old_number . 'json?language=' . app()->getLocale());
                if(array_key_exists('translations',$oldArticleResponse->json())) {
                    foreach ($oldArticleResponse->json()['translations'] as $language => $url){
                        if(!$article->hasTranslation($language)) {
                            $translatedOld = Http::get(env('ARHIVA_URL') . '/api/articles/' . $article->old_number . 'json?language=' . $language);
                            app()->setLocale($language);
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
                            $this->info('Article: '.$article->id. 'translated in '.$language);
                        }
                    }
                }
            }
        }
        $this->info('Done!');
    }
}
