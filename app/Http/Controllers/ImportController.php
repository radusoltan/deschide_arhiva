<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ImportController extends Controller
{
    public function import(){

        $typesUrl = 'http://arhiva.deschide.md/api/articles/157303.json';

        $response = Http::get($typesUrl);

        $dom = new \DomDocument();

        @$dom->loadHTML($response->object()->fields->Continut);

        $images = $dom->getElementsByTagName('img');
        $imgTags = [];

        foreach ($images as $img) {
            $imgTags[] = $dom->saveHTML($img); // Salvează fiecare tag img ca string
        }

        dump($imgTags);

    }

    public function extractNumberFromUrl($url)
    {
        $pattern = '/\d+$/'; // caută un șir de cifre la sfârșitul URL-ului
        if (preg_match($pattern, $url, $matches)) {
            return $matches[0]; // returnează numărul găsit
        }

        return null; // dacă nu a fost găsit niciun număr
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
