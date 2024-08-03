<?=
'<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

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
