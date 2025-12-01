{{-- Mise en page principale avec barre de navigation --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">


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
    <body class="bg-body">
        <x-header :logoPath='asset("images/logo_vino.png")' />
        
        <main class="bg-body mb-30" role="main" aria-label="Contenu principal">
            @yield('content')
        </main>
        
        {{-- Envoie quelle bouton a afficher sur la page. Voir navigation.blade.php --}}
        <x-navigation 
            :addCellarBtn="$__env->hasSection('add-cellar-btn')" 
            :addWineBtn="$__env->hasSection('add-wine-btn')"
        />
        <x-toast />
        <x-confirm-delete-modal />
        <x-modal-pick-cellar />

        @if(session('success') || session('error'))
            <script>
                function afficherToastFlash() {
                    if (window.showToast) {
                        @if(session('success'))
                            window.showToast({!! json_encode(session('success')) !!}, "success");
                        @endif
                        @if(session('error'))
                            window.showToast({!! json_encode(session('error')) !!}, "error");
                        @endif
                    } else {
                        setTimeout(afficherToastFlash, 50);
                    }
                }
                afficherToastFlash();
            </script>
        @endif
    </body>

    </html>