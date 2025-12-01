{{-- Mise en page sans barre de navigation --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', "Vino")</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            {{-- FALLBACK: Vite build files not found! Run: npm run build --}}
            <style>
                body { background: #ff0000 !important; color: #fff !important; font-family: monospace !important; }
                body::before { content: "⚠️ BUILD ERROR: Vite assets not found! Run: npm run build"; display: block; padding: 20px; background: #ff0000; color: #fff; font-size: 18px; font-weight: bold; text-align: center; }
            </style>
        @endif
    </head>
    {{-- Formulaire login par default --}}
    <body data-mode="{{ $mode ?? 'login' }}"> 
        {{-- Height fixer temporaire --}}
        <main class="bg-body" role="main" aria-label="Contenu principal">
            @yield('content')
        </main>
    </body>

</html>