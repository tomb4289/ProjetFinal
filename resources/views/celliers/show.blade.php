@extends('layouts.app') 

@section('title', 'Mon cellier – ' . $cellier->nom)

@section('add-wine-btn', 'true')

@section('content')
<section class="p-4 " aria-label="Détails du cellier {{ $cellier->nom }}">

{{-- Lien retour vers la liste des celliers --}}
    <x-back-btn
        route="cellar.index"
        label="Retour à mes celliers"
        class="mb-4 pt-18"
    />

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
    <div id="cellarBottlesContainer" aria-live="polite" aria-label="Liste des bouteilles">
        @include('celliers._bouteilles_list', ['cellier' => $cellier])
    </div>
</section>

{{-- Fenêtre flottante "Ajouter un vin" --}}
<div
    id="addWineBtnContainer"
    class="fixed z-50 bottom-0 left-0 w-full px-2 sm:px-4 pt-5 pb-10 bg-card border border-border-base shadow-lg rounded-t-lg transform translate-y-full transition-transform duration-300"
    aria-modal="true"
    aria-labelledby="titre-ajout-vin"
    aria-hidden="true"
>
    <span class="flex items-center justify-between mb-4">
        <h1 id="titre-ajout-vin" class="text-3xl text-heading font-heading">Ajouter un vin</h1>
        <x-dynamic-component :component="'lucide-x'" id="closeAddWine" class="w-6 h-6" />
    </span>

    <div class="flex flex-col gap-4" aria-label="Options d'ajout">
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
                        }, 18000);
                    }
                    
                    // Créer un toast personnalisé avec deux paragraphes
                    const container = document.getElementById("typewriter-toast-container");
                    if (!container) return;
                    
                    // Réduire le z-index du conteneur pour ce toast spécifique
                    container.style.zIndex = '40'; // Inférieur à la boîte "Ajouter un vin" (z-50)
                    
                    const toast = document.createElement("div");
                    toast.className = "typewriter-toast-item";
                    toast.style.cssText = `
                        position: fixed;
                        max-width: 400px;
                        min-width: 250px;
                        background: white;
                        border: 2px solid #d1d5db;
                        border-radius: 12px;
                        padding: 16px 20px;
                        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                        z-index: 9999;
                        pointer-events: auto;
                        opacity: 0;
                        transform: translateY(20px) scale(0.95);
                        transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: 0.5rem;
                        text-align: center;
                    `;
                    
                    // Premier paragraphe
                    const paragraph1 = document.createElement("p");
                    paragraph1.className = "typewriter-text-content";
                    paragraph1.style.cssText = `
                        font-family: "Caveat", cursive;
                        font-size: 1.3rem;
                        font-weight: 500;
                        color: #7a1f3d;
                        letter-spacing: 0.03em;
                        line-height: 1.4;
                        display: block;
                        word-wrap: break-word;
                        margin: 0;
                    `;
                    
                    // Deuxième paragraphe
                    const paragraph2 = document.createElement("p");
                    paragraph2.className = "typewriter-text-content";
                    paragraph2.style.cssText = `
                        font-family: "Caveat", cursive;
                        font-size: 1.3rem;
                        font-weight: 500;
                        color: #7a1f3d;
                        letter-spacing: 0.03em;
                        line-height: 1.4;
                        display: block;
                        word-wrap: break-word;
                        margin: 0;
                    `;
                    
                    toast.appendChild(paragraph1);
                    toast.appendChild(paragraph2);
                    container.appendChild(toast);
                    container.style.display = "block";
                    
                    // Animation d'entrée fluide
                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => {
                            toast.style.opacity = "1";
                            toast.style.transform = "translateY(0) scale(1)";
                        });
                    });
                    
                    // Positionner le toast à gauche du bouton "Ajouter +"
                    if (addWineBtn) {
                        const btnRect = addWineBtn.getBoundingClientRect();
                        const toastWidth = 400;
                        const spacing = 20; // Espace entre le toast et le bouton
                        // Positionner le toast à gauche du bouton (le bord droit du toast à gauche du bouton)
                        const toastRight = window.innerWidth - btnRect.left + spacing;
                        const minRight = 20; // Minimum pour éviter de sortir de l'écran
                        const calculatedRight = Math.max(minRight, toastRight);
                        
                        // Aligner verticalement avec le bouton
                        toast.style.bottom = (window.innerHeight - btnRect.bottom) + 'px';
                        toast.style.right = calculatedRight + 'px';
                    } else {
                        // Fallback: bas à droite
                        toast.style.bottom = '20px';
                        toast.style.right = '20px';
                    }
                    
                    // Animer le premier paragraphe avec requestAnimationFrame (comme les autres)
                    const message1 = "Cliquez ici pour ajouter";
                    const message2 = "votre première bouteille!";
                    
                    let index1 = 0;
                    let lastTime1 = performance.now();
                    let animationFrameId1 = null;
                    const speed = 40;
                    
                    const writeParagraph1 = (currentTime) => {
                        if (index1 < message1.length) {
                            const elapsed = currentTime - lastTime1;
                            
                            // Calculer le délai basé sur le caractère
                            let charDelay = speed;
                            const char = message1[index1];
                            
                            if (char === ',' || char === '.' || char === '!' || char === '?') {
                                charDelay = speed * 2;
                            } else if (char === ' ') {
                                charDelay = speed * 1.2;
                            } else {
                                charDelay = speed * (0.9 + Math.random() * 0.2);
                            }
                            
                            if (elapsed >= charDelay) {
                                paragraph1.textContent += char;
                                index1++;
                                lastTime1 = currentTime;
                            }
                            
                            animationFrameId1 = requestAnimationFrame(writeParagraph1);
                        } else {
                            // Commencer le deuxième paragraphe après 2 secondes
                            setTimeout(() => {
                                let index2 = 0;
                                let lastTime2 = performance.now();
                                let animationFrameId2 = null;
                                
                                const writeParagraph2 = (currentTime) => {
                                    if (index2 < message2.length) {
                                        const elapsed = currentTime - lastTime2;
                                        
                                        // Calculer le délai basé sur le caractère
                                        let charDelay = speed;
                                        const char = message2[index2];
                                        
                                        if (char === ',' || char === '.' || char === '!' || char === '?') {
                                            charDelay = speed * 2;
                                        } else if (char === ' ') {
                                            charDelay = speed * 1.2;
                                        } else {
                                            charDelay = speed * (0.9 + Math.random() * 0.2);
                                        }
                                        
                                        if (elapsed >= charDelay) {
                                            paragraph2.textContent += char;
                                            index2++;
                                            lastTime2 = currentTime;
                                        }
                                        
                                        animationFrameId2 = requestAnimationFrame(writeParagraph2);
                                    } else {
                                        // Ajouter un curseur clignotant à la fin
                                        const cursor = document.createElement("span");
                                        cursor.textContent = "|";
                                        cursor.style.cssText = `
                                            display: inline-block;
                                            margin-left: 2px;
                                            animation: blink 1s infinite;
                                            color: #7a1f3d;
                                        `;
                                        paragraph2.appendChild(cursor);
                                        
                                        // Animation terminée, attendre avant de fermer (ajout de 2 secondes)
                                        setTimeout(() => {
                                            // Animation de sortie fluide
                                            toast.style.opacity = "0";
                                            toast.style.transform = "translateY(20px) scale(0.95)";
                                            
                                            setTimeout(() => {
                                                toast.remove();
                                                if (container.children.length === 0) {
                                                    container.style.display = "none";
                                                }
                                            }, 300);
                                        }, 14000);
                                    }
                                };
                                
                                animationFrameId2 = requestAnimationFrame(writeParagraph2);
                            }, 2000);
                        }
                    };
                    
                    animationFrameId1 = requestAnimationFrame(writeParagraph1);
                }, 500);
            }
            setTimeout(initEmptyCellierToast, 500);
        })();
    </script>
@endif
@endsection