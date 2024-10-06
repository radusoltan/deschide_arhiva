<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use League\Csv\Writer;
use function PHPUnit\Framework\isEmpty;
use Alaouy\Youtube\Facades\Youtube;

class ExportController extends Controller
{
    public function exportCSV() {

        $csv = Writer::createFromFileObject(new \SplTempFileObject());

        $csv->insertOne([
            'Title',
            'Slug',
            'Body',
            'Created On',
            'Published On',
            'Updated On',
            'Category',
            'Author',
            'Main Image'
        ]);

        $articles = Article::all();
        foreach ($articles as $article) {


            $csv->insertOne([
                $article->title,
                $article->slug,
                $article->body,
                Carbon::parse($article->created_at)->format('m/d/Y h:i A'),
                Carbon::parse($article->published_at)->format('m/d/Y h:i A'),
                Carbon::parse($article->updated_at)->format('m/d/Y h:i A'),
                strtoupper($article->category->title),
                empty($article->authors()->get()->pluck('full_name')->implode(' ,')) ? "Deschide.md" : $article->authors()->get()->pluck('full_name')->implode(' ,'),
                is_null($article->images()->where('is_main',true)->first()) ? '' : env('APP_URL').'storage/images/'.$article->images()->where('is_main',true)->first()->name,
            ]);
        }
        $csv->output('articles.csv');
    }

    public function importYT(){
//        $videoList = Youtube::listChannelVideos(env('YOUTUBE_CHANNEL_ID'), 1000);
        $videoList = Youtube::searchChannelVideos('Dialog deschis', env('YOUTUBE_CHANNEL_ID'), 100);

        $csv = Writer::createFromFileObject(new \SplTempFileObject());

        $csv->insertOne([
            'Title',
            'Slug',
            'Published On',
            'Description',
            'Category',
            'Author',
            'Main Image',
            'YT_Link'
        ]);

        foreach($videoList as $video) {

            $videoDetails = Youtube::getVideoInfo($video->id->videoId);

            $title = $videoDetails->snippet->title;
            $slug = Str::slug($videoDetails->snippet->title);
            $description = $videoDetails->snippet->description;
            $published_at = Carbon::parse($videoDetails->snippet->publishedAt)->format('m/d/Y h:i A');
            $category = "Dialog deschis";
            $image = $videoDetails->snippet->thumbnails->maxres->url;

            $csv->insertOne([
                $title,
                $slug,
                $published_at,
                $description,
                $category,
                $video->snippet->channelTitle,
                $image,
                'https://www.youtube.com/watch?v='.$videoDetails->id.'&ab_channel=Deschide%C5%9Etirea'
            ]);
        }
        $csv->output('videos.csv');
    }
}
