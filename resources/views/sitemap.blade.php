<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- RSS Feed URLs -->
    @foreach ($rssUrls as $rssUrl)
        <url>
            <loc>{{ htmlspecialchars($rssUrl, ENT_XML1, 'UTF-8') }}</loc>
            <lastmod>{{ now()->toDateString() }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
        </url>
    @endforeach

    <!-- Keywords -->
    @foreach ($keywords as $keyword)
        <url>
            <loc>{{ htmlspecialchars($keyword['loc'], ENT_XML1, 'UTF-8') }}</loc>
            <lastmod>{{ $keyword['lastmod'] }}</lastmod>
            <changefreq>{{ $keyword['changefreq'] }}</changefreq>
            <priority>{{ $keyword['priority'] }}</priority>
        </url>
    @endforeach
</urlset>
