<?php

namespace App\Http\Controllers;

use Goutte\Client as GoutteClient;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class GoogleTrendsController extends Controller
{

    public function fetchTrends()
    {
        $url = 'https://trends.google.com/trends/api/dailytrends?hl=en-GB&tz=-420&geo=ID&hl=en-GB&ns=15';

        // Inisialisasi Guzzle client
        $client = new Client();

        // Melakukan permintaan GET
        $response = $client->request('GET', $url, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.6478.127 Safari/537.36',
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate, br',
            ],
            'http_errors' => false, // Jangan lemparkan exception pada kode status HTTP yang tidak 2xx
        ]);

        // Mengambil konten respons
        $json = $response->getBody()->getContents();

        // Menghapus karakter JSONP prefix jika ada
        $json = preg_replace('/^\)\]\}\',/', '', $json);

        // Decode JSON
        $data = json_decode($json, true);

        // Mengecek jika data berhasil di-decode
        if (json_last_error() === JSON_ERROR_NONE) {
            // Inisialisasi array untuk menyimpan title
            $titles = [];

            // Mengekstrak semua title dari data
            if (isset($data['default']['trendingSearchesDays'])) {
                foreach ($data['default']['trendingSearchesDays'] as $day) {
                    foreach ($day['trendingSearches'] as $search) {
                        if (isset($search['title']['query'])) {
                            $titles[] = $search['title']['query'];
                        }
                    }
                }
            }

            return response()->json(['titles' => $titles]);
        } else {
            return response()->json(['error' => 'Failed to parse JSON'], 500);
        }
    }
}
