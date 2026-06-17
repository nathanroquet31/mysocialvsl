<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/app/favicon.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    @if ($page && ! $page->bot_protection)
      {{-- Unprotected page: expose real Open Graph tags for rich link previews. --}}
      <title>{{ $page->meta_title ?: ($page->model_name ?: 'My Links') }}</title>
      <meta name="description" content="{{ $page->meta_description ?: $page->bio }}" />
      <meta property="og:type" content="website" />
      <meta property="og:title" content="{{ $page->meta_title ?: $page->model_name }}" />
      <meta property="og:description" content="{{ $page->meta_description ?: $page->bio }}" />
      @if ($page->og_image_url ?: $page->avatar_url)
        <meta property="og:image" content="{{ $page->og_image_url ?: $page->avatar_url }}" />
      @endif
    @else
      {{-- Protected / app shell: generic meta only. Real content is painted client-side. --}}
      <title>My Links</title>
      <meta name="description" content="Check out my latest content." />
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;0,14..32,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @foreach ($assets['css'] as $href)
      <link rel="stylesheet" href="{{ $href }}">
    @endforeach

    @if ($slug)
      <script>window.__VSL_SLUG__ = @json($slug);</script>
    @endif
  </head>
  <body>
    <div id="app"></div>
    @if ($assets['js'])
      <script type="module" src="{{ $assets['js'] }}"></script>
    @endif
  </body>
</html>
