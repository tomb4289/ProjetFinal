@extends('layouts.app')

@section('title', 'Liste d’achat')

@section('content')

<div class="p-4 lg:p-6 max-w-6xl mx-auto space-y-6">

    {{-- En-tête de page --}}
    <x-page-header
        title="Ma liste d’achat"
        undertitle="Planifiez vos futurs achats de bouteilles en ajoutant des articles à votre liste d’achat." />

    {{-- État vide --}}
    @if ($items->isEmpty())
    <x-empty-state
        title="Votre liste d’achat est vide"
        subtitle="Ajoutez des bouteilles à votre liste pour planifier vos achats futurs."
        actionLabel="Explorer le catalogue"
        actionUrl="{{ route('bouteille.catalogue') }}" />
    @endif

    {{-- Liste d’achat --}}
    <section class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @foreach ($items as $item)
        @php
        $b = $item->bouteilleCatalogue;
        @endphp

        <div class="relative flex flex-col rounded-2xl border border-gray-200 bg-white/80 shadow-sm 
                        hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 overflow-hidden">

            {{-- Bouton d’action (menu) --}}
            <x-dropdown-action
                :item="$item"
                deleteUrl="{{ route('listeAchat.destroy', $item) }}" />

            {{-- Image --}}
            <div class="max-h-[160px] bg-gray-50 border-b border-gray-100 flex items-center justify-center 
                            overflow-hidden aspect-3/4 py-3">
                @if ($b->image)
                <img src="{{ $b->image }}"
                    alt="Image {{ $b->nom }}"
                    class="max-w-[96px] max-h-[160px] object-contain">
                @else
                <x-dynamic-component
                    :component="'lucide-wine'"
                    class="w-7 h-7 text-primary/60" />
                @endif
            </div>

            {{-- Texte --}}
            <div class="flex-1 p-4 flex flex-col gap-2">
                {{-- Marquer comme acheté --}}
                <div class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        class="wishlist-check-achete"
                        data-url="{{ route('listeAchat.update', $item) }}"
                        data-item-id="{{ $item->id }}"
                        @checked($item->achete)
                    >
                    <span class="{{ $item->achete ? 'line-through text-gray-400' : '' }} text-xs font-medium">
                        Acheté
                    </span>
                </div>

                <p class="font-semibold text-gray-900 text-sm leading-tight line-clamp-2">
                    {{ $b->nom }}
                </p>

                {{-- pays + format --}}
                <p class="text-xs text-gray-500">
                    {{ $b->pays->nom ?? 'Pays inconnu' }} —
                    {{ $b->volume ?? 'Format inconnu' }}
                </p>

                {{-- prix / quantité / sous-total --}}
                <div class="mt-2 space-y-1 text-xs">
                    <p class="text-gray-600">
                        Prix : <span class="font-semibold">{{ number_format($b->prix, 2, ',', ' ') }} $</span>
                    </p>

                    <p class="text-gray-700">
                        Sous-total :
                        <span class="font-semibold">
                            {{ number_format($b->prix * $item->quantite, 2, ',', ' ') }} $
                        </span>
                    </p>

                    {{-- CONTRÔLE QUANTITÉ --}}
                    <div
                        class="flex items-center justify-between bg-neutral-50 border border-border-base rounded-full 
                                   p-0.5 sm:p-1 shadow-sm w-full max-w-[120px] sm:max-w-[150px]">

                        {{-- Bouton - --}}
                        <button
                            type="button"
                            class="wishlist-qty-btn flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-full 
                                       text-text-muted hover:text-danger hover:bg-white hover:shadow-sm transition-all duration-200 
                                       active:scale-95"
                            data-direction="down"
                            data-url="{{ route('listeAchat.update', $item) }}"
                            data-item-id="{{ $item->id }}">
                            <x-dynamic-component :component="'lucide-minus'" class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                        </button>

                        {{-- Affichage quantité --}}
                        <div
                            class="wishlist-qty-display text-xs sm:text-sm font-bold text-text-heading text-center select-none px-1"
                            data-item-id="{{ $item->id }}"
                            data-url="{{ route('listeAchat.update', $item) }}">
                            {{ $item->quantite }}
                        </div>

                        {{-- Bouton + --}}
                        <button
                            type="button"
                            class="wishlist-qty-btn flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-full 
                                       text-text-muted hover:text-primary hover:bg-white hover:shadow-sm transition-all duration-200 
                                       active:scale-95"
                            data-direction="up"
                            data-url="{{ route('listeAchat.update', $item) }}"
                            data-item-id="{{ $item->id }}">
                            <x-dynamic-component :component="'lucide-plus'" class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                        </button>

                    </div>
                </div>
            </div>

        </div>
        @endforeach
    </section>
    {{-- Pagination --}}
    <div class="mt-6 flex-1 w-full ">
        {{ $items->links() }}
    </div>

    {{-- Total --}}
    @if (!$items->isEmpty())
    <div class="mt-10">
        <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-6 md:p-8">

            <h3 class="text-xl font-semibold text-gray-800 mb-4">
                Résumé de votre liste
            </h3>

            <div class="grid gap-4 sm:grid-cols-3">

                {{-- Nombre total de bouteilles --}}
                <div class="flex flex-col items-center justify-center bg-gray-50 rounded-xl py-4 px-3 border border-gray-100">
                    <span class="text-sm text-gray-500">Nombre de bouteilles</span>
                    <span class="text-2xl font-bold text-gray-900">
                        {{ $totalItem }}
                    </span>
                </div>

                {{-- Prix moyen --}}
                <div class="flex flex-col items-center justify-center bg-gray-50 rounded-xl py-4 px-3 border border-gray-100">
                    <span class="text-sm text-gray-500 whitespace-nowrap">Prix moyen / bouteille</span>
                    <span class="text-2xl font-bold text-gray-900">
                        {{ number_format($avgPrice, 2, ',', ' ') }} $
                    </span>
                </div>

                {{-- Total --}}
                <div class="flex flex-col items-center justify-center bg-gray-50 rounded-xl py-4 px-3 border border-gray-100">
                    <span class="text-sm text-gray-500">Total estimé</span>
                    <span class="text-2xl font-bold text-gray-900">
                        {{ number_format($totalPrice, 2, ',', ' ') }} $
                    </span>
                </div>

            </div>

        </div>
    </div>
    @endif
</div>

@endsection