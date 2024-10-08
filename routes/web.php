<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\ImportController::class, 'import']);

//Route::get('/', [\App\Http\Controllers\ImportController::class. 'import']);
Route::get('/export-csv', [\App\Http\Controllers\ExportController::class, 'exportCSV'])->middleware('set_locale');
Route::get('yt-import', [\App\Http\Controllers\ExportController::class, 'importYT']);
Route::get('lemne',[\App\Http\Controllers\LemneController::class, 'woods']);
Route::get('facebook', [\App\Http\Controllers\FacebookController::class, 'facebook']);
Route::get('facebook-callback',[\App\Http\Controllers\FacebookController::class, 'facebookCallback']);
Route::get('get_img',[\App\Http\Controllers\ImageController::class,'getImgSrc']);
require __DIR__.'/auth.php';
