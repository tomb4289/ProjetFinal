{{-- resources/views/admin/users/show.blade.php --}}
@extends('layouts.app')

@section('title', "Détails de l’usager")

@section('content')
<div class="max-w-7xl mt-15 mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    {{-- En-tête de navigation --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-6">
        <div>
            <x-back-btn
                route="admin.users.index"
                label="Retour à la liste"
                class="mb-2" />
            
            <x-page-header 
                :title="'Usager #'.$user->id.' – '.$user->name"
                subtitle="Gestion globale du compte et des données."
                marginTop="mt-2"
            />
        </div>
        
        {{-- Badges de statut mis en évidence en haut --}}
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border
                {{ $user->is_admin ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-gray-50 text-gray-600 border-gray-200' }}">
                {{ $user->is_admin ? 'Administrateur' : 'Usager standard' }}
            </span>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border
                {{ $user->is_active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                <span class="w-2 h-2 rounded-full mr-2 {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                {{ $user->is_active ? 'Compte Actif' : 'Compte Inactif' }}
            </span>
        </div>
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Identité & Actions --}}
        <div class="lg:col-span-4 space-y-6">
            
            {{-- Informations de contact --}}
            <div class="bg-card rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50/50 px-4 py-3 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">Profil & Coordonnées</h2>
                </div>
                <div class="p-5">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="h-12 w-12 rounded-full bg-button-default text-white flex items-center justify-center text-xl font-bold uppercase">
                            {{-- Obtien la premiere lettre du nom --}}
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-text-muted">ID: {{ $user->id }}</p>
                        </div>
                    </div>

                    <dl class="space-y-4 text-sm">
                        <div class="flex flex-col">
                            <dt class="text-xs uppercase tracking-wider text-text-muted font-medium mb-1">Courriel</dt>
                            <dd class="font-medium text-gray-800 break-all">{{ $user->email }}</dd>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div>
                                <dt class="text-xs uppercase tracking-wider text-text-muted font-medium mb-1">Inscription</dt>
                                <dd class="text-gray-700">{{ optional($user->created_at)->format('Y-m-d') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wider text-text-muted font-medium mb-1">Dernier accès</dt>
                                <dd class="text-gray-700">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d') : '-' }}</dd>
                            </div>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Zone Administrative --}}
            <div class="bg-card rounded-xl shadow-sm border border-gray-100">
                <div class="bg-gray-50/50 px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-base font-semibold text-gray-800">Administration</h2>
                </div>
                
                <div class="p-5 space-y-6">
                    {{-- Toggle Active --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Accès au compte</span>
                        </div>
                        <p class="text-xs text-text-muted mb-3">
                            Bloquer ou débloquer l'accès de cet utilisateur à la plateforme.
                        </p>
                        <form method="POST" action="{{ route('admin.users.toggle-active', $user->id) }}">
                            @csrf
                            <x-primary-btn
                                type="submit"
                                class="w-full justify-center"
                                :label="$user->is_active ? 'Désactiver le compte' : 'Activer le compte'" 
                            />
                        </form>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Delete Zone --}}
                    <div>
                        <span class="text-sm font-medium text-red-600 block mb-2">Zone de danger</span>
                        <p class="text-xs text-text-muted mb-3">
                            La suppression est définitive et effacera tous les celliers associés.
                        </p>
                        <x-delete-btn
                            :route="route('admin.users.destroy', $user->id)"
                            variant="menu"
                            label="Supprimer l’usager"
                        />
                    </div>
                </div>
            </div>
        </div>

        {{--  Contenu (Celliers) --}}
        <div class="lg:col-span-8">
            <div class="bg-card rounded-xl shadow-sm border border-gray-100 h-full">
                <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Inventaire des Celliers</h2>
                    <span class="bg-white border border-gray-200 text-gray-600 px-3 py-1 rounded-full text-xs font-bold shadow-sm">
                        Total : {{ $user->celliers_count ?? 0 }}
                    </span>
                </div>

                <div class="p-6">
                    @if ($user->celliers->isEmpty())
                        <div class="text-center py-10">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <p class="text-text-muted font-medium">Cet usager n’a créé aucun cellier pour le moment.</p>
                        </div>
                    @else
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach($user->celliers as $cellier)
                                <div class="group relative bg-white border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-button-default/10 rounded-lg text-button-default">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-900">
                                                    {{ $cellier->nom ?? 'Cellier sans nom' }}
                                                </h3>
                                                <p class="text-xs text-text-muted">ID: {{ $cellier->id }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Placeholder pour futures stats par cellier si besoin --}}
                                    {{-- <div class="mt-3 pt-3 border-t border-gray-50 flex justify-end">
                                        <span class="text-xs text-text-muted font-medium flex items-center">
                                            Voir le détail &rarr;
                                        </span>
                                    </div> --}}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection