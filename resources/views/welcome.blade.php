<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/media/favicon.png">
    <title>Stubbr.dev</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a
        id="github-topbar-link"
        href="https://github.com/dmelin/stubbrdev"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Stubbr GitHub repository"
    >
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 2C6.48 2 2 6.59 2 12.24c0 4.52 2.87 8.35 6.84 9.7.5.1.68-.22.68-.5v-1.75c-2.78.62-3.37-1.38-3.37-1.38-.45-1.18-1.11-1.5-1.11-1.5-.91-.64.07-.63.07-.63 1 .07 1.53 1.06 1.53 1.06.9 1.58 2.35 1.12 2.92.86.09-.67.35-1.12.64-1.38-2.22-.26-4.55-1.14-4.55-5.06 0-1.12.39-2.03 1.03-2.75-.1-.26-.45-1.31.1-2.73 0 0 .85-.28 2.78 1.05A9.4 9.4 0 0 1 12 6.84c.85 0 1.72.12 2.53.36 1.92-1.33 2.77-1.05 2.77-1.05.55 1.42.2 2.47.1 2.73.64.72 1.03 1.63 1.03 2.75 0 3.93-2.33 4.8-4.56 5.05.36.32.68.94.68 1.89v2.8c0 .28.18.61.69.5A10.25 10.25 0 0 0 22 12.24C22 6.59 17.52 2 12 2Z" />
        </svg>
    </a>
    <div id="bmc-topbar-slot">
        <script type="text/javascript" src="https://cdnjs.buymeacoffee.com/1.0.0/button.prod.min.js" data-name="bmc-button" data-slug="melin" data-color="#FFDD00" data-emoji="" data-font="Cookie" data-text="Buy me a coffee" data-outline-color="#000000" data-font-color="#000000" data-coffee-color="#ffffff"></script>
    </div>
    <div id="app"></div>
</body>
</html>
