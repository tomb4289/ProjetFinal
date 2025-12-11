{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des usagers')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- En-tête --}}
    <x-page-header
        title="Gestion des usagers"
        undertitle="Consulter, rechercher et gérer les comptes membres."
    />

    {{-- Bouton vers le tableau de bord des statistiques --}}
    <div class="flex justify-end">
        <a href="{{ route('admin.statistics.index') }}"
           class="inline-flex items-center px-4 py-2 mb-2 rounded-lg text-sm font-medium
                  text-white bg-primary hover:bg-primary-hover
                  border border-transparent shadow-sm transition-colors">
            Voir les statistiques
        </a>
    </div>

    {{-- Conteneur Principal  --}}
    <div class="bg-card border border-border-base rounded-xl shadow-sm overflow-hidden">
        
        {{-- Barre d'outils / Recherche --}}
        <div class="p-4 border-b border-border-base bg-muted/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="w-full md:w-96">
                <x-search-form
                    :action="route('admin.users.index')"
                    name="q"
                    label="Rechercher par nom ou courriel..."
                    :value="$search"
                    class="w-full"
                />
            </div>
            <div class="text-sm text-text-muted hidden md:block">
                {{ $users->total() }} usagers trouvés
            </div>
        </div>

        {{--DESKTOP  --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-muted text-text-muted font-medium uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Utilisateur</th>
                        <th class="px-6 py-4 text-center">Rôle</th>
                        <th class="px-6 py-4 text-center">État</th>
                        <th class="px-6 py-4 text-center">Celliers</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-base bg-card">
                    @forelse($users as $user)
                        <tr class="hover:bg-card-hover transition-colors">
                            {{-- Utilisateur --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="font-semibold text-text-main hover:text-primary transition-colors">
                                            {{ $user->name }}
                                        </a>
                                        <div class="text-xs text-text-muted">{{ $user->email }}</div>
                                        <div class="text-[10px] text-text-light mt-0.5">
                                            Inscrit le {{ optional($user->created_at)->format('Y-m-d') }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Rôle --}}
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                    {{ $user->is_admin ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-gray-50 text-gray-600 border-gray-200' }}">
                                    {{ $user->is_admin ? 'Admin' : 'Usager' }}
                                </span>
                            </td>

                            {{-- État --}}
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium border
                                    {{ $user->is_active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                    <span class="relative flex h-2 w-2">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $user->is_active ? 'bg-green-400' : 'hidden' }}"></span>
                                      <span class="relative inline-flex rounded-full h-2 w-2 {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    </span>
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>

                            {{-- Celliers --}}
                            <td class="px-6 py-4 text-center font-medium text-text-secondary">
                                {{ $user->celliers_count ?? 0 }}
                            </td>

                            {{-- Actions  --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    {{-- Toggle Actif/Inactif --}}
                                    <form method="POST" action="{{ route('admin.users.toggle-active', $user->id) }}">
                                        @csrf
                                        <x-primary-btn
                                            type="submit"
                                            :label="$user->is_active ? 'Désactiver' : 'Activer'"
                                            class="!py-1.5 !px-3 !text-xs"
                                        />
                                    </form>

                                    {{-- Supprimer --}}
                                    <x-delete-btn
                                        :route="route('admin.users.destroy', $user->id)"
                                        variant="icon"
                                        label="Supprimer"
                                        :ajax="true"
                                        class="text-text-muted hover:text-red-600 transition-colors"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-text-muted">
                                Aucun usager trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- VUE MOBILE--}}
        <div class="md:hidden">
            <div class="divide-y divide-border-base">
                @forelse($users as $user)
                    <div class="p-4 bg-card space-y-4">
                        
                        {{-- Info User --}}
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="font-semibold text-text-main block">
                                        {{ $user->name }}
                                    </a>
                                    <span class="text-xs text-text-muted">{{ $user->email }}</span>
                                </div>
                            </div>
                        </div>

                        {{--  Badges & Stats --}}
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex gap-2">
                                <span class="px-2 py-1 rounded-md bg-gray-100 border border-gray-200 text-gray-700">
                                    {{ $user->is_admin ? 'Admin' : 'Usager' }}
                                </span>
                                <span class="px-2 py-1 rounded-md {{ $user->is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                            <div class="text-text-muted font-medium">
                                {{ $user->celliers_count ?? 0 }} cellier(s)
                            </div>
                        </div>

                        {{--  Actions  --}}
                        <div class="flex items-center gap-2 pt-2 border-t border-border-base">
                            <form method="POST" action="{{ route('admin.users.toggle-active', $user->id) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full py-2 px-3 text-xs font-medium text-center border rounded-md transition-colors
                                    {{ $user->is_active 
                                        ? 'border-border-base text-text-main hover:bg-muted' 
                                        : 'bg-primary text-white border-transparent hover:bg-primary-dark' }}">
                                    {{ $user->is_active ? 'Désactiver' : 'Activer' }}
                                </button>
                            </form>
                            
                            {{-- Bouton Supprimer  --}}
                            <div class="flex-none">
                                <x-delete-btn
                                    :route="route('admin.users.destroy', $user->id)"
                                    variant="icon"
                                    :ajax="true"
                                    class="p-2 text-text-muted hover:text-red-600 hover:bg-red-50 rounded-md transition-colors"
                                />
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-text-muted">
                        Aucun usager trouvé.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Pagination --}}
        <div class="bg-card px-4 py-3 border-t border-border-base">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection