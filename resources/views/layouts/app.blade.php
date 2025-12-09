{{-- Mise en page principale avec barre de navigation --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicons --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <title>@yield('title', "Vino")</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <!-- Pré-connexion pour limiter la latence vers fonts.bunny.net -->
    <link rel="preconnect" href="https://fonts.bunny.net">

    <!-- Fonts avec display=swap pour éviter le flash de texte invisible -->
    <link
        href="https://fonts.bunny.net/css?family=caveat:400,500,600,700&display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap"
        rel="stylesheet" />


    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    {{-- FALLBACK: Vite build files not found! Run: npm run build --}}
    <style>
        body {
            background: #ff0000 !important;
            color: #fff !important;
            font-family: monospace !important;
        }

        body::before {
            content: "⚠️ BUILD ERROR: Vite assets not found! Run: npm run build";
            display: block;
            padding: 20px;
            background: #ff0000;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }
    </style>
    @endif
</head>

<body class="bg-body">
    {{-- Page Loading Overlay --}}
    {{-- Covers entire viewport, header (z-50) and navigation (z-30) stay on top --}}
    <div id="page-loading-overlay" class="fixed inset-0 bg-gray-50 z-[25] flex items-center justify-center hidden" aria-hidden="true" aria-label="Chargement de la page"></div>

    <x-header :logoPath='asset("images/logo_vino.png")' />

    <main class="bg-body mb-30 max-w-6xl mx-auto" role="main" aria-label="Contenu principal">
        @yield('content')
    </main>

    {{-- Envoie quelle bouton a afficher sur la page. Voir navigation.blade.php --}}
    @php
        $canCreateMore = true;
        if (Auth::check() && $__env->hasSection('add-cellar-btn')) {
            $user = Auth::user();
            $celliersCount = $user->celliers()->count();
            $canCreateMore = $celliersCount < 6;
        }
    @endphp
    <x-navigation
        :addCellarBtn="$__env->hasSection('add-cellar-btn')"
        :addWineBtn="$__env->hasSection('add-wine-btn')"
        :canCreateMore="$canCreateMore" />
    <x-toast />
    <x-typewriter-toast />
    <x-confirm-delete-modal />
    <x-modal-pick-cellar />
    <x-share-modal />

    {{-- Shared HTML Templates --}}
    <template id="spinner-inline-template">
        <div
            class="inline-block w-6 h-6 border-2 border-neutral-200 border-t-primary rounded-full animate-spin"
            role="status"
            aria-label="Loading..."></div>
    </template>

    <template id="loading-spinner-template">
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>
    </template>

    @if(session('success') || session('error'))
    <script>
        // Affiche les messages flash sous forme de toast dès que showToast est chargé
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