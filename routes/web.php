<?php

use App\Http\Controllers\ArticleController;
use App\Models\LiveText;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacebookController;

Route::get('/', [\App\Http\Controllers\ImportController::class, 'import']);

//Route::get('/', [\App\Http\Controllers\ImportController::class. 'import']);


//Public live text
Route::get('live-text', function (){
    return LiveText::find(2)->load(['records' => function ($query) {
        $query->orderBy('published_at', 'desc');
    }]);
});


Route::get('/export-csv', [\App\Http\Controllers\ExportController::class, 'exportCSV'])->middleware('set_locale');
Route::get('yt-import', [\App\Http\Controllers\ExportController::class, 'importYT']);

Route::get('get_img',[\App\Http\Controllers\ImageController::class,'getImgSrc']);


Route::get('articles', [ArticleController::class, 'index'])->middleware('set_locale');

Route::get('auth/facebook',[FacebookController::class,'fbPage']);
Route::get('auth/facebook/callback',[FacebookController::class,'fbCallback']);

require __DIR__.'/auth.php';
