<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PingSitemap extends Controller
{
   public function pingSitemap(Request $request)
    {
        // URL sitemap Anda
        $sitemapUrl = env('APP_URL').'sitemap.xml';


        // Daftar URL ping
        $pingUrls = [
            'https://www.google.com/ping?sitemap=' . urlencode($sitemapUrl),
            'https://www.bing.com/webmasters/sitemaps/ping?sitemap=' . urlencode($sitemapUrl),
            'http://submissions.ask.com/ping?sitemap=' . urlencode($sitemapUrl),
            'http://ping.baidu.com/ping/RPC2?url=' . urlencode($sitemapUrl),
            'http://www.sogou.com/sogou?query=' . urlencode($sitemapUrl),
            'http://data.360.cn/ping?site=' . urlencode($sitemapUrl),
            'http://pingomatic.com/?sitemap=' . urlencode($sitemapUrl),
            'https://pingler.com/',
            'http://pingfarm.com/',
            'http://www.blogpingtool.com/',
            'http://www.pingmyurl.com/',
            'https://majestic.com/',
            'https://ahrefs.com/',
            'https://www.semrush.com/',
            'https://moz.com/',
            'https://www.scraperapi.com/',
            'https://serpstat.com/',
            'https://neilpatel.com/ubersuggest/',
            'http://www.gigablast.com/',
            'https://www.search.com/'
        ];

        foreach ($pingUrls as $pingUrl) {
            try {
                // Mengirimkan permintaan HTTP GET
                $response = file_get_contents($pingUrl);
                // Log atau tangani respons jika diperlukan
            } catch (\Exception $e) {
                // Tangani kesalahan jika ping gagal
            }
        }

        return response()->json(['message' => 'Ping completed.']);
    }

}
