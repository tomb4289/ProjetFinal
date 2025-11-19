@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-background pt-24">
    <div class="max-w-xl mx-auto bg-card border border-border-base rounded-xl shadow-md p-6 space-y-6">
        <h1 class="text-xl font-bold text-text-title">
            Ajout manuel d’une bouteille
        </h1>

        <form
            id="form-bouteille-manuelle"
            method="POST"
            action="{{ route('bouteilles.manuelles.store', $cellier) }}"
            novalidate
            class="space-y-4"
        >
            @csrf

            {{-- Nom (obligatoire) --}}
            <x-input
                label="Nom de la bouteille"
                name="nom"
                :required="true"
                placeholder="Ex : Château X"
            />

            {{-- Pays --}}
            <x-input
                label="Pays"
                name="pays"
                placeholder="Ex : France"
            />

            {{-- Format --}}
            <x-input
                label="Format"
                name="format"
                placeholder="Ex : 750 ml"
            />

            {{-- Quantité (obligatoire) --}}
            <x-input
                label="Quantité"
                name="quantite"
                type="number"
                :required="true"
                placeholder="Ex : 1"
            />

            {{-- Prix (obligatoire, décimal) --}}
            <x-input
                label="Prix"
                name="prix"
                type="text"
                :required="true"
                placeholder="Ex : 12.50"
            />

            <div class="pt-4 flex justify-end">
                <x-primary-btn
                    label="Ajouter la bouteille"
                    id="btn-submit-bouteille"
                />
            </div>
        </form>
    </div>
</div>

{{-- ton script JS de validation reste pareil --}}
@endsection
