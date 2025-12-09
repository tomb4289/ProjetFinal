@extends('layouts.app-noNav')

@section('title', 'Welcome')

@section('content')
    <section class="bg-card p-5 flex flex-col justify-start items-center max-w-sm mx-auto min-h-screen border-border-base">
        <header class="flex flex-col justify-center items-center py-10 gap-4">
            <img src="{{ asset('images/logo_vino.png') }}" class="w-32" alt="Logo Vino">
            <h1 class="text-text-body">Gérer vos celliers, simplement</h1>
        </header>

        <div id="authForm" class="w-full flex flex-col">

            {{-- MESSAGES DE SESSION (compte désactivé, déconnexion, etc.) --}}
            @if (session('error'))
                <div class="mb-4 px-4 py-2 rounded bg-red-100 text-red-700 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 px-4 py-2 rounded bg-green-100 text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ERREURS GÉNÉRALES (comme “Les identifiants sont incorrects”) --}}
            @if ($errors->any())
                <div class="mb-4 px-4 py-2 rounded bg-red-50 text-red-700 text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="flex w-full mb-5">
                <button id="loginBtn"
                    class="flex-1 font-bold text-lg text-primary border-b border-primary text-center py-2 hover:bg-neutral-300 transition-colors duration-300">
                    Connexion
                </button>
                <button id="registerBtn"
                    class="flex-1 font-bold text-lg text-neutral-600 border-b border-neutral-600 text-center py-2 hover:bg-neutral-300 transition-colors duration-300">
                    S'inscrire
                </button>
            </div>

            <x-form-login id="loginForm" />
            <x-form-register id="registerForm" class="hidden" />

        </div>
    </section>
@endsection
