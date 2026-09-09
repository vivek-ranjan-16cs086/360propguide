<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($items as $item)
    <url>
        <loc>{{ $item['loc'] }}</loc>
        @if (!empty($item['lastmod']))
            <lastmod>{{ $item['lastmod'] }}</lastmod>
        @endif
        <priority>{{ $item['priority'] }}</priority>
    </url>
@endforeach
</urlset>
