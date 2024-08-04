<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ $metaKeywords }}">
    <meta name="google-site-verification" content="jCUHDhH4c9xNre-89FrR2nHeR1w_mPtT5oyGJ6MMUkc" />
    <title>{{ $title }}</title>
</head>
<body>
    <p>{{ $keywords }}</p>

    @if(!empty($popular))
        <h2>Maybe interest</h2>
        <ul>
            @foreach($popular as $item)
                <li><a href="{{ url(($item['title'])) }}">{{ $item['title'] }}</a></li>
            @endforeach
        </ul>
    @endif
</body>
</html>
