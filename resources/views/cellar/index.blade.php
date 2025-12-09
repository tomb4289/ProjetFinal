@extends('layouts.app')
@section('title', 'Mes Celliers')

{{-- Ajoute le bouton Ajouter un cellier. Voir app.blade.php --}}
@section('add-cellar-btn', '')

@section('content')
    <section class="p-4 pt-2" aria-label="Gestion de mes celliers">
        {{-- En-tête avec compteur de celliers --}}
        <header class="mt-header flex flex-wrap justify-between items-center" aria-label="En-tête de la page">
            <h1 class="text-3xl font-bold font-heading text-heading">Mes Celliers</h1>
            @if(isset($celliersCount))
                <span class="text-sm font-medium text-text-muted">
                    {{ $celliersCount }}/6
                </span>
            @endif
        </header>
        
        

        <div 
            class="mt-6 {{ $celliers->isEmpty() 
            ? 'mt-6 flex justify-center' 
            : 'mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3' }} gap-3" 
            aria-label="Liste des celliers"
        >
            @forelse ($celliers as $cellier)
                <x-cellar-box 
                    :name="$cellier->nom" 
                    :amount="$cellier->total_bouteilles ?? 0" 
                    :id="$cellier->id"
                />
            {{-- Si vide, affiche un message --}}
            @empty
                <x-empty-state 
                    title="Vous n'avez pas encore de cellier" 
                    subtitle="Créez un cellier pour commencer à y ajouter des bouteilles."
                    actionLabel="Créer un cellier"
                    actionUrl="{{ route('cellar.create') }}"
                />
            @endforelse
        </div>
        
        
        @if(isset($showWelcomeTip) && $showWelcomeTip && isset($welcomeTipCellierId))
            <script>
                (function() {
                    function initWelcomeTip() {
                        if (!window.showTypewriterToast) {
                            setTimeout(initWelcomeTip, 50);
                            return;
                        }
                        
                        const cellierElement = document.getElementById('cellier-{{ $welcomeTipCellierId }}');
                        if (cellierElement) {
                            const cellierLink = cellierElement.querySelector('a[aria-label*="Ouvrir le cellier"]');
                            const rect = cellierElement.getBoundingClientRect();
                            const toastTop = window.innerHeight * 0.28;
                            const cellierCenterX = rect.left + (rect.width / 2);
                            const toastWidth = 384;
                            const padding = 20;
                            const minLeft = padding + (toastWidth / 2);
                            const maxLeft = window.innerWidth - padding - (toastWidth / 2);
                            const clampedLeft = Math.max(minLeft, Math.min(maxLeft, cellierCenterX));
                            
                            if (cellierLink) {
                                cellierLink.classList.add('flash-border-red-wine');
                                setTimeout(function() {
                                    cellierLink.classList.remove('flash-border-red-wine');
                                }, 8000);
                            }
                            
                            window.showTypewriterToast(
                                "Voici votre premier cellier, cliquez dessus pour y entrer",
                                {
                                    position: {
                                        top: toastTop,
                                        left: clampedLeft,
                                        transform: 'translateX(-50%)'
                                    },
                                    speed: 40,
                                    duration: 5500,
                                    fontSize: '1.3rem',
                                    onComplete: function() {
                                        setTimeout(function() {
                                            const cellierBox = document.getElementById('cellier-{{ $welcomeTipCellierId }}');
                                            const dropdownBtn = cellierBox ? cellierBox.querySelector('button[aria-haspopup="true"]') : null;
                                            
                                            if (dropdownBtn && window.showTypewriterToast) {
                                                const moreVerticalSvg = dropdownBtn.querySelector('svg');
                                                const rect = dropdownBtn.getBoundingClientRect();
                                                const toastTop = rect.top - 40;
                                                const toastLeft = rect.right + 15;
                                                const toastWidth = 384;
                                                const padding = 20;
                                                const maxLeft = window.innerWidth - padding - (toastWidth / 2);
                                                const clampedLeft = Math.min(maxLeft, toastLeft);
                                                
                                                if (moreVerticalSvg) {
                                                    moreVerticalSvg.classList.add('flash-border-red-wine');
                                                    setTimeout(function() {
                                                        moreVerticalSvg.classList.remove('flash-border-red-wine');
                                                    }, 7000);
                                                }
                                                
                                                window.showTypewriterToast(
                                                    "Et cliquez ici pour le modifier ou supprimer",
                                                    {
                                                        position: 'top-center',
                                                        speed: 40,
                                                        duration: 5000,
                                                        fontSize: '1.3rem'
                                                    }
                                                );
                                            } else {
                                                window.showTypewriterToast(
                                                    "Et cliquez ici pour le modifier ou supprimer",
                                                    {
                                                        position: 'top-center',
                                                        speed: 40,
                                                        duration: 5000,
                                                        fontSize: '1.3rem'
                                                    }
                                                );
                                            }
                                        }, 2000);
                                    }
                                }
                            );
                        }
                    }
                    setTimeout(initWelcomeTip, 500);
                })();
            </script>
        @endif
        
    </section>
@endsection