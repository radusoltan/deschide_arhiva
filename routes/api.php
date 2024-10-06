<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('articles', [ArticleController::class, 'index'])->middleware(['set_locale']);
Route::get('categories', [\App\Http\Controllers\CategoryController::class, 'index'])->middleware(['set_locale']);
Route::get('categories/{slug}', [\App\Http\Controllers\CategoryController::class, 'show'])->middleware(['set_locale']);
Route::get('categories/{slug}/articles',[\App\Http\Controllers\CategoryController::class, 'getCategoryArticles'])->middleware('set_locale');
