<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($keywords as $keyword)
        <url>
            <loc>{{ $keyword['loc'] }}</loc>
            <lastmod>{{ $keyword['lastmod'] }}</lastmod>
            <changefreq>{{ $keyword['changefreq'] }}</changefreq>
            <priority>{{ $keyword['priority'] }}</priority>
        </url>
    @endforeach
</urlset>
