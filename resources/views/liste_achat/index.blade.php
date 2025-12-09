@extends('layouts.app')

@section('title', 'Liste d’achat')

@section('content')

<div class="p-4 lg:p-6 max-w-6xl mx-auto space-y-6">

    {{-- En-tête de page --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex-1">
            <x-page-header
                title="Ma liste d'achat"
                undertitle="Planifiez vos futurs achats de bouteilles en ajoutant des articles à votre liste d'achat." />
        </div>
    </div>
    <x-search-filter 
            :pays="$pays" 
            :types="$types" 
            :regions="$regions" 
            :millesimes="$millesimes" 
            url="/liste-achat/search" 
            suggestionUrl="/liste-achat/suggest"
            mode="listeAchat"
            containerID="listeAchatContainer" 
        />
    {{-- État vide --}}
    @if ($items->isEmpty())
        <x-empty-state
            title="Votre liste d’achat est vide"
            subtitle="Ajoutez des bouteilles à votre liste pour planifier vos achats futurs."
            actionLabel="Explorer le catalogue"
            actionUrl="{{ route('bouteille.catalogue') }}" />
    @endif

    {{-- Liste d'achat --}}
    <section id="listeAchatContainer" class="mt-4">
        @include('liste_achat._liste_achat_list', [
            'items' => $items,
            'count' => $items->total(),
            'isSearching' => false, // Pas de recherche active sur la page initiale
            'celliersCount' => $celliersCount ?? 0, // Nombre de celliers pour le texte du bouton
        ])
    </section>

    {{-- Boutons d'action --}}
    @if (!$items->isEmpty())
        <div class="flex flex-col sm:flex-row gap-2 justify-start mt-6">
            <button
                type="button"
                class="wishlist-transfer-all-btn flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold bg-button-default border-2 border-primary text-primary rounded-lg hover:bg-button-hover hover:text-white active:bg-primary-active transition-colors duration-300 whitespace-nowrap cursor-pointer"
                data-url="{{ route('listeAchat.transferAll') }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                </svg>
                Tout transférer
            </button>
            <button
                type="button"
                class="use-confirm bg-red-600 text-white py-2 px-4 rounded-lg font-bold hover:bg-red-700 transition whitespace-nowrap cursor-pointer"
                data-action="{{ route('listeAchat.destroyAll') }}"
                data-message="Êtes-vous sûr de vouloir supprimer toutes les bouteilles de votre liste d'achat ? Cette action est irréversible."
                data-ajax="true">
                Tout supprimer
            </button>
        </div>
    @endif

    {{-- Total --}}
    @if (!$items->isEmpty())
        <div class="mt-10">
            <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-6 md:p-8">

                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    Résumé de votre liste
                </h2>

                <div class="grid gap-4 sm:grid-cols-3">

                    {{-- Nombre total de bouteilles --}}
                    <div class="flex flex-col items-center justify-center bg-gray-50 rounded-xl py-4 px-3 border border-gray-100">
                        <span class="text-sm text-gray-500">Nombre de bouteilles</span>
                        <span id="totalItemContainer" class="text-2xl font-bold text-gray-900">
                            {{ $totalItem }}
                        </span>
                    </div>

                    {{-- Prix moyen --}}
                    <div class="flex flex-col items-center justify-center bg-gray-50 rounded-xl py-4 px-3 border border-gray-100">
                        <span class="text-sm text-gray-500 whitespace-nowrap">Prix moyen / bouteille</span>
                        <span id="averagePriceContainer"  class="text-2xl font-bold text-gray-900">
                            {{ number_format($avgPrice, 2, ',', ' ') }} $
                        </span>
                    </div>

                    {{-- Total --}}
                    <div class="flex flex-col items-center justify-center bg-gray-50 rounded-xl py-4 px-3 border border-gray-100">
                        <span class="text-sm text-gray-500">Total estimé</span>
                        <span id="totalPriceContainer"  class="text-2xl font-bold text-gray-900">
                            {{ number_format($totalPrice, 2, ',', ' ') }} $
                        </span>
                    </div>

                </div>

            </div>
        </div>
    @endif
</div>

@endsection
