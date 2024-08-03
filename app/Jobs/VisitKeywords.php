<?php

namespace App\Jobs;

use App\Models\KeywordData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VisitKeywords implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('VisitKeywords job started.');

        try {
            // Ambil hingga 100 keyword dari database
            $keywords = KeywordData::limit(100)->pluck('keyword')->toArray();

            foreach ($keywords as $keyword) {
                $url = route('your.route.name', ['any' => urlencode($keyword)]);

                // Lakukan permintaan GET ke URL
                $response = Http::get($url);

                // Log atau proses respons sesuai kebutuhan
                Log::info('Visited URL: ' . $url . ' with response status: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Error in VisitKeywords job: ' . $e->getMessage());
        }

        Log::info('VisitKeywords job finished.');
    }
}
