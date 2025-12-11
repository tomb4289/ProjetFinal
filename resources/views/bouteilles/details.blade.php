@extends('layouts.app')

@section('title', 'Détails de la bouteille')

@php
    // Est-ce qu'on est sur une bouteille du catalogue ?
    $isCatalogue = isset($bouteilleCatalogue) && !isset($bouteille);

    // URL d'où on vient
    $previousUrl  = url()->previous();
    // URL de la liste d'achat
    $wishlistUrl  = route('listeAchat.index');
    // Est-ce qu'on vient de la liste d'achat ?
    $fromWishlist = str_contains($previousUrl, $wishlistUrl);
    // Est-ce qu'on vient d'un signalement admin ?
    $isSignalementShow = preg_match('#/admin/signalements/(\d+)$#', $previousUrl, $matches);


    if ($fromWishlist) {
        // Cas spécial : on vient de "Ma liste d'achat"
        $backRoute = $wishlistUrl;
        $backLabel = "Retour à ma liste d'achat";
    } elseif ($isSignalementShow) {
        // Cas spécial : on vient d'un signalement admin
        $backRoute = $previousUrl;
        $backLabel = 'Retour aux signalements';
    } elseif ($isCatalogue) {
        // Cas catalogue : on revient au catalogue (page précédente)
        $backRoute = $previousUrl;
        $backLabel = 'Retour au catalogue';
    } else {
        // Cas cellier : toujours retourner au cellier, pas à la page précédente
        $backRoute = route('cellar.show', $cellier);
        $backLabel = 'Retour au cellier';
    }
@endphp


@section('content')
<div class="min-h-screen  pt-24 pb-12" aria-label="Détails de la bouteille">
    <section class="container max-w-5xl mx-auto px-4 sm:px-6">

        {{-- En-tête simplifié --}}
        <div class="flex items-center justify-between mb-6">
            <x-back-btn :route="$backRoute" :label="$backLabel" />
        </div>

        {{-- Carte Principale --}}
        <div class="bg-card rounded-2xl shadow-sm border border-border-base overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-12">

                {{-- Colonne image + type --}}
                <div class="md:col-span-4 bg-neutral-200 flex items-center justify-center p-8 md:p-10 border-b md:border-b-0 md:border-r border-border-base relative">
                    {{-- Badge Type --}}
                    @if($donnees['type'])
                    <span class="absolute top-4 left-4 bg-card px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider text-muted shadow-sm border border-border-base">
                        {{ $donnees['type'] }}
                    </span>
                    @endif

                    @if($donnees['image'])
                    @php
                    $imagePath = ltrim($donnees['image'], '/');
                    if (str_starts_with($imagePath, 'http')) {
                    $imageUrl = $imagePath;
                    } else {
                    // Nettoyer les "storage/storage/..."
                    while (str_starts_with($imagePath, 'storage/')) {
                    $imagePath = substr($imagePath, 8);
                    }
                    $imageUrl = asset('storage/' . $imagePath);
                    }
                    @endphp

                    {{-- 🔍 Image avec zoom (composant réutilisable) --}}
                    <x-image-zoom
                        :src="$imageUrl"
                        :alt="'Bouteille '.$donnees['nom']"
                        img-class="w-auto h-64 md:h-80 object-contain drop-shadow-lg transition-transform duration-500 hover:scale-105" />
                    @else
                    <div class="text-center opacity-20" role="status">
                        <svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="150" height="150" viewBox="0 0 300 300">
                            <g transform="translate(0,300) scale(0.05,-0.05)" fill="currentColor" stroke="none">
                                <path d="M2771 5765 c-8 -19 -13 -325 -12 -680 3 -785 6 -767 -189 -955 -231 -222 -214 -70 -225 -2018 -10 -1815 -11 -1791 100 -1831 215 -77 1028 -70 1116 10 73 66 77 168 80 1839 4 1928 18 1815 -254 2058 -141 126 -147 164 -147 878 0 321 -6 618 -13 659 l-12 75 -215 0 c-187 0 -218 -5 -229 -35z" />
                            </g>
                        </svg>
                    </div>
                    @endif
                </div>

                {{-- Colonne texte / infos --}}
                <div class="md:col-span-8 p-6 md:p-8 flex flex-col h-full">

                    {{-- 1. Header: Nom & Prix --}}
                    <div class="flex flex-wrap justify-between items-start gap-4 mb-6 border-b border-border-base pb-6">
                        <div class="flex-1">
                            <h1 class="text-2xl md:text-3xl font-bold text-heading leading-tight">
                                {{ $donnees['nom'] }}
                            </h1>
                            <div class="flex items-center flex-wrap gap-2 mt-2 text-sm text-neutral-700">
                                @if($donnees['pays'])
                                <span>{{ $donnees['pays'] }}</span>
                                @endif
                                @if(isset($donnees['region']) && $donnees['region'])
                                <span class="w-1 h-1 bg-neutral-600 rounded-full"></span>
                                <span>{{ $donnees['region'] }}</span>
                                @endif
                            </div>
                        </div>

                        @if($donnees['prix'])
                        <div class="text-right">
                            <p class="text-2xl font-bold text-primary">
                                {{ number_format($donnees['prix'], 2, ',', ' ') }} $
                            </p>
                        </div>
                        @endif
                    </div>

                    {{-- 2. Grid de Détails --}}
                    <div class="grid grid-cols-2 gap-y-6 gap-x-4 mb-8">

                        @if(isset($donnees['type']) && $donnees['type'])
                        <div>
                            <span class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Type</span>
                            <span class="text-heading font-medium">{{ $donnees['type'] }}</span>
                        </div>
                        @endif

                        @if($donnees['pays'])
                        <div>
                            <span class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Pays</span>
                            <span class="text-heading font-medium">{{ $donnees['pays'] }}</span>
                        </div>
                        @endif

                        @if(isset($donnees['region']) && $donnees['region'])
                        <div>
                            <span class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Région</span>
                            <span class="text-heading font-medium">{{ $donnees['region'] }}</span>
                        </div>
                        @endif

                        @if($donnees['millesime'])
                        <div>
                            <span class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Millésime</span>
                            <span class="text-heading font-medium">{{ $donnees['millesime'] }}</span>
                        </div>
                        @endif

                        @if(isset($donnees['format']) && $donnees['format'])
                        <div>
                            <span class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">{{ $isCatalogue ? 'Volume' : 'Format' }}</span>
                            <span class="text-heading font-medium">{{ $donnees['format'] }}</span>
                        </div>
                        @endif

                        @if(isset($donnees['code_saq']) && $donnees['code_saq'])
                        <div>
                            <span class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Code SAQ</span>
                            <span class="font-mono text-heading font-medium">{{ $donnees['code_saq'] }}</span>
                        </div>
                        @endif

                        {{-- Lien SAQ.com (pour catalogue et bouteilles du cellier avec code_saq) --}}
                        @if(isset($donnees['url_saq']) && $donnees['url_saq'])
                        <div class="col-span-2">
                            <span class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Voir sur SAQ.com</span>
                            <a
                                href="{{ $donnees['url_saq'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 text-primary hover:text-primary-hover hover:underline font-semibold transition-colors"
                                aria-label="Ouvrir la page de la bouteille sur SAQ.com dans un nouvel onglet">
                                <span>Visiter la page SAQ</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        </div>
                        @endif

                        @if(!$isCatalogue && isset($donnees['quantite']))
                        <div>
                            <span class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">En stock</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium text-primary">
                                {{ $donnees['quantite'] }} {{ $donnees['quantite'] > 1 ? 'bouteilles' : 'bouteille' }}
                            </span>
                        </div>
                        @endif

                        @if(!$isCatalogue && isset($donnees['date_ajout']) && $donnees['date_ajout'])
                        <div class="col-span-2">
                            <span class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Ajouté le</span>
                            <span class="text-heading font-medium">{{ $donnees['date_ajout']->format('d F Y') }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- 3. Zone d'Actions --}}
                    <div class="mt-auto pt-6 border-t border-border-base">

                        {{-- Catalogue (Ajout) --}}
                        @if($isCatalogue)
                        <form class="flex flex-col sm:flex-row gap-4 items-end sm:items-stretch md-5" aria-label="Ajouter au cellier">
                            <input type="hidden" name="bottle_id" value="{{ $bouteilleCatalogue->id }}">

                            <div class="w-full sm:w-32">
                                <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1.5 ml-1" for="quantity">Quantité</label>
                                <div class="relative">
                                    <input
                                        type="number"
                                        name="quantity"
                                        label="Quantité"
                                        id="quantity"
                                        aria-label="Quantité"
                                        min="1" max="10" value="1"
                                        class="block w-full text-center rounded-lg border-border-base shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5">
                                </div>
                            </div>

                            <button type="submit" data-bottle-id="{{ $bouteilleCatalogue->id }}" class="add-to-cellar-btn w-full sm:flex-1 text-primary bg-button-default border-2 border-primary hover:bg-primary hover:text-card font-medium py-2.5 px-4 rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center gap-2">
                                <x-dynamic-component :component="'lucide-plus'" class="w-4 h-4" />
                                Ajouter au cellier
                            </button>
                        </form>
                        <a href="{{ route('signalement.create', $bouteilleCatalogue->id) }}" class="inline-block mt-4 text-base font-semibold text-primary hover:text-primary-active hover:underline transition-colors px-3 py-2 rounded-lg hover:bg-primary/10">Signaler un problème</a>

                        {{-- Cellier (Notes & Modif) --}}
                        @else
                        <div class="space-y-6">
                            {{-- Note et Avis --}}
                            <div class="bg-neutral-200 rounded-xl p-5 border border-border-base relative">
                                <div class="flex justify-between items-center mb-3">
                                    <h2 class="font-semibold text-heading">Notes de dégustation</h2>
                                    @if(isset($donnees['note_degustation']) || isset($donnees['rating']))
                                    <x-dropdown-action
                                        :id="'note-' . $bouteille->id"
                                        :deleteUrl="route('bouteilles.note.delete', [$cellier, $bouteille])"
                                        :editUrl="route('bouteilles.note.edit', [$cellier, $bouteille])" />
                                    @else
                                    <a href="{{ route('bouteilles.note.edit', [$cellier, $bouteille]) }}" class="text-base font-semibold text-primary hover:text-primary-active hover:underline transition-colors px-3 py-1.5 rounded-lg hover:bg-primary/10">
                                        Rédiger un avis
                                    </a>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <x-star-rating :rating="$donnees['rating'] ?? null" :editable="false" />
                                </div>

                                @if(isset($donnees['note_degustation']) && $donnees['note_degustation'])
                                <p class="text-sm text-muted italic leading-relaxed">"{{ $donnees['note_degustation'] }}"</p>
                                @else
                                <p class="text-sm text-muted italic">Aucune note écrite pour le moment.</p>
                                @endif
                            </div>

                        </div>
                        @endif

                        {{-- Bouton Partager et Modifier (seulement pour les bouteilles du cellier) --}}
                        @if(!$isCatalogue)
                        <div class="flex justify-between items-center mt-[25px]">
                            <button
                                type="button"
                                id="shareBottleBtn"
                                class="flex items-center gap-3 px-6 py-3 bg-button-default border-2 border-primary text-primary font-semibold rounded-lg hover:bg-button-hover hover:text-card active:bg-primary-active transition-colors duration-300 text-base cursor-pointer"
                                data-bouteille-id="{{ $bouteille->id }}"
                                aria-label="Partager cette bouteille">
                                <x-dynamic-component :component="'lucide-share-2'" class="w-6 h-6" />
                                <span>Partager</span>
                            </button>
                            @if(empty($bouteille->code_saq))
                            <x-edit-btn :route="route('bouteilles.edit', [$cellier, $bouteille])" label="Modifier la fiche" />
                            @endif
                        </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
@endsection