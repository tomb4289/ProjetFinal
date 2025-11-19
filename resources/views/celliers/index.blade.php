@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-background pt-24">
    <div class="max-w-4xl mx-auto bg-card border border-border-base rounded-xl shadow-md p-6 space-y-4">
        <h1 class="text-xl font-bold text-text-title mb-4">
            Mes celliers
        </h1>

        @forelse($celliers as $cellier)
            <div class="border border-border-base rounded-lg px-4 py-3 flex items-center justify-between mb-2">
                <div>
                    {{-- Nom du cellier cliquable vers la vue principale (PV-13) --}}
                    <a
                        href="{{ route('celliers.show', $cellier) }}"
                        class="font-semibold text-text-body hover:underline"
                    >
                        {{ $cellier->nom }}
                    </a>
                </div>

                {{-- Raccourci direct vers l’ajout manuel (PV-15) --}}
                <a
                    href="{{ route('bouteilles.manuelles.create', $cellier) }}"
                    class="text-sm font-medium text-primary hover:underline"
                >
                    Ajouter une bouteille
                </a>
            </div>
        @empty
            <p class="text-text-muted">
                Vous n’avez encore aucun cellier.
            </p>
        @endforelse
    </div>
</div>
@endsection
