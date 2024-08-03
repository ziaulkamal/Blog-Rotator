<?php

namespace App\Services;

use App\Models\KeywordData;
use Illuminate\Support\Facades\Http;

class KeywordService
{
    public function scrapeAndSaveKeywords($any)
    {
        $apiKey = 'AIzaSyDDpxGjixl23nxUASuOdb4ydhRdr4zgpI8';
        $cseId = '918c00d62fdd34400';
        $query = urlencode($any);
        $url = "https://www.googleapis.com/customsearch/v1?q=$query&key=$apiKey&cx=$cseId";

        $response = Http::get($url);
        $data = $response->json();

        $suggestions = [];
        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                $suggestions[] = $item['title'];
            }
        }

        // Save keywords to the database
        foreach ($suggestions as $suggestion) {
            $keywordData = KeywordData::firstOrCreate(['keyword' => $suggestion]);
            $keywordData->increment('hit');
        }
    }
}
