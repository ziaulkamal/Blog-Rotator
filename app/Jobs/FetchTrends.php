<?php

namespace App\Jobs;

use App\Services\KeywordService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class FetchTrends implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $keywordService;

    /**
     * Create a new job instance.
     *
     * @param KeywordService $keywordService
     * @return void
     */
    public function __construct(KeywordService $keywordService)
    {
        $this->keywordService = $keywordService;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Job dimulai');

        try {
            // Inisialisasi Guzzle client
            $client = new Client();

            // URL endpoint Google Trends
            $url = 'https://trends.google.com/trends/api/dailytrends?hl=en-GB&tz=-420&geo=ID&hl=en-GB&ns=50';

            // Melakukan permintaan GET
            $response = $client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.6478.127 Safari/537.36',
                    'Accept' => 'application/json',
                    'Accept-Encoding' => 'gzip, deflate, br',
                ],
                'http_errors' => false,
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

                // Pilih salah satu query secara acak dan panggil metode scrapeAndSaveKeywords
                if (!empty($titles)) {
                    $randomQuery = $titles[array_rand($titles)];
                    $this->keywordService->scrapeAndSaveKeywords($randomQuery);
                } else {
                    Log::info('Tidak ada query trending yang ditemukan.');
                }
            } else {
                Log::error('Gagal parse JSON.');
            }
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan dalam job FetchTrends: ' . $e->getMessage());
        }

        Log::info('Job selesai');
    }
}
