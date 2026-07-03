<?php

use App\Http\Controllers\FileUploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FileUploadController::class, 'create'])->name('files.create');

Route::prefix('files')->name('files.')->group(function () {
    Route::get('/', [FileUploadController::class, 'index'])->name('index');

    Route::post('/', [FileUploadController::class, 'store'])->name('store');

    Route::get('/{file}/download', [FileUploadController::class, 'download'])->name('download');

    Route::delete('/{file}', [FileUploadController::class, 'destroy'])->name('destroy');
});
