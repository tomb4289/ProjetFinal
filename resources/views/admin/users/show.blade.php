{{-- resources/views/admin/users/show.blade.php --}}
@extends('layouts.app')

@section('title', "Détails de l’usager")

@section('content')
<div class="max-w-6xl mx-auto px-4 mt-8 space-y-6">

    <x-back-btn
        route="admin.users.index"
        label="Retour à la liste"
        class="pt-20" />


    {{-- Titre de la page --}}
    <x-page-header 
        :title="'Usager #'.$user->id.' – '.$user->name"
        subtitle="Détails du compte et actions d’administration." />

    {{-- Messages flash --}}
    @if(session('success'))
    <div class="bg-green-100 text-green-800 text-sm px-4 py-2 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 text-red-800 text-sm px-4 py-2 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <div class="grid gap-6 md:grid-cols-2">
        {{-- Carte informations usager --}}
        <div class="bg-card rounded-xl shadow p-4">
            <h2 class="text-lg font-semibold mb-3">Informations du compte</h2>

            <dl class="text-sm space-y-2">
                <div>
                    <dt class="font-medium text-text-muted">Nom</dt>
                    <dd class="font-semibold text-button-default">
                        {{ $user->name }}
                    </dd>
                </div>

                <div>
                    <dt class="font-medium text-text-muted">Courriel</dt>
                    <dd>{{ $user->email }}</dd>
                </div>

                <div>
                    <dt class="font-medium text-text-muted">Date d’inscription</dt>
                    <dd>{{ optional($user->created_at)->format('Y-m-d H:i') }}</dd>
                </div>

                <div>
                    <dt class="font-medium text-text-muted">Dernière connexion</dt>
                    <dd>
                        {{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'Jamais' }}
                    </dd>
                </div>

                <div>
                    <dt class="font-medium text-text-muted">Rôle</dt>
                    <dd>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                            {{ $user->is_admin ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $user->is_admin ? 'Administrateur' : 'Usager' }}
                        </span>
                    </dd>
                </div>

                <div>
                    <dt class="font-medium text-text-muted">État du compte</dt>
                    <dd>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                            {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $user->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </dd>
                </div>

                <div>
                    <dt class="font-medium text-text-muted">Nombre de celliers</dt>
                    <dd>{{ $user->celliers_count ?? 0 }}</dd>
                </div>
            </dl>
        </div>

        {{-- Carte actions admin --}}
        <div class="bg-card rounded-xl shadow p-4">
            <h2 class="text-lg font-semibold mb-3">Actions d’administration</h2>

            {{-- Activer / désactiver --}}
            <form method="POST" action="{{ route('admin.users.toggle-active', $user->id) }}" class="mb-4 space-y-2">
                @csrf

                <p class="text-sm text-text-muted">
                    État actuel :
                    <span class="{{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">
                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </p>

                <x-primary-btn
                    type="submit"
                    :label="$user->is_active ? 'Désactiver le compte' : 'Activer le compte'" />
            </form>

            {{-- Supprimer --}}
            <div class="space-y-2">
                <p class="text-sm text-text-muted">
                    Supprimer définitivement ce compte (action irréversible).
                </p>

                <x-delete-btn :route="route('admin.users.destroy', $user->id)" />
            </div>
        </div>
    </div>

    {{-- Liste des celliers --}}
    <div class="bg-card rounded-xl shadow p-4">
        <h2 class="text-lg font-semibold mb-3">Celliers de l’usager</h2>

        @if ($user->celliers->isEmpty())
        <p class="text-sm text-text-muted">Cet usager n’a aucun cellier.</p>
        @else
        <ul class="text-sm list-disc pl-5 space-y-1">
            @foreach($user->celliers as $cellier)
            <li>
                {{ $cellier->nom ?? 'Cellier sans nom' }} (ID: {{ $cellier->id }})
            </li>
            @endforeach
        </ul>
        @endif
    </div>

</div>
@endsection