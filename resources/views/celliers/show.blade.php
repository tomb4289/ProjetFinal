@extends('layouts.app') 

@section('title', 'Mon cellier – ' . $cellier->nom)

@section('add-wine-btn', '')

@section('content')
<section class="p-4 pt-2" aria-label="Détails du cellier {{ $cellier->nom }}">
    <x-page-header
        :title="$cellier->nom"
        :undertitle="$undertitle"
    />
    {{-- Composant de recherche / filtres / tri (mode cellier) --}}
    <x-search-filter
        :pays="$pays"
        :types="$types"
        :regions="$regions"
        :millesimes="$millesimes"
        mode="cellier" 
        data-search-url="{{ route('celliers.search', $cellier) }}"
        data-target-container="cellarBottlesContainer"
        class="mt-3 mb-8"
    />

    {{-- Conteneur mis à jour par AJAX --}}
    {{-- Ajout de aria-live="polite" pour que les lecteurs d'écran annoncent les changements AJAX --}}
    <div id="cellarBottlesContainer" role="region" aria-live="polite" aria-label="Liste des bouteilles">
        @include('celliers._bouteilles_list', ['cellier' => $cellier])
    </div>
</section>

{{-- Fenêtre flottante "Ajouter un vin" --}}
{{-- Ajout de role="dialog" pour la modale --}}
<div
    id="addWineBtnContainer"
    class="fixed z-50 bottom-0 left-0 w-full p-4 py-10 bg-card border border-border-base shadow-lg rounded-t-lg transform translate-y-full transition-transform duration-300"
    role="dialog"
    aria-modal="true"
    aria-labelledby="titre-ajout-vin"
    aria-hidden="true"
>
    <span class="flex items-center justify-between mb-4">
        {{-- Ajout d'un ID pour lier le titre au role="dialog" --}}
        <h1 id="titre-ajout-vin" class="text-3xl text-heading font-heading">Ajouter un vin</h1>
        <x-dynamic-component :component="'lucide-x'" id="closeAddWine" class="w-6 h-6" />
    </span>

    <div class="flex flex-col gap-4" role="group" aria-label="Options d'ajout">
        <x-icon-text-btn
            :href="route('bouteille.catalogue')"
            icon="wine"
            title="Explorer le catalogue SAQ"
            subtitle="Recherchez des vins répertoriés à la SAQ."
        />
        <x-icon-text-btn
            :href="route('bouteilles.manuelles.create', $cellier->id)"
            icon="notebook-pen"
            title="Ajouter manuellement"
            subtitle="Pour les vins non répertoriés à la SAQ."
        />
    </div>
</div>
@endsection