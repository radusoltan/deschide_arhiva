<?php

use App\Http\Controllers\Admin\LiveTextController;
use App\Http\Controllers\Admin\LiveTextRecordController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return 'test';
});


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('articles', [ArticleController::class, 'index'])->middleware(['set_locale']);
Route::get('article/{old_number}',[ArticleController::class, 'show'])->middleware(['set_locale']);
Route::get('categories', [CategoryController::class, 'index'])->middleware(['set_locale']);
Route::get('categories/{slug}', [CategoryController::class, 'show'])->middleware(['set_locale']);
Route::get('categories/{slug}/articles',[CategoryController::class, 'getCategoryArticles'])->middleware('set_locale');

Route::group(['middleware' => ['auth:sanctum','set_locale'], 'prefix' => 'admin'], function () {

    //Categories
    Route::apiResource('categories', \App\Http\Controllers\Admin\CategoryController::class);

    // Rute pentru LiveText
    Route::apiResource('livetexts', LiveTextController::class);

    // Rute pentru LiveTextRecords
    Route::apiResource('livetexts.records', LiveTextRecordController::class)->shallow();

    Route::post('upload-image', [ImageController::class, 'store']);

});

Route::post('import-image', [ImageController::class, 'importImage']);
