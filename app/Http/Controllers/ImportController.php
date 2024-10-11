<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;

use App\Services\ImageService;
use Corcel\Model\Post;
use Corcel\Model\Taxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ImportController extends Controller
{
    private $imageService;

    public function __construct(ImageService $imageService){
        $this->imageService = $imageService;
    }

    public function import(){

        $imageUrl = "http://arhiva.deschide.md/images/cms-image-000084911.jpg?itok=NKsPXH9H";

        $image = $this->imageService->uploadFromUrl($imageUrl, 'cms-image-000084911.jpg');

        dump($image);

//        $posts = Post::published()->get();
//
//        $categories = Taxonomy::where('taxonomy', 'category')->get();
//
//        foreach($categories as $category) {
//
//            foreach ($category->posts as $post) {
//                $author = $post->author;
//
//                $postDetails = [
//                    'title' => $post->post_title,
//                    'content' => $post->post_content,
//                    'category' => $category->name,
//                    'author' => $author ? $author->display_name : 'Unknown Author',
//                ];
//
//                dump($postDetails);
//
//            }
//
//        }


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
