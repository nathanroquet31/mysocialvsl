<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>My Links</title>
    <meta name="description" content="Check out my latest content." />
    {{-- No-JS fallback: still send the visitor to the destination. --}}
    <noscript><meta http-equiv="refresh" content="0;url={{ $url }}" /></noscript>
    <style>
      body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0d0d0d; color: #fff; margin: 0; }
      .wrap { max-width: 480px; margin: 0 auto; padding: 96px 24px; text-align: center; }
      .logo { width: 48px; height: 48px; border-radius: 12px; background: #6D4EE8; margin: 0 auto 18px; display: flex; align-items: center; justify-content: center; }
      .logo span { color: #fff; font-weight: 900; font-size: 14px; }
      h1 { font-size: 18px; font-weight: 700; margin: 0 0 8px; }
      p { color: rgba(255,255,255,0.55); font-size: 14px; margin: 0; }
    </style>
  </head>
  <body>
    <div class="wrap">
      <div class="logo"><span>VSL</span></div>
      <h1>Opening…</h1>
      <p>Taking you there. If nothing happens, <a href="{{ $url }}" style="color:#A78BFA">tap here</a>.</p>
    </div>

    <script>
      (function () {
        var url = @json($url);
        var deepLink = @json((bool) $deepLink);
        var ua = navigator.userAgent || '';
        var isInApp = /Instagram|FBAN|FBAV|TikTok/.test(ua);
        var isAndroid = /Android/.test(ua);

        // Mirror the SPA's deep-link bypass so direct links escape the in-app
        // webview (Instagram/Facebook/TikTok) instead of opening trapped inside it.
        if (deepLink && isInApp) {
          var enc = encodeURIComponent(url);
          if (isAndroid) {
            try {
              var u = new URL(url);
              window.location.href = 'intent://' + u.host + u.pathname + u.search +
                '#Intent;scheme=https;S.browser_fallback_url=' + enc + ';end';
              return;
            } catch (e) { /* fall through to plain redirect */ }
          } else {
            // iOS in-app browser: try to bounce out to the system browser,
            // then fall back to the plain URL if the scheme is ignored.
            window.location.href = 'instagram://extbrowser/?url=' + enc;
            setTimeout(function () { window.location.replace(url); }, 1500);
            return;
          }
        }

        window.location.replace(url);
      })();
    </script>
  </body>
</html>
