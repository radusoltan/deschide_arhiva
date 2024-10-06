<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Csv\Writer;
use function PHPUnit\Framework\isEmpty;

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
}
