<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Stubbr is an instant mock API that simulates failed responses, latency and awkward payloads, and that you don't have to rip out once the real backend arrives.">
    <meta name="theme-color" content="#14121a">
    <meta property="og:title" content="Stubbr — fake the API, break the API, keep it forever">
    <meta property="og:description" content="An instant mock API with failures, latency and fake data built in. No signup, no cleanup.">
    <meta property="og:type" content="website">
    <link rel="icon" type="image/png" href="/media/favicon.png">
    <title>Stubbr — fake the API, break the API, keep it forever</title>
    <script>
        (function () {
            try {
                var theme = localStorage.getItem('stubbr_theme_v2');
                if (theme !== 'light' && theme !== 'dark') {
                    var legacy = localStorage.getItem('stubbr_theme_v1');
                    theme = (legacy === 'ice' || legacy === 'mustard') ? 'light' : 'dark';
                }
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700;12..96,800&family=IBM+Plex+Mono:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
    <noscript>
        <p style="padding: 24px; font-family: system-ui, sans-serif;">Stubbr's builder needs JavaScript. The API itself works from anywhere: see the README on GitHub.</p>
    </noscript>
</body>
</html>
