<?php
use App\Http\Controllers\GoogleTrendsController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PingSitemap;
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

// Rute untuk sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index']);

// Rute untuk ping sitemap
Route::post('pingsite', [PingSitemap::class, 'pingSitemap'])->name('ping.me');

// Rute wildcard harus ditempatkan setelah rute yang lebih spesifik
Route::get('/{any}', [MainController::class, 'scrapeAndSaveKeywords'])
    ->where('any', '.*')->name('visit.me');

// Rute tambahan
// Route::get('/fetch-trends', [GoogleTrendsController::class, 'fetchTrends']);
