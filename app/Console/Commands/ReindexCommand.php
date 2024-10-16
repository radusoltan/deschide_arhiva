<?php

namespace App\Console\Commands;

use App\Http\Resources\ArticleResource;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ImageResource;
use App\Models\Article;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ElasticsearchException;
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
                    'id' => $article->index_id,
                ];

//                dump($article->title);

//                if(!$article->getIndexId()) {
                    $this->info('Article '.$article->getId().' Article not found or not indexed');

                    $elasticArticle = $this->elasticsearch->index([
                        'index' => $article->getSearchIndex(),
                        'type' => $article->getSearchType(),
                        'body' => $article->toSearchArray(),
                    ]);
                    $article->update([
                        'index_id' => $elasticArticle->asObject()->_id
                    ]);
//                } else {
//                    try {
//                        $response = $this->elasticsearch->get($params);
//                        $this->info('Article '.$response->asObject()->_id.' Article indexed');
//                    } catch (\Exception $exception){
//                        dump($exception);
//                    }
//
//                }

            }
        }

        $this->info("\nDone!");
    }
}
