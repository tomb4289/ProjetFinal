{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des usagers')

@section('content')
<div class="max-w-6xl mx-auto px-4 space-y-6">

    {{-- Titre de page --}}
    <div class="pt-5 pb-2">
        <x-page-header
            title="Gestion des usagers"
            subtitle="Consulter, rechercher et gérer les comptes (activation, désactivation, suppression)."
        />
    </div>

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

    {{-- Barre de recherche --}}
    <x-search-form
        :action="route('admin.users.index')"
        name="q"
        label="Rechercher un usager"
        :value="$search"
        class="mt-2"
    />

    {{-- ====== VERSION TABLE (TABLETTE / DESKTOP) ====== --}}
    <div class="hidden md:block">
        <div class="bg-card rounded-xl shadow overflow-x-auto mt-4">
            <table class="min-w-full text-sm">
                <thead class="bg-muted">
                    <tr class="text-left text-xs font-semibold text-text-muted uppercase">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Nom</th>
                        <th class="px-4 py-3">Courriel</th>
                        <th class="px-4 py-3">Inscription</th>
                        <th class="px-4 py-3 text-center">Celliers</th>
                        <th class="px-4 py-3">Rôle</th>
                        <th class="px-4 py-3">État</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-t border-border-base hover:bg-card-hover">
                            <td class="px-4 py-3">{{ $user->id }}</td>

                            <td class="px-4 py-3">
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                   class="font-semibold text-button-default hover:underline">
                                    {{ $user->name }}
                                </a>
                            </td>

                            <td class="px-4 py-3">{{ $user->email }}</td>

                            <td class="px-4 py-3">
                                {{ optional($user->created_at)->format('Y-m-d') }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                {{ $user->celliers_count ?? 0 }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                    {{ $user->is_admin ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $user->is_admin ? 'Admin' : 'Usager' }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                    {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right whitespace-nowrap space-x-2">
                                {{-- Activer / désactiver --}}
                                <form method="POST"
                                      action="{{ route('admin.users.toggle-active', $user->id) }}"
                                      class="inline">
                                    @csrf
                                    <x-primary-btn
                                        type="submit"
                                        :label="$user->is_active ? 'Désactiver' : 'Activer'"
                                        class="!inline-block !py-1 !px-3 text-xs"
                                    />
                                </form>

                                {{-- Supprimer --}}
                                <x-delete-btn
                                    :route="route('admin.users.destroy', $user->id)"
                                    class="align-middle"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-text-muted">
                                Aucun usager trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ====== VERSION MOBILE (CARDS) ====== --}}
    <div class="md:hidden mt-4 space-y-3">
        @forelse($users as $user)
            <div class="bg-card rounded-xl shadow p-4 text-sm space-y-2">
                {{-- Ligne haut : nom + badges --}}
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <a href="{{ route('admin.users.show', $user->id) }}"
                           class="font-semibold text-button-default hover:underline">
                            {{ $user->name }}
                        </a>
                        <p class="text-xs text-text-muted break-all">
                            {{ $user->email }}
                        </p>
                    </div>

                    <div class="flex flex-col items-end gap-1">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                            {{ $user->is_admin ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $user->is_admin ? 'Admin' : 'Usager' }}
                        </span>

                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                            {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $user->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                </div>

                {{-- Infos secondaires --}}
                <div class="flex justify-between text-xs text-text-muted mt-1">
                    <span>Inscription : {{ optional($user->created_at)->format('Y-m-d') }}</span>
                    <span>Celliers : {{ $user->celliers_count ?? 0 }}</span>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between gap-2 pt-2 border-t border-border-base mt-2">
                    <form method="POST"
                          action="{{ route('admin.users.toggle-active', $user->id) }}">
                        @csrf
                        <x-primary-btn
                            type="submit"
                            :label="$user->is_active ? 'Désactiver' : 'Activer'"
                            class="!py-1 !px-3 text-xs"
                        />
                    </form>

                    <x-delete-btn
                        :route="route('admin.users.destroy', $user->id)"
                    />
                </div>
            </div>
        @empty
            <p class="text-center text-text-muted text-sm">
                Aucun usager trouvé.
            </p>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $users->links() }}
    </div>

</div>
@endsection
