@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="max-w-lg mx-auto p-4 flex flex-col gap-8" role="region" aria-label="Gestion du profil utilisateur">

    {{-- TITRE --}}
    <h1 class="text-3xl font-bold text-center">Mon Profil</h1>

    {{-- INFOS --}}
    <section class="bg-card p-4 rounded-lg shadow border border-border-base" aria-label="Informations personnelles">
        <h2 class="text-xl font-semibold mb-4">Informations personnelles</h2>

        {{-- Le formulaire pointe vers 'profile.updateInfo', qui doit exister même si la méthode dans le contrôleur est vide --}}
        <form 
            method="POST" 
            action="{{ route('profile.updateInfo') }}" 
            class="flex flex-col gap-4"
            aria-label="Formulaire de mise à jour des informations"
        >
            @csrf
            <x-input label="Nom" name="name" value="{{ $user->name }}" required />

            <x-input label="Courriel" name="email" type="email" value="{{ $user->email }}" required />

            <x-primary-btn type="submit" label="Mettre à jour" />
        </form>
    </section>

    {{-- MOT DE PASSE --}}
    <section class="bg-card p-4 rounded-lg shadow border border-border-base" aria-label="Sécurité du mot de passe">
        <h2 class="text-xl font-semibold mb-4">Changer le mot de passe</h2>

        {{-- Le formulaire pointe vers 'profile.updatePassword', qui doit exister --}}
        <form 
            method="POST" 
            action="{{ route('profile.updatePassword') }}" 
            class="flex flex-col gap-4"
            aria-label="Formulaire de changement de mot de passe"
        >
            @csrf

            <x-input label="Mot de passe actuel" name="current_password" type="password" required />

            <x-input label="Nouveau mot de passe" name="password" type="password" required />

            <x-input label="Confirmer le mot de passe" name="password_confirmation" type="password" required />

            <x-primary-btn type="submit" label="Changer le mot de passe" />
        </form>
    </section>

    {{-- DÉCONNEXION --}}
    <section class="bg-card p-4 rounded-lg shadow border border-border-base" aria-label="Zone de déconnexion">
        <h2 class="text-xl font-semibold mb-4">Sécurité</h2>

        <form method="POST" action="{{ route('logout') }}" aria-label="Déconnexion de l'application">
            @csrf
            <button 
                type="submit"
                class="w-full bg-red-600 text-white py-3 rounded-lg font-semibold hover:bg-red-700 transition"
                aria-label="Se déconnecter de la session"
            >
                Déconnexion
            </button>
        </form>
    </section>

</div>
@endsection