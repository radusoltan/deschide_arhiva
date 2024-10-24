<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;

use App\Services\ImageService;
use Corcel\Model\Post;
use Corcel\Model\Taxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    private $imageService;

    public function __construct(ImageService $imageService){
        $this->imageService = $imageService;
    }

    public function import(){

        return response()->json(
            app()->version()
        );

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
