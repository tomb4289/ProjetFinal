@extends('layouts.app')

@section('title', 'Détails de la bouteille')

@php
    // Déterminer si c'est une bouteille du catalogue ou d'un cellier
    $isCatalogue = isset($bouteilleCatalogue) && !isset($bouteille);
    $backRoute = $isCatalogue ? route('bouteille.catalogue') : route('cellar.show', $cellier);
    $backLabel = $isCatalogue ? 'Retour au catalogue' : 'Retour au cellier';
@endphp

@section('content')
<div class="min-h-screen bg-background pt-24">
    <section class="p-4 sm:w-full max-w-4xl mx-auto">
        <div class="bg-card border border-border-base rounded-xl shadow-md p-6 space-y-6">
            
            {{-- En-tête avec bouton retour --}}
            <div class="flex items-center justify-between mb-6">
                <x-page-header 
                    title="Détails de la bouteille" 
                    :undertitle="$isCatalogue ? 'Informations complètes sur cette bouteille du catalogue' : 'Informations complètes sur cette bouteille'" 
                />
                <x-back-btn :route="$backRoute" />
            </div>

            {{-- Contenu principal --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- Image de la bouteille --}}
                <div class="flex items-center justify-center bg-gray-50 rounded-lg p-8 min-h-[400px]">
                    @if($donnees['image'])
                        @php
                            // Normaliser le chemin de l'image
                            $imagePath = ltrim($donnees['image'], '/');
                            // Si l'image commence déjà par http, c'est une URL complète
                            if (str_starts_with($imagePath, 'http')) {
                                $imageUrl = $imagePath;
                            } else {
                                // Sinon, utiliser asset() pour le chemin local
                                while (str_starts_with($imagePath, 'storage/')) {
                                    $imagePath = substr($imagePath, 8);
                                }
                                $imageUrl = asset('storage/' . $imagePath);
                            }
                        @endphp
                        <img 
                            src="{{ $imageUrl }}" 
                            alt="{{ $donnees['nom'] }}" 
                            class="max-w-full max-h-[400px] object-contain"
                        >
                    @else
                        <div class="text-center text-text-muted">
                            <svg class="w-24 h-24 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm">Aucune image disponible</p>
                        </div>
                    @endif
                </div>

                {{-- Informations de la bouteille --}}
                <div class="space-y-6">
                    
                    {{-- Nom --}}
                    <div>
                        <h2 class="text-3xl font-bold text-text-heading mb-2">
                            {{ $donnees['nom'] }}
                        </h2>
                    </div>

                    {{-- Informations détaillées --}}
                    <div class="space-y-4">
                        
                        {{-- Type --}}
                        @if($donnees['type'])
                            <div class="border-b border-border-base pb-3">
                                <span class="text-sm font-medium text-text-muted uppercase tracking-wide">Type</span>
                                <p class="text-lg text-text-body mt-1">{{ $donnees['type'] }}</p>
                            </div>
                        @endif

                        {{-- Pays --}}
                        @if($donnees['pays'])
                            <div class="border-b border-border-base pb-3">
                                <span class="text-sm font-medium text-text-muted uppercase tracking-wide">Pays</span>
                                <p class="text-lg text-text-body mt-1">{{ $donnees['pays'] }}</p>
                            </div>
                        @endif

                        {{-- Région (seulement pour le catalogue) --}}
                        @if($isCatalogue && isset($donnees['region']) && $donnees['region'])
                            <div class="border-b border-border-base pb-3">
                                <span class="text-sm font-medium text-text-muted uppercase tracking-wide">Région</span>
                                <p class="text-lg text-text-body mt-1">{{ $donnees['region'] }}</p>
                            </div>
                        @endif

                        {{-- Millésime --}}
                        @if($donnees['millesime'])
                            <div class="border-b border-border-base pb-3">
                                <span class="text-sm font-medium text-text-muted uppercase tracking-wide">Millésime</span>
                                <p class="text-lg text-text-body mt-1 font-semibold">{{ $donnees['millesime'] }}</p>
                            </div>
                        @endif

                        {{-- Format/Volume --}}
                        @if($donnees['format'])
                            <div class="border-b border-border-base pb-3">
                                <span class="text-sm font-medium text-text-muted uppercase tracking-wide">{{ $isCatalogue ? 'Volume' : 'Format' }}</span>
                                <p class="text-lg text-text-body mt-1">{{ $donnees['format'] }}</p>
                            </div>
                        @endif

                        {{-- Prix --}}
                        @if($donnees['prix'])
                            <div class="border-b border-border-base pb-3">
                                <span class="text-sm font-medium text-text-muted uppercase tracking-wide">Prix</span>
                                <p class="text-lg text-text-body mt-1 font-semibold">
                                    {{ number_format($donnees['prix'], 2, ',', ' ') }} $
                                </p>
                            </div>
                        @endif

                        {{-- Code SAQ (seulement pour le catalogue) --}}
                        @if($isCatalogue && isset($donnees['code_saq']) && $donnees['code_saq'])
                            <div class="border-b border-border-base pb-3">
                                <span class="text-sm font-medium text-text-muted uppercase tracking-wide">Code SAQ</span>
                                <p class="text-lg text-text-body mt-1">{{ $donnees['code_saq'] }}</p>
                            </div>
                        @endif

                        {{-- Quantité (seulement pour les bouteilles du cellier) --}}
                        @if(!$isCatalogue && isset($donnees['quantite']))
                            <div class="border-b border-border-base pb-3">
                                <span class="text-sm font-medium text-text-muted uppercase tracking-wide">Quantité</span>
                                <p class="text-lg text-text-body mt-1 font-semibold">
                                    {{ $donnees['quantite'] }} {{ $donnees['quantite'] > 1 ? 'bouteilles' : 'bouteille' }}
                                </p>
                            </div>
                        @endif

                        {{-- Date d'ajout (seulement pour les bouteilles du cellier) --}}
                        @if(!$isCatalogue && isset($donnees['date_ajout']) && $donnees['date_ajout'])
                            <div class="border-b border-border-base pb-3">
                                <span class="text-sm font-medium text-text-muted uppercase tracking-wide">Date d'ajout</span>
                                <p class="text-lg text-text-body mt-1">
                                    {{ $donnees['date_ajout']->format('d/m/Y') }}
                                </p>
                            </div>
                        @endif

                    </div>

                    {{-- Section notation et notes de dégustation (seulement pour les bouteilles du cellier) --}}
                    @if(!$isCatalogue)
                        <div class="mt-8 pt-6 border-t border-border-base">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-semibold text-text-heading">Évaluation</h3>
                                <a 
                                    href="{{ route('bouteilles.note.edit', [$cellier, $bouteille]) }}"
                                    class="text-sm text-primary hover:underline"
                                >
                                    {{ (isset($donnees['note_degustation']) && $donnees['note_degustation']) || (isset($donnees['rating']) && $donnees['rating']) ? 'Modifier' : 'Ajouter' }}
                                </a>
                            </div>
                            
                            {{-- Affichage de la notation par étoiles --}}
                            <div class="mb-4">
                                <span class="text-sm font-medium text-text-muted mb-2 block">Note</span>
                                <x-star-rating 
                                    :rating="$donnees['rating'] ?? null" 
                                    :editable="false"
                                />
                            </div>
                            
                            {{-- Affichage des notes de dégustation --}}
                            <div class="mt-4">
                                <span class="text-sm font-medium text-text-muted mb-2 block">Notes de dégustation</span>
                                @if(isset($donnees['note_degustation']) && $donnees['note_degustation'])
                                    <div class="bg-gray-50 rounded-lg p-4 border border-border-base">
                                        <p class="text-text-body whitespace-pre-wrap">{{ $donnees['note_degustation'] }}</p>
                                    </div>
                                @else
                                    <div class="bg-gray-50 rounded-lg p-4 border border-border-base text-center">
                                        <p class="text-text-muted italic">Aucune note de dégustation pour le moment.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Formulaire d'ajout au cellier (seulement pour les bouteilles du catalogue) --}}
                    @if($isCatalogue)
                        <div class="mt-6 pt-6 border-t border-border-base">
                            <form class="flex gap-3 flex-row flex-wrap items-end add-to-cellar-form">
                                <input 
                                    type="hidden" 
                                    name="bottle_id" 
                                    value="{{ $bouteilleCatalogue->id }}"
                                >

                                <div class="flex-1 min-w-[120px]">
                                    <label class="block text-sm font-medium text-text-muted mb-2">
                                        Quantité
                                    </label>
                                    <input 
                                        type="number"
                                        name="quantity"
                                        min="1"
                                        max="10"
                                        value="1"
                                        class="w-full text-center border border-border-base rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                                    />
                                </div>

                                <button 
                                    type="submit"
                                    class="add-to-cellar-btn bg-button-default active:bg-primary-active hover:bg-button-hover animation duration-200 text-white rounded-lg px-6 py-2 font-medium"
                                    data-bottle-id="{{ $bouteilleCatalogue->id }}"
                                >
                                    Ajouter au cellier
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex gap-3 mt-6 pt-6 border-t border-border-base">
                        <x-back-btn :route="$backRoute" :label="$backLabel" />
                        
                        {{-- Bouton modifier (seulement pour les bouteilles du cellier ajoutées manuellement, sans code SAQ) --}}
                        @if(!$isCatalogue && empty($bouteille->code_saq))
                            <x-edit-btn
                                :route="route('bouteilles.edit', [$cellier, $bouteille])"
                                label="Modifier"
                            />
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
@endsection
