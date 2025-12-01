@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-background pt-24" role="main" aria-label="Gestion de mes celliers">
    <div class="max-w-4xl mx-auto bg-card border border-border-base rounded-xl shadow-md p-6 space-y-4" role="list">
        <h1 class="text-xl font-bold text-text-title mb-4">
            Mes celliers
        </h1>

        @forelse($celliers as $cellier)
            <div class="border border-border-base rounded-lg px-4 py-3 flex items-center justify-between mb-2" role="listitem">
                <div>
                    {{-- Nom du cellier cliquable vers la vue principale (PV-13) --}}
                    <a
                        href="{{ route('celliers.show', $cellier) }}"
                        class="font-semibold text-text-body hover:underline"
                        aria-label="Ouvrir le cellier {{ $cellier->nom }}"
                    >
                        {{ $cellier->nom }}
                    </a>
                </div>

                {{-- Raccourci direct vers l’ajout manuel (PV-15) --}}
                <a
                    href="{{ route('bouteilles.manuelles.create', $cellier) }}"
                    class="text-sm font-medium text-primary hover:underline"
                    aria-label="Ajouter une bouteille au cellier {{ $cellier->nom }}"
                >
                    Ajouter une bouteille
                </a>
            </div>
        @empty
            <p class="text-text-muted" role="status">
                Vous n’avez encore aucun cellier.
            </p>
        @endforelse
    </div>
</div>
@endsection