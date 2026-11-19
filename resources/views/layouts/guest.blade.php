<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Welcome') | W Articles</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/inkflow.css') }}">
</head>
<body class="auth-body"><div class="auth-layout">
    <section class="auth-showcase">
        <a class="brand" href="{{ route('login') }}">@include('partials.brand')</a>
        <div class="showcase-copy"><span class="eyebrow eyebrow-light">YOUR CREATIVE WORKSPACE</span><h1>Every great story starts with a <em>simple idea.</em></h1><p>Bring your ideas to life. Write, organize and manage your articles in one beautiful space.</p><div class="showcase-sample"><div class="sample-top"><span class="sample-icon">✎</span><span>YOUR WORKSPACE</span><span>● ● ●</span></div><div class="sample-article"><span class="sample-label">EDITOR'S PICK</span><div class="sample-lines"><span></span><span></span><span></span></div><div class="sample-footer"><span class="sample-avatar">A</span><span>Made for creators, by creators</span><span>↗</span></div></div></div></div>
        <p class="showcase-footer">A little space for your big ideas.</p>
    </section>
    <main class="auth-panel"><div class="auth-container"><a class="brand mobile-brand" href="{{ route('login') }}">@include('partials.brand')</a>@yield('content')</div></main>
</div></body>
</html>
