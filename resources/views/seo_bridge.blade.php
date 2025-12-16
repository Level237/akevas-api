<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>{{ $data['title'] }}</title>
    <meta name="description" content="{{ $data['description'] }}">

    <meta property="og:title" content="{{ $data['title'] }}">
    <meta property="og:description" content="{{ $data['description'] }}">
    <meta property="og:image" content="{{ $data['image'] }}">
    <meta property="og:url" content="{{ $data['url'] }}">
    <meta property="og:type" content="product">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ $data['image'] }}">
</head>

<body>
    <h1>{{ $data['title'] }}</h1>
    <p>{{ $data['description'] }}</p>
    <img src="{{ $data['image'] }}" />
</body>

</html>