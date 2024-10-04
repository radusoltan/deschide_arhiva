<?php

namespace App\Http\Controllers;

use Http;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function import(Request $request){

        $articlesUrl = "https://deschide.md/api/articles.json";

        $resp = Http::withQueryParameters([
            'language' => 'ro',
//            'section' => $category->old_number,
            'items_per_page' => 100,
            'sort[number]' => 'desc',
            'type' => 'stiri',
            'page' => 1,
//            "access_token" => "NTRjZDZjNDI3OWFjMTQwNjZiZjIxZjFkMTFhMjkyZjc0YTdmOGFmNTA4ZDVlMWRmNTc1NzFjNjI4ZTQyYmY4MQ"
        ])->timeout(360)
            ->withOptions(['verify' => FALSE])->accept('application/json')->get($articlesUrl);

        foreach($resp->object()->items as $article){
            $article = $this->getArticleByNumber($article->number);
            dump($article);
        }

    }

    private function getArticleByNumber($number) {

        $articleUrl = "https://deschide.md/api/articles/{$number}.json";
        $article = Http::withOptions(['verify' => false])->get($articleUrl);
        if($article && !property_exists($article->object(),'errors')){
            return $article->object();
        } else {
            return new \stdClass();
        }

    }
}
