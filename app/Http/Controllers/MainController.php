<?php

namespace App\Http\Controllers;

use App\Models\KeywordData;
use Goutte\Client as GoutteClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;


class MainController extends Controller
{
    public function scrapeAndSaveKeywords($any = '')
    {

        $query = urlencode($any ?: ''); // Gunakan string kosong jika $any kosong

        $credentials = $this->dataConst();
        $apiKey = $credentials['apiKey'];
        $cseId = $credentials['cseId'];
        $url = "https://www.googleapis.com/customsearch/v1?q=" . urlencode($query) . "&key=$apiKey&cx=$cseId";

        $response = Http::get($url);
        $data = $response->json();

        $suggestions = [];
        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                if (isset($item['title'])) {
                    $suggestions[] = $item['title'];
                }
            }
        }

        // Simpan keyword ke dalam database
        foreach ($suggestions as $suggestion) {
            $keywordData = KeywordData::firstOrCreate(['keyword' => $suggestion]);
            $keywordData->increment('hit');
        }

        // Pilih salah satu keyword secara acak
        $popular = [];
        if (count($suggestions) > 0) {
            $randomKeyword = $suggestions[array_rand($suggestions)];
            $popular = $this->searchKeyword($randomKeyword);

        }

        // Ambil hingga 100 keyword dari database
        $keywords = KeywordData::limit(300)->pluck('keyword')->toArray();
        shuffle($keywords);
        $keywordsString = implode(', ', $keywords);

        // Ambil 7 keyword pertama untuk meta keywords dan 3 keyword pertama untuk meta description
        $metaKeywords = implode(', ', array_slice($keywords, 0, 7));
        $metaDescription = implode(', ', array_slice($keywords, 0, 3));

        return view('index', [
            'title' => $any,
            'keywords' => $keywordsString,
            'metaKeywords' => $metaKeywords,
            'metaDescription' => $metaDescription,
            'popular' => $popular,
        ]);
    }

    public function searchKeyword($keyword)
    {
        $query = urlencode($keyword);
        $url = "https://www.google.com/search?q=$query";

        $goutteClient = new GoutteClient();
        $crawler = $goutteClient->request('GET', $url, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.6478.127 Safari/537.36',
                'Accept-Language' => 'en-US',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            ]
        ]);
        // dd($crawler);
        $searchResults = [];

        $crawler->filter('h3')->each(function ($node) use (&$searchResults) {
            $title = $node->text();
            $linkElement = $node->closest('a');
            $link = $linkElement ? $linkElement->attr('href') : null;

            if (!empty($title)) {
                $searchResults[] = [
                    'title' => $title,
                    'link' => $link,
                ];
            }
        });

        return $searchResults;
    }

    private function dataConst() {
        $data = [
            'account_1' => [
                'apiKey'    => 'AIzaSyCdogjHgWu2BJndKr8s-Pyj1VfbHleMqEo',
                'cseId'     => '04327ce68475f4076'
            ],
            'account_2' => [
                'apiKey'    => 'AIzaSyBhvs4xfTu8A7ayg-RsKQREqBDH--DrZW8',
                'cseId'     => '36ee2f8543f584667'
            ],
            'account_3' => [
                'apiKey'    => 'AIzaSyDDpxGjixl23nxUASuOdb4ydhRdr4zgpI8',
                'cseId'     => '918c00d62fdd34400'
            ],
        ];

        $index = Cache::get('google_api_key_index', 0);

        // Pilih API Key dan CSE ID berdasarkan indeks
        $apiKey = $data["account_" . ($index + 1)]['apiKey'];
        $cseId = $data["account_" . ($index + 1)]['cseId'];

        // Update indeks untuk rotasi berikutnya
        $nextIndex = ($index + 1) % count($data);
        Cache::put('google_api_key_index', $nextIndex, now()->addSeconds(10)); // Ganti sesuai dengan frekuensi rotasi

        return ['apiKey' => $apiKey, 'cseId' => $cseId];
    }
}
