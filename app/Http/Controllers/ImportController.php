<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleTelegramPost;
use App\Models\Author;
use App\Models\Category;

use App\Services\ImageService;
use Corcel\Model\Post;
use Corcel\Model\Taxonomy;
use Elastic\Elasticsearch\Client;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

use Facebook;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;
use App\Notifications\FacebookPoster;


class ImportController extends Controller
{
    private $imageService;
    protected $telegram;

    protected $client;

    public function __construct(ImageService $imageService, Api $telegram, Client $client){
        $this->imageService = $imageService;
        $this->telegram = $telegram;
        $this->client = $client;
    }

    /**
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function import(){
        dump('here');
//        $appId = '367676820233510';
//        $appSecret = '63d933d6ec13eb1548f04e6a7d9dcf55';
//        $pageId = '507699295948485';
////        {
////            access_token: "EAAFOZAm5DHSYBO7iidLb3fsd9g2savjdoEFvEaDucEjx3mogFJ5ihMtyszfsatLb0zGw7DvmFsLvvbY1TLjm89XvWgVULfhDFomKPZACLZCvydXZBkxjkzfeqM38v9YxwcadIW0ULZAZBIZAHbC9KdB9NAltwAZAcmDMwFEbNrOteMX3aiZAqK5e3bOJELc2kiRKYGPkCimZC83nZAiMgZDZD",
////token_type: "bearer"
////}
//
//        $userAccessToken = 'EAAFOZAm5DHSYBO7iidLb3fsd9g2savjdoEFvEaDucEjx3mogFJ5ihMtyszfsatLb0zGw7DvmFsLvvbY1TLjm89XvWgVULfhDFomKPZACLZCvydXZBkxjkzfeqM38v9YxwcadIW0ULZAZBIZAHbC9KdB9NAltwAZAcmDMwFEbNrOteMX3aiZAqK5e3bOJELc2kiRKYGPkCimZC83nZAiMgZDZD';
//        $fb = new Facebook\Facebook([
//            'app_id' => $appId,
//            'app_secret' => $appSecret,
//            'default_graph_version' => 'v2.10',
//            'default_access_token' => $userAccessToken, // optional
//        ]);
//        try {
//            $response = $fb->get('/me');
//
//            dump($response);
//        } catch(FacebookResponseException $e) {
//            // Graph API error
//            echo 'Graph returned an error: ' . $e->getMessage();
//        } catch(FacebookSDKException $e) {
//            // SDK error
//            echo 'Facebook SDK returned an error: ' . $e->getMessage();
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
