<?php

use App\Http\Controllers\GoogleTrendsController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route untuk sitemap harus ditempatkan sebelum route wildcard
Route::get('/sitemap.xml', [SitemapController::class, 'index']);

// Route wildcard
Route::get('/{any}', [MainController::class, 'scrapeAndSaveKeywords'])
    ->where('any', '.*')->name('visit.me');

// Route tambahan
// Route::get('/fetch-trends', [GoogleTrendsController::class, 'fetchTrends']);
