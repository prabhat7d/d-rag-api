<?php

use App\Http\Controllers\Api\Document\DocumentController;
use App\Http\Controllers\Api\Document\SearchController;
use App\Http\Controllers\Api\Document\AskController;
use Illuminate\Support\Facades\Route;

Route::post('/documents', [DocumentController::class, 'store']);
Route::post('/search', SearchController::class);
Route::post('/ask', AskController::class);
