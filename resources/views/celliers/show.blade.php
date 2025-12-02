@extends('layouts.app') 

@section('title', 'Mon cellier – ' . $cellier->nom)

@section('add-wine-btn', 'true')

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

@php
    // Vérifier si le cellier est vide (sans filtres actifs)
    $bottles = $cellier->bouteilles ?? collect();
    $hasFilters = request()->filled('nom')
        || request()->filled('pays')
        || request()->filled('type')
        || request()->filled('millesime')
        || request()->filled('prix_min')
        || request()->filled('prix_max');
    $isCellierEmpty = $bottles->isEmpty() && !$hasFilters;
@endphp

@if($isCellierEmpty)
    <script>
        (function() {
            function initEmptyCellierToast() {
                if (!window.showTypewriterToast) {
                    setTimeout(initEmptyCellierToast, 50);
                    return;
                }
                
                // Attendre que le DOM soit complètement chargé
                setTimeout(function() {
                    const addWineBtn = document.getElementById('addWineToCellar');
                    
                    if (addWineBtn) {
                        // Ajouter un effet visuel au bouton
                        addWineBtn.classList.add('flash-border-red-wine');
                        setTimeout(function() {
                            addWineBtn.classList.remove('flash-border-red-wine');
                        }, 12000);
                    }
                    
                    // Créer un toast personnalisé avec deux paragraphes
                    const container = document.getElementById("typewriter-toast-container");
                    if (!container) return;
                    
                    const toast = document.createElement("div");
                    toast.className = `
                        text-primary font-medium text-base
                        animate-fade-in
                        relative
                    `;
                    toast.style.display = 'flex';
                    toast.style.flexDirection = 'column';
                    toast.style.alignItems = 'flex-end';
                    toast.style.gap = '0.5rem';
                    toast.style.maxWidth = '384px';
                    toast.style.textAlign = 'right';
                    
                    // Positionner le toast (sera ajusté après l'ajout au DOM)
                    toast.style.position = 'fixed';
                    toast.style.zIndex = '50';
                    
                    // Premier paragraphe
                    const paragraph1 = document.createElement("p");
                    paragraph1.className = "typewriter-text handwriting-text";
                    paragraph1.style.fontFamily = '"Caveat", cursive';
                    paragraph1.style.fontSize = '1.5rem';
                    paragraph1.style.fontWeight = '500';
                    paragraph1.style.letterSpacing = '0.03em';
                    paragraph1.style.display = 'inline-block';
                    paragraph1.style.margin = '0';
                    
                    // Deuxième paragraphe
                    const paragraph2 = document.createElement("p");
                    paragraph2.className = "typewriter-text handwriting-text";
                    paragraph2.style.fontFamily = '"Caveat", cursive';
                    paragraph2.style.fontSize = '1.5rem';
                    paragraph2.style.fontWeight = '500';
                    paragraph2.style.letterSpacing = '0.03em';
                    paragraph2.style.display = 'inline-block';
                    paragraph2.style.margin = '0';
                    
                    toast.appendChild(paragraph1);
                    toast.appendChild(paragraph2);
                    container.appendChild(toast);
                    container.style.display = "block";
                    
                    // Positionner le toast à gauche du bouton "Ajouter +" ou à 20% depuis la droite
                    if (addWineBtn) {
                        const btnRect = addWineBtn.getBoundingClientRect();
                        const toastWidth = 384;
                        const spacing = 20; // Espace entre le toast et le bouton
                        // Positionner le toast à gauche du bouton (le bord droit du toast à gauche du bouton)
                        const toastRight = window.innerWidth - btnRect.left + spacing;
                        const minRight = 20; // Minimum pour éviter de sortir de l'écran
                        const calculatedRight = Math.max(minRight, toastRight);
                        
                        // Aligner verticalement avec le bouton
                        toast.style.bottom = (window.innerHeight - btnRect.bottom) + 'px';
                        toast.style.right = calculatedRight + 'px';
                    } else {
                        // Fallback: 20% depuis la droite
                        toast.style.bottom = '100px';
                        toast.style.right = '20%';
                    }
                    
                    // Animer le premier paragraphe
                    const message1 = "Cliquez ici pour ajouter";
                    const message2 = "votre première bouteille!";
                    
                    let index1 = 0;
                    const writeParagraph1 = () => {
                        if (index1 < message1.length) {
                            const char = message1[index1];
                            paragraph1.textContent += char;
                            index1++;
                            
                            let nextDelay = 60;
                            if (char === ',' || char === '.' || char === '!' || char === '?') {
                                nextDelay = 60 * 2.5;
                            } else if (char === ' ') {
                                nextDelay = 60 * 1.5;
                            } else {
                                nextDelay = 60 * (0.8 + Math.random() * 0.4);
                            }
                            
                            setTimeout(writeParagraph1, nextDelay);
                        } else {
                            // Commencer le deuxième paragraphe après une petite pause
                            setTimeout(() => {
                                let index2 = 0;
                                const writeParagraph2 = () => {
                                    if (index2 < message2.length) {
                                        const char = message2[index2];
                                        paragraph2.textContent += char;
                                        index2++;
                                        
                                        let nextDelay = 60;
                                        if (char === ',' || char === '.' || char === '!' || char === '?') {
                                            nextDelay = 60 * 2.5;
                                        } else if (char === ' ') {
                                            nextDelay = 60 * 1.5;
                                        } else {
                                            nextDelay = 60 * (0.8 + Math.random() * 0.4);
                                        }
                                        
                                        setTimeout(writeParagraph2, nextDelay);
                                    } else {
                                        // Animation terminée, attendre avant de fermer
                                        setTimeout(() => {
                                            toast.style.opacity = "0";
                                            toast.style.transform = "translateY(-20px)";
                                            toast.style.transition = "opacity 0.5s, transform 0.5s";
                                            setTimeout(() => {
                                                toast.remove();
                                                if (container.children.length === 0) {
                                                    container.style.display = "none";
                                                }
                                            }, 500);
                                        }, 12000);
                                    }
                                };
                                writeParagraph2();
                            }, 300);
                        }
                    };
                    
                    writeParagraph1();
                }, 500);
            }
            setTimeout(initEmptyCellierToast, 500);
        })();
    </script>
@endif
@endsection