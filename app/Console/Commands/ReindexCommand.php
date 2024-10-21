<?php

namespace App\Console\Commands;

use App\Models\Article;
use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;

class ReindexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:reindex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexes all articles to Elasticsearch';

    /** @var Client */
    private $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        parent::__construct();

        $this->elasticsearch = $elasticsearch;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Indexing all articles to Elasticsearch. This might take a while...');
        foreach (config('translatable.locales') as $locale){
            app()->setLocale($locale);
            foreach (Article::translatedIn(app()->getLocale())->get() as $article){

                $params = [
                    'index' => $article->getSearchIndex(),
                    'id' => $article->getIndexId(),
                ];


                if(!$article->getIndexId() && $article->status === "P") {
                    $this->info('Article ' . $article->getId() . ' Article not found or not indexed');

                    $elasticArticle = $this->elasticsearch->index([
                        'index' => $article->getSearchIndex(),
                        'type' => $article->getSearchType(),
                        'body' => $article->toSearchArray(),
                    ]);
                    $article->update([
                        'index_id' => $elasticArticle->asObject()->_id
                    ]);
                }
            }
        }

        $this->info("\nDone!");
    }
}
