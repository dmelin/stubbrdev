<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>500 | Server Error</title>
    <style>
        body { background: #1a202c; color: #fff; font-family: sans-serif; text-align: center; padding: 50px; }
        pre { text-align: left; background: #2d3748; padding: 20px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>500 | SERVER ERROR</h1>

    @if(config('app.debug') && isset($exception))
        <div style="max-width: 800px; margin: 40px auto;">
            <h2>{{ get_class($exception) }}</h2>
            <pre>{{ $exception->getMessage() }}</pre>
            <pre>{{ $exception->getFile() }} : {{ $exception->getLine() }}</pre>
            <pre>{{ $exception->getTraceAsString() }}</pre>
        </div>
    @endif
</body>
</html>
