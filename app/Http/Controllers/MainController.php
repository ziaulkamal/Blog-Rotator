<?php

namespace App\Http\Controllers;

use App\Models\ConsumerKey;
use App\Models\KeywordData;
use Goutte\Client as GoutteClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;


class MainController extends Controller
{
    public function scrapeAndSaveKeywords($any = '')
    {
        // if (empty(request()->header('Referer'))) {
        //     // Hentikan eksekusi kode dan putuskan koneksi
        //     die();
        // }
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

            $keywordData = KeywordData::firstOrCreate(['keyword' => $this->getFirstFourSentences($suggestion)]);
            $keywordData->increment('hit');
        }
        // dd($suggestions);
        // Pilih salah satu keyword secara acak
        $popular = [];
        if (count($suggestions) > 0) {
            $randomKeyword = $suggestions[array_rand($suggestions)];
            $popular = $this->searchKeyword($query);

        }

        // Ambil hingga 100 keyword dari database
        $keywords = KeywordData::limit(500)->pluck('keyword')->toArray();
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
        $url = "https://search.aol.com/aol/search?q=$query&s_qt=ac&rp=&s_chn=prt_bon&s_it=comsearch";

        $goutteClient = new GoutteClient();
        $goutteClient->setServerParameters([
            'verify' => false
        ]);
        $crawler = $goutteClient->request('GET', $url, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.6478.127 Safari/537.36',
                'Accept-Language' => 'en-US',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            ]
        ]);
        // dd($crawler);
        $searchResults = [];

        $crawler->filter('td.w-50p.pr-28')->each(function ($node) use (&$searchResults) {
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
        // dd($searchResults);
        return $searchResults;
    }

    private function dataConst() {
        // Ambil data dengan hit terkecil
        $consumerKey = ConsumerKey::orderBy('hit', 'asc')->first();

        if (!$consumerKey) {
            return ['apiKey' => '', 'cseId' => '']; // Tidak ada data
        }

        // Ambil API Key dan CSE ID dari data yang ditemukan
        $apiKey = $consumerKey->apiKey;
        $cseId = $consumerKey->cseId;

        // Update hit count
        $consumerKey->hit++;
        $consumerKey->save();

        return ['apiKey' => $apiKey, 'cseId' => $cseId];
    }

    private function getFirstFourSentences($text)
    {
            // Menemukan posisi tanda kurung
            // Memecah teks berdasarkan tanda titik, tanda seru, dan tanda tanya
        $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z])/', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Ambil 4 kalimat pertama atau semua jika kurang dari 4
        $firstFourSentences = array_slice($sentences, 0, 4);

        // Gabungkan kalimat menjadi string
        return implode(' ', $firstFourSentences);
    }
}
