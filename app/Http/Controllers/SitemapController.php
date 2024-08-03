<?php

namespace App\Http\Controllers;

use App\Models\KeywordData;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    public function index()
    {
        $rssUrls = [
            'https://example.com/rss1.xml',
            'https://example.com/rss2.xml',
            // Tambahkan URL RSS lainnya di sini
        ];

        $keywords = KeywordData::all();
        $formattedKeywords = $keywords->map(function ($keywordData) {
            $keyword = $keywordData->keyword;

            // Hapus semua simbol dan ubah spasi menjadi -
            $keyword = preg_replace('/[^a-zA-Z0-9\s]/', '', $keyword);
            $keyword = str_replace(' ', '-', $keyword);

            return [
                'loc' => url("/search/$keyword"), // URL untuk setiap keyword
                'lastmod' => $keywordData->updated_at->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ];
        });

        $sitemap = view('sitemap', [
            'rssUrls' => $rssUrls,
            'keywords' => $formattedKeywords,
        ]);

        return response($sitemap, 200, ['Content-Type' => 'application/xml']);
    }
}
