<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function(){
    return response()->json([
        'Laravel' => app()->version()
    ]);
});

//Route::get('/', [\App\Http\Controllers\ImportController::class. 'import']);
Route::get('/export-csv', [\App\Http\Controllers\ExportController::class, 'exportCSV'])->middleware('set_locale');
require __DIR__.'/auth.php';
